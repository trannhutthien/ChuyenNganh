<?php

namespace App\Http\Controllers;

use App\Models\BaiKiemTra;
use App\Models\BaiKiemTra_CauHoi;
use App\Models\LanLamBai;
use App\Models\TraLoi;
use App\Models\CauHoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BaiKiemTraController extends Controller
{
    /**
     * Lấy danh sách bài kiểm tra công khai
     */
    public function index()
    {
        $baiKiemTras = BaiKiemTra::congKhai()
            ->with(['khoaHoc:KhoaHocId,TieuDe', 'baiHoc:BaiHocId,TieuDe'])
            ->get()
            ->map(function ($item) {
                return $this->formatBaiKiemTra($item);
            });

        return response()->json($baiKiemTras);
    }

    /**
     * Lấy bài kiểm tra theo khóa học
     */
    public function getByKhoaHoc($khoaHocId)
    {
        $baiKiemTras = BaiKiemTra::thuocKhoaHoc($khoaHocId)
            ->congKhai()
            ->with('baiHoc:BaiHocId,TieuDe')
            ->get()
            ->map(function ($item) {
                return $this->formatBaiKiemTra($item);
            });

        return response()->json($baiKiemTras);
    }

    /**
     * Lấy bài kiểm tra theo bài học
     */
    public function getByBaiHoc($baiHocId)
    {
        $baiKiemTras = BaiKiemTra::thuocBaiHoc($baiHocId)
            ->congKhai()
            ->get()
            ->map(function ($item) {
                return $this->formatBaiKiemTra($item);
            });

        return response()->json($baiKiemTras);
    }

    /**
     * Chi tiết bài kiểm tra
     */
    public function show($id)
    {
        $baiKiemTra = BaiKiemTra::with(['khoaHoc:KhoaHocId,TieuDe', 'baiHoc:BaiHocId,TieuDe'])
            ->find($id);

        if (!$baiKiemTra) {
            return response()->json(['message' => 'Không tìm thấy bài kiểm tra'], 404);
        }

        return response()->json($this->formatBaiKiemTra($baiKiemTra, true));
    }

    /**
     * Tạo bài kiểm tra mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'khoaHocId' => 'required|exists:KhoaHoc,KhoaHocId',
            'baiHocId' => 'nullable|exists:BaiHoc,BaiHocId',
            'tieuDe' => 'required|string|max:300',
            'moTa' => 'nullable|string',
            'thoiGianLamBai' => 'nullable|integer|min:1',
            'soCauHoi' => 'nullable|integer|min:1',
            'diemDat' => 'nullable|numeric|min:0',
            'soLanLamToiDa' => 'nullable|integer|min:0',
            'xaoTronCauHoi' => 'nullable|boolean',
            'xaoTronDapAn' => 'nullable|boolean',
            'hienThiDapAn' => 'nullable|boolean',
            'trangThai' => 'nullable|in:NHAP,CONG_KHAI,DONG',
            'cauHoiIds' => 'nullable|array',
            'cauHoiIds.*' => 'exists:CauHoi,CauHoiId'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $baiKiemTra = DB::transaction(function () use ($request) {
            $baiKiemTra = BaiKiemTra::create([
                'KhoaHocId' => $request->khoaHocId,
                'BaiHocId' => $request->baiHocId,
                'TieuDe' => $request->tieuDe,
                'MoTa' => $request->moTa,
                'ThoiGianLamBai' => $request->thoiGianLamBai,
                'SoCauHoi' => $request->soCauHoi,
                'DiemDat' => $request->diemDat ?? 5,
                'SoLanLamToiDa' => $request->soLanLamToiDa,
                'XaoTronCauHoi' => $request->xaoTronCauHoi ?? false,
                'XaoTronDapAn' => $request->xaoTronDapAn ?? false,
                'HienThiDapAn' => $request->hienThiDapAn ?? true,
                'TrangThai' => $request->trangThai ?? 'NHAP',
                'NgayTao' => now()
            ]);

            // Thêm câu hỏi vào bài kiểm tra
            if ($request->has('cauHoiIds') && is_array($request->cauHoiIds)) {
                foreach ($request->cauHoiIds as $index => $cauHoiId) {
                    BaiKiemTra_CauHoi::create([
                        'BaiKiemTraId' => $baiKiemTra->BaiKiemTraId,
                        'CauHoiId' => $cauHoiId,
                        'ThuTu' => $index + 1,
                        'Diem' => 1
                    ]);
                }
            }

            return $baiKiemTra;
        });

        return response()->json([
            'message' => 'Tạo bài kiểm tra thành công',
            'data' => $this->formatBaiKiemTra($baiKiemTra)
        ], 201);
    }

    /**
     * Cập nhật bài kiểm tra
     */
    public function update(Request $request, $id)
    {
        $baiKiemTra = BaiKiemTra::find($id);

        if (!$baiKiemTra) {
            return response()->json(['message' => 'Không tìm thấy bài kiểm tra'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tieuDe' => 'required|string|max:300',
            'moTa' => 'nullable|string',
            'thoiGianLamBai' => 'nullable|integer|min:1',
            'soCauHoi' => 'nullable|integer|min:1',
            'diemDat' => 'nullable|numeric|min:0',
            'soLanLamToiDa' => 'nullable|integer|min:0',
            'xaoTronCauHoi' => 'nullable|boolean',
            'xaoTronDapAn' => 'nullable|boolean',
            'hienThiDapAn' => 'nullable|boolean',
            'trangThai' => 'nullable|in:NHAP,CONG_KHAI,DONG',
            'cauHoiIds' => 'nullable|array',
            'cauHoiIds.*' => 'exists:CauHoi,CauHoiId'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, $baiKiemTra) {
            $baiKiemTra->update([
                'TieuDe' => $request->tieuDe,
                'MoTa' => $request->moTa,
                'ThoiGianLamBai' => $request->thoiGianLamBai,
                'SoCauHoi' => $request->soCauHoi,
                'DiemDat' => $request->diemDat ?? $baiKiemTra->DiemDat,
                'SoLanLamToiDa' => $request->soLanLamToiDa,
                'XaoTronCauHoi' => $request->xaoTronCauHoi ?? $baiKiemTra->XaoTronCauHoi,
                'XaoTronDapAn' => $request->xaoTronDapAn ?? $baiKiemTra->XaoTronDapAn,
                'HienThiDapAn' => $request->hienThiDapAn ?? $baiKiemTra->HienThiDapAn,
                'TrangThai' => $request->trangThai ?? $baiKiemTra->TrangThai
            ]);

            // Cập nhật danh sách câu hỏi nếu có
            if ($request->has('cauHoiIds')) {
                // Xóa câu hỏi cũ
                BaiKiemTra_CauHoi::where('BaiKiemTraId', $baiKiemTra->BaiKiemTraId)->delete();
                
                // Thêm câu hỏi mới
                foreach ($request->cauHoiIds as $index => $cauHoiId) {
                    BaiKiemTra_CauHoi::create([
                        'BaiKiemTraId' => $baiKiemTra->BaiKiemTraId,
                        'CauHoiId' => $cauHoiId,
                        'ThuTu' => $index + 1,
                        'Diem' => 1
                    ]);
                }
            }
        });

        return response()->json([
            'message' => 'Cập nhật bài kiểm tra thành công',
            'data' => $this->formatBaiKiemTra($baiKiemTra->fresh())
        ]);
    }

    /**
     * Xóa bài kiểm tra
     */
    public function destroy($id)
    {
        $baiKiemTra = BaiKiemTra::find($id);

        if (!$baiKiemTra) {
            return response()->json(['message' => 'Không tìm thấy bài kiểm tra'], 404);
        }

        DB::transaction(function () use ($baiKiemTra) {
            // Xóa các bản ghi liên quan
            $lanLamBaiIds = $baiKiemTra->lanLamBais()->pluck('LanLamBaiId');
            TraLoi::whereIn('LanLamBaiId', $lanLamBaiIds)->delete();
            $baiKiemTra->lanLamBais()->delete();
            BaiKiemTra_CauHoi::where('BaiKiemTraId', $baiKiemTra->BaiKiemTraId)->delete();
            $baiKiemTra->delete();
        });

        return response()->json(['message' => 'Xóa bài kiểm tra thành công']);
    }

    /**
     * Lấy câu hỏi của bài kiểm tra (cho việc làm bài)
     */
    public function getCauHois($id)
    {
        $baiKiemTra = BaiKiemTra::with(['cauHois.luaChons'])->find($id);

        if (!$baiKiemTra) {
            return response()->json(['message' => 'Không tìm thấy bài kiểm tra'], 404);
        }

        $cauHois = $baiKiemTra->cauHois;

        // Xáo trộn câu hỏi nếu được bật
        if ($baiKiemTra->XaoTronCauHoi) {
            $cauHois = $cauHois->shuffle();
        }

        $formattedCauHois = $cauHois->map(function ($cauHoi) use ($baiKiemTra) {
            $luaChons = $cauHoi->luaChons;
            
            // Xáo trộn đáp án nếu được bật
            if ($baiKiemTra->XaoTronDapAn) {
                $luaChons = $luaChons->shuffle();
            }

            return [
                'id' => $cauHoi->CauHoiId,
                'loai' => $cauHoi->Loai,
                'deBai' => $cauHoi->DeBai,
                'doKho' => $cauHoi->DoKho,
                'diem' => $cauHoi->pivot->Diem ?? 1,
                'luaChons' => $luaChons->map(function ($luaChon) {
                    return [
                        'id' => $luaChon->LuaChonId,
                        'noiDung' => $luaChon->NoiDung,
                        'thuTu' => $luaChon->ThuTu
                    ];
                })
            ];
        });

        return response()->json([
            'baiKiemTra' => $this->formatBaiKiemTra($baiKiemTra),
            'cauHois' => $formattedCauHois
        ]);
    }

    /**
     * Thêm câu hỏi vào bài kiểm tra
     */
    public function addCauHoi(Request $request, $id)
    {
        $baiKiemTra = BaiKiemTra::find($id);

        if (!$baiKiemTra) {
            return response()->json(['message' => 'Không tìm thấy bài kiểm tra'], 404);
        }

        $validator = Validator::make($request->all(), [
            'cauHoiId' => 'required|exists:CauHoi,CauHoiId',
            'thuTu' => 'nullable|integer',
            'diem' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kiểm tra câu hỏi đã tồn tại chưa
        $existing = BaiKiemTra_CauHoi::where('BaiKiemTraId', $id)
            ->where('CauHoiId', $request->cauHoiId)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Câu hỏi đã tồn tại trong bài kiểm tra'], 422);
        }

        // Lấy thứ tự lớn nhất hiện tại
        $maxThuTu = BaiKiemTra_CauHoi::where('BaiKiemTraId', $id)->max('ThuTu') ?? 0;

        BaiKiemTra_CauHoi::create([
            'BaiKiemTraId' => $id,
            'CauHoiId' => $request->cauHoiId,
            'ThuTu' => $request->thuTu ?? ($maxThuTu + 1),
            'Diem' => $request->diem ?? 1
        ]);

        return response()->json(['message' => 'Thêm câu hỏi thành công']);
    }

    /**
     * Xóa câu hỏi khỏi bài kiểm tra
     */
    public function removeCauHoi($id, $cauHoiId)
    {
        $deleted = BaiKiemTra_CauHoi::where('BaiKiemTraId', $id)
            ->where('CauHoiId', $cauHoiId)
            ->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Không tìm thấy câu hỏi trong bài kiểm tra'], 404);
        }

        return response()->json(['message' => 'Xóa câu hỏi khỏi bài kiểm tra thành công']);
    }

    /**
     * Format bài kiểm tra
     */
    private function formatBaiKiemTra($baiKiemTra, $includeStats = false)
    {
        $data = [
            'id' => $baiKiemTra->BaiKiemTraId,
            'khoaHocId' => $baiKiemTra->KhoaHocId,
            'tenKhoaHoc' => $baiKiemTra->khoaHoc->TieuDe ?? null,
            'baiHocId' => $baiKiemTra->BaiHocId,
            'tenBaiHoc' => $baiKiemTra->baiHoc->TieuDe ?? null,
            'tieuDe' => $baiKiemTra->TieuDe,
            'moTa' => $baiKiemTra->MoTa,
            'thoiGianLamBai' => $baiKiemTra->ThoiGianLamBai,
            'soCauHoi' => $baiKiemTra->SoCauHoi,
            'diemDat' => $baiKiemTra->DiemDat,
            'soLanLamToiDa' => $baiKiemTra->SoLanLamToiDa,
            'xaoTronCauHoi' => $baiKiemTra->XaoTronCauHoi,
            'xaoTronDapAn' => $baiKiemTra->XaoTronDapAn,
            'hienThiDapAn' => $baiKiemTra->HienThiDapAn,
            'trangThai' => $baiKiemTra->TrangThai,
            'ngayTao' => $baiKiemTra->NgayTao
        ];

        if ($includeStats) {
            $data['soNguoiLam'] = $baiKiemTra->lanLamBais()->distinct('NguoiDungId')->count();
            $data['soCauHoiThucTe'] = $baiKiemTra->cauHois()->count();
        }

        return $data;
    }
}
