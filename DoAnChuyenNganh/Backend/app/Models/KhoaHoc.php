<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhoaHoc extends Model
{
    use HasFactory;

    protected $table = 'KhoaHoc';
    protected $primaryKey = 'KhoaHocId';

    // Map timestamps sang tên cột tiếng Việt
    const CREATED_AT = 'TaoLuc';
    const UPDATED_AT = 'CapNhatLuc';

    protected $fillable = [
        'TieuDe',
        'TomTat',
        'HinhAnhUrl',
        'CapDo',
        'Tags',
        'DieuKienTienQuyet',
        'GiaTien',
        'TrangThai'
    ];

    /**
     * Quan hệ với NguoiDung (người tạo khóa học)
     */
    public function nguoiTao()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiTaoId', 'NguoiDungId');
    }

    /**
     * Quan hệ với BaiHoc (một khóa học có nhiều bài học)
     */
    public function baiHocs()
    {
        return $this->hasMany(BaiHoc::class, 'KhoaHocId', 'KhoaHocId');
    }

    /**
     * Quan hệ với NguoiDung qua bảng trung gian DangKyKhoaHoc
     * (Danh sách học viên đăng ký khóa học)
     */
    public function hocViens()
    {
        return $this->belongsToMany(
            NguoiDung::class, 
            'DangKyKhoaHoc', 
            'KhoaHocId', 
            'NguoiDungId'
        )->withPivot('NgayDangKy', 'TrangThai', 'TienDo');
    }

    /**
     * Đếm số học viên đăng ký
     */
    public function getSoHocVienAttribute()
    {
        return $this->hocViens()->count();
    }

    /**
     * Đếm số bài học
     */
    public function getSoBaiHocAttribute()
    {
        return $this->baiHocs()->count();
    }

    /**
     * Scope: Lọc khóa học đang hoạt động (TrangThai = 1 hoặc 2)
     * TrangThai: 1 = active, 2 = đã xuất bản/published
     */
    public function scopeActive($query)
    {
        return $query->whereIn('TrangThai', [1, 2]);
    }

    /**
     * Scope: Lọc khóa học chờ duyệt (TrangThai = 0)
     */
    public function scopePending($query)
    {
        return $query->where('TrangThai', 0);
    }

    /**
     * Scope: Lọc khóa học nháp (TrangThai = -1)
     */
    public function scopeDraft($query)
    {
        return $query->where('TrangThai', -1);
    }
}
