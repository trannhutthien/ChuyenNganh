-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th12 20, 2025 lúc 02:08 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `elearning_kynangcntt`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baihoc`
--

CREATE TABLE `baihoc` (
  `BaiHocId` int(11) NOT NULL,
  `KhoaHocId` int(11) NOT NULL,
  `TieuDe` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `MoTa` text DEFAULT NULL,
  `NoiDung` text DEFAULT NULL,
  `LoaiBaiHoc` varchar(50) NOT NULL DEFAULT 'video',
  `ThuTu` int(11) DEFAULT 1,
  `ThoiLuong` int(11) NOT NULL DEFAULT 0,
  `VideoUrl` varchar(500) DEFAULT NULL,
  `TrangThai` tinyint(4) DEFAULT 2,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `baihoc`
--

INSERT INTO `baihoc` (`BaiHocId`, `KhoaHocId`, `TieuDe`, `MoTa`, `NoiDung`, `LoaiBaiHoc`, `ThuTu`, `ThoiLuong`, `VideoUrl`, `TrangThai`, `TaoLuc`, `CapNhatLuc`) VALUES
(1, 11, 'Giới thiệu khóa học và các kiến thức nền tảng', 'Dưới đây là đoạn mô tả – giới thiệu khóa học HTML & CSS cơ bản, viết bằng lời, ngắn gọn, dễ hiểu, phù hợp dùng cho website, brochure hoặc báo cáo học tập:\nGiới thiệu khóa học HTML & CSS cơ bản\n\nKhóa học HTML & CSS cơ bản được thiết kế dành cho người mới bắt đầu làm quen với lĩnh vực lập trình web. Khóa học giúp học viên hiểu rõ cách cấu trúc một trang web bằng HTML và cách thiết kế, trình bày giao diện bằng CSS.\n\nTrong khóa học, học viên sẽ được học cách tạo các thành phần cơ bản của website như tiêu đề, đoạn văn, hình ảnh, liên kết, bảng biểu và biểu mẫu. Bên cạnh đó, khóa học còn hướng dẫn cách sử dụng CSS để định dạng màu sắc, font chữ, bố cục trang, căn chỉnh nội dung và tạo giao diện đẹp mắt, dễ sử dụng.\n\nThông qua các bài thực hành trực tiếp, học viên sẽ từng bước xây dựng được các trang web tĩnh đơn giản, hiểu được mối quan hệ giữa HTML và CSS, đồng thời tạo nền tảng vững chắc để tiếp tục học các công nghệ web nâng cao như JavaScript, Bootstrap, React hoặc VueJS.\n\nKhóa học phù hợp với sinh viên công nghệ thông tin, người mới học lập trình hoặc bất kỳ ai muốn tự xây dựng website cá nhân và hiểu rõ cách hoạt động của giao diện web.', 'Giới thiệu khóa học HTML & CSS cơ bản\n\nKhóa học HTML & CSS cơ bản được thiết kế dành cho người mới bắt đầu làm quen với lĩnh vực lập trình web. Khóa học giúp học viên hiểu rõ cách cấu trúc một trang web bằng HTML và cách thiết kế, trình bày giao diện bằng CSS.\n\nTrong khóa học, học viên sẽ được học cách tạo các thành phần cơ bản của website như tiêu đề, đoạn văn, hình ảnh, liên kết, bảng biểu và biểu mẫu. Bên cạnh đó, khóa học còn hướng dẫn cách sử dụng CSS để định dạng màu sắc, font chữ, bố cục trang, căn chỉnh nội dung và tạo giao diện đẹp mắt, dễ sử dụng.\n\nThông qua các bài thực hành trực tiếp, học viên sẽ từng bước xây dựng được các trang web tĩnh đơn giản, hiểu được mối quan hệ giữa HTML và CSS, đồng thời tạo nền tảng vững chắc để tiếp tục học các công nghệ web nâng cao như JavaScript, Bootstrap, React hoặc VueJS.\n\nKhóa học phù hợp với sinh viên công nghệ thông tin, người mới học lập trình hoặc bất kỳ ai muốn tự xây dựng website cá nhân và hiểu rõ cách hoạt động của giao diện web.', 'text', 1, 0, NULL, 1, '2025-12-05 17:29:38', '2025-12-15 07:21:08'),
(2, 11, 'Cài đặt môi trường và công cụ cần thiết', NULL, NULL, 'video', 2, 0, NULL, 1, '2025-12-05 17:29:38', '2025-12-09 17:46:51'),
(3, 11, 'Các khái niệm cơ bản và ví dụ thực hành đầu tiên', NULL, NULL, 'video', 3, 0, NULL, 1, '2025-12-05 17:29:38', '2025-12-09 17:51:16'),
(4, 11, 'Thực hành bài tập ứng dụng nhỏ', NULL, NULL, 'video', 4, 0, NULL, 1, '2025-12-05 17:29:38', '2025-12-15 07:16:22'),
(12, 11, 'Ôn tập cuối khóa', 'ôn tập cuối khóa', 'dfvfvfdev', 'text', 5, 0, NULL, 1, '2025-12-09 17:36:54', '2025-12-15 07:16:57'),
(13, 11, 'tổng kết chương học', 'làm bài kiểm tra', NULL, 'quiz', 6, 15, NULL, 1, '2025-12-15 07:12:13', '2025-12-15 07:12:13');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baikiemtra`
--

CREATE TABLE `baikiemtra` (
  `BaiKiemTraId` int(11) NOT NULL,
  `KhoaHocId` int(11) DEFAULT NULL,
  `BaiHocId` int(11) DEFAULT NULL,
  `TieuDe` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ThietLapJson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ThietLapJson`)),
  `DiemDat` decimal(5,2) DEFAULT 5.00,
  `TrangThai` tinyint(4) DEFAULT 2,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `baikiemtra`
--

INSERT INTO `baikiemtra` (`BaiKiemTraId`, `KhoaHocId`, `BaiHocId`, `TieuDe`, `ThietLapJson`, `DiemDat`, `TrangThai`, `TaoLuc`, `CapNhatLuc`) VALUES
(8, 11, 1, 'bài kiểm tra đầu vào', '{\"soCauHoi\":5,\"thoiGianLamBai\":5,\"soLanLamToiDa\":2,\"xaoTronCauHoi\":true,\"xaoTronDapAn\":true,\"hienThiDapAn\":true,\"nganHangIds\":[1,4]}', 5.00, 2, '2025-12-19 18:07:28', '2025-12-19 18:07:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cauhoi`
--

CREATE TABLE `cauhoi` (
  `CauHoiId` int(11) NOT NULL,
  `NganHangId` int(11) NOT NULL,
  `BaiHocId` int(11) DEFAULT NULL,
  `Loai` enum('single','multiple','true_false') NOT NULL DEFAULT 'single',
  `DeBai` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `GiaiThich` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DoKho` tinyint(4) DEFAULT 1,
  `ChuDeTags` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ThuTu` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cauhoi`
--

INSERT INTO `cauhoi` (`CauHoiId`, `NganHangId`, `BaiHocId`, `Loai`, `DeBai`, `GiaiThich`, `DoKho`, `ChuDeTags`, `ThuTu`) VALUES
(1, 1, NULL, 'multiple', 'Thẻ HTML nào dùng để tạo tiêu đề?', 'Vì <h1> là thẻ tiêu đề lớn nhất.', 1, 'html,heading', 1),
(2, 1, NULL, 'single', 'Thuộc tính nào trong HTML dùng để đặt đường dẫn ảnh?', 'Vì src là thuộc tính chứa đường dẫn ảnh.', 1, 'html,image', 2),
(3, 1, NULL, 'single', 'CSS viết tắt của từ gì?', 'Vì CSS viết tắt của Cascading Style Sheets.', 1, 'css,basic', 3),
(5, 1, NULL, 'single', 'Thẻ HTML nào chứa toàn bộ phần nội dung hiển thị?', 'Vì nội dung trang nằm trong thẻ <body>.', 1, 'html,body', 5),
(6, 1, NULL, 'multiple', 'Thuộc tính nào để đổi màu chữ trong CSS?', 'Vì thuộc tính color dùng để đổi màu chữ.', 1, 'css,color', 6),
(7, 1, NULL, 'single', 'Đơn vị px trong CSS dùng để làm gì?', 'Vì px là đơn vị kích thước cố định.', 1, 'css,unit', 7),
(8, 1, NULL, 'single', 'Thẻ HTML nào dùng để tạo danh sách không thứ tự?', 'Vì <ul> tạo danh sách không thứ tự.', 1, 'html,list', 8),
(9, 1, NULL, 'true_false', 'File CSS thường có phần mở rộng là gì?', 'Vì file CSS có phần mở rộng .css.', 1, 'css,file', 9);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ghidanh`
--

CREATE TABLE `ghidanh` (
  `NguoiDungId` int(11) NOT NULL,
  `KhoaHocId` int(11) NOT NULL,
  `PhanTramHoanThanh` decimal(5,2) DEFAULT 0.00,
  `BaiHocCuoiCungId` int(11) DEFAULT NULL,
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khoahoc`
--

CREATE TABLE `khoahoc` (
  `KhoaHocId` int(11) NOT NULL,
  `TieuDe` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `HinhAnhUrl` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `TomTat` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CapDo` tinyint(4) DEFAULT 1 CHECK (`CapDo` between 1 and 3),
  `Tags` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `DieuKienTienQuyet` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `GiaTien` decimal(10,2) DEFAULT 0.00,
  `TrangThai` tinyint(4) DEFAULT 2,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `khoahoc`
--

INSERT INTO `khoahoc` (`KhoaHocId`, `TieuDe`, `HinhAnhUrl`, `TomTat`, `CapDo`, `Tags`, `DieuKienTienQuyet`, `GiaTien`, `TrangThai`, `TaoLuc`, `CapNhatLuc`) VALUES
(3, 'Lập Trình PHP & MySQL', '/images/phpmysql.jpg', 'Xây dựng website động bằng PHP và MySQL.', 2, 'php,mysql,backend', NULL, 399000.00, 1, '2025-12-05 16:09:58', '2025-12-09 15:16:53'),
(4, 'Laravel Framework Cơ Bản', '/images/laravel.jpg', 'Lập trình web hiện đại theo mô hình MVC.', 3, 'laravel,backend', 'PHP cơ bản', 499000.00, 1, '2025-12-05 16:09:58', '2025-12-09 15:16:53'),
(6, 'Tin Học Văn Phòng Cơ Bản', '/images/office.jpg', 'Hướng dẫn Word, Excel, PowerPoint cho người mới.', 1, 'excel,word,powerpoint', NULL, 0.00, 1, '2025-12-05 16:09:58', '2025-12-09 15:16:53'),
(7, 'Giới Thiệu Về Lập Trình', '/images/programming.jpg', 'Khóa nhập môn dành cho người chưa biết gì về CNTT.', 1, 'coding,beginner', NULL, 0.00, 1, '2025-12-05 16:09:58', '2025-12-09 15:16:53'),
(9, 'Tư Duy Lập Trình Logic', '/images/logic.jpg', 'Xây dựng tư duy giải thuật cho người mới học.', 1, 'logic,thinking', NULL, 0.00, 1, '2025-12-05 16:09:58', '2025-12-09 15:16:53'),
(11, 'HTML & CSS Cơ Bản', '/images/htmlcss.jpg', 'Frontend cơ bản', 2, 'html,css,frontend', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-09 14:22:08'),
(16, 'Tin Học Văn Phòng', '/images/office.jpg', 'Học Word, Excel, PowerPoint cho người mới', 1, 'excel,word,powerpoint', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(17, 'Giới Thiệu Về Lập Trình', '/images/programming_intro.jpg', 'Khóa học căn bản dành cho người mới học lập trình', 1, 'coding,beginner', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(18, 'Kỹ Năng Tìm Kiếm Google', '/images/google_skill.jpg', 'Học cách sử dụng Google hiệu quả', 1, 'google,research', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(19, 'Tư Duy Lập Trình Logic', '/images/logic.jpg', 'Xây dựng tư duy giải thuật cơ bản', 1, 'logic,thinking', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(21, 'Lập trình C', '/image/phpmyaddmin', 'Kỹ năng lập trình', 1, 'Kỹ năng lập trình', NULL, 0.00, 1, '2025-12-09 09:18:53', '2025-12-09 09:18:53');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kynang`
--

CREATE TABLE `kynang` (
  `KyNangId` int(11) NOT NULL,
  `TenKyNang` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `MoTa` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CapDo` tinyint(4) DEFAULT 1 CHECK (`CapDo` between 1 and 3)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kynang_khoahoc`
--

CREATE TABLE `kynang_khoahoc` (
  `KyNangId` int(11) NOT NULL,
  `KhoaHocId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `lanlambai`
--

CREATE TABLE `lanlambai` (
  `LanLamBaiId` int(11) NOT NULL,
  `BaiKiemTraId` int(11) NOT NULL,
  `NguoiDungId` int(11) NOT NULL,
  `BatDauLuc` datetime DEFAULT current_timestamp(),
  `NopBaiLuc` datetime DEFAULT NULL,
  `DiemSo` decimal(5,2) DEFAULT NULL,
  `ChiTietJson` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ChiTietJson`)),
  `TrangThai` enum('INPROGRESS','SUBMITTED','CANCELLED') DEFAULT 'INPROGRESS'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `luachon`
--

CREATE TABLE `luachon` (
  `LuaChonId` int(11) NOT NULL,
  `CauHoiId` int(11) NOT NULL,
  `NoiDung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `DungHaySai` tinyint(1) DEFAULT 0,
  `ThuTu` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `luachon`
--

INSERT INTO `luachon` (`LuaChonId`, `CauHoiId`, `NoiDung`, `DungHaySai`, `ThuTu`) VALUES
(499, 2, 'src', 1, 1),
(500, 2, 'href', 0, 2),
(501, 2, 'alt', 0, 3),
(502, 3, 'Cascading Style Sheets', 1, 1),
(503, 3, 'Creative Style System', 0, 2),
(504, 3, 'Color Styling Source', 0, 3),
(508, 5, '<body>', 1, 1),
(509, 5, '<main>', 0, 2),
(510, 5, '<section>', 0, 3),
(511, 6, 'color', 1, 1),
(512, 6, 'font-weight', 0, 2),
(513, 6, 'background-size', 0, 3),
(514, 7, 'px', 1, 1),
(515, 7, '%', 0, 2),
(516, 7, 'em', 0, 3),
(517, 8, '<ul>', 1, 1),
(518, 8, '<li>', 0, 2),
(519, 8, '<ol>', 0, 3),
(520, 9, 'Đúng', 1, 1),
(521, 9, 'Sai', 0, 2),
(545, 1, '<h1>', 1, 1),
(546, 1, '<h3>', 1, 2),
(547, 1, '<h4>', 1, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2025_12_09_173316_add_lesson_fields_to_baihoc_table', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nganhangcauhoi`
--

CREATE TABLE `nganhangcauhoi` (
  `NganHangId` int(11) NOT NULL,
  `KhoaHocId` int(11) NOT NULL,
  `TenNganHang` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `MoTa` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `CapDoMacDinh` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nganhangcauhoi`
--

INSERT INTO `nganhangcauhoi` (`NganHangId`, `KhoaHocId`, `TenNganHang`, `MoTa`, `CapDoMacDinh`) VALUES
(1, 11, 'Ngân hàng câu hỏi cơ bản', 'Các câu hỏi mức độ dễ, kiểm tra kiến thức nền tảng.', 1),
(4, 11, 'ngan hang b', 'ngan hang tb', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `NguoiDungId` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `GoogleId` varchar(255) DEFAULT NULL,
  `MatKhauHash` varchar(255) NOT NULL,
  `HoTen` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `AvatarUrl` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`NguoiDungId`, `Email`, `GoogleId`, `MatKhauHash`, `HoTen`, `AvatarUrl`, `TrangThai`, `TaoLuc`, `CapNhatLuc`) VALUES
(1, 'admin@example.com', NULL, 'password123', 'Admin Hệ Thống', NULL, 1, '2025-12-05 17:42:44', '2025-12-06 00:54:36'),
(2, 'nhutthien@example.com', NULL, '123456', 'Nhựt Thiên', NULL, 1, '2025-12-05 17:42:44', '2025-12-05 17:45:51'),
(3, 'danthanh@example.com', NULL, '123456', 'Đan Thanh', NULL, 1, '2025-12-05 17:42:44', '2025-12-05 17:45:51'),
(5, 'trannhutthien012345@gmail.com', '108453134530556851460', '$2y$12$xMLgPPgreSMF2hCPYMiyWOehdSgWEFYzKSIZfwyMVxykWdkN2TT9W', 'thiên nhựt', 'https://lh3.googleusercontent.com/a/ACg8ocJxVSoZLkHbaHj8WQQ6GsObHn6yvmm8vQXbM4x44P-Cay05Lm3N=s96-c', 1, '2025-12-08 06:30:14', '2025-12-08 06:30:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung_vaitro`
--

CREATE TABLE `nguoidung_vaitro` (
  `NguoiDungId` int(11) NOT NULL,
  `VaiTroId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung_vaitro`
--

INSERT INTO `nguoidung_vaitro` (`NguoiDungId`, `VaiTroId`) VALUES
(1, 1),
(2, 2),
(3, 3),
(5, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `noidungbaihoc`
--

CREATE TABLE `noidungbaihoc` (
  `NoiDungId` int(11) NOT NULL,
  `BaiHocId` int(11) NOT NULL,
  `LoaiNoiDung` enum('heading','subheading','paragraph','image','video','quote','list') NOT NULL,
  `TieuDe` varchar(500) DEFAULT NULL,
  `NoiDung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ThuTu` int(11) DEFAULT 1,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `noidungbaihoc`
--

INSERT INTO `noidungbaihoc` (`NoiDungId`, `BaiHocId`, `LoaiNoiDung`, `TieuDe`, `NoiDung`, `ThuTu`, `TaoLuc`, `CapNhatLuc`) VALUES
(6, 2, 'heading', NULL, 'Cài đặt các công cụ cần thiết', 1, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(7, 2, 'paragraph', NULL, 'Trong bài này bạn sẽ được hướng dẫn cài đặt phần mềm và công cụ hỗ trợ.', 2, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(8, 2, 'image', NULL, '/uploads/baihoc11_setup.png', 3, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(9, 2, 'subheading', NULL, 'Các bước cài đặt', 4, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(10, 2, 'list', NULL, '- Tải phần mềm\n- Cài đặt môi trường\n- Kiểm tra phiên bản', 5, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(11, 3, 'heading', NULL, 'Các khái niệm cơ bản', 1, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(12, 3, 'paragraph', NULL, 'Bạn sẽ học các khái niệm trọng tâm và áp dụng vào ví dụ đầu tiên.', 2, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(13, 3, 'image', NULL, '/uploads/baihoc11_basic.png', 3, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(14, 3, 'subheading', NULL, 'Ví dụ đầu tiên', 4, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(15, 3, 'list', NULL, '- Khởi tạo dự án\n- Viết đoạn mã đầu tiên\n- Chạy thử chương trình', 5, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(16, 4, 'heading', NULL, 'Thực hành: Xây dựng ứng dụng nhỏ', 1, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(17, 4, 'paragraph', NULL, 'Bạn sẽ áp dụng kiến thức đã học để xây dựng một ứng dụng thực tế.', 2, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(18, 4, 'image', NULL, '/uploads/baihoc11_app.png', 3, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(19, 4, 'subheading', NULL, 'Các bước thực hiện', 4, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(20, 4, 'list', NULL, '- Phân tích yêu cầu\n- Thiết kế giao diện\n- Hoàn thiện chức năng', 5, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(33, 1, 'heading', 'Các phần tử HTML', 'Một phần tử HTML được định nghĩa bởi thẻ mở, nội dung và thẻ đóng', 1, '2025-12-15 09:16:37', '2025-12-15 09:16:37'),
(34, 1, 'subheading', 'Các phần tử HTML', 'Phần tử HTML bao gồm tất cả mọi thứ từ thẻ mở đến thẻ đóng:\n\n<tagname> Nội dung ở đây ... </tagname>', 2, '2025-12-15 09:17:21', '2025-12-15 09:17:21'),
(35, 1, 'paragraph', 'Các phần tử HTML lồng nhau', 'Các phần tử HTML có thể lồng nhau (điều này có nghĩa là các phần tử có thể chứa các phần tử khác).\n\nTất cả các tài liệu HTML đều bao gồm các phần tử HTML lồng nhau.\n\nVí dụ sau đây chứa bốn phần tử HTML ( <html>, <body>, <h1> và <p>):', 3, '2025-12-15 09:17:44', '2025-12-15 09:17:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\NguoiDung', 1, 'api_token', '1b2ec20f3fb8db50851b1c695b8a555a6b5f56b40595bea0623d480f23cb70e7', '[\"*\"]', '2025-12-05 04:43:35', NULL, '2025-12-05 03:52:51', '2025-12-05 04:43:35'),
(3, 'App\\Models\\NguoiDung', 1, 'api_token', '4c78a087b2f5b7f9c51a47bab6913a5f5942e37f440cd794491806a21c7ae396', '[\"*\"]', '2025-12-07 07:47:31', NULL, '2025-12-05 10:55:00', '2025-12-07 07:47:31'),
(7, 'App\\Models\\NguoiDung', 4, 'google-auth-token', 'f4ca9b515c016f2f70be9e547c95d3f60bfaf82815bb0f1e40f9634009827c85', '[\"*\"]', NULL, NULL, '2025-12-07 23:20:06', '2025-12-07 23:20:06'),
(8, 'App\\Models\\NguoiDung', 5, 'google-auth-token', 'f6c06f8fcc834858ca701d979dd2b777e952d547a477d0f077b3db169cc531ba', '[\"*\"]', NULL, NULL, '2025-12-07 23:30:15', '2025-12-07 23:30:15'),
(12, 'App\\Models\\NguoiDung', 1, 'api_token', 'b8311e8d3616b6b871e021b50ec9dad26b2290dcca21ca24903470be304c3186', '[\"*\"]', '2025-12-19 11:07:28', NULL, '2025-12-15 00:09:11', '2025-12-19 11:07:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `traloi`
--

CREATE TABLE `traloi` (
  `TraLoiId` int(11) NOT NULL,
  `LanLamBaiId` int(11) NOT NULL,
  `CauHoiId` int(11) NOT NULL,
  `LuaChonIds` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`LuaChonIds`)),
  `TraLoiText` text DEFAULT NULL,
  `DungHaySai` tinyint(1) DEFAULT NULL,
  `ThoiGianGiay` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vaitro`
--

CREATE TABLE `vaitro` (
  `VaiTroId` int(11) NOT NULL,
  `MaVaiTro` varchar(50) NOT NULL,
  `TenVaiTro` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `MoTa` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vaitro`
--

INSERT INTO `vaitro` (`VaiTroId`, `MaVaiTro`, `TenVaiTro`, `MoTa`) VALUES
(1, 'ADMIN', 'Quản trị hệ thống', 'Người quản lý toàn bộ hệ thống'),
(2, 'STUDENT', 'Học viên', 'Người tham gia học các khóa'),
(3, 'TEACHER', 'Giáo viên', 'Người tạo và quản lý bài giảng');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `baihoc`
--
ALTER TABLE `baihoc`
  ADD PRIMARY KEY (`BaiHocId`),
  ADD UNIQUE KEY `KhoaHocId` (`KhoaHocId`,`ThuTu`);

--
-- Chỉ mục cho bảng `baikiemtra`
--
ALTER TABLE `baikiemtra`
  ADD PRIMARY KEY (`BaiKiemTraId`),
  ADD KEY `KhoaHocId` (`KhoaHocId`),
  ADD KEY `BaiHocId` (`BaiHocId`);

--
-- Chỉ mục cho bảng `cauhoi`
--
ALTER TABLE `cauhoi`
  ADD PRIMARY KEY (`CauHoiId`),
  ADD UNIQUE KEY `NganHangId` (`NganHangId`,`ThuTu`),
  ADD KEY `BaiHocId` (`BaiHocId`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `ghidanh`
--
ALTER TABLE `ghidanh`
  ADD PRIMARY KEY (`NguoiDungId`,`KhoaHocId`),
  ADD KEY `KhoaHocId` (`KhoaHocId`),
  ADD KEY `BaiHocCuoiCungId` (`BaiHocCuoiCungId`);

--
-- Chỉ mục cho bảng `khoahoc`
--
ALTER TABLE `khoahoc`
  ADD PRIMARY KEY (`KhoaHocId`);

--
-- Chỉ mục cho bảng `kynang`
--
ALTER TABLE `kynang`
  ADD PRIMARY KEY (`KyNangId`),
  ADD UNIQUE KEY `TenKyNang` (`TenKyNang`);

--
-- Chỉ mục cho bảng `kynang_khoahoc`
--
ALTER TABLE `kynang_khoahoc`
  ADD PRIMARY KEY (`KyNangId`,`KhoaHocId`),
  ADD KEY `KhoaHocId` (`KhoaHocId`);

--
-- Chỉ mục cho bảng `lanlambai`
--
ALTER TABLE `lanlambai`
  ADD PRIMARY KEY (`LanLamBaiId`),
  ADD KEY `BaiKiemTraId` (`BaiKiemTraId`),
  ADD KEY `NguoiDungId` (`NguoiDungId`);

--
-- Chỉ mục cho bảng `luachon`
--
ALTER TABLE `luachon`
  ADD PRIMARY KEY (`LuaChonId`),
  ADD UNIQUE KEY `CauHoiId` (`CauHoiId`,`ThuTu`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `nganhangcauhoi`
--
ALTER TABLE `nganhangcauhoi`
  ADD PRIMARY KEY (`NganHangId`),
  ADD KEY `KhoaHocId` (`KhoaHocId`);

--
-- Chỉ mục cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`NguoiDungId`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `UX_GoogleId` (`GoogleId`);

--
-- Chỉ mục cho bảng `nguoidung_vaitro`
--
ALTER TABLE `nguoidung_vaitro`
  ADD PRIMARY KEY (`NguoiDungId`,`VaiTroId`),
  ADD KEY `VaiTroId` (`VaiTroId`);

--
-- Chỉ mục cho bảng `noidungbaihoc`
--
ALTER TABLE `noidungbaihoc`
  ADD PRIMARY KEY (`NoiDungId`),
  ADD KEY `IX_NoiDung_BaiHoc` (`BaiHocId`),
  ADD KEY `IX_NoiDung_ThuTu` (`ThuTu`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `traloi`
--
ALTER TABLE `traloi`
  ADD PRIMARY KEY (`TraLoiId`),
  ADD KEY `LanLamBaiId` (`LanLamBaiId`),
  ADD KEY `CauHoiId` (`CauHoiId`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Chỉ mục cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  ADD PRIMARY KEY (`VaiTroId`),
  ADD UNIQUE KEY `MaVaiTro` (`MaVaiTro`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `baihoc`
--
ALTER TABLE `baihoc`
  MODIFY `BaiHocId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `baikiemtra`
--
ALTER TABLE `baikiemtra`
  MODIFY `BaiKiemTraId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `cauhoi`
--
ALTER TABLE `cauhoi`
  MODIFY `CauHoiId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khoahoc`
--
ALTER TABLE `khoahoc`
  MODIFY `KhoaHocId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `kynang`
--
ALTER TABLE `kynang`
  MODIFY `KyNangId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lanlambai`
--
ALTER TABLE `lanlambai`
  MODIFY `LanLamBaiId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT cho bảng `luachon`
--
ALTER TABLE `luachon`
  MODIFY `LuaChonId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=548;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `nganhangcauhoi`
--
ALTER TABLE `nganhangcauhoi`
  MODIFY `NganHangId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `NguoiDungId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `noidungbaihoc`
--
ALTER TABLE `noidungbaihoc`
  MODIFY `NoiDungId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `traloi`
--
ALTER TABLE `traloi`
  MODIFY `TraLoiId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=234;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  MODIFY `VaiTroId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `baihoc`
--
ALTER TABLE `baihoc`
  ADD CONSTRAINT `baihoc_ibfk_1` FOREIGN KEY (`KhoaHocId`) REFERENCES `khoahoc` (`KhoaHocId`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `baikiemtra`
--
ALTER TABLE `baikiemtra`
  ADD CONSTRAINT `baikiemtra_ibfk_1` FOREIGN KEY (`KhoaHocId`) REFERENCES `khoahoc` (`KhoaHocId`) ON DELETE CASCADE,
  ADD CONSTRAINT `baikiemtra_ibfk_2` FOREIGN KEY (`BaiHocId`) REFERENCES `baihoc` (`BaiHocId`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cauhoi`
--
ALTER TABLE `cauhoi`
  ADD CONSTRAINT `cauhoi_ibfk_1` FOREIGN KEY (`NganHangId`) REFERENCES `nganhangcauhoi` (`NganHangId`) ON DELETE CASCADE,
  ADD CONSTRAINT `cauhoi_ibfk_2` FOREIGN KEY (`BaiHocId`) REFERENCES `baihoc` (`BaiHocId`);

--
-- Các ràng buộc cho bảng `ghidanh`
--
ALTER TABLE `ghidanh`
  ADD CONSTRAINT `ghidanh_ibfk_1` FOREIGN KEY (`NguoiDungId`) REFERENCES `nguoidung` (`NguoiDungId`) ON DELETE CASCADE,
  ADD CONSTRAINT `ghidanh_ibfk_2` FOREIGN KEY (`KhoaHocId`) REFERENCES `khoahoc` (`KhoaHocId`) ON DELETE CASCADE,
  ADD CONSTRAINT `ghidanh_ibfk_3` FOREIGN KEY (`BaiHocCuoiCungId`) REFERENCES `baihoc` (`BaiHocId`);

--
-- Các ràng buộc cho bảng `kynang_khoahoc`
--
ALTER TABLE `kynang_khoahoc`
  ADD CONSTRAINT `kynang_khoahoc_ibfk_1` FOREIGN KEY (`KyNangId`) REFERENCES `kynang` (`KyNangId`) ON DELETE CASCADE,
  ADD CONSTRAINT `kynang_khoahoc_ibfk_2` FOREIGN KEY (`KhoaHocId`) REFERENCES `khoahoc` (`KhoaHocId`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `lanlambai`
--
ALTER TABLE `lanlambai`
  ADD CONSTRAINT `lanlambai_ibfk_1` FOREIGN KEY (`BaiKiemTraId`) REFERENCES `baikiemtra` (`BaiKiemTraId`) ON DELETE CASCADE,
  ADD CONSTRAINT `lanlambai_ibfk_2` FOREIGN KEY (`NguoiDungId`) REFERENCES `nguoidung` (`NguoiDungId`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `luachon`
--
ALTER TABLE `luachon`
  ADD CONSTRAINT `luachon_ibfk_1` FOREIGN KEY (`CauHoiId`) REFERENCES `cauhoi` (`CauHoiId`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nganhangcauhoi`
--
ALTER TABLE `nganhangcauhoi`
  ADD CONSTRAINT `nganhangcauhoi_ibfk_1` FOREIGN KEY (`KhoaHocId`) REFERENCES `khoahoc` (`KhoaHocId`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nguoidung_vaitro`
--
ALTER TABLE `nguoidung_vaitro`
  ADD CONSTRAINT `nguoidung_vaitro_ibfk_1` FOREIGN KEY (`NguoiDungId`) REFERENCES `nguoidung` (`NguoiDungId`) ON DELETE CASCADE,
  ADD CONSTRAINT `nguoidung_vaitro_ibfk_2` FOREIGN KEY (`VaiTroId`) REFERENCES `vaitro` (`VaiTroId`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `noidungbaihoc`
--
ALTER TABLE `noidungbaihoc`
  ADD CONSTRAINT `noidungbaihoc_ibfk_1` FOREIGN KEY (`BaiHocId`) REFERENCES `baihoc` (`BaiHocId`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `traloi`
--
ALTER TABLE `traloi`
  ADD CONSTRAINT `traloi_ibfk_1` FOREIGN KEY (`LanLamBaiId`) REFERENCES `lanlambai` (`LanLamBaiId`) ON DELETE CASCADE,
  ADD CONSTRAINT `traloi_ibfk_2` FOREIGN KEY (`CauHoiId`) REFERENCES `cauhoi` (`CauHoiId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
