<?php

namespace App\Http\Controllers;

use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Xử lý đăng nhập/đăng ký bằng Google
     * Verify Google access token và tạo/đăng nhập user
     */
    public function handleGoogleLogin(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string'
        ]);

        try {
            // Verify Google token bằng cách gọi Google API
            $googleUser = $this->verifyGoogleToken($request->access_token);
            
            if (!$googleUser) {
                return response()->json([
                    'message' => 'Token Google không hợp lệ'
                ], 401);
            }

            // Tìm user theo GoogleId hoặc Email
            $user = NguoiDung::where('GoogleId', $googleUser['id'])
                            ->orWhere('Email', $googleUser['email'])
                            ->first();

            if (!$user) {
                // Tạo user mới từ Google account
                $user = NguoiDung::create([
                    'HoTen' => $googleUser['name'],
                    'Email' => $googleUser['email'],
                    'GoogleId' => $googleUser['id'],
                    'MatKhauHash' => Hash::make(Str::random(32)), // Random password
                    'TrangThai' => 1, // 1 = Hoạt động
                    'AvatarUrl' => $googleUser['picture'] ?? null
                ]);
                
                // Gán vai trò HOC_VIEN mặc định (VaiTroId = 2)
                $user->vaiTros()->attach(2);
            } else {
                // Cập nhật GoogleId và Avatar nếu chưa có
                $updateData = [];
                if (!$user->GoogleId) {
                    $updateData['GoogleId'] = $googleUser['id'];
                }
                if (!$user->AvatarUrl && isset($googleUser['picture'])) {
                    $updateData['AvatarUrl'] = $googleUser['picture'];
                }
                if (!empty($updateData)) {
                    $user->update($updateData);
                }
            }

            // Kiểm tra trạng thái user (1 = hoạt động)
            if ($user->TrangThai != 1) {
                return response()->json([
                    'message' => 'Tài khoản đã bị khóa'
                ], 403);
            }

            // Tạo token
            $token = $user->createToken('google-auth-token')->plainTextToken;

            // Lấy roles từ quan hệ vaiTros
            $roles = $user->vaiTros->pluck('TenVaiTro')->map(function($role) {
                if ($role === 'ADMIN') return 'ADMIN';
                if ($role === 'HOC_VIEN') return 'STUDENT';
                if ($role === 'GIANG_VIEN') return 'INSTRUCTOR';
                return $role;
            })->toArray();

            return response()->json([
                'message' => 'Đăng nhập Google thành công',
                'token' => $token,
                'user' => [
                    'id' => $user->NguoiDungId,
                    'name' => $user->HoTen,
                    'email' => $user->Email,
                    'avatar' => $user->AvatarUrl,
                    'roles' => $roles
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi xác thực Google: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Google access token
     * Gọi Google API để lấy thông tin user
     */
    private function verifyGoogleToken($accessToken)
    {
        try {
            $url = 'https://www.googleapis.com/oauth2/v3/userinfo';
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode !== 200) {
                return null;
            }
            
            $userData = json_decode($response, true);
            
            // Kiểm tra email đã verified
            if (!isset($userData['email_verified']) || !$userData['email_verified']) {
                return null;
            }
            
            return [
                'id' => $userData['sub'] ?? null,
                'email' => $userData['email'] ?? null,
                'name' => $userData['name'] ?? null,
                'picture' => $userData['picture'] ?? null
            ];
            
        } catch (\Exception $e) {
            Log::error('Google token verification failed: ' . $e->getMessage());
            return null;
        }
    }
}
