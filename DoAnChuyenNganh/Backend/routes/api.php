<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\KhoaHocController;
use App\Http\Controllers\BaiHocController;
use App\Http\Controllers\NganHangCauHoiController;
use App\Http\Controllers\BaiKiemTraController;
use App\Http\Controllers\LanLamBaiController;
use App\Http\Controllers\BaiKiemTraCuoiKhoaController;
use App\Http\Controllers\NoiDungBaiHocController;
use App\Http\Controllers\NguoiDungController;

// ========== AUTH ROUTES ==========
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/google', [GoogleAuthController::class, 'handleGoogleLogin']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ========== KHÓA HỌC ROUTES (PUBLIC) ==========
// Route với prefix 'khoa-hoc' (tiếng Việt - cho frontend hiện tại)
Route::prefix('khoa-hoc')->group(function () {
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

// Route với prefix 'courses' (tiếng Anh - alias)
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
    Route::delete('/{id}', [KhoaHocController::class, 'destroy']);      // Xóa khóa học
    Route::put('/{id}', [KhoaHocController::class, 'update']);          // Cập nhật khóa học
    Route::post('/', [KhoaHocController::class, 'store']);              // Tạo khóa học mới
});

// ========== BÀI HỌC ROUTES (PUBLIC) ==========
Route::prefix('lessons')->group(function () {
    Route::get('/{id}', [BaiHocController::class, 'show']);             // Chi tiết bài học
    Route::get('/{id}/content', [BaiHocController::class, 'getContent']); // Nội dung chi tiết bài học
    Route::post('/', [BaiHocController::class, 'store']);               // Tạo bài học mới
    Route::put('/{id}', [BaiHocController::class, 'update']);           // Cập nhật bài học
    Route::delete('/{id}', [BaiHocController::class, 'destroy']);       // Xóa bài học
    
    // Nội dung bài học (lesson contents)
    Route::get('/{baiHocId}/contents', [NoiDungBaiHocController::class, 'index']);       // Lấy danh sách nội dung
    Route::post('/{baiHocId}/contents', [NoiDungBaiHocController::class, 'store']);      // Thêm nội dung mới
    Route::put('/{baiHocId}/contents/order', [NoiDungBaiHocController::class, 'updateOrder']); // Cập nhật thứ tự
});

// ========== NỘI DUNG BÀI HỌC ROUTES ==========
Route::prefix('lesson-contents')->group(function () {
    Route::get('/{id}', [NoiDungBaiHocController::class, 'show']);       // Chi tiết nội dung
    Route::put('/{id}', [NoiDungBaiHocController::class, 'update']);     // Cập nhật nội dung
    Route::delete('/{id}', [NoiDungBaiHocController::class, 'destroy']); // Xóa nội dung
});

// ========== NGÂN HÀNG CÂU HỎI ROUTES ==========
Route::prefix('ngan-hang-cau-hoi')->group(function () {
    // Public routes
    Route::get('/', [NganHangCauHoiController::class, 'index']);                    // Lấy tất cả ngân hàng
    Route::get('/khoa-hoc/{khoaHocId}', [NganHangCauHoiController::class, 'getByKhoaHoc']); // Lấy theo khóa học
    Route::get('/{id}', [NganHangCauHoiController::class, 'show']);                 // Chi tiết ngân hàng
    Route::get('/{id}/cau-hoi', [NganHangCauHoiController::class, 'getCauHois']);   // Lấy câu hỏi trong ngân hàng
    Route::get('/{id}/random', [NganHangCauHoiController::class, 'getRandomCauHois']); // Lấy câu hỏi ngẫu nhiên
});

// ========== NGÂN HÀNG CÂU HỎI ROUTES (PROTECTED) ==========
Route::middleware('auth:sanctum')->prefix('ngan-hang-cau-hoi')->group(function () {
    // CRUD ngân hàng câu hỏi
    Route::post('/', [NganHangCauHoiController::class, 'store']);                   // Tạo ngân hàng
    Route::put('/{id}', [NganHangCauHoiController::class, 'update']);               // Cập nhật ngân hàng
    Route::delete('/{id}', [NganHangCauHoiController::class, 'destroy']);           // Xóa ngân hàng
    
    // CRUD câu hỏi trong ngân hàng
    Route::post('/{nganHangId}/cau-hoi', [NganHangCauHoiController::class, 'storeCauHoi']);           // Thêm câu hỏi
    Route::put('/{nganHangId}/cau-hoi/{cauHoiId}', [NganHangCauHoiController::class, 'updateCauHoi']); // Cập nhật câu hỏi
    Route::delete('/{nganHangId}/cau-hoi/{cauHoiId}', [NganHangCauHoiController::class, 'destroyCauHoi']); // Xóa câu hỏi
});

// ========== BÀI KIỂM TRA ROUTES (PUBLIC) ==========
Route::prefix('bai-kiem-tra')->group(function () {
    Route::get('/', [BaiKiemTraController::class, 'index']);                        // Danh sách bài kiểm tra
    Route::get('/khoa-hoc/{khoaHocId}', [BaiKiemTraController::class, 'getByKhoaHoc']); // Bài kiểm tra theo khóa học
    Route::get('/bai-hoc/{baiHocId}', [BaiKiemTraController::class, 'getByBaiHoc']);    // Bài kiểm tra theo bài học
    Route::get('/{id}', [BaiKiemTraController::class, 'show']);                     // Chi tiết bài kiểm tra
    Route::get('/{id}/cau-hoi', [BaiKiemTraController::class, 'getCauHois']);       // Lấy câu hỏi của bài kiểm tra
});

// ========== BÀI KIỂM TRA ROUTES (PROTECTED) ==========
Route::middleware('auth:sanctum')->prefix('bai-kiem-tra')->group(function () {
    // CRUD bài kiểm tra (Admin/Editor)
    Route::post('/', [BaiKiemTraController::class, 'store']);                       // Tạo bài kiểm tra
    Route::put('/{id}', [BaiKiemTraController::class, 'update']);                   // Cập nhật bài kiểm tra
    Route::delete('/{id}', [BaiKiemTraController::class, 'destroy']);               // Xóa bài kiểm tra
    Route::post('/{id}/cau-hoi', [BaiKiemTraController::class, 'addCauHoi']);       // Thêm câu hỏi vào bài kiểm tra
    Route::delete('/{id}/cau-hoi/{cauHoiId}', [BaiKiemTraController::class, 'removeCauHoi']); // Xóa câu hỏi khỏi bài kiểm tra
});

// ========== LÀM BÀI KIỂM TRA ROUTES (PROTECTED) ==========
Route::middleware('auth:sanctum')->prefix('lam-bai')->group(function () {
    Route::post('/bat-dau', [LanLamBaiController::class, 'batDauLamBai']);          // Bắt đầu làm bài
    Route::post('/{lanLamBaiId}/tra-loi', [LanLamBaiController::class, 'luuTraLoi']); // Lưu câu trả lời
    Route::post('/{lanLamBaiId}/nop-bai', [LanLamBaiController::class, 'nopBai']);   // Nộp bài
    Route::get('/{lanLamBaiId}/ket-qua', [LanLamBaiController::class, 'xemKetQua']); // Xem kết quả
    Route::get('/lich-su', [LanLamBaiController::class, 'lichSuLamBai']);           // Lịch sử làm bài
    Route::get('/lich-su/{baiKiemTraId}', [LanLamBaiController::class, 'lichSuTheoBaiKiemTra']); // Lịch sử theo bài kiểm tra
    Route::get('/dang-lam/{baiKiemTraId}', [LanLamBaiController::class, 'getLanLamBaiDangLam']); // Lấy bài đang làm
});

// ========== BÀI KIỂM TRA CUỐI KHÓA ROUTES ==========
Route::prefix('khoa-hoc/{khoaHocId}/kiem-tra-cuoi-khoa')->group(function () {
    Route::get('/', [BaiKiemTraCuoiKhoaController::class, 'getBaiKiemTraCuoiKhoa']); // Lấy thông tin bài kiểm tra cuối khóa
});

Route::middleware('auth:sanctum')->prefix('kiem-tra-cuoi-khoa')->group(function () {
    Route::post('/{baiKiemTraId}/bat-dau', [BaiKiemTraCuoiKhoaController::class, 'batDauLamBai']);    // Bắt đầu làm bài
    Route::post('/{lanLamBaiId}/tra-loi', [BaiKiemTraCuoiKhoaController::class, 'luuTraLoi']);       // Lưu câu trả lời
    Route::post('/{lanLamBaiId}/nop-bai', [BaiKiemTraCuoiKhoaController::class, 'nopBai']);          // Nộp bài
    Route::get('/{lanLamBaiId}/ket-qua', [BaiKiemTraCuoiKhoaController::class, 'xemKetQua']);        // Xem kết quả
});

// ========== QUẢN LÝ NGƯỜI DÙNG ROUTES (ADMIN ONLY) ==========
Route::middleware('auth:sanctum')->prefix('users')->group(function () {
    Route::get('/', [NguoiDungController::class, 'index']);         // Danh sách người dùng
    Route::get('/{id}', [NguoiDungController::class, 'show']);      // Chi tiết người dùng
    Route::post('/', [NguoiDungController::class, 'store']);        // Tạo người dùng
    Route::put('/{id}', [NguoiDungController::class, 'update']);    // Cập nhật người dùng
    Route::delete('/{id}', [NguoiDungController::class, 'destroy']); // Xóa người dùng
});
