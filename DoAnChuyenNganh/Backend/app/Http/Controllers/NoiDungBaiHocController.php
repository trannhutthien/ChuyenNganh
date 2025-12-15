<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NoiDungBaiHoc;
use App\Models\BaiHoc;
use Illuminate\Support\Facades\Validator;

class NoiDungBaiHocController extends Controller
{
    /**
     * Thêm nội dung mới vào bài học
     */
    public function store(Request $request, $baiHocId)
    {
        $validator = Validator::make($request->all(), [
            'LoaiNoiDung' => 'required|in:heading,subheading,paragraph,image,video,quote,list',
            'TieuDe' => 'nullable|string|max:500',
            'NoiDung' => 'nullable|string',
            'ThuTu' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Kiểm tra bài học tồn tại
        $baiHoc = BaiHoc::find($baiHocId);
        if (!$baiHoc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bài học'
            ], 404);
        }

        try {
            $noiDung = NoiDungBaiHoc::create([
                'BaiHocId' => $baiHocId,
                'LoaiNoiDung' => $request->LoaiNoiDung,
                'TieuDe' => $request->TieuDe,
                'NoiDung' => $request->NoiDung,
                'ThuTu' => $request->ThuTu
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thêm nội dung thành công',
                'data' => $this->formatNoiDung($noiDung)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi thêm nội dung: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật nội dung
     */
    public function update(Request $request, $id)
    {
        $noiDung = NoiDungBaiHoc::find($id);

        if (!$noiDung) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nội dung'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'LoaiNoiDung' => 'sometimes|in:heading,subheading,paragraph,image,video,quote,list',
            'TieuDe' => 'nullable|string|max:500',
            'NoiDung' => 'nullable|string',
            'ThuTu' => 'sometimes|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $noiDung->update($request->only(['LoaiNoiDung', 'TieuDe', 'NoiDung', 'ThuTu']));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật nội dung thành công',
                'data' => $this->formatNoiDung($noiDung->fresh())
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật nội dung: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa nội dung
     */
    public function destroy($id)
    {
        $noiDung = NoiDungBaiHoc::find($id);

        if (!$noiDung) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nội dung'
            ], 404);
        }

        try {
            $noiDung->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa nội dung thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa nội dung: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật thứ tự nhiều nội dung
     */
    public function updateOrder(Request $request, $baiHocId)
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|exists:NoiDungBaiHoc,NoiDungId',
            'items.*.order' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->items as $item) {
                NoiDungBaiHoc::where('NoiDungId', $item['id'])
                    ->update(['ThuTu' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật thứ tự thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật thứ tự: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format dữ liệu nội dung
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
}
