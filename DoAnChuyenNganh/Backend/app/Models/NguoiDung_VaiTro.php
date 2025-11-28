<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NguoiDung_VaiTro extends Model
{
    protected $table = 'NguoiDung_VaiTro';
    
    // Không cần timestamps nếu bảng pivot không có created_at, updated_at
    public $timestamps = false;
    
    protected $fillable = [
        'NguoiDungId',
        'VaiTroId'
    ];

    // Quan hệ ngược lại với NguoiDung
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiDungId', 'NguoiDungId');
    }

    // Quan hệ ngược lại với VaiTro
    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'VaiTroId', 'VaiTroId');
    }
}
