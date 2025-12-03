<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TraLoi extends Model
{
    use HasFactory;

    protected $table = 'TraLoi';
    protected $primaryKey = 'TraLoiId';
    
    public $timestamps = false;

    protected $fillable = [
        'LanLamBaiId',
        'CauHoiId',
        'LuaChonIds',
        'DungHaySai',
        'ThoiGianGiay'
    ];

    protected $casts = [
        'LuaChonIds' => 'array',
        'DungHaySai' => 'boolean'
    ];

    /**
     * Quan hệ với LanLamBai
     */
    public function lanLamBai()
    {
        return $this->belongsTo(LanLamBai::class, 'LanLamBaiId', 'LanLamBaiId');
    }

    /**
     * Quan hệ với CauHoi
     */
    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'CauHoiId', 'CauHoiId');
    }

    /**
     * Lấy danh sách ID lựa chọn đã chọn
     */
    public function getLuaChonIdsArrayAttribute()
    {
        $ids = $this->LuaChonIds;
        if (empty($ids)) {
            return [];
        }
        if (is_array($ids)) {
            return $ids;
        }
        return json_decode($ids, true) ?? [];
    }

    /**
     * Kiểm tra và chấm điểm câu trả lời
     */
    public function chamDiem()
    {
        $cauHoi = $this->cauHoi()->with('luaChons')->first();
        
        if (!$cauHoi) {
            return false;
        }

        $luaChonDung = $cauHoi->luaChons
            ->where('DungHaySai', true)
            ->pluck('LuaChonId')
            ->toArray();

        $luaChonDaChon = $this->luaChonIdsArray;

        // Kiểm tra đáp án
        $laDung = false;
        
        switch ($cauHoi->Loai) {
            case CauHoi::LOAI_MOT_DAP_AN:
            case CauHoi::LOAI_DUNG_SAI:
                // Chỉ cần chọn đúng 1 đáp án
                $laDung = count($luaChonDaChon) === 1 
                    && in_array($luaChonDaChon[0], $luaChonDung);
                break;
                
            case CauHoi::LOAI_NHIEU_DAP_AN:
                // Phải chọn đủ và đúng tất cả đáp án
                sort($luaChonDung);
                sort($luaChonDaChon);
                $laDung = $luaChonDung === $luaChonDaChon;
                break;
                
            case CauHoi::LOAI_DIEN_KHUYET:
                // So sánh nội dung trả lời với đáp án trong LuaChon
                $dapAnDung = $cauHoi->luaChons->first()?->NoiDung;
                // LuaChonIds chứa text trả lời cho dạng điền khuyết
                $noiDungTraLoi = is_array($this->LuaChonIds) ? ($this->LuaChonIds[0] ?? '') : '';
                $laDung = strtolower(trim($noiDungTraLoi)) 
                    === strtolower(trim($dapAnDung ?? ''));
                break;
        }

        $this->update([
            'DungHaySai' => $laDung
        ]);

        return $laDung;
    }

    /**
     * Scope: Câu trả lời đúng
     */
    public function scopeDung($query)
    {
        return $query->where('DungHaySai', true);
    }

    /**
     * Scope: Câu trả lời sai
     */
    public function scopeSai($query)
    {
        return $query->where('DungHaySai', false);
    }
}
