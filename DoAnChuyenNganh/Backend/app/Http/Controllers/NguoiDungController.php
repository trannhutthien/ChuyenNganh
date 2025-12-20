<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use App\Models\VaiTro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class NguoiDungController extends Controller
{
    /**
     * Lấy danh sách người dùng
     */
    public function index()
    {
        $nguoiDungs = NguoiDung::with('vaiTros')->get()->map(function ($user) {
            return $this->formatNguoiDung($user);
        });

        return response()->json([
            'success' => true,
            'data' => $nguoiDungs
        ]);
    }

    /**
     * Chi tiết người dùng
     */
    public function show($id)
    {
        $nguoiDung = NguoiDung::with('vaiTros')->find($id);

        if (!$nguoiDung) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatNguoiDung($nguoiDung)
        ]);
    }

    /**
     * Tạo người dùng mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'HoTen' => 'required|string|max:100',
            'Email' => 'required|email|unique:NguoiDung,Email',
            'MatKhau' => 'required|string|min:6',
            'SoDienThoai' => 'nullable|string|max:20',
            'VaiTro' => 'nullable|string|in:admin,instructor,student',
            'TrangThai' => 'nullable|integer|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $nguoiDung = DB::transaction(function () use ($request) {
            $nguoiDung = NguoiDung::create([
                'HoTen' => $request->HoTen,
                'Email' => $request->Email,
                'MatKhauHash' => Hash::make($request->MatKhau),
                'TrangThai' => $request->TrangThai ?? 1
            ]);

            // Gán vai trò
            $roleName = $this->mapRoleToVaiTro($request->VaiTro ?? 'student');
            $vaiTro = VaiTro::where('TenVaiTro', $roleName)->first();
            if ($vaiTro) {
                $nguoiDung->vaiTros()->attach($vaiTro->VaiTroId);
            }

            return $nguoiDung;
        });

        return response()->json([
            'success' => true,
            'message' => 'Tạo người dùng thành công',
            'data' => $this->formatNguoiDung($nguoiDung->load('vaiTros'))
        ], 201);
    }

    /**
     * Cập nhật người dùng
     */
    public function update(Request $request, $id)
    {
        $nguoiDung = NguoiDung::find($id);

        if (!$nguoiDung) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        $validator = Validator::make($request->all(), [
            'HoTen' => 'required|string|max:100',
            'Email' => 'required|email|unique:NguoiDung,Email,' . $id . ',NguoiDungId',
            'SoDienThoai' => 'nullable|string|max:20',
            'VaiTro' => 'nullable|string|in:admin,instructor,student',
            'TrangThai' => 'nullable|integer|in:0,1'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::transaction(function () use ($request, $nguoiDung) {
            $nguoiDung->update([
                'HoTen' => $request->HoTen,
                'Email' => $request->Email,
                'TrangThai' => $request->TrangThai ?? $nguoiDung->TrangThai
            ]);

            // Cập nhật vai trò nếu có
            if ($request->has('VaiTro')) {
                $roleName = $this->mapRoleToVaiTro($request->VaiTro);
                $vaiTro = VaiTro::where('TenVaiTro', $roleName)->first();
                if ($vaiTro) {
                    $nguoiDung->vaiTros()->sync([$vaiTro->VaiTroId]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật người dùng thành công',
            'data' => $this->formatNguoiDung($nguoiDung->fresh()->load('vaiTros'))
        ]);
    }

    /**
     * Xóa người dùng
     */
    public function destroy($id)
    {
        $nguoiDung = NguoiDung::find($id);

        if (!$nguoiDung) {
            return response()->json(['message' => 'Không tìm thấy người dùng'], 404);
        }

        DB::transaction(function () use ($nguoiDung) {
            // Xóa vai trò liên kết
            $nguoiDung->vaiTros()->detach();
            // Xóa người dùng
            $nguoiDung->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Xóa người dùng thành công'
        ]);
    }

    /**
     * Format người dùng
     */
    private function formatNguoiDung($nguoiDung)
    {
        $roles = $nguoiDung->vaiTros->pluck('TenVaiTro')->toArray();
        $role = $this->mapVaiTroToRole($roles[0] ?? 'STUDENT');

        return [
            'id' => $nguoiDung->NguoiDungId,
            'name' => $nguoiDung->HoTen,
            'email' => $nguoiDung->Email,
            'avatar' => $nguoiDung->AvatarUrl,
            'role' => $role,
            'roles' => $roles,
            'status' => $nguoiDung->TrangThai,
            'createdAt' => $nguoiDung->TaoLuc
        ];
    }

    /**
     * Map role từ frontend sang tên vai trò trong DB
     */
    private function mapRoleToVaiTro($role)
    {
        $map = [
            'admin' => 'ADMIN',
            'instructor' => 'EDITOR',
            'student' => 'STUDENT'
        ];
        return $map[$role] ?? 'STUDENT';
    }

    /**
     * Map vai trò từ DB sang role cho frontend
     */
    private function mapVaiTroToRole($vaiTro)
    {
        $map = [
            'ADMIN' => 'admin',
            'EDITOR' => 'instructor',
            'STUDENT' => 'student'
        ];
        return $map[$vaiTro] ?? 'student';
    }
}
