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

    /**
     * Lấy danh sách bài học của một khóa học
     */
    public function getLessons($courseId)
    {
        $khoaHoc = KhoaHoc::find($courseId);

        if (!$khoaHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        $baiHocs = \App\Models\BaiHoc::where('KhoaHocId', $courseId)
            ->orderBy('ThuTu', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'course' => [
                'id' => $khoaHoc->KhoaHocId,
                'title' => $khoaHoc->TieuDe,
            ],
            'data' => $baiHocs->map(function ($baiHoc) {
                return $this->formatBaiHoc($baiHoc);
            })
        ]);
    }

    /**
     * Format dữ liệu bài học để trả về frontend
     */
    private function formatBaiHoc($baiHoc)
    {
        return [
            'id' => $baiHoc->BaiHocId,
            'title' => $baiHoc->TieuDe,
            'description' => $baiHoc->MoTa,
            'content' => $baiHoc->NoiDung,
            'type' => $baiHoc->LoaiBaiHoc,
            'order' => $baiHoc->ThuTu,
            'duration' => $baiHoc->ThoiLuong,
            'videoUrl' => $baiHoc->VideoUrl,
            'status' => $baiHoc->TrangThai,
            'completed' => false // TODO: Kiểm tra tiến độ học của user
        ];
    }

    /**
     * Xóa khóa học
     */
    public function destroy($id)
    {
        $khoaHoc = KhoaHoc::find($id);

        if (!$khoaHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        try {
            // Xóa các bài học liên quan trước
            $khoaHoc->baiHocs()->delete();
            
            // Xóa khóa học
            $khoaHoc->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa khóa học thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa khóa học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo khóa học mới
     */
    public function store(Request $request)
    {
        try {
            $khoaHoc = KhoaHoc::create([
                'TieuDe' => $request->title,
                'TomTat' => $request->description,
                'HinhAnhUrl' => $request->thumbnail,
                'CapDo' => $request->level ?? 1,
                'Tags' => $request->tags,
                'DieuKienTienQuyet' => $request->prerequisites,
                'GiaTien' => $request->price ?? 0,
                'TrangThai' => $request->status ?? 0, // Mặc định: Chờ duyệt
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo khóa học thành công',
                'data' => $this->formatKhoaHoc($khoaHoc)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo khóa học: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật khóa học
     */
    public function update(Request $request, $id)
    {
        $khoaHoc = KhoaHoc::find($id);

        if (!$khoaHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        try {
            $khoaHoc->update([
                'TieuDe' => $request->title ?? $khoaHoc->TieuDe,
                'TomTat' => $request->description ?? $khoaHoc->TomTat,
                'HinhAnhUrl' => $request->thumbnail ?? $khoaHoc->HinhAnhUrl,
                'CapDo' => $request->level ?? $khoaHoc->CapDo,
                'Tags' => $request->tags ?? $khoaHoc->Tags,
                'DieuKienTienQuyet' => $request->prerequisites ?? $khoaHoc->DieuKienTienQuyet,
                'GiaTien' => $request->price ?? $khoaHoc->GiaTien,
                'TrangThai' => $request->status ?? $khoaHoc->TrangThai,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật khóa học thành công',
                'data' => $this->formatKhoaHoc($khoaHoc->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật khóa học: ' . $e->getMessage()
            ], 500);
        }
    }
}
