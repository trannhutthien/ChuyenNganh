
DROP DATABASE IF EXISTS Elearning_KyNangCNTT;
CREATE DATABASE Elearning_KyNangCNTT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Elearning_KyNangCNTT;

-- ==============================
-- 1) NGƯỜI DÙNG & VAI TRÒ
-- ==============================
CREATE TABLE VaiTro (
    VaiTroId INT AUTO_INCREMENT PRIMARY KEY,
    MaVaiTro VARCHAR(50) UNIQUE NOT NULL,   -- ADMIN / STUDENT / EDITOR
    TenVaiTro NVARCHAR(100) NOT NULL,
    MoTa NVARCHAR(255)
);

CREATE TABLE NguoiDung (
    NguoiDungId INT AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(255) UNIQUE NOT NULL,
    MatKhauHash VARCHAR(255) NOT NULL,
    HoTen NVARCHAR(150) NOT NULL,
    AvatarUrl NVARCHAR(300),
    TrangThai TINYINT DEFAULT 1, -- 1=active, 0=locked
    TaoLuc DATETIME DEFAULT CURRENT_TIMESTAMP,
    CapNhatLuc DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE NguoiDung_VaiTro (
    NguoiDungId INT NOT NULL,
    VaiTroId INT NOT NULL,
    PRIMARY KEY (NguoiDungId, VaiTroId),
    FOREIGN KEY (NguoiDungId) REFERENCES NguoiDung(NguoiDungId) ON DELETE CASCADE,
    FOREIGN KEY (VaiTroId) REFERENCES VaiTro(VaiTroId) ON DELETE CASCADE
);

-- ==============================
-- 2) KỸ NĂNG — KHÓA HỌC (N–N)
-- ==============================
CREATE TABLE KyNang (
    KyNangId INT AUTO_INCREMENT PRIMARY KEY,
    TenKyNang NVARCHAR(150) UNIQUE NOT NULL,
    MoTa NVARCHAR(500),
    CapDo TINYINT DEFAULT 1 CHECK (CapDo BETWEEN 1 AND 3)
);

CREATE TABLE KhoaHoc (
    KhoaHocId INT AUTO_INCREMENT PRIMARY KEY,
    TieuDe NVARCHAR(200) NOT NULL,
    HinhAnhUrl NVARCHAR(300),                    -- ảnh đại diện khóa học
    TomTat NVARCHAR(500),
    CapDo TINYINT DEFAULT 1 CHECK (CapDo BETWEEN 1 AND 3),
    Tags NVARCHAR(300),
    DieuKienTienQuyet NVARCHAR(300),
    GiaTien DECIMAL(10,2) DEFAULT 0,             -- giá khóa học
    TrangThai TINYINT DEFAULT 2 CHECK (TrangThai IN (1,2,3)), -- 1=draft,2=published,3=archived
    TaoLuc DATETIME DEFAULT CURRENT_TIMESTAMP,
    CapNhatLuc DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE KyNang_KhoaHoc (
    KyNangId INT NOT NULL,
    KhoaHocId INT NOT NULL,
    PRIMARY KEY (KyNangId, KhoaHocId),
    FOREIGN KEY (KyNangId) REFERENCES KyNang(KyNangId) ON DELETE CASCADE,
    FOREIGN KEY (KhoaHocId) REFERENCES KhoaHoc(KhoaHocId) ON DELETE CASCADE
);

-- ==============================
-- 3) BÀI HỌC (METADATA)
-- ==============================
CREATE TABLE BaiHoc (
    BaiHocId INT AUTO_INCREMENT PRIMARY KEY,
    KhoaHocId INT NOT NULL,
    TieuDe NVARCHAR(200) NOT NULL,
    -- ĐÃ BỎ: NoiDung TEXT
    ThuTu INT DEFAULT 1,
    TrangThai TINYINT DEFAULT 2 CHECK (TrangThai IN (1,2,3)), -- 1=draft,2=published,3=archived
    TaoLuc DATETIME DEFAULT CURRENT_TIMESTAMP,
    CapNhatLuc DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (KhoaHocId) REFERENCES KhoaHoc(KhoaHocId) ON DELETE CASCADE,
    UNIQUE (KhoaHocId, ThuTu)
);

-- ==============================
-- 3b) NỘI DUNG BÀI HỌC (BLOCK)
-- ==============================
-- Mỗi bài học gồm nhiều khối nội dung: text, hình, code, video...
-- Admin sẽ soạn các block này trên trang quản trị.
CREATE TABLE NoiDungBaiHoc (
    NoiDungId INT AUTO_INCREMENT PRIMARY KEY,
    BaiHocId INT NOT NULL,
    ThuTu INT NOT NULL,   -- thứ tự block trong bài học
    LoaiBlock ENUM('TEXT','IMAGE','CODE','VIDEO','QUOTE','LIST') NOT NULL,
    DuLieu JSON NOT NULL, -- JSON lưu chi tiết (html, url hình, code, language, v.v.)
    TaoLuc DATETIME DEFAULT CURRENT_TIMESTAMP,
    CapNhatLuc DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (BaiHocId) REFERENCES BaiHoc(BaiHocId) ON DELETE CASCADE
);

-- Ví dụ DuLieu cho từng loại:
-- TEXT:  {"html": "<p>Giới thiệu về Git...</p>"}
-- IMAGE: {"url": "https://.../image.png", "caption": "Sơ đồ Git"}
-- CODE:  {"language": "js", "code": "console.log('Hello');"}
-- VIDEO: {"url": "https://www.youtube.com/...", "provider": "youtube"}

-- ==============================
-- 4) NGÂN HÀNG — CÂU HỎI — LỰA CHỌN
-- ==============================
-- Ngân hàng câu hỏi thuộc KHÓA HỌC
CREATE TABLE NganHangCauHoi (
    NganHangId INT AUTO_INCREMENT PRIMARY KEY,
    KhoaHocId INT NOT NULL,
    TenNganHang NVARCHAR(200) NOT NULL,
    MoTa NVARCHAR(300),
    CapDoMacDinh TINYINT DEFAULT 1 CHECK (CapDoMacDinh BETWEEN 1 AND 3),
    FOREIGN KEY (KhoaHocId) REFERENCES KhoaHoc(KhoaHocId) ON DELETE CASCADE
);

-- Câu hỏi: thuộc Ngân hàng, có thể gắn Bài học (hoặc NULL nếu câu chung)
CREATE TABLE CauHoi (
    CauHoiId INT AUTO_INCREMENT PRIMARY KEY,
    NganHangId INT NOT NULL,
    BaiHocId INT NULL,
    Loai ENUM('MOT_DAP_AN','NHIEU_DAP_AN','DUNG_SAI','DIEN_KHUYET') NOT NULL,
    DeBai TEXT NOT NULL,
    GiaiThich TEXT,
    DoKho TINYINT DEFAULT 1 CHECK (DoKho BETWEEN 1 AND 3),
    ChuDeTags NVARCHAR(300),
    ThuTu INT DEFAULT 1,
    FOREIGN KEY (NganHangId) REFERENCES NganHangCauHoi(NganHangId) ON DELETE CASCADE,
    FOREIGN KEY (BaiHocId) REFERENCES BaiHoc(BaiHocId),
    UNIQUE KEY UX_CauHoi_NganHang_ThuTu (NganHangId, ThuTu) -- thứ tự không trùng trong 1 ngân hàng
);

CREATE TABLE LuaChon (
    LuaChonId INT AUTO_INCREMENT PRIMARY KEY,
    CauHoiId INT NOT NULL,
    NoiDung TEXT NOT NULL,
    DungHaySai BOOLEAN DEFAULT 0,
    ThuTu INT DEFAULT 1,
    FOREIGN KEY (CauHoiId) REFERENCES CauHoi(CauHoiId) ON DELETE CASCADE,
    UNIQUE KEY UX_LuaChon_CauHoi_ThuTu (CauHoiId, ThuTu) -- thứ tự lựa chọn không trùng trong 1 câu
);

-- ==============================
-- 5) BÀI KIỂM TRA — LẦN LÀM — TRẢ LỜI
-- ==============================
CREATE TABLE BaiKiemTra (
    BaiKiemTraId INT AUTO_INCREMENT PRIMARY KEY,
    KhoaHocId INT NULL,
    BaiHocId INT NULL,
    TieuDe NVARCHAR(200) NOT NULL,
    ThietLapJson JSON,
    DiemDat DECIMAL(5,2) DEFAULT 5.00,
    TrangThai TINYINT DEFAULT 2 CHECK (TrangThai IN (1,2,3)), -- 1=draft,2=published,3=archived
    TaoLuc DATETIME DEFAULT CURRENT_TIMESTAMP,
    CapNhatLuc DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (KhoaHocId) REFERENCES KhoaHoc(KhoaHocId) ON DELETE CASCADE,
    FOREIGN KEY (BaiHocId) REFERENCES BaiHoc(BaiHocId) ON DELETE CASCADE
    -- Quy ước nghiệp vụ:
    --  - Quiz cuối bài:  KhoaHocId != NULL, BaiHocId != NULL
    --  - Quiz cấp khóa: KhoaHocId != NULL, BaiHocId IS NULL
);

CREATE TABLE LanLamBai (
    LanLamBaiId INT AUTO_INCREMENT PRIMARY KEY,
    BaiKiemTraId INT NOT NULL,
    NguoiDungId INT NOT NULL,
    BatDauLuc DATETIME DEFAULT CURRENT_TIMESTAMP,
    NopBaiLuc DATETIME,
    DiemSo DECIMAL(5,2),
    ChiTietJson JSON, -- snapshot bộ câu, cấu hình chấm...
    TrangThai ENUM('INPROGRESS','SUBMITTED','CANCELLED') DEFAULT 'INPROGRESS',
    FOREIGN KEY (BaiKiemTraId) REFERENCES BaiKiemTra(BaiKiemTraId) ON DELETE CASCADE,
    FOREIGN KEY (NguoiDungId) REFERENCES NguoiDung(NguoiDungId) ON DELETE CASCADE
);

CREATE TABLE TraLoi (
    TraLoiId INT AUTO_INCREMENT PRIMARY KEY,
    LanLamBaiId INT NOT NULL,
    CauHoiId INT NOT NULL,
    LuaChonIds NVARCHAR(300), -- có thể đổi sang JSON nếu cần
    DungHaySai BOOLEAN,
    ThoiGianGiay INT,
    FOREIGN KEY (LanLamBaiId) REFERENCES LanLamBai(LanLamBaiId) ON DELETE CASCADE,
    FOREIGN KEY (CauHoiId) REFERENCES CauHoi(CauHoiId) ON DELETE CASCADE
);

-- ==============================
-- 6) GHI DANH / TIẾN ĐỘ
-- ==============================
CREATE TABLE GhiDanh (
    NguoiDungId INT NOT NULL,
    KhoaHocId INT NOT NULL,
    PhanTramHoanThanh DECIMAL(5,2) DEFAULT 0.00,
    BaiHocCuoiCungId INT,
    CapNhatLuc DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (NguoiDungId, KhoaHocId),
    FOREIGN KEY (NguoiDungId) REFERENCES NguoiDung(NguoiDungId) ON DELETE CASCADE,
    FOREIGN KEY (KhoaHocId) REFERENCES KhoaHoc(KhoaHocId) ON DELETE CASCADE,
    FOREIGN KEY (BaiHocCuoiCungId) REFERENCES BaiHoc(BaiHocId)
);

-- ==============================
-- 7) BÀI VIẾT (BLOG / TIN TỨC)
-- ==============================
CREATE TABLE ChuyenMucBaiViet (
    ChuyenMucId INT AUTO_INCREMENT PRIMARY KEY,
    TenChuyenMuc NVARCHAR(150) NOT NULL,
    MoTa NVARCHAR(300),
    Slug VARCHAR(255) UNIQUE
);

CREATE TABLE BaiViet (
    BaiVietId INT AUTO_INCREMENT PRIMARY KEY,
    TacGiaId INT NOT NULL,                          -- FK tới NguoiDung
    TieuDe NVARCHAR(200) NOT NULL,
    Slug VARCHAR(255) UNIQUE,
    AnhDaiDienUrl NVARCHAR(300),
    TomTat NVARCHAR(500),
    NoiDung LONGTEXT NOT NULL,                      -- markdown hoặc html
    Tags NVARCHAR(300),
    TrangThai TINYINT DEFAULT 2 CHECK (TrangThai IN (1,2,3)), -- 1=draft,2=published,3=archived
    LuotXem INT DEFAULT 0,
    TaoLuc DATETIME DEFAULT CURRENT_TIMESTAMP,
    CapNhatLuc DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (TacGiaId) REFERENCES NguoiDung(NguoiDungId) ON DELETE CASCADE
);

CREATE TABLE BaiViet_ChuyenMuc (
    BaiVietId INT NOT NULL,
    ChuyenMucId INT NOT NULL,
    PRIMARY KEY (BaiVietId, ChuyenMucId),
    FOREIGN KEY (BaiVietId) REFERENCES BaiViet(BaiVietId) ON DELETE CASCADE,
    FOREIGN KEY (ChuyenMucId) REFERENCES ChuyenMucBaiViet(ChuyenMucId) ON DELETE CASCADE
);

-- ==============================
-- 8) INDEX CHO KHÓA NGOẠI (TỐI ƯU TRUY VẤN)
-- ==============================
CREATE INDEX IX_BaiHoc_KhoaHocId           ON BaiHoc(KhoaHocId);
CREATE INDEX IX_NoiDungBaiHoc_BaiHocId     ON NoiDungBaiHoc(BaiHocId);

CREATE INDEX IX_NganHangCauHoi_KhoaHocId   ON NganHangCauHoi(KhoaHocId);
CREATE INDEX IX_CauHoi_NganHangId          ON CauHoi(NganHangId);
CREATE INDEX IX_CauHoi_BaiHocId            ON CauHoi(BaiHocId);
CREATE INDEX IX_LuaChon_CauHoiId           ON LuaChon(CauHoiId);

CREATE INDEX IX_BaiKiemTra_KhoaHocId       ON BaiKiemTra(KhoaHocId);
CREATE INDEX IX_BaiKiemTra_BaiHocId        ON BaiKiemTra(BaiHocId);
CREATE INDEX IX_LanLamBai_BaiKiemTraId     ON LanLamBai(BaiKiemTraId);
CREATE INDEX IX_LanLamBai_NguoiDungId      ON LanLamBai(NguoiDungId);
CREATE INDEX IX_TraLoi_LanLamBaiId         ON TraLoi(LanLamBaiId);
CREATE INDEX IX_TraLoi_CauHoiId            ON TraLoi(CauHoiId);

CREATE INDEX IX_GhiDanh_KhoaHocId          ON GhiDanh(KhoaHocId);

CREATE INDEX IX_BaiViet_TacGiaId           ON BaiViet(TacGiaId);
CREATE INDEX IX_BaiVietChuyenMuc_ChuyenMucId ON BaiViet_ChuyenMuc(ChuyenMucId);


USE Elearning_KyNangCNTT;

-- =========================================
-- 1) VAI TRÒ & NGƯỜI DÙNG
-- =========================================
INSERT INTO VaiTro (MaVaiTro, TenVaiTro, MoTa) VALUES
('ADMIN',   N'Quản trị hệ thống',   N'Quản lý toàn bộ hệ thống'),
('STUDENT', N'Học viên',            N'Người học các khóa kỹ năng'),
('EDITOR',  N'Biên tập viên',       N'Người soạn nội dung khóa học');

-- Giả sử:
-- VaiTroId: 1=ADMIN, 2=STUDENT, 3=EDITOR

INSERT INTO NguoiDung (Email, MatKhauHash, HoTen, AvatarUrl, TrangThai) VALUES
('admin@example.com',  'hash_admin',  N'Nguyễn Admin',        NULL, 1),
('hv1@example.com',    'hash_hv1',    N'Trần Học Viên 1',     NULL, 1),
('hv2@example.com',    'hash_hv2',    N'Lê Học Viên 2',       NULL, 1),
('editor@example.com', 'hash_editor', N'Phạm Biên Tập Viên',  NULL, 1);

-- Giả sử:
-- NguoiDungId: 1=admin, 2=hv1, 3=hv2, 4=editor

INSERT INTO NguoiDung_VaiTro (NguoiDungId, VaiTroId) VALUES
(1, 1),  -- admin -> ADMIN
(2, 2),  -- hv1   -> STUDENT
(3, 2),  -- hv2   -> STUDENT
(4, 3);  -- editor-> EDITOR

-- =========================================
-- 2) KỸ NĂNG, KHÓA HỌC, LIÊN KẾT KỸ NĂNG-KHÓA
-- =========================================
INSERT INTO KyNang (TenKyNang, MoTa, CapDo) VALUES
(N'Lập trình Web cơ bản',  N'HTML, CSS, JavaScript cơ bản', 1),
(N'Cơ sở dữ liệu',         N'SQL, mô hình ERD, thiết kế DB', 1),
(N'Git & DevOps',          N'Quản lý mã nguồn và CI/CD cơ bản', 2);

-- Giả sử KyNangId: 1,2,3

INSERT INTO KhoaHoc (TieuDe, HinhAnhUrl, TomTat, CapDo, Tags, DieuKienTienQuyet, GiaTien, TrangThai) VALUES
(N'HTML & CSS cho người mới', 
 'https://example.com/images/html-css.png',
 N'Học cách xây dựng giao diện web tĩnh với HTML và CSS.',
 1,
 N'frontend,html,css',
 N'Biết sử dụng máy tính và trình duyệt web',
 0,
 2),

(N'JavaScript căn bản',
 'https://example.com/images/js-basic.png',
 N'Làm quen với JavaScript, biến, hàm, vòng lặp.',
 1,
 N'frontend,javascript,logic',
 N'Nên học xong khóa HTML & CSS cho người mới',
 199000,
 2),

(N'Cơ sở dữ liệu MySQL',
 'https://example.com/images/mysql.png',
 N'Tìm hiểu mô hình quan hệ, SQL cơ bản trên MySQL.',
 1,
 N'database,sql,mysql',
 N'Hiểu về lập trình cơ bản',
 249000,
 2);

-- Giả sử KhoaHocId: 1=HTML/CSS, 2=JS, 3=MySQL

INSERT INTO KyNang_KhoaHoc (KyNangId, KhoaHocId) VALUES
(1, 1), -- Web cơ bản -> HTML/CSS
(1, 2), -- Web cơ bản -> JS
(2, 3), -- CSDL       -> MySQL
(3, 2); -- Git/DevOps -> JS (vd dùng cho bài Git trong JS)

-- =========================================
-- 3) BÀI HỌC (METADATA)
-- =========================================
INSERT INTO BaiHoc (KhoaHocId, TieuDe, ThuTu, TrangThai) VALUES
(1, N'Giới thiệu HTML',         1, 2),
(1, N'Thẻ HTML cơ bản',         2, 2),
(1, N'Giới thiệu CSS',          3, 2),

(2, N'Biến và kiểu dữ liệu',    1, 2),
(2, N'Câu lệnh điều kiện',      2, 2),

(3, N'Giới thiệu cơ sở dữ liệu',1, 2),
(3, N'Các câu lệnh SELECT cơ bản', 2, 2);

-- Giả sử BaiHocId: 1..7 theo thứ tự trên

-- =========================================
-- 3b) NỘI DUNG BÀI HỌC (BLOCK)
-- =========================================
INSERT INTO NoiDungBaiHoc (BaiHocId, ThuTu, LoaiBlock, DuLieu) VALUES
  -- Bài 1: Giới thiệu HTML (BaiHocId = 1)
  (1, 1, 'TEXT',
   '{ "html": "<h2>HTML là gì?</h2><p>HTML là ngôn ngữ đánh dấu để xây dựng cấu trúc trang web.</p>" }'),

  (1, 2, 'IMAGE',
   '{ "url": "https://example.com/images/html-structure.png", "caption": "Cấu trúc cơ bản của trang HTML" }'),

  (1, 3, 'CODE',
   '{ "language": "html", "code": "<!DOCTYPE html>\\n<html>\\n  <head>\\n    <title>Trang đầu tiên</title>\\n  </head>\\n  <body>\\n    <h1>Xin chào!</h1>\\n  </body>\\n</html>" }'),

  -- Bài 2: Thẻ HTML cơ bản (BaiHocId = 2)
  (2, 1, 'TEXT',
   '{ "html": "<p>Một số thẻ HTML thường gặp: &lt;p&gt;, &lt;h1&gt;, &lt;a&gt;, &lt;img&gt;...</p>" }'),

  (2, 2, 'LIST',
   '{ "items": ["Thẻ &lt;p&gt;: đoạn văn bản", "Thẻ &lt;h1&gt;–&lt;h6&gt;: tiêu đề", "Thẻ &lt;a&gt;: liên kết", "Thẻ &lt;img&gt;: hình ảnh"] }'),

  -- Bài 4: Biến và kiểu dữ liệu JS (BaiHocId = 4)
  (4, 1, 'TEXT',
   '{ "html": "<p>Trong JavaScript, bạn có thể khai báo biến bằng var, let, const.</p>" }'),

  (4, 2, 'CODE',
   '{ "language": "javascript", "code": "let age = 20;\\nconst PI = 3.14;\\nconsole.log(age, PI);" }'),

  -- Bài 7: SELECT cơ bản MySQL (BaiHocId = 7)
  (7, 1, 'TEXT',
   '{ "html": "<p>Câu lệnh SELECT dùng để lấy dữ liệu từ bảng.</p>" }'),

  (7, 2, 'CODE',
   '{ "language": "sql", "code": "SELECT * FROM SinhVien;\\nSELECT HoTen, NamSinh FROM SinhVien WHERE NamSinh &gt;= 2000;" }');


-- =========================================
-- 4) NGÂN HÀNG CÂU HỎI, CÂU HỎI, LỰA CHỌN
-- =========================================
INSERT INTO NganHangCauHoi (KhoaHocId, TenNganHang, MoTa, CapDoMacDinh) VALUES
(1, N'Ngân hàng HTML/CSS cơ bản',  N'Câu hỏi trắc nghiệm về HTML và CSS', 1),
(2, N'Ngân hàng JavaScript cơ bản',N'Câu hỏi trắc nghiệm về JS', 1),
(3, N'Ngân hàng MySQL cơ bản',    N'Câu hỏi trắc nghiệm về SQL', 1);

-- Giả sử NganHangId: 1=HTML/CSS, 2=JS, 3=MySQL

INSERT INTO CauHoi (NganHangId, BaiHocId, Loai, DeBai, GiaiThich, DoKho, ChuDeTags, ThuTu) VALUES
-- HTML/CSS
(1, 1, 'MOT_DAP_AN',
 N'Thẻ nào dùng để tạo đoạn văn bản trong HTML?',
 N'Thẻ &lt;p&gt; là viết tắt của paragraph (đoạn văn).',
 1,
 N'html,tag,paragraph',
 1),

(1, 2, 'MOT_DAP_AN',
 N'Thuộc tính nào dùng để đổi màu chữ trong CSS?',
 N'Thuộc tính color dùng để đổi màu chữ.',
 1,
 N'css,color',
 2),

-- JavaScript
(2, 4, 'MOT_DAP_AN',
 N'Từ khóa nào dùng để khai báo hằng số trong JavaScript?',
 N'const dùng để khai báo một biến không thể gán lại.',
 1,
 N'js,const,variable',
 1),

-- MySQL
(3, 7, 'DUNG_SAI',
 N'Câu lệnh SELECT * FROM SinhVien; sẽ lấy tất cả các cột trong bảng SinhVien.',
 N'Trả lời Đúng, dấu * thể hiện tất cả các cột.',
 1,
 N'sql,select',
 1);

-- Giả sử CauHoiId: 1..4 theo thứ tự trên

INSERT INTO LuaChon (CauHoiId, NoiDung, DungHaySai, ThuTu) VALUES
-- Câu 1: thẻ đoạn văn
(1, N'&lt;p&gt;',    1, 1),
(1, N'&lt;div&gt;', 0, 2),
(1, N'&lt;span&gt;',0, 3),
(1, N'&lt;h1&gt;',  0, 4),

-- Câu 2: thuộc tính màu chữ
(2, N'background-color', 0, 1),
(2, N'font-size',        0, 2),
(2, N'color',            1, 3),
(2, N'text-align',       0, 4),

-- Câu 3: const
(3, N'let',   0, 1),
(3, N'var',   0, 2),
(3, N'const', 1, 3),
(3, N'function', 0, 4),

-- Câu 4: SELECT *
(4, N'Đúng', 1, 1),
(4, N'Sai',  0, 2);

-- =========================================
-- 5) BÀI KIỂM TRA, LẦN LÀM, TRẢ LỜI
-- =========================================
INSERT INTO BaiKiemTra (KhoaHocId, BaiHocId, TieuDe, ThietLapJson, DiemDat, TrangThai) VALUES
(1, 2,
 N'Quiz HTML/CSS cơ bản - Bài 2',
 '{ "soCauHoi": 2, "thoiGianPhut": 10, "chinhSachDiem": "trung_binh" }',
 5.00,
 2),

(2, 4,
 N'Quiz JavaScript căn bản - Biến và kiểu dữ liệu',
 '{ "soCauHoi": 1, "thoiGianPhut": 5 }',
 5.00,
 2),

(3, 7,
 N'Quiz MySQL cơ bản - SELECT',
 '{ "soCauHoi": 1, "thoiGianPhut": 5 }',
 5.00,
 2);

-- Giả sử BaiKiemTraId: 1..3

INSERT INTO LanLamBai (BaiKiemTraId, NguoiDungId, BatDauLuc, NopBaiLuc, DiemSo, ChiTietJson, TrangThai) VALUES
(1, 2,
 NOW() - INTERVAL 1 DAY,
 NOW() - INTERVAL 1 DAY + INTERVAL 10 MINUTE,
 8.00,
 '{ "soCauHoi": 2, "soCauDung": 2 }',
 'SUBMITTED'),

(2, 2,
 NOW() - INTERVAL 1 DAY,
 NOW() - INTERVAL 1 DAY + INTERVAL 3 MINUTE,
 10.00,
 '{ "soCauHoi": 1, "soCauDung": 1 }',
 'SUBMITTED'),

(3, 3,
 NOW() - INTERVAL 2 DAY,
 NOW() - INTERVAL 2 DAY + INTERVAL 4 MINUTE,
 5.00,
 '{ "soCauHoi": 1, "soCauDung": 1 }',
 'SUBMITTED');

-- Giả sử LanLamBaiId: 1..3

INSERT INTO TraLoi (LanLamBaiId, CauHoiId, LuaChonIds, DungHaySai, ThoiGianGiay) VALUES
(1, 1, '1', 1, 30),     -- hv1 làm đúng câu 1
(1, 2, '3', 1, 45),     -- hv1 làm đúng câu 2
(2, 3, '3', 1, 20),     -- hv1 làm đúng câu 3
(3, 4, '1', 1, 25);     -- hv2 làm đúng câu 4

-- =========================================
-- 6) GHI DANH / TIẾN ĐỘ
-- =========================================
INSERT INTO GhiDanh (NguoiDungId, KhoaHocId, PhanTramHoanThanh, BaiHocCuoiCungId) VALUES
(2, 1, 60.00, 2),  -- hv1 học 60% khóa HTML/CSS, đến bài 2
(2, 2, 30.00, 4),  -- hv1 học 30% khóa JS
(3, 3, 20.00, 7);  -- hv2 học 20% khóa MySQL

-- =========================================
-- 7) BÀI VIẾT (BLOG / TIN TỨC)
-- =========================================
INSERT INTO ChuyenMucBaiViet (TenChuyenMuc, MoTa, Slug) VALUES
(N'Tin tức',         N'Tin cập nhật về khóa học và cộng đồng', 'tin-tuc'),
(N'Kinh nghiệm học', N'Chia sẻ mẹo và kinh nghiệm tự học',     'kinh-nghiem-hoc'),
(N'Kiến thức nền tảng', N'Các bài viết củng cố kiến thức cơ bản', 'kien-thuc-nen-tang');

-- Giả sử ChuyenMucId: 1..3

INSERT INTO BaiViet (TacGiaId, TieuDe, Slug, AnhDaiDienUrl, TomTat, NoiDung, Tags, TrangThai, LuotXem) VALUES
(4,
 N'5 mẹo để tự học lập trình hiệu quả',
 '5-meo-tu-hoc-lap-trinh-hieu-qua',
 'https://example.com/images/blog-self-learning.png',
 N'Một vài mẹo nhỏ giúp bạn tự học lập trình bền bỉ và hiệu quả hơn.',
 N'<p>Tự học lập trình không hề dễ, nhưng nếu có lộ trình đúng đắn...</p>',
 N'tu-hoc,lap-trinh,kinh-nghiem',
 2,
 120),

(1,
 N'Cập nhật khóa học JavaScript căn bản',
 'cap-nhat-khoa-hoc-javascript-can-ban',
 'https://example.com/images/blog-js-update.png',
 N'Thông báo cập nhật nội dung và ví dụ mới cho khóa JavaScript căn bản.',
 N'<p>Chúng tôi vừa bổ sung thêm nhiều ví dụ minh họa, bài tập tương tác...</p>',
 N'javascript,cap-nhat,khoa-hoc',
 2,
 80);

-- Giả sử BaiVietId: 1..2

INSERT INTO BaiViet_ChuyenMuc (BaiVietId, ChuyenMucId) VALUES
(1, 2), -- bài "5 mẹo tự học" thuộc "Kinh nghiệm học"
(1, 3), -- và "Kiến thức nền tảng"
(2, 1), -- bài cập nhật JS thuộc "Tin tức"
(2, 3); -- và "Kiến thức nền tảng"

select * from nguoidung;