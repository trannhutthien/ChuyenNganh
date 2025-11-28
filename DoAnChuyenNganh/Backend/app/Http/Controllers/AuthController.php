<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NguoiDung;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validate
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // 2. Tìm user theo email
        $user = NguoiDung::where('Email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email hoặc mật khẩu sai'], 401);
        }

        // 3. So sánh mật khẩu đơn giản (không hash)
        if ($request->password !== $user->MatKhauHash) {
            return response()->json(['message' => 'Email hoặc mật khẩu sai'], 401);
        }

        // 4. Lấy role
        $roles = $user->vaiTros->pluck('MaVaiTro');

        // 5. Chỉ cho phép STUDENT
        if (!$roles->contains('STUDENT')) {
            return response()->json(['message' => 'Không phải học viên'], 403);
        }

        // 6. Tạo token
        $token = $user->createToken('api_token')->plainTextToken;

        // 7. Trả JSON
        return response()->json([
            'message' => 'Đăng nhập thành công',
            'user' => [
                'id' => $user->NguoiDungId,
                'name' => $user->HoTen,
                'email' => $user->Email,
                'roles' => $roles
            ],
            'token' => $token
        ]);
    }

    public function register(Request $request)
    {
        // 1. Validate input
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:NguoiDung,Email',
            'password' => 'required|string|min:6'
        ]);

        try {
            // 2. Tạo user mới
            $user = NguoiDung::create([
                'HoTen' => $request->username,
                'Email' => $request->email,
                'MatKhauHash' => $request->password  // TODO: hash password với bcrypt
            ]);

            // 3. Gán role mặc định STUDENT (ID = 2) - dùng insert trực tiếp
            $user->vaiTros()->attach(2);  // 2 = STUDENT role

            // 4. Reload user với roles
            $user->load('vaiTros');
            $roles = $user->vaiTros->pluck('MaVaiTro');

            // 5. Tạo token
            $token = $user->createToken('api_token')->plainTextToken;

            // 6. Trả JSON
            return response()->json([
                'message' => 'Đăng ký thành công',
                'user' => [
                    'id' => $user->NguoiDungId,
                    'name' => $user->HoTen,
                    'email' => $user->Email,
                    'roles' => $roles
                ],
                'token' => $token
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi đăng ký: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        // Revoke token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Đã đăng xuất thành công']);
    }
}
