<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaiKiemTra extends Model
{
    use HasFactory;

    protected $table = 'BaiKiemTra';
    protected $primaryKey = 'BaiKiemTraId';
    
    public $timestamps = false;

    protected $fillable = [
        'KhoaHocId',
        'BaiHocId',
        'TieuDe',
        'ThietLapJson',
        'DiemDat',
        'TrangThai',
        'TaoLuc',
        'CapNhatLuc'
    ];

    protected $casts = [
        'ThietLapJson' => 'array',
        'TaoLuc' => 'datetime',
        'CapNhatLuc' => 'datetime'
    ];

    /**
     * Trạng thái bài kiểm tra
     */
    const TRANG_THAI_NHAP = 1;
    const TRANG_THAI_CONG_KHAI = 2;
    const TRANG_THAI_DONG = 3;

    /**
     * Quan hệ với KhoaHoc
     */
    public function khoaHoc()
    {
        return $this->belongsTo(KhoaHoc::class, 'KhoaHocId', 'KhoaHocId');
    }

    /**
     * Quan hệ với BaiHoc (bài kiểm tra có thể thuộc bài học cụ thể)
     */
    public function baiHoc()
    {
        return $this->belongsTo(BaiHoc::class, 'BaiHocId', 'BaiHocId');
    }

    /**
     * Quan hệ với LanLamBai (bài kiểm tra có nhiều lần làm bài)
     */
    public function lanLamBais()
    {
        return $this->hasMany(LanLamBai::class, 'BaiKiemTraId', 'BaiKiemTraId');
    }

    // Không dùng quan hệ cauHois() vì câu hỏi được random từ ngân hàng
    // thay vì gán cố định vào bài kiểm tra

    /**
     * Scope: Bài kiểm tra công khai
     */
    public function scopeCongKhai($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_CONG_KHAI);
    }

    /**
     * Scope: Bài kiểm tra cuối khóa (BaiHocId = NULL)
     */
    public function scopeCuoiKhoa($query)
    {
        return $query->whereNull('BaiHocId');
    }

    /**
     * Scope: Bài kiểm tra theo khóa học
     */
    public function scopeThuocKhoaHoc($query, $khoaHocId)
    {
        return $query->where('KhoaHocId', $khoaHocId);
    }

    /**
     * Scope: Bài kiểm tra theo bài học
     */
    public function scopeThuocBaiHoc($query, $baiHocId)
    {
        return $query->where('BaiHocId', $baiHocId);
    }

    /**
     * Kiểm tra đây có phải bài kiểm tra cuối khóa không
     */
    public function laBaiKiemTraCuoiKhoa()
    {
        return $this->BaiHocId === null && $this->KhoaHocId !== null;
    }

    /**
     * Lấy cấu hình từ ThietLapJson
     */
    public function getThietLap($key = null, $default = null)
    {
        $thietLap = $this->ThietLapJson ?? [];
        
        if ($key === null) {
            return $thietLap;
        }
        
        return $thietLap[$key] ?? $default;
    }

    /**
     * Kiểm tra người dùng còn lượt làm bài không
     */
    public function conLuotLamBai($nguoiDungId)
    {
        $soLanLamToiDa = $this->getThietLap('soLanLamToiDa', 0);
        
        if ($soLanLamToiDa === 0) {
            return true; // Không giới hạn
        }

        $soLanDaLam = $this->lanLamBais()
            ->where('NguoiDungId', $nguoiDungId)
            ->count();

        return $soLanDaLam < $soLanLamToiDa;
    }

    /**
     * Lấy điểm cao nhất của người dùng
     */
    public function diemCaoNhat($nguoiDungId)
    {
        return $this->lanLamBais()
            ->where('NguoiDungId', $nguoiDungId)
            ->where('TrangThai', 'SUBMITTED')
            ->max('DiemSo');
    }

    /**
     * Đếm số lần đã làm
     */
    public function soLanDaLam($nguoiDungId)
    {
        return $this->lanLamBais()
            ->where('NguoiDungId', $nguoiDungId)
            ->count();
    }
}
