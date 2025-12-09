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
        'TraLoiText',  // Thêm cột mới cho dạng điền khuyết
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
            \Log::warning("chamDiem: Không tìm thấy câu hỏi cho TraLoiId={$this->TraLoiId}");
            return false;
        }

        // Lấy danh sách ID lựa chọn đúng (DungHaySai = true)
        $luaChonDung = $cauHoi->luaChons
            ->filter(fn($lc) => $lc->DungHaySai == true || $lc->DungHaySai == 1)
            ->pluck('LuaChonId')
            ->map(fn($id) => (int) $id)  // Đảm bảo là integer
            ->toArray();

        // Lấy danh sách ID lựa chọn đã chọn và chuyển thành integer
        $luaChonDaChon = collect($this->luaChonIdsArray)
            ->map(fn($id) => (int) $id)
            ->toArray();

        \Log::info("chamDiem: CauHoiId={$cauHoi->CauHoiId}, Loai={$cauHoi->Loai}");
        \Log::info("chamDiem: LuaChonDung=" . json_encode($luaChonDung));
        \Log::info("chamDiem: LuaChonDaChon=" . json_encode($luaChonDaChon));

        // Kiểm tra đáp án
        $laDung = false;
        
        switch ($cauHoi->Loai) {
            case CauHoi::LOAI_MOT_DAP_AN:  // 'single'
            case CauHoi::LOAI_DUNG_SAI:    // 'true_false'
                // Chỉ cần chọn đúng 1 đáp án
                $laDung = count($luaChonDaChon) === 1 
                    && in_array($luaChonDaChon[0], $luaChonDung);
                \Log::info("chamDiem: single/true_false - count=" . count($luaChonDaChon) . ", in_array=" . (in_array($luaChonDaChon[0] ?? -1, $luaChonDung) ? 'true' : 'false'));
                break;
                
            case CauHoi::LOAI_NHIEU_DAP_AN:  // 'multiple'
                // Phải chọn đủ và đúng tất cả đáp án (không thiếu, không dư)
                sort($luaChonDung);
                sort($luaChonDaChon);
                $laDung = $luaChonDung === $luaChonDaChon;
                \Log::info("chamDiem: multiple - sorted dung=" . json_encode($luaChonDung) . ", sorted chon=" . json_encode($luaChonDaChon));
                break;
                
            default:
                \Log::warning("chamDiem: Loại câu hỏi không xác định: {$cauHoi->Loai}");
        }

        \Log::info("chamDiem: Kết quả = " . ($laDung ? 'ĐÚNG' : 'SAI'));

        $this->update([
            'DungHaySai' => $laDung ? 1 : 0
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
