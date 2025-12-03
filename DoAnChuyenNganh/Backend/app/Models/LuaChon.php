<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LuaChon extends Model
{
    use HasFactory;

    protected $table = 'LuaChon';
    protected $primaryKey = 'LuaChonId';
    
    // Không có timestamps trong bảng này
    public $timestamps = false;

    protected $fillable = [
        'CauHoiId',
        'NoiDung',
        'DungHaySai',
        'ThuTu'
    ];

    protected $casts = [
        'DungHaySai' => 'boolean'
    ];

    /**
     * Quan hệ với CauHoi (lựa chọn thuộc về câu hỏi)
     */
    public function cauHoi()
    {
        return $this->belongsTo(CauHoi::class, 'CauHoiId', 'CauHoiId');
    }

    /**
     * Scope: Lọc đáp án đúng
     */
    public function scopeDapAnDung($query)
    {
        return $query->where('DungHaySai', true);
    }

    /**
     * Scope: Lọc đáp án sai
     */
    public function scopeDapAnSai($query)
    {
        return $query->where('DungHaySai', false);
    }
}
