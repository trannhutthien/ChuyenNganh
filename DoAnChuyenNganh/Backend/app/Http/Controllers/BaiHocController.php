<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BaiHoc;
use App\Models\NoiDungBaiHoc;

class BaiHocController extends Controller
{
    /**
     * Lấy chi tiết bài học theo ID
     */
    public function show($id)
    {
        $baiHoc = BaiHoc::with(['noiDungs', 'khoaHoc'])->find($id);

        if (!$baiHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài học'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatBaiHoc($baiHoc)
        ]);
    }

    /**
     * Lấy nội dung chi tiết của bài học
     */
    public function getContent($id)
    {
        $baiHoc = BaiHoc::find($id);

        if (!$baiHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài học'
            ], 404);
        }

        // Lấy nội dung bài học, sắp xếp theo thứ tự
        $noiDungs = NoiDungBaiHoc::where('BaiHocId', $id)
            ->orderBy('ThuTu', 'asc')
            ->orderBy('NoiDungId', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'lesson' => [
                'id' => $baiHoc->BaiHocId,
                'title' => $baiHoc->TieuDe,
                'description' => $baiHoc->MoTa
            ],
            'contents' => $noiDungs->map(function ($noiDung) {
                return $this->formatNoiDung($noiDung);
            })
        ]);
    }

    /**
     * Format dữ liệu bài học
     */
    private function formatBaiHoc($baiHoc)
    {
        return [
            'id' => $baiHoc->BaiHocId,
            'courseId' => $baiHoc->KhoaHocId,
            'courseName' => $baiHoc->khoaHoc ? $baiHoc->khoaHoc->TieuDe : null,
            'title' => $baiHoc->TieuDe,
            'description' => $baiHoc->MoTa,
            'type' => $baiHoc->LoaiBaiHoc,
            'order' => $baiHoc->ThuTu,
            'duration' => $baiHoc->ThoiLuong,
            'videoUrl' => $baiHoc->VideoUrl,
            'status' => $baiHoc->TrangThai,
            'contents' => $baiHoc->noiDungs->map(function ($noiDung) {
                return $this->formatNoiDung($noiDung);
            })
        ];
    }

    /**
     * Format dữ liệu nội dung bài học
     */
    private function formatNoiDung($noiDung)
    {
        return [
            'id' => $noiDung->NoiDungId,
            'type' => $noiDung->LoaiNoiDung,
            'title' => $noiDung->TieuDe,
            'content' => $noiDung->NoiDung,
            'order' => $noiDung->ThuTu
        ];
    }

    /**
     * Tạo bài học mới
     */
    public function store(Request $request)
    {
        try {
            $baiHoc = BaiHoc::create([
                'KhoaHocId' => $request->KhoaHocId,
                'TieuDe' => $request->TieuDe,
                'MoTa' => $request->MoTa,
                'NoiDung' => $request->NoiDung,
                'LoaiBaiHoc' => $request->LoaiBaiHoc ?? 'video',
                'ThuTu' => $request->ThuTu ?? 1,
                'ThoiLuong' => $request->ThoiLuong ?? 0,
                'VideoUrl' => $request->VideoUrl,
                'TrangThai' => $request->TrangThai ?? 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo bài học thành công',
                'data' => $this->formatBaiHoc($baiHoc)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật bài học
     */
    public function update(Request $request, $id)
    {
        $baiHoc = BaiHoc::find($id);

        if (!$baiHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài học'
            ], 404);
        }

        try {
            $baiHoc->update([
                'TieuDe' => $request->TieuDe ?? $baiHoc->TieuDe,
                'MoTa' => $request->MoTa ?? $baiHoc->MoTa,
                'NoiDung' => $request->NoiDung ?? $baiHoc->NoiDung,
                'LoaiBaiHoc' => $request->LoaiBaiHoc ?? $baiHoc->LoaiBaiHoc,
                'ThuTu' => $request->ThuTu ?? $baiHoc->ThuTu,
                'ThoiLuong' => $request->ThoiLuong ?? $baiHoc->ThoiLuong,
                'VideoUrl' => $request->VideoUrl ?? $baiHoc->VideoUrl,
                'TrangThai' => $request->TrangThai ?? $baiHoc->TrangThai,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật bài học thành công',
                'data' => $this->formatBaiHoc($baiHoc->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật bài học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa bài học
     */
    public function destroy($id)
    {
        $baiHoc = BaiHoc::find($id);

        if (!$baiHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài học'
            ], 404);
        }

        try {
            // Xóa nội dung bài học liên quan
            $baiHoc->noiDungs()->delete();
            
            // Xóa bài học
            $baiHoc->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa bài học thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa bài học: ' . $e->getMessage()
            ], 500);
        }
    }
}
