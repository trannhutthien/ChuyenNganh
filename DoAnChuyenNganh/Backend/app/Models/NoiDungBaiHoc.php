<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoiDungBaiHoc extends Model
{
    use HasFactory;

    protected $table = 'NoiDungBaiHoc';
    protected $primaryKey = 'NoiDungId';

    // Map timestamps sang tên cột tiếng Việt
    const CREATED_AT = 'TaoLuc';
    const UPDATED_AT = 'CapNhatLuc';

    protected $fillable = [
        'BaiHocId',
        'LoaiNoiDung',
        'NoiDung',
        'ThuTu'
    ];

    /**
     * Các loại nội dung hỗ trợ
     */
    const TYPE_HEADING = 'heading';
    const TYPE_SUBHEADING = 'subheading';
    const TYPE_PARAGRAPH = 'paragraph';
    const TYPE_IMAGE = 'image';
    const TYPE_VIDEO = 'video';
    const TYPE_QUOTE = 'quote';
    const TYPE_LIST = 'list';

    /**
     * Quan hệ với BaiHoc (nội dung thuộc về bài học)
     */
    public function baiHoc()
    {
        return $this->belongsTo(BaiHoc::class, 'BaiHocId', 'BaiHocId');
    }

    /**
     * Scope: Sắp xếp theo thứ tự
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('ThuTu', 'asc');
    }

    /**
     * Scope: Lọc theo loại nội dung
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('LoaiNoiDung', $type);
    }
}
