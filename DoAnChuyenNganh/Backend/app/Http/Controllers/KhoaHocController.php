<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KhoaHoc;

class KhoaHocController extends Controller
{
    /**
     * Lấy tất cả khóa học active (có phân trang)
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $khoaHocs = KhoaHoc::with(['baiHocs'])
            ->active()
            ->orderBy('TaoLuc', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $khoaHocs->map(function ($khoaHoc) {
                return $this->formatKhoaHoc($khoaHoc);
            }),
            'pagination' => [
                'total' => $khoaHocs->total(),
                'per_page' => $khoaHocs->perPage(),
                'current_page' => $khoaHocs->currentPage(),
                'last_page' => $khoaHocs->lastPage()
            ]
        ]);
    }

    /**
     * Lấy tất cả khóa học (bao gồm cả active và inactive) - dành cho admin
     */
    public function getAll(Request $request)
    {
        $perPage = $request->get('per_page', 20);
        
        $khoaHocs = KhoaHoc::with(['baiHocs'])
            ->orderBy('TaoLuc', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $khoaHocs->map(function ($khoaHoc) {
                return $this->formatKhoaHoc($khoaHoc);
            }),
            'pagination' => [
                'total' => $khoaHocs->total(),
                'per_page' => $khoaHocs->perPage(),
                'current_page' => $khoaHocs->currentPage(),
                'last_page' => $khoaHocs->lastPage()
            ]
        ]);
    }

    /**
     * Lấy khóa học theo ID
     */
    public function show($id)
    {
        $khoaHoc = KhoaHoc::with(['baiHocs'])
            ->find($id);

        if (!$khoaHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatKhoaHoc($khoaHoc)
        ]);
    }

    /**
     * Lấy khóa học Pro (có phí)
     */
    public function getProCourses(Request $request)
    {
        $limit = $request->get('limit', 8);

        $khoaHocs = KhoaHoc::with(['baiHocs'])
            ->active()
            ->where('GiaTien', '>', 0)  // Khóa học có phí
            ->orderBy('TaoLuc', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $khoaHocs->map(function ($khoaHoc) {
                return $this->formatKhoaHoc($khoaHoc);
            })
        ]);
    }

    /**
     * Lấy khóa học miễn phí
     */
    public function getFreeCourses(Request $request)
    {
        $limit = $request->get('limit', 8);

        $khoaHocs = KhoaHoc::with(['baiHocs'])
            ->active()
            ->where(function($query) {
                $query->where('GiaTien', 0)
                      ->orWhereNull('GiaTien');
            })
            ->orderBy('TaoLuc', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $khoaHocs->map(function ($khoaHoc) {
                return $this->formatKhoaHoc($khoaHoc);
            })
        ]);
    }

    /**
     * Lấy khóa học phổ biến (nhiều học viên nhất)
     */
    public function getPopular(Request $request)
    {
        $limit = $request->get('limit', 8);

        $khoaHocs = KhoaHoc::with(['baiHocs'])
            ->active()
            ->orderBy('TaoLuc', 'desc')  // TODO: sắp xếp theo số học viên khi có bảng đăng ký
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $khoaHocs->map(function ($khoaHoc) {
                return $this->formatKhoaHoc($khoaHoc);
            })
        ]);
    }

    /**
     * Lấy khóa học mới nhất
     */
    public function getLatest(Request $request)
    {
        $limit = $request->get('limit', 8);

        $khoaHocs = KhoaHoc::with(['baiHocs'])
            ->active()
            ->orderBy('TaoLuc', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $khoaHocs->map(function ($khoaHoc) {
                return $this->formatKhoaHoc($khoaHoc);
            })
        ]);
    }

    /**
     * Tìm kiếm khóa học
     */
    public function search(Request $request)
    {
        $keyword = $request->get('q', '');
        $perPage = $request->get('per_page', 10);

        $khoaHocs = KhoaHoc::with(['baiHocs'])
            ->active()
            ->where(function ($query) use ($keyword) {
                $query->where('TieuDe', 'like', "%{$keyword}%")
                      ->orWhere('TomTat', 'like', "%{$keyword}%");
            })
            ->orderBy('TaoLuc', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $khoaHocs->map(function ($khoaHoc) {
                return $this->formatKhoaHoc($khoaHoc);
            }),
            'pagination' => [
                'total' => $khoaHocs->total(),
                'per_page' => $khoaHocs->perPage(),
                'current_page' => $khoaHocs->currentPage(),
                'last_page' => $khoaHocs->lastPage()
            ]
        ]);
    }

    /**
     * Format dữ liệu khóa học để trả về frontend
     */
    private function formatKhoaHoc($khoaHoc)
    {
        return [
            'id' => $khoaHoc->KhoaHocId,
            'title' => $khoaHoc->TieuDe,
            'description' => $khoaHoc->TomTat,
            'thumbnail' => $khoaHoc->HinhAnhUrl,
            'icon' => '📚',
            'level' => $khoaHoc->CapDo,
            'tags' => $khoaHoc->Tags,
            'prerequisites' => $khoaHoc->DieuKienTienQuyet,
            'price' => $khoaHoc->GiaTien ?? 0,
            'originalPrice' => null,
            'status' => $khoaHoc->TrangThai,
            'students' => 0, // TODO: đếm từ bảng đăng ký nếu có
            'lessons' => $khoaHoc->baiHocs ? $khoaHoc->baiHocs->count() : 0,
            'createdAt' => $khoaHoc->TaoLuc
        ];
    }
}
