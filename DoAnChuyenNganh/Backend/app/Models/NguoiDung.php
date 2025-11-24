<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\VaiTro;

class NguoiDung extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'NguoiDung';
    protected $primaryKey = 'NguoiDungId';

    protected $fillable = [
        'Email',
        'MatKhauHash',
        'HoTen',
        'TrangThai'
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
