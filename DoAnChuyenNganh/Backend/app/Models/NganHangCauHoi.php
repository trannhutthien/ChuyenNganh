<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NganHangCauHoi extends Model
{
    use HasFactory;

    protected $table = 'NganHangCauHoi';
    protected $primaryKey = 'NganHangId';
    
    // Không có timestamps trong bảng này
    public $timestamps = false;

    protected $fillable = [
        'KhoaHocId',
        'TenNganHang',
        'MoTa',
        'CapDoMacDinh'
    ];

    /**
     * Quan hệ với KhoaHoc (ngân hàng thuộc về khóa học)
     */
    public function khoaHoc()
    {
        return $this->belongsTo(KhoaHoc::class, 'KhoaHocId', 'KhoaHocId');
    }

    /**
     * Quan hệ với CauHoi (ngân hàng có nhiều câu hỏi)
     */
    public function cauHois()
    {
        return $this->hasMany(CauHoi::class, 'NganHangId', 'NganHangId')
            ->orderBy('ThuTu', 'asc');
    }

    /**
     * Đếm số câu hỏi trong ngân hàng
     */
    public function getSoCauHoiAttribute()
    {
        return $this->cauHois()->count();
    }
}
