<?php

namespace App\Http\Controllers;

use App\Models\BaiKiemTra;
use App\Models\LanLamBai;
use App\Models\TraLoi;
use App\Models\CauHoi;
use App\Models\NganHangCauHoi;
use App\Models\GhiDanh;
use App\Models\BaiHoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BaiKiemTraCuoiKhoaController extends Controller
{
    /**
     * Lấy bài kiểm tra cuối khóa của một khóa học
     */
    public function getBaiKiemTraCuoiKhoa(Request $request, $khoaHocId)
    {
        $baiKiemTra = BaiKiemTra::thuocKhoaHoc($khoaHocId)
            ->cuoiKhoa()
            ->congKhai()
            ->with('khoaHoc:KhoaHocId,TieuDe')
            ->first();

        if (!$baiKiemTra) {
            return response()->json([
                'message' => 'Khóa học này chưa có bài kiểm tra cuối khóa'
            ], 404);
        }

        $nguoiDungId = $request->user()?->NguoiDungId;
        $canLamBai = false;
        $lyDoKhongDuocLam = null;
        $tienDo = null;

        if ($nguoiDungId) {
            // Kiểm tra tiến độ học
            $ghiDanh = GhiDanh::where('NguoiDungId', $nguoiDungId)
                ->where('KhoaHocId', $khoaHocId)
                ->first();

            if (!$ghiDanh) {
                $lyDoKhongDuocLam = 'Bạn chưa ghi danh khóa học này';
            } else {
                $tienDo = [
                    'phanTramHoanThanh' => $ghiDanh->PhanTramHoanThanh,
                    'daHoanThanhTatCaBaiHoc' => $ghiDanh->PhanTramHoanThanh >= 100
                ];

                if ($ghiDanh->PhanTramHoanThanh < 100) {
                    $lyDoKhongDuocLam = 'Bạn cần hoàn thành tất cả bài học trước khi làm bài kiểm tra cuối khóa';
                } elseif (!$baiKiemTra->conLuotLamBai($nguoiDungId)) {
                    $lyDoKhongDuocLam = 'Bạn đã hết lượt làm bài kiểm tra này';
                } else {
                    $canLamBai = true;
                }
            }
        } else {
            $lyDoKhongDuocLam = 'Vui lòng đăng nhập để làm bài kiểm tra';
        }

        return response()->json([
            'baiKiemTra' => $this->formatBaiKiemTra($baiKiemTra, $nguoiDungId),
            'canLamBai' => $canLamBai,
            'lyDoKhongDuocLam' => $lyDoKhongDuocLam,
            'tienDo' => $tienDo
        ]);
    }

    /**
     * Bắt đầu làm bài kiểm tra cuối khóa
     * Lấy câu hỏi từ tất cả ngân hàng câu hỏi của khóa học theo ThietLapJson
     */
    public function batDauLamBai(Request $request, $baiKiemTraId)
    {
        $nguoiDungId = $request->user()->NguoiDungId;

        $baiKiemTra = BaiKiemTra::find($baiKiemTraId);

        if (!$baiKiemTra) {
            return response()->json(['message' => 'Không tìm thấy bài kiểm tra'], 404);
        }

        // Kiểm tra bài kiểm tra có công khai không
        if ($baiKiemTra->TrangThai !== BaiKiemTra::TRANG_THAI_CONG_KHAI) {
            return response()->json(['message' => 'Bài kiểm tra không khả dụng'], 403);
        }

        // Kiểm tra đây có phải bài kiểm tra cuối khóa không
        if (!$baiKiemTra->laBaiKiemTraCuoiKhoa()) {
            return response()->json(['message' => 'Đây không phải bài kiểm tra cuối khóa'], 400);
        }

        // Kiểm tra tiến độ học
        $ghiDanh = GhiDanh::where('NguoiDungId', $nguoiDungId)
            ->where('KhoaHocId', $baiKiemTra->KhoaHocId)
            ->first();

        if (!$ghiDanh || $ghiDanh->PhanTramHoanThanh < 100) {
            return response()->json([
                'message' => 'Bạn cần hoàn thành tất cả bài học trước khi làm bài kiểm tra cuối khóa',
                'phanTramHoanThanh' => $ghiDanh?->PhanTramHoanThanh ?? 0
            ], 403);
        }

        // Kiểm tra còn lượt làm bài không
        if (!$baiKiemTra->conLuotLamBai($nguoiDungId)) {
            return response()->json(['message' => 'Bạn đã hết lượt làm bài kiểm tra này'], 403);
        }

        // Kiểm tra có bài làm dở không
        $lanLamBaiDangLam = LanLamBai::where('BaiKiemTraId', $baiKiemTraId)
            ->where('NguoiDungId', $nguoiDungId)
            ->dangLam()
            ->first();

        if ($lanLamBaiDangLam) {
            // Kiểm tra đã hết giờ chưa
            if ($lanLamBaiDangLam->daHetGio()) {
                $this->tuDongNopBai($lanLamBaiDangLam);
            } else {
                // Trả về bài làm dở
                return response()->json([
                    'message' => 'Tiếp tục bài làm dở',
                    'lanLamBai' => $this->formatLanLamBai($lanLamBaiDangLam),
                    'cauHois' => $this->getCauHoiTuChiTiet($lanLamBaiDangLam)
                ]);
            }
        }

        // Lấy câu hỏi từ ngân hàng câu hỏi theo ThietLapJson
        $cauHois = $this->layCauHoiTuNganHang($baiKiemTra);

        if (empty($cauHois)) {
            return response()->json(['message' => 'Bài kiểm tra chưa có câu hỏi'], 400);
        }

        // Tạo lần làm bài mới
        $lanLamBai = DB::transaction(function () use ($baiKiemTra, $nguoiDungId, $cauHois) {
            // Lưu snapshot câu hỏi vào ChiTietJson
            $chiTietJson = [
                'cauHoiIds' => collect($cauHois)->pluck('CauHoiId')->toArray(),
                'soCauHoi' => count($cauHois),
                'thietLap' => $baiKiemTra->getThietLap()
            ];

            $lanLamBai = LanLamBai::create([
                'BaiKiemTraId' => $baiKiemTra->BaiKiemTraId,
                'NguoiDungId' => $nguoiDungId,
                'BatDauLuc' => now(),
                'ChiTietJson' => $chiTietJson,
                'TrangThai' => LanLamBai::TRANG_THAI_DANG_LAM
            ]);

            return $lanLamBai;
        });

        return response()->json([
            'message' => 'Bắt đầu làm bài thành công',
            'lanLamBai' => $this->formatLanLamBai($lanLamBai),
            'cauHois' => $this->formatCauHoisForQuiz($cauHois, $baiKiemTra)
        ], 201);
    }

    /**
     * Lấy câu hỏi từ các ngân hàng câu hỏi theo ThietLapJson
     */
    private function layCauHoiTuNganHang(BaiKiemTra $baiKiemTra)
    {
        $thietLap = $baiKiemTra->getThietLap();
        $khoaHocId = $baiKiemTra->KhoaHocId;

        // Cấu hình mặc định
        $soCauHoi = $thietLap['soCauHoi'] ?? 20;
        $nganHangIds = $thietLap['nganHangIds'] ?? null; // null = lấy tất cả ngân hàng của khóa học
        $tyLeCauHoi = $thietLap['tyLeCauHoi'] ?? null; // Tỷ lệ câu hỏi theo độ khó

        // Lấy danh sách ngân hàng câu hỏi
        $query = NganHangCauHoi::where('KhoaHocId', $khoaHocId);
        
        if ($nganHangIds && is_array($nganHangIds)) {
            $query->whereIn('NganHangId', $nganHangIds);
        }

        $nganHangs = $query->get();
        $nganHangIdList = $nganHangs->pluck('NganHangId')->toArray();

        if (empty($nganHangIdList)) {
            return [];
        }

        // Lấy câu hỏi từ các ngân hàng
        $cauHoiQuery = CauHoi::whereIn('NganHangId', $nganHangIdList)
            ->with('luaChons');

        // Nếu có tỷ lệ theo độ khó
        if ($tyLeCauHoi && is_array($tyLeCauHoi)) {
            $cauHois = collect();
            
            foreach ($tyLeCauHoi as $doKho => $soLuong) {
                $cauHoiTheoDoKho = CauHoi::whereIn('NganHangId', $nganHangIdList)
                    ->where('DoKho', $doKho)
                    ->with('luaChons')
                    ->inRandomOrder()
                    ->limit($soLuong)
                    ->get();
                
                $cauHois = $cauHois->merge($cauHoiTheoDoKho);
            }

            // Xáo trộn thứ tự câu hỏi
            if ($thietLap['xaoTronCauHoi'] ?? true) {
                $cauHois = $cauHois->shuffle();
            }

            return $cauHois->values()->all();
        }

        // Lấy câu hỏi ngẫu nhiên
        $cauHois = $cauHoiQuery->inRandomOrder()
            ->limit($soCauHoi)
            ->get();

        // Xáo trộn nếu cần
        if ($thietLap['xaoTronCauHoi'] ?? true) {
            $cauHois = $cauHois->shuffle();
        }

        return $cauHois->values()->all();
    }

    /**
     * Format câu hỏi cho quiz (không hiện đáp án đúng)
     */
    private function formatCauHoisForQuiz($cauHois, BaiKiemTra $baiKiemTra)
    {
        $thietLap = $baiKiemTra->getThietLap();
        $xaoTronDapAn = $thietLap['xaoTronDapAn'] ?? true;

        return collect($cauHois)->map(function ($cauHoi, $index) use ($xaoTronDapAn) {
            $luaChons = $cauHoi->luaChons;
            
            if ($xaoTronDapAn) {
                $luaChons = $luaChons->shuffle();
            }

            return [
                'id' => $cauHoi->CauHoiId,
                'stt' => $index + 1,
                'loai' => $this->mapLoaiCauHoi($cauHoi->Loai),
                'deBai' => $cauHoi->DeBai,
                'doKho' => $cauHoi->DoKho,
                'luaChons' => $luaChons->map(function ($luaChon) {
                    return [
                        'id' => $luaChon->LuaChonId,
                        'noiDung' => $luaChon->NoiDung
                    ];
                })->values()
            ];
        })->values();
    }

    /**
     * Map loại câu hỏi từ database sang frontend
     * Theo ENUM: single, multiple, true_false
     */
    private function mapLoaiCauHoi($loai)
    {
        // ENUM đã đúng format, trả về trực tiếp
        $validTypes = ['single', 'multiple', 'true_false'];
        
        if (in_array($loai, $validTypes)) {
            return $loai;
        }

        // Fallback cho dữ liệu cũ (nếu có)
        $map = [
            'MOT_DAP_AN' => 'single',
            'NHIEU_DAP_AN' => 'multiple',
            'DUNG_SAI' => 'true_false'
        ];

        return $map[$loai] ?? 'single';
    }

    /**
     * Lấy câu hỏi từ ChiTietJson của lần làm bài
     */
    private function getCauHoiTuChiTiet(LanLamBai $lanLamBai)
    {
        $cauHoiIds = $lanLamBai->getChiTiet('cauHoiIds', []);
        
        if (empty($cauHoiIds)) {
            return [];
        }

        $cauHois = CauHoi::whereIn('CauHoiId', $cauHoiIds)
            ->with('luaChons')
            ->get()
            ->sortBy(function ($item) use ($cauHoiIds) {
                return array_search($item->CauHoiId, $cauHoiIds);
            });

        return $this->formatCauHoisForQuiz($cauHois->values(), $lanLamBai->baiKiemTra);
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

        if ($lanLamBai->NguoiDungId !== $request->user()->NguoiDungId) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        if ($lanLamBai->TrangThai !== LanLamBai::TRANG_THAI_DANG_LAM) {
            return response()->json(['message' => 'Bài làm đã kết thúc'], 400);
        }

        if ($lanLamBai->daHetGio()) {
            $result = $this->tuDongNopBai($lanLamBai);
            return response()->json([
                'message' => 'Đã hết thời gian làm bài',
                'ketQua' => $result
            ], 400);
        }

        $request->validate([
            'cauHoiId' => 'required|exists:CauHoi,CauHoiId',
            'luaChonIds' => 'nullable|array',
            'traLoiText' => 'nullable|string|max:1000',  // Cho dạng điền khuyết
            'thoiGianGiay' => 'nullable|integer'
        ]);

        // Tìm hoặc tạo câu trả lời
        TraLoi::updateOrCreate(
            [
                'LanLamBaiId' => $lanLamBaiId,
                'CauHoiId' => $request->cauHoiId
            ],
            [
                'LuaChonIds' => $request->luaChonIds,
                'TraLoiText' => $request->traLoiText,  // Cho dạng điền khuyết
                'ThoiGianGiay' => $request->thoiGianGiay
            ]
        );

        return response()->json(['message' => 'Đã lưu câu trả lời']);
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

        if ($lanLamBai->NguoiDungId !== $request->user()->NguoiDungId) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        if ($lanLamBai->TrangThai !== LanLamBai::TRANG_THAI_DANG_LAM) {
            return response()->json(['message' => 'Bài làm đã được nộp trước đó'], 400);
        }

        $result = $this->chamDiemVaNopBai($lanLamBai);

        return response()->json([
            'message' => 'Nộp bài thành công',
            'ketQua' => $result
        ]);
    }

    /**
     * Tự động nộp bài khi hết giờ
     */
    private function tuDongNopBai(LanLamBai $lanLamBai)
    {
        return $this->chamDiemVaNopBai($lanLamBai);
    }

    /**
     * Chấm điểm và nộp bài
     */
    private function chamDiemVaNopBai(LanLamBai $lanLamBai)
    {
        return DB::transaction(function () use ($lanLamBai) {
            $traLois = $lanLamBai->traLois()->with('cauHoi.luaChons')->get();
            
            $soCauDung = 0;
            $tongSoCau = $lanLamBai->getChiTiet('soCauHoi', 0);

            // Chấm điểm từng câu
            foreach ($traLois as $traLoi) {
                if ($traLoi->chamDiem()) {
                    $soCauDung++;
                }
            }

            // Tính điểm theo thang 10
            $diemSo = $tongSoCau > 0 ? round(($soCauDung / $tongSoCau) * 10, 2) : 0;

            // Cập nhật lần làm bài
            $lanLamBai->update([
                'NopBaiLuc' => now(),
                'DiemSo' => $diemSo,
                'TrangThai' => LanLamBai::TRANG_THAI_HOAN_THANH,
                'ChiTietJson' => array_merge($lanLamBai->ChiTietJson ?? [], [
                    'soCauDung' => $soCauDung,
                    'tongSoCau' => $tongSoCau
                ])
            ]);

            $baiKiemTra = $lanLamBai->baiKiemTra;
            $diemDat = $baiKiemTra->DiemDat ?? 5;

            return [
                'lanLamBaiId' => $lanLamBai->LanLamBaiId,
                'diemSo' => $diemSo,
                'soCauDung' => $soCauDung,
                'tongSoCau' => $tongSoCau,
                'diemDat' => $diemDat,
                'dat' => $diemSo >= $diemDat,
                'thoiGianLam' => $lanLamBai->BatDauLuc->diffInMinutes($lanLamBai->NopBaiLuc)
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

        if ($lanLamBai->NguoiDungId !== $request->user()->NguoiDungId) {
            return response()->json(['message' => 'Không có quyền truy cập'], 403);
        }

        if ($lanLamBai->TrangThai === LanLamBai::TRANG_THAI_DANG_LAM) {
            return response()->json(['message' => 'Bài làm chưa được nộp'], 400);
        }

        $baiKiemTra = $lanLamBai->baiKiemTra;
        $thietLap = $baiKiemTra->getThietLap();
        $hienThiDapAn = $thietLap['hienThiDapAn'] ?? true;

        $chiTiet = $lanLamBai->traLois->map(function ($traLoi) use ($hienThiDapAn) {
            $cauHoi = $traLoi->cauHoi;
            
            $data = [
                'cauHoiId' => $cauHoi->CauHoiId,
                'loai' => $this->mapLoaiCauHoi($cauHoi->Loai),
                'deBai' => $cauHoi->DeBai,
                'luaChonDaChon' => $traLoi->luaChonIdsArray,
                'dungHaySai' => $traLoi->DungHaySai
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

        $chiTietLanLam = $lanLamBai->ChiTietJson ?? [];

        return response()->json([
            'lanLamBai' => [
                'id' => $lanLamBai->LanLamBaiId,
                'diemSo' => $lanLamBai->DiemSo,
                'soCauDung' => $chiTietLanLam['soCauDung'] ?? 0,
                'tongSoCau' => $chiTietLanLam['tongSoCau'] ?? 0,
                'diemDat' => $baiKiemTra->DiemDat,
                'dat' => $lanLamBai->DiemSo >= ($baiKiemTra->DiemDat ?? 5),
                'batDauLuc' => $lanLamBai->BatDauLuc,
                'nopBaiLuc' => $lanLamBai->NopBaiLuc
            ],
            'chiTiet' => $chiTiet
        ]);
    }

    /**
     * Format bài kiểm tra
     */
    private function formatBaiKiemTra(BaiKiemTra $baiKiemTra, $nguoiDungId = null)
    {
        $thietLap = $baiKiemTra->getThietLap();

        $data = [
            'id' => $baiKiemTra->BaiKiemTraId,
            'khoaHocId' => $baiKiemTra->KhoaHocId,
            'tenKhoaHoc' => $baiKiemTra->khoaHoc->TieuDe ?? null,
            'tieuDe' => $baiKiemTra->TieuDe,
            'moTa' => $thietLap['moTa'] ?? null,
            'thoiGianLamBai' => $thietLap['thoiGianLamBai'] ?? 30,
            'soCauHoi' => $thietLap['soCauHoi'] ?? 20,
            'diemDat' => $baiKiemTra->DiemDat,
            'soLanLamToiDa' => $thietLap['soLanLamToiDa'] ?? 0
        ];

        if ($nguoiDungId) {
            $data['soLanDaLam'] = $baiKiemTra->soLanDaLam($nguoiDungId);
            $data['diemCaoNhat'] = $baiKiemTra->diemCaoNhat($nguoiDungId);
            $data['conLuotLamBai'] = $baiKiemTra->conLuotLamBai($nguoiDungId);
        }

        return $data;
    }

    /**
     * Format lần làm bài
     */
    private function formatLanLamBai(LanLamBai $lanLamBai)
    {
        return [
            'id' => $lanLamBai->LanLamBaiId,
            'baiKiemTraId' => $lanLamBai->BaiKiemTraId,
            'batDauLuc' => $lanLamBai->BatDauLuc,
            'thoiGianConLai' => $lanLamBai->thoiGianConLai(),
            'trangThai' => $lanLamBai->TrangThai
        ];
    }
}
