<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CauHoi extends Model
{
    use HasFactory;

    protected $table = 'CauHoi';
    protected $primaryKey = 'CauHoiId';
    
    // Không có timestamps trong bảng này
    public $timestamps = false;

    protected $fillable = [
        'NganHangId',
        'BaiHocId',
        'Loai',
        'DeBai',
        'GiaiThich',
        'DoKho',
        'ChuDeTags',
        'ThuTu'
    ];

    /**
     * Các loại câu hỏi
     */
    const LOAI_MOT_DAP_AN = 'MOT_DAP_AN';
    const LOAI_NHIEU_DAP_AN = 'NHIEU_DAP_AN';
    const LOAI_DUNG_SAI = 'DUNG_SAI';
    const LOAI_DIEN_KHUYET = 'DIEN_KHUYET';

    /**
     * Quan hệ với NganHangCauHoi (câu hỏi thuộc về ngân hàng)
     */
    public function nganHang()
    {
        return $this->belongsTo(NganHangCauHoi::class, 'NganHangId', 'NganHangId');
    }

    /**
     * Quan hệ với BaiHoc (câu hỏi có thể thuộc bài học cụ thể)
     */
    public function baiHoc()
    {
        return $this->belongsTo(BaiHoc::class, 'BaiHocId', 'BaiHocId');
    }

    /**
     * Quan hệ với LuaChon (câu hỏi có nhiều lựa chọn)
     */
    public function luaChons()
    {
        return $this->hasMany(LuaChon::class, 'CauHoiId', 'CauHoiId')
            ->orderBy('ThuTu', 'asc');
    }

    /**
     * Lấy đáp án đúng
     */
    public function dapAnDung()
    {
        return $this->luaChons()->where('DungHaySai', true);
    }

    /**
     * Scope: Lọc theo độ khó
     */
    public function scopeDoKho($query, $doKho)
    {
        return $query->where('DoKho', $doKho);
    }

    /**
     * Scope: Lọc theo loại câu hỏi
     */
    public function scopeLoai($query, $loai)
    {
        return $query->where('Loai', $loai);
    }

    /**
     * Scope: Câu hỏi thuộc bài học cụ thể
     */
    public function scopeThuocBaiHoc($query, $baiHocId)
    {
        return $query->where('BaiHocId', $baiHocId);
    }

    /**
     * Scope: Câu hỏi chung (không thuộc bài học cụ thể)
     */
    public function scopeCauHoiChung($query)
    {
        return $query->whereNull('BaiHocId');
    }
}
