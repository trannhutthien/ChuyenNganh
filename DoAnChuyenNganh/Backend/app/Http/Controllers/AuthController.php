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
}
