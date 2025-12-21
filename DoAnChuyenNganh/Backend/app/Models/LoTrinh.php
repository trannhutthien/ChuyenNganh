<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LoTrinh extends Model
{
    use HasFactory;

    protected $table = 'LoTrinh';
    protected $primaryKey = 'LoTrinhId';

    const CREATED_AT = 'TaoLuc';
    const UPDATED_AT = 'CapNhatLuc';

    protected $fillable = [
        'TieuDe',
        'Slug',
        'MoTa',
        'HinhAnhUrl',
        'Icon',
        'ThoiGianHoanThanh',
        'CapDo',
        'TrangThai',
        'ThuTu'
    ];

    /**
     * Boot method để tự động tạo slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loTrinh) {
            if (empty($loTrinh->Slug)) {
                $loTrinh->Slug = Str::slug($loTrinh->TieuDe);
            }
        });
    }

    /**
     * Quan hệ với KhoaHoc qua bảng trung gian LoTrinh_KhoaHoc
     */
    public function khoaHocs()
    {
        return $this->belongsToMany(
            KhoaHoc::class,
            'LoTrinh_KhoaHoc',
            'LoTrinhId',
            'KhoaHocId'
        )->withPivot('ThuTu', 'BatBuoc', 'GhiChu')
         ->orderBy('LoTrinh_KhoaHoc.ThuTu', 'asc');
    }

    /**
     * Đếm số khóa học trong lộ trình
     */
    public function getSoKhoaHocAttribute()
    {
        return $this->khoaHocs()->count();
    }

    /**
     * Đếm số khóa học bắt buộc
     */
    public function getSoKhoaHocBatBuocAttribute()
    {
        return $this->khoaHocs()->wherePivot('BatBuoc', 1)->count();
    }

    /**
     * Lấy text cấp độ
     */
    public function getCapDoTextAttribute()
    {
        $levels = [
            1 => 'Beginner',
            2 => 'Intermediate', 
            3 => 'Advanced'
        ];
        return $levels[$this->CapDo] ?? 'Beginner';
    }

    /**
     * Scope: Lọc lộ trình active
     */
    public function scopeActive($query)
    {
        return $query->where('TrangThai', 1);
    }

    /**
     * Scope: Sắp xếp theo thứ tự
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ThuTu', 'asc');
    }
}
