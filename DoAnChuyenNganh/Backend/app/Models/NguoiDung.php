<?php

namespace App\Models;

// giúp tạo db ảo để test khi chưa kết nối đến db thật
use Illuminate\Database\Eloquent\Factories\HasFactory;
//giống như class gốc của laravel để hổ trợ toàn bộ chức đăng đăng nhập, sau as là đổi tên lại cho ngắn gọn
use Illuminate\Foundation\Auth\User as Authenticatable;
// gửi thông báo qua email, sms, database
use Illuminate\Notifications\Notifiable;
//thư viện để tạo ,quản lý... token
use Laravel\Sanctum\HasApiTokens;
//nạp file VaiTra vào để module NguoiDung thấy
use App\Models\VaiTro;

class NguoiDung extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'NguoiDung';
    protected $primaryKey = 'NguoiDungId';

    // Map timestamps sang tên cột tiếng Việt
    const CREATED_AT = 'TaoLuc';
    const UPDATED_AT = 'CapNhatLuc';

    protected $fillable = [
        'Email',
        'MatKhauHash',
        'HoTen',
        'TrangThai',
        'AvatarUrl',
        'GoogleId'
    ];

 
    protected $hidden = [
        'MatKhauHash'
    ];

    // Quan hệ tới bảng vai trò
    public function vaiTros()
    {
        return $this->belongsToMany(VaiTro::class, 'NguoiDung_VaiTro', 'NguoiDungId', 'VaiTroId');
    }

}
