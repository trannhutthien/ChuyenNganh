<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanLamBai extends Model
{
    use HasFactory;

    protected $table = 'LanLamBai';
    protected $primaryKey = 'LanLamBaiId';
    
    public $timestamps = false;

    protected $fillable = [
        'BaiKiemTraId',
        'NguoiDungId',
        'BatDauLuc',
        'NopBaiLuc',
        'DiemSo',
        'ChiTietJson',
        'TrangThai'
    ];

    protected $casts = [
        'BatDauLuc' => 'datetime',
        'NopBaiLuc' => 'datetime',
        'DiemSo' => 'float',
        'ChiTietJson' => 'array'
    ];

    /**
     * Trạng thái lần làm bài
     */
    const TRANG_THAI_DANG_LAM = 'INPROGRESS';
    const TRANG_THAI_HOAN_THANH = 'SUBMITTED';
    const TRANG_THAI_HUY = 'CANCELLED';

    /**
     * Quan hệ với BaiKiemTra
     */
    public function baiKiemTra()
    {
        return $this->belongsTo(BaiKiemTra::class, 'BaiKiemTraId', 'BaiKiemTraId');
    }

    /**
     * Quan hệ với NguoiDung
     */
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiDungId', 'NguoiDungId');
    }

    /**
     * Quan hệ với TraLoi (lần làm bài có nhiều câu trả lời)
     */
    public function traLois()
    {
        return $this->hasMany(TraLoi::class, 'LanLamBaiId', 'LanLamBaiId');
    }

    /**
     * Scope: Lần làm bài đang làm
     */
    public function scopeDangLam($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DANG_LAM);
    }

    /**
     * Scope: Lần làm bài đã hoàn thành
     */
    public function scopeHoanThanh($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_HOAN_THANH);
    }

    /**
     * Scope: Lần làm bài của người dùng
     */
    public function scopeCuaNguoiDung($query, $nguoiDungId)
    {
        return $query->where('NguoiDungId', $nguoiDungId);
    }

    /**
     * Kiểm tra bài làm đã hết giờ chưa
     */
    public function daHetGio()
    {
        if ($this->TrangThai !== self::TRANG_THAI_DANG_LAM) {
            return false;
        }

        $thoiGianLamBai = $this->baiKiemTra->getThietLap('thoiGianLamBai', 0); // phút
        if (!$thoiGianLamBai) {
            return false; // Không giới hạn thời gian
        }

        $thoiGianConLai = $this->BatDauLuc->addMinutes($thoiGianLamBai);
        return now()->gt($thoiGianConLai);
    }

    /**
     * Tính thời gian còn lại (giây)
     */
    public function thoiGianConLai()
    {
        if ($this->TrangThai !== self::TRANG_THAI_DANG_LAM) {
            return 0;
        }

        $thoiGianLamBai = $this->baiKiemTra->getThietLap('thoiGianLamBai', 0);
        if (!$thoiGianLamBai) {
            return null; // Không giới hạn
        }

        $thoiGianKetThuc = $this->BatDauLuc->addMinutes($thoiGianLamBai);
        $conLai = now()->diffInSeconds($thoiGianKetThuc, false);
        
        return max(0, $conLai);
    }

    /**
     * Lấy chi tiết từ ChiTietJson
     */
    public function getChiTiet($key = null, $default = null)
    {
        $chiTiet = $this->ChiTietJson ?? [];
        
        if ($key === null) {
            return $chiTiet;
        }
        
        return $chiTiet[$key] ?? $default;
    }

    /**
     * Cập nhật chi tiết
     */
    public function setChiTiet($key, $value)
    {
        $chiTiet = $this->ChiTietJson ?? [];
        $chiTiet[$key] = $value;
        $this->ChiTietJson = $chiTiet;
        return $this;
    }

    /**
     * Tính điểm dựa trên số câu đúng
     * Điểm = (soCauDung / tongSoCau) * 10
     */
    public function tinhDiem()
    {
        // Lấy tất cả câu trả lời của lần làm bài này
        $traLois = $this->traLois;
        
        // Đếm số câu đúng (DungHaySai = true/1)
        $soCauDung = $traLois->where('DungHaySai', true)->count();
        
        // Lấy tổng số câu từ ChiTietJson
        $chiTiet = $this->ChiTietJson ?? [];
        $tongSoCau = $chiTiet['soCauHoi'] ?? $traLois->count();
        
        // Nếu tongSoCau = 0, lấy từ số câu trả lời
        if ($tongSoCau == 0) {
            $tongSoCau = $traLois->count();
        }
        
        // Tính điểm: (soCauDung / tongSoCau) * 10, làm tròn 2 chữ số
        $diem = $tongSoCau > 0 ? round(($soCauDung / $tongSoCau) * 10, 2) : 0;
        
        return [
            'diem' => $diem,
            'soCauDung' => $soCauDung,
            'tongSoCau' => $tongSoCau
        ];
    }
}
