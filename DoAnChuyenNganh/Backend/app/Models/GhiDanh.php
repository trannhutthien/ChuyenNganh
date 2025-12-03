<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GhiDanh extends Model
{
    use HasFactory;

    protected $table = 'GhiDanh';
    
    // Composite primary key
    protected $primaryKey = ['NguoiDungId', 'KhoaHocId'];
    public $incrementing = false;
    
    public $timestamps = false;

    protected $fillable = [
        'NguoiDungId',
        'KhoaHocId',
        'PhanTramHoanThanh',
        'BaiHocCuoiCungId',
        'CapNhatLuc'
    ];

    protected $casts = [
        'PhanTramHoanThanh' => 'float',
        'CapNhatLuc' => 'datetime'
    ];

    /**
     * Override để xử lý composite primary key
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('NguoiDungId', $this->getAttribute('NguoiDungId'))
              ->where('KhoaHocId', $this->getAttribute('KhoaHocId'));
        return $query;
    }

    /**
     * Quan hệ với NguoiDung
     */
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiDungId', 'NguoiDungId');
    }

    /**
     * Quan hệ với KhoaHoc
     */
    public function khoaHoc()
    {
        return $this->belongsTo(KhoaHoc::class, 'KhoaHocId', 'KhoaHocId');
    }

    /**
     * Quan hệ với BaiHoc cuối cùng
     */
    public function baiHocCuoiCung()
    {
        return $this->belongsTo(BaiHoc::class, 'BaiHocCuoiCungId', 'BaiHocId');
    }

    /**
     * Kiểm tra đã hoàn thành khóa học chưa (100%)
     */
    public function daHoanThanh()
    {
        return $this->PhanTramHoanThanh >= 100;
    }

    /**
     * Kiểm tra đã hoàn thành tất cả bài học chưa
     */
    public function daHoanThanhTatCaBaiHoc()
    {
        // Lấy tổng số bài học của khóa học
        $tongSoBaiHoc = BaiHoc::where('KhoaHocId', $this->KhoaHocId)->count();
        
        if ($tongSoBaiHoc === 0) {
            return true;
        }
        
        // Tính phần trăm cần đạt được để coi là hoàn thành tất cả bài học
        // Nếu PhanTramHoanThanh >= 100% thì đã hoàn thành
        return $this->PhanTramHoanThanh >= 100;
    }

    /**
     * Scope: Lọc theo người dùng
     */
    public function scopeCuaNguoiDung($query, $nguoiDungId)
    {
        return $query->where('NguoiDungId', $nguoiDungId);
    }

    /**
     * Scope: Lọc theo khóa học
     */
    public function scopeThuocKhoaHoc($query, $khoaHocId)
    {
        return $query->where('KhoaHocId', $khoaHocId);
    }

    /**
     * Scope: Đã hoàn thành
     */
    public function scopeDaHoanThanh($query)
    {
        return $query->where('PhanTramHoanThanh', '>=', 100);
    }
}
