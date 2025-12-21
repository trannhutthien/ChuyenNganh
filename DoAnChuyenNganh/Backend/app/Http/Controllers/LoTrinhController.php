<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoTrinh;
use App\Models\KhoaHoc;
use Illuminate\Support\Str;

class LoTrinhController extends Controller
{
    /**
     * Lấy danh sách lộ trình active
     */
    public function index()
    {
        $loTrinhs = LoTrinh::with(['khoaHocs'])
            ->active()
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $loTrinhs->map(function ($loTrinh) {
                return $this->formatLoTrinh($loTrinh);
            })
        ]);
    }

    /**
     * Lấy tất cả lộ trình (Admin)
     */
    public function getAll()
    {
        $loTrinhs = LoTrinh::with(['khoaHocs'])
            ->ordered()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $loTrinhs->map(function ($loTrinh) {
                return $this->formatLoTrinh($loTrinh);
            })
        ]);
    }

    /**
     * Lấy chi tiết lộ trình theo slug
     */
    public function show($slug)
    {
        $loTrinh = LoTrinh::where('Slug', $slug)->first();

        if (!$loTrinh) {
            // Thử tìm theo ID nếu không phải slug
            $loTrinh = LoTrinh::find($slug);
        }

        if (!$loTrinh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lộ trình'
            ], 404);
        }

        // Load khoaHocs với baiHocs riêng để đảm bảo đếm đúng
        $loTrinh->load(['khoaHocs' => function($query) {
            $query->orderBy('LoTrinh_KhoaHoc.ThuTu', 'asc');
        }, 'khoaHocs.baiHocs']);

        return response()->json([
            'success' => true,
            'data' => $this->formatLoTrinhDetail($loTrinh)
        ]);
    }

    /**
     * Lấy danh sách khóa học trong lộ trình
     */
    public function getCourses($id)
    {
        $loTrinh = LoTrinh::find($id);

        if (!$loTrinh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lộ trình'
            ], 404);
        }

        $khoaHocs = $loTrinh->khoaHocs()->with('baiHocs')->get();

        return response()->json([
            'success' => true,
            'data' => $khoaHocs->map(function ($khoaHoc) {
                return $this->formatKhoaHocInLoTrinh($khoaHoc);
            })
        ]);
    }

    /**
     * Tạo lộ trình mới
     */
    public function store(Request $request)
    {
        try {
            $slug = $request->slug ?: Str::slug($request->title);
            
            // Kiểm tra slug đã tồn tại
            if (LoTrinh::where('Slug', $slug)->exists()) {
                $slug = $slug . '-' . time();
            }

            $loTrinh = LoTrinh::create([
                'TieuDe' => $request->title,
                'Slug' => $slug,
                'MoTa' => $request->description,
                'HinhAnhUrl' => $request->thumbnail,
                'Icon' => $request->icon ?? '📚',
                'ThoiGianHoanThanh' => $request->duration,
                'CapDo' => $request->level ?? 1,
                'TrangThai' => $request->status ?? 0,
                'ThuTu' => $request->order ?? 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo lộ trình thành công',
                'data' => $this->formatLoTrinh($loTrinh)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo lộ trình: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật lộ trình
     */
    public function update(Request $request, $id)
    {
        $loTrinh = LoTrinh::find($id);

        if (!$loTrinh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lộ trình'
            ], 404);
        }

        try {
            $loTrinh->update([
                'TieuDe' => $request->title ?? $loTrinh->TieuDe,
                'Slug' => $request->slug ?? $loTrinh->Slug,
                'MoTa' => $request->description ?? $loTrinh->MoTa,
                'HinhAnhUrl' => $request->thumbnail ?? $loTrinh->HinhAnhUrl,
                'Icon' => $request->icon ?? $loTrinh->Icon,
                'ThoiGianHoanThanh' => $request->duration ?? $loTrinh->ThoiGianHoanThanh,
                'CapDo' => $request->level ?? $loTrinh->CapDo,
                'TrangThai' => $request->status ?? $loTrinh->TrangThai,
                'ThuTu' => $request->order ?? $loTrinh->ThuTu
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật lộ trình thành công',
                'data' => $this->formatLoTrinh($loTrinh->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa lộ trình
     */
    public function destroy($id)
    {
        $loTrinh = LoTrinh::find($id);

        if (!$loTrinh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lộ trình'
            ], 404);
        }

        try {
            $loTrinh->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa lộ trình thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thêm khóa học vào lộ trình
     */
    public function addCourse(Request $request, $id)
    {
        $loTrinh = LoTrinh::find($id);

        if (!$loTrinh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lộ trình'
            ], 404);
        }

        $khoaHocId = $request->khoaHocId;
        
        // Kiểm tra khóa học tồn tại
        if (!KhoaHoc::find($khoaHocId)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khóa học'
            ], 404);
        }

        // Kiểm tra đã có trong lộ trình chưa
        if ($loTrinh->khoaHocs()->where('KhoaHoc.KhoaHocId', $khoaHocId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Khóa học đã có trong lộ trình'
            ], 400);
        }

        try {
            $loTrinh->khoaHocs()->attach($khoaHocId, [
                'ThuTu' => $request->order ?? ($loTrinh->khoaHocs()->count() + 1),
                'BatBuoc' => $request->required ?? 1,
                'GhiChu' => $request->note
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm khóa học vào lộ trình thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật khóa học trong lộ trình
     */
    public function updateCourse(Request $request, $id, $khoaHocId)
    {
        $loTrinh = LoTrinh::find($id);

        if (!$loTrinh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lộ trình'
            ], 404);
        }

        try {
            $loTrinh->khoaHocs()->updateExistingPivot($khoaHocId, [
                'ThuTu' => $request->order,
                'BatBuoc' => $request->required,
                'GhiChu' => $request->note
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa khóa học khỏi lộ trình
     */
    public function removeCourse($id, $khoaHocId)
    {
        $loTrinh = LoTrinh::find($id);

        if (!$loTrinh) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lộ trình'
            ], 404);
        }

        try {
            $loTrinh->khoaHocs()->detach($khoaHocId);

            return response()->json([
                'success' => true,
                'message' => 'Xóa khóa học khỏi lộ trình thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format lộ trình cho response
     */
    private function formatLoTrinh($loTrinh)
    {
        $totalLessons = 0;
        if ($loTrinh->khoaHocs) {
            foreach ($loTrinh->khoaHocs as $khoaHoc) {
                $totalLessons += $khoaHoc->baiHocs ? $khoaHoc->baiHocs->count() : 0;
            }
        }

        return [
            'id' => $loTrinh->LoTrinhId,
            'title' => $loTrinh->TieuDe,
            'slug' => $loTrinh->Slug,
            'description' => $loTrinh->MoTa,
            'thumbnail' => $loTrinh->HinhAnhUrl,
            'icon' => $loTrinh->Icon,
            'duration' => $loTrinh->ThoiGianHoanThanh,
            'level' => $loTrinh->CapDo,
            'levelText' => $loTrinh->cap_do_text,
            'status' => $loTrinh->TrangThai,
            'order' => $loTrinh->ThuTu,
            'totalCourses' => $loTrinh->khoaHocs ? $loTrinh->khoaHocs->count() : 0,
            'totalLessons' => $totalLessons
        ];
    }

    /**
     * Format chi tiết lộ trình
     */
    private function formatLoTrinhDetail($loTrinh)
    {
        $courses = $loTrinh->khoaHocs->map(function ($khoaHoc) {
            return $this->formatKhoaHocInLoTrinh($khoaHoc);
        });

        $requiredCourses = $loTrinh->khoaHocs->filter(function ($khoaHoc) {
            return $khoaHoc->pivot->BatBuoc == 1;
        })->count();

        return [
            'id' => $loTrinh->LoTrinhId,
            'title' => $loTrinh->TieuDe,
            'slug' => $loTrinh->Slug,
            'description' => $loTrinh->MoTa,
            'thumbnail' => $loTrinh->HinhAnhUrl,
            'icon' => $loTrinh->Icon,
            'duration' => $loTrinh->ThoiGianHoanThanh,
            'level' => $loTrinh->CapDo,
            'levelText' => $loTrinh->cap_do_text,
            'status' => $loTrinh->TrangThai,
            'courses' => $courses,
            'totalCourses' => $courses->count(),
            'requiredCourses' => $requiredCourses,
            'optionalCourses' => $courses->count() - $requiredCourses
        ];
    }

    /**
     * Format khóa học trong lộ trình
     */
    private function formatKhoaHocInLoTrinh($khoaHoc)
    {
        // Đếm số bài học - sử dụng count() trên collection đã load
        $lessonCount = 0;
        if ($khoaHoc->relationLoaded('baiHocs')) {
            $lessonCount = $khoaHoc->baiHocs->count();
        } else {
            // Fallback: query trực tiếp nếu chưa load
            $lessonCount = \App\Models\BaiHoc::where('KhoaHocId', $khoaHoc->KhoaHocId)->count();
        }

        return [
            'id' => $khoaHoc->KhoaHocId,
            'title' => $khoaHoc->TieuDe,
            'description' => $khoaHoc->TomTat,
            'thumbnail' => $khoaHoc->HinhAnhUrl,
            'level' => $khoaHoc->CapDo,
            'price' => $khoaHoc->GiaTien ?? 0,
            'order' => $khoaHoc->pivot->ThuTu,
            'required' => (bool) $khoaHoc->pivot->BatBuoc,
            'note' => $khoaHoc->pivot->GhiChu,
            'lessons' => $lessonCount
        ];
    }
}
