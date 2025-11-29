<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KhoaHocController;
use App\Http\Controllers\BaiHocController;

// ========== AUTH ROUTES ==========
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ========== KHÓA HỌC ROUTES (PUBLIC) ==========
Route::prefix('courses')->group(function () {
    Route::get('/', [KhoaHocController::class, 'index']);           // Lấy tất cả khóa học active
    Route::get('/all', [KhoaHocController::class, 'getAll']);       // Lấy tất cả khóa học (bao gồm inactive)
    Route::get('/pro', [KhoaHocController::class, 'getProCourses']);    // Khóa học Pro
    Route::get('/free', [KhoaHocController::class, 'getFreeCourses']);  // Khóa học miễn phí
    Route::get('/popular', [KhoaHocController::class, 'getPopular']);   // Khóa học phổ biến
    Route::get('/latest', [KhoaHocController::class, 'getLatest']);     // Khóa học mới nhất
    Route::get('/search', [KhoaHocController::class, 'search']);        // Tìm kiếm
    Route::get('/{id}', [KhoaHocController::class, 'show']);            // Chi tiết khóa học
    Route::get('/{id}/lessons', [KhoaHocController::class, 'getLessons']); // Lấy bài học của khóa học
});

// ========== BÀI HỌC ROUTES (PUBLIC) ==========
Route::prefix('lessons')->group(function () {
    Route::get('/{id}', [BaiHocController::class, 'show']);             // Chi tiết bài học
    Route::get('/{id}/content', [BaiHocController::class, 'getContent']); // Nội dung chi tiết bài học
});
