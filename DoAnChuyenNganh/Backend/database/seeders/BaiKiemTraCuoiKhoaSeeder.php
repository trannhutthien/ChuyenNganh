<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BaiKiemTra;
use App\Models\NganHangCauHoi;
use App\Models\CauHoi;
use App\Models\LuaChon;
use App\Models\KhoaHoc;

class BaiKiemTraCuoiKhoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy khóa học đầu tiên
        $khoaHoc = KhoaHoc::first();
        
        if (!$khoaHoc) {
            $this->command->error('Không có khóa học nào! Hãy tạo khóa học trước.');
            return;
        }

        $khoaHocId = $khoaHoc->KhoaHocId;
        $this->command->info("Tạo dữ liệu cho khóa học: {$khoaHoc->TieuDe} (ID: {$khoaHocId})");

        // 1. Tạo Ngân hàng câu hỏi
        $nganHang = NganHangCauHoi::create([
            'TenNganHang' => 'Ngân hàng câu hỏi cuối khóa - ' . $khoaHoc->TieuDe,
            'MoTa' => 'Ngân hàng câu hỏi cho bài kiểm tra cuối khóa',
            'KhoaHocId' => $khoaHocId,
            'NguoiTaoId' => 1,
            'TrangThai' => 'ACTIVE'
        ]);
        $this->command->info("Đã tạo ngân hàng câu hỏi ID: {$nganHang->NganHangId}");

        // 2. Tạo các câu hỏi
        $danhSachCauHoi = [
            [
                'noiDung' => 'HTML là viết tắt của từ gì?',
                'loai' => 'SINGLE',
                'luaChon' => [
                    ['noiDung' => 'Hyper Text Markup Language', 'laDapAnDung' => true],
                    ['noiDung' => 'High Tech Modern Language', 'laDapAnDung' => false],
                    ['noiDung' => 'Hyper Transfer Markup Language', 'laDapAnDung' => false],
                    ['noiDung' => 'Home Tool Markup Language', 'laDapAnDung' => false],
                ]
            ],
            [
                'noiDung' => 'CSS là viết tắt của từ gì?',
                'loai' => 'SINGLE',
                'luaChon' => [
                    ['noiDung' => 'Cascading Style Sheets', 'laDapAnDung' => true],
                    ['noiDung' => 'Creative Style Sheets', 'laDapAnDung' => false],
                    ['noiDung' => 'Computer Style Sheets', 'laDapAnDung' => false],
                    ['noiDung' => 'Colorful Style Sheets', 'laDapAnDung' => false],
                ]
            ],
            [
                'noiDung' => 'Thẻ HTML nào dùng để tạo tiêu đề lớn nhất?',
                'loai' => 'SINGLE',
                'luaChon' => [
                    ['noiDung' => '<h1>', 'laDapAnDung' => true],
                    ['noiDung' => '<heading>', 'laDapAnDung' => false],
                    ['noiDung' => '<h6>', 'laDapAnDung' => false],
                    ['noiDung' => '<head>', 'laDapAnDung' => false],
                ]
            ],
            [
                'noiDung' => 'Thuộc tính CSS nào dùng để thay đổi màu nền?',
                'loai' => 'SINGLE',
                'luaChon' => [
                    ['noiDung' => 'background-color', 'laDapAnDung' => true],
                    ['noiDung' => 'color', 'laDapAnDung' => false],
                    ['noiDung' => 'bgcolor', 'laDapAnDung' => false],
                    ['noiDung' => 'background', 'laDapAnDung' => false],
                ]
            ],
            [
                'noiDung' => 'HTML và CSS là ngôn ngữ lập trình.',
                'loai' => 'TRUEFALSE',
                'luaChon' => [
                    ['noiDung' => 'Đúng', 'laDapAnDung' => false],
                    ['noiDung' => 'Sai', 'laDapAnDung' => true],
                ]
            ],
            [
                'noiDung' => 'Thẻ nào dùng để tạo danh sách không có thứ tự?',
                'loai' => 'SINGLE',
                'luaChon' => [
                    ['noiDung' => '<ul>', 'laDapAnDung' => true],
                    ['noiDung' => '<ol>', 'laDapAnDung' => false],
                    ['noiDung' => '<li>', 'laDapAnDung' => false],
                    ['noiDung' => '<list>', 'laDapAnDung' => false],
                ]
            ],
            [
                'noiDung' => 'Thuộc tính nào dùng để căn giữa một block element?',
                'loai' => 'SINGLE',
                'luaChon' => [
                    ['noiDung' => 'margin: 0 auto', 'laDapAnDung' => true],
                    ['noiDung' => 'text-align: center', 'laDapAnDung' => false],
                    ['noiDung' => 'align: center', 'laDapAnDung' => false],
                    ['noiDung' => 'center: true', 'laDapAnDung' => false],
                ]
            ],
            [
                'noiDung' => 'Flexbox là một module CSS để tạo layout.',
                'loai' => 'TRUEFALSE',
                'luaChon' => [
                    ['noiDung' => 'Đúng', 'laDapAnDung' => true],
                    ['noiDung' => 'Sai', 'laDapAnDung' => false],
                ]
            ],
            [
                'noiDung' => 'Giá trị mặc định của thuộc tính position là gì?',
                'loai' => 'SINGLE',
                'luaChon' => [
                    ['noiDung' => 'static', 'laDapAnDung' => true],
                    ['noiDung' => 'relative', 'laDapAnDung' => false],
                    ['noiDung' => 'absolute', 'laDapAnDung' => false],
                    ['noiDung' => 'fixed', 'laDapAnDung' => false],
                ]
            ],
            [
                'noiDung' => 'Selector #myId chọn phần tử theo gì?',
                'loai' => 'SINGLE',
                'luaChon' => [
                    ['noiDung' => 'ID', 'laDapAnDung' => true],
                    ['noiDung' => 'Class', 'laDapAnDung' => false],
                    ['noiDung' => 'Tag', 'laDapAnDung' => false],
                    ['noiDung' => 'Attribute', 'laDapAnDung' => false],
                ]
            ],
        ];

        foreach ($danhSachCauHoi as $index => $cauHoiData) {
            $cauHoi = CauHoi::create([
                'NganHangId' => $nganHang->NganHangId,
                'NoiDung' => $cauHoiData['noiDung'],
                'LoaiCauHoi' => $cauHoiData['loai'],
                'DoKho' => 'MEDIUM',
                'DiemMacDinh' => 1,
                'TrangThai' => 'ACTIVE',
                'ThuTu' => $index + 1,
            ]);

            foreach ($cauHoiData['luaChon'] as $lcIndex => $lcData) {
                LuaChon::create([
                    'CauHoiId' => $cauHoi->CauHoiId,
                    'NoiDung' => $lcData['noiDung'],
                    'LaDapAnDung' => $lcData['laDapAnDung'],
                    'ThuTu' => $lcIndex + 1,
                ]);
            }
        }
        $this->command->info("Đã tạo " . count($danhSachCauHoi) . " câu hỏi");

        // 3. Tạo Bài kiểm tra cuối khóa (BaiHocId = NULL)
        $baiKiemTra = BaiKiemTra::create([
            'TieuDe' => 'Bài kiểm tra cuối khóa - ' . $khoaHoc->TieuDe,
            'MoTa' => 'Bài kiểm tra tổng hợp kiến thức khóa học',
            'KhoaHocId' => $khoaHocId,
            'BaiHocId' => null, // NULL = bài kiểm tra cuối khóa
            'ThoiGianLamBai' => 30, // 30 phút
            'SoCauHoi' => 10,
            'DiemDat' => 5,
            'SoLanLamToiDa' => 3,
            'XaoTronCauHoi' => true,
            'XaoTronDapAn' => true,
            'HienThiDapAn' => true,
            'TrangThai' => 'ACTIVE',
            'NgayBatDau' => now(),
            'ThietLapJson' => json_encode([
                'nganHangIds' => [$nganHang->NganHangId],
                'soCauMoiNganHang' => [
                    (string)$nganHang->NganHangId => 10
                ],
                'thoiGianLamBai' => 30,
                'xaoTronCauHoi' => true,
                'xaoTronDapAn' => true,
                'hienThiDapAn' => true,
                'soLanLamToiDa' => 3
            ])
        ]);

        $this->command->info("Đã tạo bài kiểm tra cuối khóa ID: {$baiKiemTra->BaiKiemTraId}");
        $this->command->info("✓ Hoàn thành tạo dữ liệu bài kiểm tra cuối khóa!");
    }
}
