<?php

namespace App\Http\Controllers;

use App\Models\LanLamBai;
use App\Models\BaiKiemTra;
use App\Models\TraLoi;
use App\Models\CauHoi;
use App\Models\NganHangCauHoi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class LanLamBaiController extends Controller
{
    /**
     * Bắt đầu làm bài kiểm tra
     * - Nếu bài kiểm tra thuộc khóa học (BaiHocId = NULL): Lấy câu hỏi từ các ngân hàng của khóa học
     * - Nếu bài kiểm tra thuộc bài học: Lấy câu hỏi từ ngân hàng của bài học đó
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
        if ($baiKiemTra->TrangThai !== BaiKiemTra::TRANG_THAI_CONG_KHAI) {
            return response()->json(['message' => 'Bài kiểm tra không khả dụng'], 403);
        }

        // Kiểm tra còn lượt làm bài không (sau này sẽ giới hạn 3 lần)
        // if (!$baiKiemTra->conLuotLamBai($nguoiDungId)) {
        //     return response()->json(['message' => 'Bạn đã hết lượt làm bài kiểm tra này'], 403);
        // }

        // Tự động hủy các bài làm dở trước đó (nếu có)
        LanLamBai::where('BaiKiemTraId', $request->baiKiemTraId)
            ->where('NguoiDungId', $nguoiDungId)
            ->dangLam()
            ->update(['TrangThai' => LanLamBai::TRANG_THAI_HUY]);

        // Lấy câu hỏi từ ngân hàng theo loại bài kiểm tra
        $cauHois = $this->layCauHoiTuNganHang($baiKiemTra);

        if (empty($cauHois)) {
            return response()->json(['message' => 'Bài kiểm tra chưa có câu hỏi'], 400);
        }

        // Tạo lần làm bài mới với snapshot câu hỏi
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
            'data' => $this->formatLanLamBai($lanLamBai, true),
            'cauHois' => $this->formatCauHoisForQuiz($cauHois, $baiKiemTra)
        ], 201);
    }

    /**
     * Lấy câu hỏi từ ngân hàng dựa vào loại bài kiểm tra
     * - Bài kiểm tra cuối khóa (BaiHocId = NULL): Lấy từ tất cả ngân hàng của khóa học
     * - Bài kiểm tra bài học: Lấy từ ngân hàng của bài học đó
     */
    private function layCauHoiTuNganHang(BaiKiemTra $baiKiemTra)
    {
        $thietLap = $baiKiemTra->getThietLap();
        $soCauHoi = $thietLap['soCauHoi'] ?? 10;
        $xaoTronCauHoi = $thietLap['xaoTronCauHoi'] ?? true;

        // Kiểm tra bài kiểm tra thuộc khóa học hay bài học
        if ($baiKiemTra->laBaiKiemTraCuoiKhoa()) {
            // Bài kiểm tra cuối khóa: Lấy từ tất cả ngân hàng của khóa học
            $khoaHocId = $baiKiemTra->KhoaHocId;
            $nganHangIds = $thietLap['nganHangIds'] ?? null;

            $query = NganHangCauHoi::where('KhoaHocId', $khoaHocId);
            if ($nganHangIds && is_array($nganHangIds)) {
                $query->whereIn('NganHangId', $nganHangIds);
            }
            $nganHangIdList = $query->pluck('NganHangId')->toArray();

        } else {
            // Bài kiểm tra bài học: Lấy từ ngân hàng của bài học đó
            $baiHocId = $baiKiemTra->BaiHocId;
            
            // Lấy ngân hàng câu hỏi liên kết với bài học
            // Câu hỏi có BaiHocId = baiHocId hoặc nằm trong ngân hàng của khóa học
            $nganHangIdList = CauHoi::where('BaiHocId', $baiHocId)
                ->distinct()
                ->pluck('NganHangId')
                ->toArray();

            // Nếu không có câu hỏi riêng cho bài học, lấy từ ngân hàng của khóa học
            if (empty($nganHangIdList)) {
                $khoaHocId = $baiKiemTra->khoaHoc?->KhoaHocId ?? $baiKiemTra->baiHoc?->khoaHoc?->KhoaHocId;
                if ($khoaHocId) {
                    $nganHangIdList = NganHangCauHoi::where('KhoaHocId', $khoaHocId)
                        ->pluck('NganHangId')
                        ->toArray();
                }
            }
        }

        if (empty($nganHangIdList)) {
            return [];
        }

        // Lấy câu hỏi từ các ngân hàng
        $cauHoiQuery = CauHoi::whereIn('NganHangId', $nganHangIdList)
            ->with('luaChons');

        // Nếu bài kiểm tra thuộc bài học, ưu tiên câu hỏi của bài học đó
        if (!$baiKiemTra->laBaiKiemTraCuoiKhoa() && $baiKiemTra->BaiHocId) {
            $cauHoiQuery->orderByRaw("CASE WHEN BaiHocId = ? THEN 0 ELSE 1 END", [$baiKiemTra->BaiHocId]);
        }

        // Lấy câu hỏi ngẫu nhiên
        $cauHois = $cauHoiQuery->inRandomOrder()
            ->limit($soCauHoi)
            ->get();

        // Xáo trộn nếu cần
        if ($xaoTronCauHoi) {
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
                'type' => $this->mapLoaiCauHoi($cauHoi->Loai),
                'text' => $cauHoi->DeBai,
                'doKho' => $cauHoi->DoKho,
                'points' => 1, // Có thể lấy từ thietLap nếu cần
                'options' => $luaChons->map(function ($luaChon) {
                    return [
                        'id' => $luaChon->LuaChonId,
                        'text' => $luaChon->NoiDung
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
     * Lấy câu hỏi từ ChiTietJson của lần làm bài (cho bài làm dở)
     */
    private function getCauHoiTuChiTiet(LanLamBai $lanLamBai)
    {
        $chiTietJson = $lanLamBai->ChiTietJson ?? [];
        $cauHoiIds = $chiTietJson['cauHoiIds'] ?? [];
        
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

        // Debug log request data
        \Log::info('=== LƯU CÂU TRẢ LỜI ===', [
            'lanLamBaiId' => $lanLamBaiId,
            'request_all' => $request->all()
        ]);
        
        $validator = Validator::make($request->all(), [
            'cauHoiId' => 'required|exists:CauHoi,CauHoiId',
            'luaChonIds' => 'nullable|array',
            'luaChonIds.*' => 'integer',  // Chỉ validate là số nguyên, không check exists
            'traLoiText' => 'nullable|string|max:1000'  // Cho dạng điền khuyết
        ]);

        if ($validator->fails()) {
            \Log::error('=== VALIDATION FAILED ===', [
                'errors' => $validator->errors()->toArray()
            ]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Tìm hoặc tạo câu trả lời
        $traLoi = TraLoi::updateOrCreate(
            [
                'LanLamBaiId' => $lanLamBaiId,
                'CauHoiId' => $request->cauHoiId
            ],
            [
                'LuaChonIds' => $request->luaChonIds ?? [],  // Lưu dạng JSON array
                'TraLoiText' => $request->traLoiText ?? null,  // Cho dạng điền khuyết
                'ThoiGianGiay' => $request->thoiGianGiay ?? 0
            ]
        );

        return response()->json([
            'message' => 'Lưu câu trả lời thành công',
            'data' => [
                'id' => $traLoi->TraLoiId,
                'cauHoiId' => $traLoi->CauHoiId,
                'luaChonIds' => $traLoi->LuaChonIds ?? [],
                'traLoiText' => $traLoi->TraLoiText,
                'thoiGianGiay' => $traLoi->ThoiGianGiay
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
        
        // Reload để lấy chi tiết câu trả lời
        $lanLamBai->load(['traLois.cauHoi.luaChons']);
        
        // Build chi tiết câu hỏi
        $chiTiet = $lanLamBai->traLois->map(function ($traLoi, $index) {
            $cauHoi = $traLoi->cauHoi;
            $luaChonDaChon = $traLoi->luaChonIdsArray ?? [];
            
            return [
                'questionNumber' => $index + 1,
                'cauHoiId' => $cauHoi->CauHoiId,
                'noiDung' => $cauHoi->DeBai,
                'isCorrect' => $traLoi->DungHaySai === true || $traLoi->DungHaySai === 1,
                'giaiThich' => $cauHoi->GiaiThich,
                'luaChons' => $cauHoi->luaChons->map(function ($luaChon) use ($luaChonDaChon) {
                    return [
                        'id' => $luaChon->LuaChonId,
                        'noiDung' => $luaChon->NoiDung,
                        'isCorrect' => $luaChon->DungHaySai === true || $luaChon->DungHaySai === 1,
                        'isUserAnswer' => in_array($luaChon->LuaChonId, $luaChonDaChon)
                    ];
                })->values()->all()
            ];
        })->values()->all();
        
        $result['chiTiet'] = $chiTiet;

        return response()->json([
            'message' => 'Nộp bài thành công',
            'data' => $result
        ]);
    }

    /**
     * Xử lý nộp bài nội bộ
     * Logic chấm điểm:
     * 1. Lấy tất cả câu trả lời của lần làm bài
     * 2. Với mỗi câu trả lời, kiểm tra lựa chọn có đúng không
     * 3. Tính điểm: (soCauDung / tongSoCau) * 10
     */
    private function nopBaiInternal(LanLamBai $lanLamBai)
    {
        return DB::transaction(function () use ($lanLamBai) {
            // Chấm điểm từng câu trả lời
            $traLois = $lanLamBai->traLois()->with('cauHoi.luaChons')->get();
            
            foreach ($traLois as $traLoi) {
                // Gọi phương thức chamDiem() của model TraLoi
                $traLoi->chamDiem();
            }

            // Refresh lại để lấy kết quả chấm điểm mới
            $lanLamBai->load('traLois');
            
            // Tính tổng điểm
            $ketQua = $lanLamBai->tinhDiem();

            // Cập nhật điểm vào bảng LanLamBai
            $lanLamBai->update([
                'DiemSo' => $ketQua['diem'],
                'NopBaiLuc' => now(),
                'TrangThai' => LanLamBai::TRANG_THAI_HOAN_THANH
            ]);

            // Kiểm tra đạt hay không
            $baiKiemTra = $lanLamBai->baiKiemTra;
            $diemDat = $baiKiemTra->DiemDat ?? 5;
            $dat = $ketQua['diem'] >= $diemDat;

            // Tính thời gian làm bài
            $thoiGianLam = $lanLamBai->BatDauLuc 
                ? now()->diffInMinutes($lanLamBai->BatDauLuc) 
                : 0;

            return [
                'lanLamBaiId' => $lanLamBai->LanLamBaiId,
                'diem' => $ketQua['diem'],
                'soCauDung' => $ketQua['soCauDung'],
                'tongSoCau' => $ketQua['tongSoCau'],
                'diemDat' => $diemDat,
                'dat' => $dat,
                'trangThai' => LanLamBai::TRANG_THAI_HOAN_THANH,
                'thoiGianLam' => $thoiGianLam . ' phút',
                'batDauLuc' => $lanLamBai->BatDauLuc,
                'nopBaiLuc' => $lanLamBai->NopBaiLuc,
                'khoaHocId' => $baiKiemTra->KhoaHocId ?? $baiKiemTra->baiHoc?->KhoaHocId ?? null
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
            'khoaHocId' => $lanLamBai->baiKiemTra->KhoaHocId ?? null,
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
