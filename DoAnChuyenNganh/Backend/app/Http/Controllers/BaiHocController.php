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
            'content' => $noiDung->NoiDung,
            'order' => $noiDung->ThuTu
        ];
    }
}
