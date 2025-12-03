<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BaiKiemTra_CauHoi extends Pivot
{
    protected $table = 'BaiKiemTra_CauHoi';
    
    public $timestamps = false;

    protected $fillable = [
        'BaiKiemTraId',
        'CauHoiId',
        'ThuTu',
        'Diem'
    ];

    protected $casts = [
        'Diem' => 'float'
    ];
}
