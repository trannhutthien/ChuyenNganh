<?php

namespace App\Http\Controllers;

use App\Models\LanLamBai;
use App\Models\BaiKiemTra;
use App\Models\TraLoi;
use App\Models\CauHoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LanLamBaiController extends Controller
{
    /**
     * Bắt đầu làm bài kiểm tra
     */
    public function batDauLamBai(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'baiKiemTraId' => 'required|exists:BaiKiemTra,BaiKiemTraId'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nguoiDungId = $request->user()->NguoiDungId;
        $baiKiemTra = BaiKiemTra::find($request->baiKiemTraId);

        // Kiểm tra bài kiểm tra có công khai không
        if ($baiKiemTra->TrangThai !== 'CONG_KHAI') {
            return response()->json(['message' => 'Bài kiểm tra không khả dụng'], 403);
        }

        // Kiểm tra còn lượt làm bài không
        if (!$baiKiemTra->conLuotLamBai($nguoiDungId)) {
            return response()->json(['message' => 'Bạn đã hết lượt làm bài kiểm tra này'], 403);
        }

        // Kiểm tra có bài làm dở không
        $lanLamBaiDangLam = LanLamBai::where('BaiKiemTraId', $request->baiKiemTraId)
            ->where('NguoiDungId', $nguoiDungId)
            ->dangLam()
            ->first();

        if ($lanLamBaiDangLam) {
            // Kiểm tra đã hết giờ chưa
            if ($lanLamBaiDangLam->daHetGio()) {
                // Tự động nộp bài
                $this->nopBaiInternal($lanLamBaiDangLam);
            } else {
                // Trả về bài làm dở
                return response()->json([
                    'message' => 'Bạn có bài làm dở, đang tiếp tục...',
                    'data' => $this->formatLanLamBai($lanLamBaiDangLam, true)
                ]);
            }
        }

        // Tạo lần làm bài mới
        $lanLamBai = LanLamBai::create([
            'BaiKiemTraId' => $request->baiKiemTraId,
            'NguoiDungId' => $nguoiDungId,
            'ThoiGianBatDau' => now(),
            'TrangThai' => LanLamBai::TRANG_THAI_DANG_LAM
        ]);

        return response()->json([
            'message' => 'Bắt đầu làm bài thành công',
            'data' => $this->formatLanLamBai($lanLamBai, true)
        ], 201);
    }

    /**
     * Lưu câu trả lời
     */
    public function luuTraLoi(Request $request, $lanLamBaiId)
    {
        $lanLamBai = LanLamBai::find($lanLamBaiId);

        if (!$lanLamBai) {
            return response()->json(['message' => 'Không tìm thấy lần làm bài'], 404);
        }

        // Kiểm tra quyền
        if ($lanLamBai->NguoiDungId !== $request->user()->NguoiDungId) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        // Kiểm tra trạng thái
        if ($lanLamBai->TrangThai !== LanLamBai::TRANG_THAI_DANG_LAM) {
            return response()->json(['message' => 'Bài làm đã kết thúc'], 400);
        }

        // Kiểm tra hết giờ
        if ($lanLamBai->daHetGio()) {
            $this->nopBaiInternal($lanLamBai);
            return response()->json(['message' => 'Đã hết thời gian làm bài'], 400);
        }

        $validator = Validator::make($request->all(), [
            'cauHoiId' => 'required|exists:CauHoi,CauHoiId',
            'luaChonIds' => 'nullable|array',
            'luaChonIds.*' => 'exists:LuaChon,LuaChonId',
            'noiDungTraLoi' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm hoặc tạo câu trả lời
        $traLoi = TraLoi::updateOrCreate(
            [
                'LanLamBaiId' => $lanLamBaiId,
                'CauHoiId' => $request->cauHoiId
            ],
            [
                'LuaChonIds' => $request->luaChonIds ? implode(',', $request->luaChonIds) : null,
                'NoiDungTraLoi' => $request->noiDungTraLoi,
                'ThoiGianTraLoi' => now()
            ]
        );

        return response()->json([
            'message' => 'Lưu câu trả lời thành công',
            'data' => [
                'id' => $traLoi->TraLoiId,
                'cauHoiId' => $traLoi->CauHoiId,
                'luaChonIds' => $traLoi->luaChonIdsArray,
                'noiDungTraLoi' => $traLoi->NoiDungTraLoi
            ]
        ]);
    }

    /**
     * Nộp bài
     */
    public function nopBai(Request $request, $lanLamBaiId)
    {
        $lanLamBai = LanLamBai::find($lanLamBaiId);

        if (!$lanLamBai) {
            return response()->json(['message' => 'Không tìm thấy lần làm bài'], 404);
        }

        // Kiểm tra quyền
        if ($lanLamBai->NguoiDungId !== $request->user()->NguoiDungId) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        // Kiểm tra trạng thái
        if ($lanLamBai->TrangThai !== LanLamBai::TRANG_THAI_DANG_LAM) {
            return response()->json(['message' => 'Bài làm đã được nộp trước đó'], 400);
        }

        $result = $this->nopBaiInternal($lanLamBai);

        return response()->json([
            'message' => 'Nộp bài thành công',
            'data' => $result
        ]);
    }

    /**
     * Xử lý nộp bài nội bộ
     */
    private function nopBaiInternal(LanLamBai $lanLamBai)
    {
        return DB::transaction(function () use ($lanLamBai) {
            // Chấm điểm từng câu trả lời
            $traLois = $lanLamBai->traLois()->with('cauHoi')->get();
            
            foreach ($traLois as $traLoi) {
                // Lấy điểm từ bảng BaiKiemTra_CauHoi
                $diemCauHoi = DB::table('BaiKiemTra_CauHoi')
                    ->where('BaiKiemTraId', $lanLamBai->BaiKiemTraId)
                    ->where('CauHoiId', $traLoi->CauHoiId)
                    ->value('Diem') ?? 1;

                $traLoi->chamDiem($diemCauHoi);
            }

            // Tính tổng điểm
            $ketQua = $lanLamBai->tinhDiem();

            // Cập nhật trạng thái
            $trangThai = $lanLamBai->daHetGio() 
                ? LanLamBai::TRANG_THAI_HET_GIO 
                : LanLamBai::TRANG_THAI_HOAN_THANH;

            $lanLamBai->update([
                'ThoiGianKetThuc' => now(),
                'TrangThai' => $trangThai
            ]);

            // Kiểm tra đạt hay không
            $baiKiemTra = $lanLamBai->baiKiemTra;
            $dat = $ketQua['diem'] >= ($baiKiemTra->DiemDat ?? 0);

            return [
                'lanLamBaiId' => $lanLamBai->LanLamBaiId,
                'diem' => $ketQua['diem'],
                'soCauDung' => $ketQua['soCauDung'],
                'tongSoCau' => $ketQua['tongSoCau'],
                'diemDat' => $baiKiemTra->DiemDat,
                'dat' => $dat,
                'trangThai' => $trangThai,
                'thoiGianBatDau' => $lanLamBai->ThoiGianBatDau,
                'thoiGianKetThuc' => $lanLamBai->ThoiGianKetThuc
            ];
        });
    }

    /**
     * Xem kết quả chi tiết
     */
    public function xemKetQua(Request $request, $lanLamBaiId)
    {
        $lanLamBai = LanLamBai::with(['baiKiemTra', 'traLois.cauHoi.luaChons'])
            ->find($lanLamBaiId);

        if (!$lanLamBai) {
            return response()->json(['message' => 'Không tìm thấy lần làm bài'], 404);
        }

        // Kiểm tra quyền
        if ($lanLamBai->NguoiDungId !== $request->user()->NguoiDungId) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        // Kiểm tra bài làm đã hoàn thành chưa
        if ($lanLamBai->TrangThai === LanLamBai::TRANG_THAI_DANG_LAM) {
            return response()->json(['message' => 'Bài làm chưa được nộp'], 400);
        }

        $baiKiemTra = $lanLamBai->baiKiemTra;
        $hienThiDapAn = $baiKiemTra->HienThiDapAn;

        $chiTiet = $lanLamBai->traLois->map(function ($traLoi) use ($hienThiDapAn) {
            $cauHoi = $traLoi->cauHoi;
            
            $data = [
                'cauHoiId' => $cauHoi->CauHoiId,
                'loai' => $cauHoi->Loai,
                'deBai' => $cauHoi->DeBai,
                'luaChonDaChon' => $traLoi->luaChonIdsArray,
                'noiDungTraLoi' => $traLoi->NoiDungTraLoi,
                'laDung' => $traLoi->LaDung,
                'diem' => $traLoi->Diem
            ];

            if ($hienThiDapAn) {
                $data['giaiThich'] = $cauHoi->GiaiThich;
                $data['luaChons'] = $cauHoi->luaChons->map(function ($luaChon) {
                    return [
                        'id' => $luaChon->LuaChonId,
                        'noiDung' => $luaChon->NoiDung,
                        'dungHaySai' => $luaChon->DungHaySai
                    ];
                });
            }

            return $data;
        });

        return response()->json([
            'lanLamBai' => $this->formatLanLamBai($lanLamBai),
            'chiTiet' => $chiTiet
        ]);
    }

    /**
     * Lấy lịch sử làm bài của người dùng
     */
    public function lichSuLamBai(Request $request)
    {
        $nguoiDungId = $request->user()->NguoiDungId;

        $lichSu = LanLamBai::with(['baiKiemTra.khoaHoc:KhoaHocId,TieuDe'])
            ->cuaNguoiDung($nguoiDungId)
            ->orderBy('ThoiGianBatDau', 'desc')
            ->get()
            ->map(function ($item) {
                return $this->formatLanLamBai($item);
            });

        return response()->json($lichSu);
    }

    /**
     * Lấy lịch sử làm bài theo bài kiểm tra
     */
    public function lichSuTheoBaiKiemTra(Request $request, $baiKiemTraId)
    {
        $nguoiDungId = $request->user()->NguoiDungId;

        $lichSu = LanLamBai::with('baiKiemTra')
            ->where('BaiKiemTraId', $baiKiemTraId)
            ->cuaNguoiDung($nguoiDungId)
            ->orderBy('ThoiGianBatDau', 'desc')
            ->get()
            ->map(function ($item) {
                return $this->formatLanLamBai($item);
            });

        return response()->json($lichSu);
    }

    /**
     * Lấy thông tin lần làm bài đang làm (nếu có)
     */
    public function getLanLamBaiDangLam(Request $request, $baiKiemTraId)
    {
        $nguoiDungId = $request->user()->NguoiDungId;

        $lanLamBai = LanLamBai::with('traLois')
            ->where('BaiKiemTraId', $baiKiemTraId)
            ->where('NguoiDungId', $nguoiDungId)
            ->dangLam()
            ->first();

        if (!$lanLamBai) {
            return response()->json(['message' => 'Không có bài làm đang thực hiện'], 404);
        }

        // Kiểm tra hết giờ
        if ($lanLamBai->daHetGio()) {
            $result = $this->nopBaiInternal($lanLamBai);
            return response()->json([
                'message' => 'Bài làm đã hết giờ và được tự động nộp',
                'ketQua' => $result
            ]);
        }

        return response()->json($this->formatLanLamBai($lanLamBai, true));
    }

    /**
     * Format lần làm bài
     */
    private function formatLanLamBai(LanLamBai $lanLamBai, $includeTraLoi = false)
    {
        $data = [
            'id' => $lanLamBai->LanLamBaiId,
            'baiKiemTraId' => $lanLamBai->BaiKiemTraId,
            'tenBaiKiemTra' => $lanLamBai->baiKiemTra->TieuDe ?? null,
            'tenKhoaHoc' => $lanLamBai->baiKiemTra->khoaHoc->TieuDe ?? null,
            'thoiGianBatDau' => $lanLamBai->ThoiGianBatDau,
            'thoiGianKetThuc' => $lanLamBai->ThoiGianKetThuc,
            'thoiGianConLai' => $lanLamBai->thoiGianConLai(),
            'diem' => $lanLamBai->Diem,
            'soCauDung' => $lanLamBai->SoCauDung,
            'tongSoCau' => $lanLamBai->TongSoCau,
            'trangThai' => $lanLamBai->TrangThai
        ];

        if ($includeTraLoi && $lanLamBai->TrangThai === LanLamBai::TRANG_THAI_DANG_LAM) {
            $data['traLois'] = $lanLamBai->traLois->map(function ($traLoi) {
                return [
                    'cauHoiId' => $traLoi->CauHoiId,
                    'luaChonIds' => $traLoi->luaChonIdsArray,
                    'noiDungTraLoi' => $traLoi->NoiDungTraLoi
                ];
            });
        }

        return $data;
    }
}
