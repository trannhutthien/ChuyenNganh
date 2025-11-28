<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiHoc extends Model
{
    use HasFactory;

    protected $table = 'BaiHoc';
    protected $primaryKey = 'BaiHocId';

    // Map timestamps sang tên cột tiếng Việt
    const CREATED_AT = 'TaoLuc';
    const UPDATED_AT = 'CapNhatLuc';

    protected $fillable = [
        'KhoaHocId',
        'TieuDe',
        'MoTa',
        'NoiDung',
        'LoaiBaiHoc',
        'ThuTu',
        'ThoiLuong',
        'VideoUrl',
        'TrangThai'
    ];

    /**
     * Quan hệ với KhoaHoc (bài học thuộc về khóa học)
     */
    public function khoaHoc()
    {
        return $this->belongsTo(KhoaHoc::class, 'KhoaHocId', 'KhoaHocId');
    }

    /**
     * Quan hệ với tiến độ học của người dùng
     */
    public function tienDoHocs()
    {
        return $this->hasMany(TienDoHoc::class, 'BaiHocId', 'BaiHocId');
    }

    /**
     * Kiểm tra người dùng đã hoàn thành bài học chưa
     */
    public function daHoanThanh($nguoiDungId)
    {
        return $this->tienDoHocs()
            ->where('NguoiDungId', $nguoiDungId)
            ->where('TrangThai', 'COMPLETED')
            ->exists();
    }

    /**
     * Scope: Lọc bài học theo loại VIDEO
     */
    public function scopeVideo($query)
    {
        return $query->where('LoaiBaiHoc', 'VIDEO');
    }

    /**
     * Scope: Lọc bài học theo loại TEXT
     */
    public function scopeText($query)
    {
        return $query->where('LoaiBaiHoc', 'TEXT');
    }

    /**
     * Scope: Lọc bài học theo loại QUIZ
     */
    public function scopeQuiz($query)
    {
        return $query->where('LoaiBaiHoc', 'QUIZ');
    }

    /**
     * Scope: Sắp xếp theo thứ tự
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ThuTu', 'asc');
    }

    /**
     * Scope: Lọc bài học đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('TrangThai', 'ACTIVE');
    }
}
