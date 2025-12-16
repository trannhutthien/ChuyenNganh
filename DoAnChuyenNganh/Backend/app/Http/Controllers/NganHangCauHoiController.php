<?php

namespace App\Http\Controllers;

use App\Models\NganHangCauHoi;
use App\Models\CauHoi;
use App\Models\LuaChon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class NganHangCauHoiController extends Controller
{
    /**
     * Lấy danh sách tất cả ngân hàng câu hỏi
     */
    public function index()
    {
        $nganHangs = NganHangCauHoi::with('khoaHoc:KhoaHocId,TieuDe')
            ->withCount('cauHois')
            ->get()
            ->map(function ($nganHang) {
                return [
                    'id' => $nganHang->NganHangId,
                    'khoaHocId' => $nganHang->KhoaHocId,
                    'tenKhoaHoc' => $nganHang->khoaHoc->TieuDe ?? null,
                    'tenNganHang' => $nganHang->TenNganHang,
                    'moTa' => $nganHang->MoTa,
                    'capDoMacDinh' => $nganHang->CapDoMacDinh,
                    'soCauHoi' => $nganHang->cau_hois_count
                ];
            });

        return response()->json($nganHangs);
    }

    /**
     * Lấy ngân hàng câu hỏi theo khóa học
     */
    public function getByKhoaHoc($khoaHocId)
    {
        $nganHangs = NganHangCauHoi::where('KhoaHocId', $khoaHocId)
            ->withCount('cauHois')
            ->get()
            ->map(function ($nganHang) {
                return [
                    'id' => $nganHang->NganHangId,
                    'khoaHocId' => $nganHang->KhoaHocId,
                    'tenNganHang' => $nganHang->TenNganHang,
                    'moTa' => $nganHang->MoTa,
                    'capDoMacDinh' => $nganHang->CapDoMacDinh,
                    'soCauHoi' => $nganHang->cau_hois_count
                ];
            });

        return response()->json($nganHangs);
    }

    /**
     * Lấy chi tiết một ngân hàng câu hỏi kèm danh sách câu hỏi
     */
    public function show($id)
    {
        $nganHang = NganHangCauHoi::with(['khoaHoc:KhoaHocId,TieuDe', 'cauHois.luaChons'])
            ->find($id);

        if (!$nganHang) {
            return response()->json(['message' => 'Không tìm thấy ngân hàng câu hỏi'], 404);
        }

        return response()->json([
            'id' => $nganHang->NganHangId,
            'khoaHocId' => $nganHang->KhoaHocId,
            'tenKhoaHoc' => $nganHang->khoaHoc->TieuDe ?? null,
            'tenNganHang' => $nganHang->TenNganHang,
            'moTa' => $nganHang->MoTa,
            'capDoMacDinh' => $nganHang->CapDoMacDinh,
            'cauHois' => $nganHang->cauHois->map(function ($cauHoi) {
                return $this->formatCauHoi($cauHoi);
            })
        ]);
    }

    /**
     * Tạo ngân hàng câu hỏi mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'khoaHocId' => 'required|exists:KhoaHoc,KhoaHocId',
            'tenNganHang' => 'required|string|max:300',
            'moTa' => 'nullable|string',
            'capDoMacDinh' => 'nullable|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nganHang = NganHangCauHoi::create([
            'KhoaHocId' => $request->khoaHocId,
            'TenNganHang' => $request->tenNganHang,
            'MoTa' => $request->moTa,
            'CapDoMacDinh' => $request->capDoMacDinh ?? 1
        ]);

        return response()->json([
            'message' => 'Tạo ngân hàng câu hỏi thành công',
            'data' => [
                'id' => $nganHang->NganHangId,
                'khoaHocId' => $nganHang->KhoaHocId,
                'tenNganHang' => $nganHang->TenNganHang,
                'moTa' => $nganHang->MoTa,
                'capDoMacDinh' => $nganHang->CapDoMacDinh
            ]
        ], 201);
    }

    /**
     * Cập nhật ngân hàng câu hỏi
     */
    public function update(Request $request, $id)
    {
        $nganHang = NganHangCauHoi::find($id);

        if (!$nganHang) {
            return response()->json(['message' => 'Không tìm thấy ngân hàng câu hỏi'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tenNganHang' => 'required|string|max:300',
            'moTa' => 'nullable|string',
            'capDoMacDinh' => 'nullable|integer|min:1|max:5'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nganHang->update([
            'TenNganHang' => $request->tenNganHang,
            'MoTa' => $request->moTa,
            'CapDoMacDinh' => $request->capDoMacDinh ?? $nganHang->CapDoMacDinh
        ]);

        return response()->json([
            'message' => 'Cập nhật ngân hàng câu hỏi thành công',
            'data' => [
                'id' => $nganHang->NganHangId,
                'khoaHocId' => $nganHang->KhoaHocId,
                'tenNganHang' => $nganHang->TenNganHang,
                'moTa' => $nganHang->MoTa,
                'capDoMacDinh' => $nganHang->CapDoMacDinh
            ]
        ]);
    }

    /**
     * Xóa ngân hàng câu hỏi
     */
    public function destroy($id)
    {
        $nganHang = NganHangCauHoi::find($id);

        if (!$nganHang) {
            return response()->json(['message' => 'Không tìm thấy ngân hàng câu hỏi'], 404);
        }

        // Xóa tất cả câu hỏi và lựa chọn liên quan
        DB::transaction(function () use ($nganHang) {
            // Xóa lựa chọn của các câu hỏi
            LuaChon::whereIn('CauHoiId', $nganHang->cauHois()->pluck('CauHoiId'))->delete();
            // Xóa câu hỏi
            $nganHang->cauHois()->delete();
            // Xóa ngân hàng
            $nganHang->delete();
        });

        return response()->json(['message' => 'Xóa ngân hàng câu hỏi thành công']);
    }

    /**
     * Lấy danh sách câu hỏi trong ngân hàng
     */
    public function getCauHois($nganHangId, Request $request)
    {
        $query = CauHoi::where('NganHangId', $nganHangId)
            ->with('luaChons');

        // Lọc theo độ khó
        if ($request->has('doKho')) {
            $query->doKho($request->doKho);
        }

        // Lọc theo loại câu hỏi
        if ($request->has('loai')) {
            $query->loai($request->loai);
        }

        // Lọc theo bài học
        if ($request->has('baiHocId')) {
            $query->thuocBaiHoc($request->baiHocId);
        }

        $cauHois = $query->orderBy('ThuTu', 'asc')
            ->get()
            ->map(function ($cauHoi) {
                return $this->formatCauHoi($cauHoi);
            });

        return response()->json($cauHois);
    }

    /**
     * Thêm câu hỏi vào ngân hàng
     */
    public function storeCauHoi(Request $request, $nganHangId)
    {
        $nganHang = NganHangCauHoi::find($nganHangId);

        if (!$nganHang) {
            return response()->json(['message' => 'Không tìm thấy ngân hàng câu hỏi'], 404);
        }

        $validator = Validator::make($request->all(), [
            'baiHocId' => 'nullable|exists:BaiHoc,BaiHocId',
            'loai' => 'required|in:single,multiple,true_false,fill_blank',
            'deBai' => 'required|string',
            'giaiThich' => 'nullable|string',
            'doKho' => 'nullable|integer|min:1|max:5',
            'chuDeTags' => 'nullable|string|max:300',
            'thuTu' => 'nullable|integer',
            'luaChons' => 'required|array|min:2',
            'luaChons.*.noiDung' => 'required|string',
            'luaChons.*.dungHaySai' => 'required|boolean',
            'luaChons.*.thuTu' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cauHoi = DB::transaction(function () use ($request, $nganHangId) {
            // Tạo câu hỏi
            $cauHoi = CauHoi::create([
                'NganHangId' => $nganHangId,
                'BaiHocId' => $request->baiHocId,
                'Loai' => $request->loai,
                'DeBai' => $request->deBai,
                'GiaiThich' => $request->giaiThich,
                'DoKho' => $request->doKho ?? 1,
                'ChuDeTags' => $request->chuDeTags,
                'ThuTu' => $request->thuTu ?? 1
            ]);

            // Tạo các lựa chọn
            foreach ($request->luaChons as $index => $luaChon) {
                LuaChon::create([
                    'CauHoiId' => $cauHoi->CauHoiId,
                    'NoiDung' => $luaChon['noiDung'],
                    'DungHaySai' => $luaChon['dungHaySai'],
                    'ThuTu' => $luaChon['thuTu'] ?? ($index + 1)
                ]);
            }

            return $cauHoi->load('luaChons');
        });

        return response()->json([
            'message' => 'Thêm câu hỏi thành công',
            'data' => $this->formatCauHoi($cauHoi)
        ], 201);
    }

    /**
     * Cập nhật câu hỏi
     */
    public function updateCauHoi(Request $request, $nganHangId, $cauHoiId)
    {
        $cauHoi = CauHoi::where('NganHangId', $nganHangId)
            ->where('CauHoiId', $cauHoiId)
            ->first();

        if (!$cauHoi) {
            return response()->json(['message' => 'Không tìm thấy câu hỏi'], 404);
        }

        $validator = Validator::make($request->all(), [
            'baiHocId' => 'nullable|exists:BaiHoc,BaiHocId',
            'loai' => 'required|in:single,multiple,true_false,fill_blank',
            'deBai' => 'required|string',
            'giaiThich' => 'nullable|string',
            'doKho' => 'nullable|integer|min:1|max:5',
            'chuDeTags' => 'nullable|string|max:300',
            'thuTu' => 'nullable|integer',
            'luaChons' => 'required|array|min:2',
            'luaChons.*.noiDung' => 'required|string',
            'luaChons.*.dungHaySai' => 'required|boolean',
            'luaChons.*.thuTu' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cauHoi = DB::transaction(function () use ($request, $cauHoi) {
            // Cập nhật câu hỏi
            $cauHoi->update([
                'BaiHocId' => $request->baiHocId,
                'Loai' => $request->loai,
                'DeBai' => $request->deBai,
                'GiaiThich' => $request->giaiThich,
                'DoKho' => $request->doKho ?? $cauHoi->DoKho,
                'ChuDeTags' => $request->chuDeTags,
                'ThuTu' => $request->thuTu ?? $cauHoi->ThuTu
            ]);

            // Xóa các lựa chọn cũ và tạo mới
            $cauHoi->luaChons()->delete();
            
            foreach ($request->luaChons as $index => $luaChon) {
                LuaChon::create([
                    'CauHoiId' => $cauHoi->CauHoiId,
                    'NoiDung' => $luaChon['noiDung'],
                    'DungHaySai' => $luaChon['dungHaySai'],
                    'ThuTu' => $luaChon['thuTu'] ?? ($index + 1)
                ]);
            }

            return $cauHoi->load('luaChons');
        });

        return response()->json([
            'message' => 'Cập nhật câu hỏi thành công',
            'data' => $this->formatCauHoi($cauHoi)
        ]);
    }

    /**
     * Xóa câu hỏi
     */
    public function destroyCauHoi($nganHangId, $cauHoiId)
    {
        $cauHoi = CauHoi::where('NganHangId', $nganHangId)
            ->where('CauHoiId', $cauHoiId)
            ->first();

        if (!$cauHoi) {
            return response()->json(['message' => 'Không tìm thấy câu hỏi'], 404);
        }

        DB::transaction(function () use ($cauHoi) {
            $cauHoi->luaChons()->delete();
            $cauHoi->delete();
        });

        return response()->json(['message' => 'Xóa câu hỏi thành công']);
    }

    /**
     * Lấy câu hỏi ngẫu nhiên từ ngân hàng
     */
    public function getRandomCauHois($nganHangId, Request $request)
    {
        $soLuong = $request->get('soLuong', 10);
        $doKho = $request->get('doKho');
        $loai = $request->get('loai');

        $query = CauHoi::where('NganHangId', $nganHangId)
            ->with('luaChons');

        if ($doKho) {
            $query->doKho($doKho);
        }

        if ($loai) {
            $query->loai($loai);
        }

        $cauHois = $query->inRandomOrder()
            ->limit($soLuong)
            ->get()
            ->map(function ($cauHoi) {
                return $this->formatCauHoi($cauHoi, false); // không hiện đáp án đúng
            });

        return response()->json($cauHois);
    }

    /**
     * Format câu hỏi để trả về
     */
    private function formatCauHoi($cauHoi, $showAnswer = true)
    {
        $data = [
            'id' => $cauHoi->CauHoiId,
            'nganHangId' => $cauHoi->NganHangId,
            'baiHocId' => $cauHoi->BaiHocId,
            'loai' => $cauHoi->Loai,
            'deBai' => $cauHoi->DeBai,
            'giaiThich' => $showAnswer ? $cauHoi->GiaiThich : null,
            'doKho' => $cauHoi->DoKho,
            'chuDeTags' => $cauHoi->ChuDeTags,
            'thuTu' => $cauHoi->ThuTu,
            'luaChons' => $cauHoi->luaChons->map(function ($luaChon) use ($showAnswer) {
                $item = [
                    'id' => $luaChon->LuaChonId,
                    'noiDung' => $luaChon->NoiDung,
                    'thuTu' => $luaChon->ThuTu
                ];
                if ($showAnswer) {
                    $item['dungHaySai'] = $luaChon->DungHaySai;
                }
                return $item;
            })
        ];

        return $data;
    }
}
