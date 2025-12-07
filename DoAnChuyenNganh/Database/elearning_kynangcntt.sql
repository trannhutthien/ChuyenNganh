-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th12 07, 2025 lúc 07:06 AM
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
  `ThuTu` int(11) DEFAULT 1,
  `TrangThai` tinyint(4) DEFAULT 2,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `baihoc`
--

INSERT INTO `baihoc` (`BaiHocId`, `KhoaHocId`, `TieuDe`, `ThuTu`, `TrangThai`, `TaoLuc`, `CapNhatLuc`) VALUES
(1, 11, 'Giới thiệu khóa học và các kiến thức nền tảng', 1, 2, '2025-12-05 17:29:38', '2025-12-05 17:29:38'),
(2, 11, 'Cài đặt môi trường và công cụ cần thiết', 2, 2, '2025-12-05 17:29:38', '2025-12-05 17:29:38'),
(3, 11, 'Các khái niệm cơ bản và ví dụ thực hành đầu tiên', 3, 2, '2025-12-05 17:29:38', '2025-12-05 17:29:38'),
(4, 11, 'Thực hành bài tập ứng dụng nhỏ', 4, 2, '2025-12-05 17:29:38', '2025-12-05 17:29:38'),
(5, 11, 'Tổng kết chương và bài tập ôn luyện', 5, 2, '2025-12-05 17:29:38', '2025-12-05 17:29:38');

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
(1, 11, NULL, 'Bài kiểm tra cuối khóa – Khóa học ID 11', '{\"tongSoCau\": 10, \"thoiGian\": 15, \"randomCauHoi\": true, \"randomLuaChon\": true, \"moTa\": \"Bài kiểm tra gồm 10 câu trắc nghiệm tổng hợp kiến thức toàn khóa\"}', 7.00, 2, '2025-12-05 17:37:32', '2025-12-05 17:37:32');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cauhoi`
--

CREATE TABLE `cauhoi` (
  `CauHoiId` int(11) NOT NULL,
  `NganHangId` int(11) NOT NULL,
  `BaiHocId` int(11) DEFAULT NULL,
  `Loai` enum('MOT_DAP_AN','NHIEU_DAP_AN','DUNG_SAI','DIEN_KHUYET') NOT NULL,
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
(1, 1, NULL, 'MOT_DAP_AN', 'Thẻ HTML nào dùng để tạo tiêu đề lớn nhất?', 'Vì <h1> là thẻ tiêu đề lớn nhất.', 1, 'html,heading', 1),
(2, 1, NULL, 'MOT_DAP_AN', 'Thuộc tính nào trong HTML dùng để đặt đường dẫn ảnh?', 'Vì src là thuộc tính chứa đường dẫn ảnh.', 1, 'html,image', 2),
(3, 1, NULL, 'MOT_DAP_AN', 'CSS viết tắt của từ gì?', 'Vì CSS viết tắt của Cascading Style Sheets.', 1, 'css,basic', 3),
(4, 1, NULL, 'MOT_DAP_AN', 'Thẻ nào dùng để xuống dòng trong HTML?', 'Vì <br> dùng để xuống dòng.', 1, 'html,br', 4),
(5, 1, NULL, 'MOT_DAP_AN', 'Thẻ HTML nào chứa toàn bộ phần nội dung hiển thị?', 'Vì nội dung trang nằm trong thẻ <body>.', 1, 'html,body', 5),
(6, 1, NULL, 'MOT_DAP_AN', 'Thuộc tính nào để đổi màu chữ trong CSS?', 'Vì thuộc tính color dùng để đổi màu chữ.', 1, 'css,color', 6),
(7, 1, NULL, 'MOT_DAP_AN', 'Đơn vị px trong CSS dùng để làm gì?', 'Vì px là đơn vị kích thước cố định.', 1, 'css,unit', 7),
(8, 1, NULL, 'MOT_DAP_AN', 'Thẻ HTML nào dùng để tạo danh sách không thứ tự?', 'Vì <ul> tạo danh sách không thứ tự.', 1, 'html,list', 8),
(9, 1, NULL, 'MOT_DAP_AN', 'File CSS thường có phần mở rộng là gì?', 'Vì file CSS có phần mở rộng .css.', 1, 'css,file', 9),
(10, 1, NULL, 'MOT_DAP_AN', 'Thuộc tính font-size dùng để làm gì?', 'Vì font-size dùng để thay đổi kích thước chữ.', 1, 'css,font', 10),
(11, 2, NULL, 'MOT_DAP_AN', 'Thuộc tính nào dùng để căn giữa văn bản trong CSS?', 'Vì text-align:center căn giữa văn bản.', 2, 'css,text', 1),
(12, 2, NULL, 'MOT_DAP_AN', 'Thẻ <a> trong HTML dùng để làm gì?', 'Vì <a> được dùng để tạo liên kết.', 2, 'html,link', 2),
(13, 2, NULL, 'MOT_DAP_AN', 'Box model trong CSS bao gồm những thành phần nào?', 'Vì box model gồm margin, border, padding và content.', 2, 'css,boxmodel', 3),
(14, 2, NULL, 'MOT_DAP_AN', 'Thuộc tính nào thay đổi kích thước chiều rộng của phần tử?', 'Vì width dùng để đặt chiều rộng phần tử.', 2, 'css,width', 4),
(15, 2, NULL, 'MOT_DAP_AN', 'display: flex dùng để làm gì?', 'Vì flex giúp sắp xếp phần tử theo trục.', 2, 'css,flex', 5),
(16, 2, NULL, 'MOT_DAP_AN', 'Thuộc tính href trong thẻ <a> để làm gì?', 'Vì href chứa địa chỉ URL cần liên kết.', 2, 'html,href', 6),
(17, 2, NULL, 'MOT_DAP_AN', 'CSS selector .menu li chọn đối tượng nào?', 'Vì selector .menu li chọn tất cả thẻ li trong .menu.', 2, 'css,selector', 7),
(18, 2, NULL, 'MOT_DAP_AN', 'Thuộc tính border-radius dùng để làm gì?', 'Vì border-radius dùng để bo góc phần tử.', 2, 'css,border', 8),
(19, 2, NULL, 'MOT_DAP_AN', 'Thẻ <strong> trong HTML mang ý nghĩa gì?', 'Vì <strong> thể hiện nội dung được nhấn mạnh.', 2, 'html,strong', 9),
(20, 2, NULL, 'MOT_DAP_AN', 'Thuộc tính nào trong CSS dùng để thay đổi nền?', 'Vì background-color thay đổi màu nền.', 2, 'css,background', 10),
(21, 3, NULL, 'MOT_DAP_AN', 'Flexbox: thuộc tính justify-content dùng cho trục nào?', 'Vì justify-content tác động lên trục chính của flex.', 3, 'css,flex', 1),
(22, 3, NULL, 'MOT_DAP_AN', 'HTML5 giới thiệu thẻ nào để định nghĩa khu vực nội dung?', 'Vì <section> là thẻ semantic cho khu vực nội dung.', 3, 'html,section', 2),
(23, 3, NULL, 'MOT_DAP_AN', 'Grid layout sử dụng thuộc tính nào để chia cột?', 'Vì grid-template-columns dùng để chia cột trong Grid.', 3, 'css,grid', 3),
(24, 3, NULL, 'MOT_DAP_AN', 'Sự khác nhau giữa margin và padding?', 'Vì margin ở ngoài border còn padding ở trong.', 3, 'css,spacing', 4),
(25, 3, NULL, 'MOT_DAP_AN', 'Thuộc tính position: absolute phụ thuộc vào gì?', 'Vì absolute phụ thuộc phần tử cha có position không phải static.', 3, 'css,position', 5),
(26, 3, NULL, 'MOT_DAP_AN', 'SEO-friendly HTML nên dùng thẻ nào để mô tả nội dung chính?', 'Vì <main> mô tả phần nội dung chính của trang.', 3, 'html,seo', 6),
(27, 3, NULL, 'MOT_DAP_AN', 'Pseudo-class :hover dùng khi nào?', 'Vì :hover kích hoạt khi người dùng rê chuột vào phần tử.', 3, 'css,pseudo', 7),
(28, 3, NULL, 'MOT_DAP_AN', 'Cơ chế cascade trong CSS có ý nghĩa gì?', 'Vì cascade quyết định thứ tự quy tắc CSS được áp dụng.', 3, 'css,cascade', 8),
(29, 3, NULL, 'MOT_DAP_AN', 'HTML semantic giúp cải thiện điều gì?', 'Vì semantic HTML giúp cải thiện khả năng hiểu nội dung của trình duyệt và SEO.', 3, 'html,semantic', 9),
(30, 3, NULL, 'MOT_DAP_AN', 'Thuộc tính z-index hoạt động khi nào?', 'Vì z-index chỉ hoạt động khi phần tử có position khác static.', 3, 'css,zindex', 10),
(31, 3, NULL, 'MOT_DAP_AN', 'Thuộc tính nào dùng để thay đổi kiểu đường viền của phần tử?', 'Thuộc tính border-style cho phép đặt kiểu đường viền như solid, dashed, dotted.', 1, 'css,border', 11),
(32, 3, NULL, 'MOT_DAP_AN', 'Thuộc tính nào dùng để căn giữa văn bản theo chiều ngang?', 'text-align dùng để căn lề văn bản: left, right, center, justify.', 1, 'css,text', 12),
(33, 3, NULL, 'MOT_DAP_AN', 'Giá trị nào của display giúp tạo bố cục linh hoạt theo trục chính?', 'display: flex giúp tạo bố cục linh hoạt và dễ căn chỉnh.', 2, 'css,flexbox', 13),
(34, 3, NULL, 'MOT_DAP_AN', 'Thuộc tính nào dùng để chia một phần tử thành nhiều cột văn bản?', 'Thuộc tính column-count cho phép chia văn bản thành nhiều cột.', 2, 'css,layout', 14),
(35, 3, NULL, 'MOT_DAP_AN', 'Trong CSS Grid, thuộc tính nào dùng để thiết lập số cột?', 'grid-template-columns định nghĩa số cột và kích thước từng cột.', 3, 'css,grid', 15);

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
(2, 'JavaScript Nền Tảng', '/images/js.jpg', 'Nắm vững các khái niệm JS căn bản và thực hành nhiều bài tập.', 2, 'javascript,frontend', 'HTML & CSS Cơ Bản', 299000.00, 2, '2025-12-05 16:09:58', '2025-12-05 16:09:58'),
(3, 'Lập Trình PHP & MySQL', '/images/phpmysql.jpg', 'Xây dựng website động bằng PHP và MySQL.', 2, 'php,mysql,backend', NULL, 399000.00, 2, '2025-12-05 16:09:58', '2025-12-05 16:09:58'),
(4, 'Laravel Framework Cơ Bản', '/images/laravel.jpg', 'Lập trình web hiện đại theo mô hình MVC.', 3, 'laravel,backend', 'PHP cơ bản', 499000.00, 2, '2025-12-05 16:09:58', '2025-12-05 16:09:58'),
(5, 'VueJS 3 Từ A-Z', '/images/vue3.jpg', 'Xây dựng SPA bằng Vue 3 và Composition API.', 3, 'vuejs,frontend', 'JavaScript cơ bản', 499000.00, 2, '2025-12-05 16:09:58', '2025-12-05 16:09:58'),
(6, 'Tin Học Văn Phòng Cơ Bản', '/images/office.jpg', 'Hướng dẫn Word, Excel, PowerPoint cho người mới.', 1, 'excel,word,powerpoint', NULL, 0.00, 2, '2025-12-05 16:09:58', '2025-12-05 16:09:58'),
(7, 'Giới Thiệu Về Lập Trình', '/images/programming.jpg', 'Khóa nhập môn dành cho người chưa biết gì về CNTT.', 1, 'coding,beginner', NULL, 0.00, 2, '2025-12-05 16:09:58', '2025-12-05 16:09:58'),
(8, 'Kỹ Năng Tìm Kiếm Google', '/images/google.jpg', 'Học cách sử dụng Google hiệu quả.', 1, 'google,research', NULL, 0.00, 2, '2025-12-05 16:09:58', '2025-12-05 16:09:58'),
(9, 'Tư Duy Lập Trình Logic', '/images/logic.jpg', 'Xây dựng tư duy giải thuật cho người mới học.', 1, 'logic,thinking', NULL, 0.00, 2, '2025-12-05 16:09:58', '2025-12-05 16:09:58'),
(10, 'HTML Căn Bản Cho Người Mới', '/images/html.jpg', 'Khóa học HTML miễn phí dành cho người bắt đầu.', 1, 'html,web', NULL, 0.00, 2, '2025-12-05 16:09:58', '2025-12-05 16:09:58'),
(11, 'HTML & CSS Cơ Bản', '/images/htmlcss.jpg', 'Học từ con số 0 để xây giao diện web', 1, 'html,css,frontend', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(12, 'JavaScript Nâng Cao', '/images/js_advanced.jpg', 'Làm chủ ngôn ngữ JavaScript với các khái niệm nâng cao', 3, 'javascript,frontend', 'HTML & CSS Cơ Bản', 299000.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(13, 'Lập trình PHP & MySQL', '/images/phpmysql.jpg', 'Xây dựng website động bằng PHP kết hợp MySQL', 2, 'php,mysql,backend', NULL, 399000.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(14, 'Laravel Framework Cơ Bản', '/images/laravel.jpg', 'Lập trình web theo mô hình MVC với Laravel', 3, 'laravel,backend', 'PHP Cơ Bản', 499000.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(15, 'VueJS 3 Từ A-Z', '/images/vue3.jpg', 'Xây dựng SPA bằng VueJS 3 và Composition API', 3, 'vue3,frontend', 'JavaScript Cơ Bản', 499000.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(16, 'Tin Học Văn Phòng', '/images/office.jpg', 'Học Word, Excel, PowerPoint cho người mới', 1, 'excel,word,powerpoint', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(17, 'Giới Thiệu Về Lập Trình', '/images/programming_intro.jpg', 'Khóa học căn bản dành cho người mới học lập trình', 1, 'coding,beginner', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(18, 'Kỹ Năng Tìm Kiếm Google', '/images/google_skill.jpg', 'Học cách sử dụng Google hiệu quả', 1, 'google,research', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(19, 'Tư Duy Lập Trình Logic', '/images/logic.jpg', 'Xây dựng tư duy giải thuật cơ bản', 1, 'logic,thinking', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02'),
(20, 'HTML Căn Bản Cho Người Mới', '/images/html_basic.jpg', 'Khóa học HTML miễn phí cho người mới học lập trình', 1, 'html,web', NULL, 0.00, 1, '2025-12-05 16:38:02', '2025-12-05 16:38:02');

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

--
-- Đang đổ dữ liệu cho bảng `lanlambai`
--

INSERT INTO `lanlambai` (`LanLamBaiId`, `BaiKiemTraId`, `NguoiDungId`, `BatDauLuc`, `NopBaiLuc`, `DiemSo`, `ChiTietJson`, `TrangThai`) VALUES
(8, 1, 1, '2025-12-05 18:22:36', NULL, NULL, '{\"cauHoiIds\":[5,8,26,33,23,30,2,35,17,19],\"soCauHoi\":10,\"thietLap\":{\"tongSoCau\":10,\"thoiGian\":15,\"randomCauHoi\":true,\"randomLuaChon\":true,\"moTa\":\"B\\u00e0i ki\\u1ec3m tra g\\u1ed3m 10 c\\u00e2u tr\\u1eafc nghi\\u1ec7m t\\u1ed5ng h\\u1ee3p ki\\u1ebfn th\\u1ee9c to\\u00e0n kh\\u00f3a\"}}', 'CANCELLED'),
(9, 1, 1, '2025-12-05 18:25:00', NULL, NULL, '{\"cauHoiIds\":[25,6,28,20,8,4,33,29,13,3],\"soCauHoi\":10,\"thietLap\":{\"tongSoCau\":10,\"thoiGian\":15,\"randomCauHoi\":true,\"randomLuaChon\":true,\"moTa\":\"B\\u00e0i ki\\u1ec3m tra g\\u1ed3m 10 c\\u00e2u tr\\u1eafc nghi\\u1ec7m t\\u1ed5ng h\\u1ee3p ki\\u1ebfn th\\u1ee9c to\\u00e0n kh\\u00f3a\"}}', 'CANCELLED'),
(10, 1, 1, '2025-12-05 18:31:44', NULL, NULL, '{\"cauHoiIds\":[23,1,27,33,32,3,21,4,15,11],\"soCauHoi\":10,\"thietLap\":{\"tongSoCau\":10,\"thoiGian\":15,\"randomCauHoi\":true,\"randomLuaChon\":true,\"moTa\":\"B\\u00e0i ki\\u1ec3m tra g\\u1ed3m 10 c\\u00e2u tr\\u1eafc nghi\\u1ec7m t\\u1ed5ng h\\u1ee3p ki\\u1ebfn th\\u1ee9c to\\u00e0n kh\\u00f3a\"}}', 'INPROGRESS');

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
(1, 1, '<h1>', 1, 1),
(2, 1, '<h3>', 0, 2),
(3, 1, '<h6>', 0, 3),
(4, 2, 'src', 1, 1),
(5, 2, 'href', 0, 2),
(6, 2, 'alt', 0, 3),
(7, 3, 'Cascading Style Sheets', 1, 1),
(8, 3, 'Creative Style System', 0, 2),
(9, 3, 'Color Styling Source', 0, 3),
(10, 4, '<br>', 1, 1),
(11, 4, '<p>', 0, 2),
(12, 4, '<hr>', 0, 3),
(13, 5, '<body>', 1, 1),
(14, 5, '<main>', 0, 2),
(15, 5, '<section>', 0, 3),
(16, 6, 'color', 1, 1),
(17, 6, 'font-weight', 0, 2),
(18, 6, 'background-size', 0, 3),
(19, 7, 'px', 1, 1),
(20, 7, '%', 0, 2),
(21, 7, 'em', 0, 3),
(22, 8, '<ul>', 1, 1),
(23, 8, '<li>', 0, 2),
(24, 8, '<ol>', 0, 3),
(25, 9, '.css', 1, 1),
(26, 9, '.html', 0, 2),
(27, 9, '.js', 0, 3),
(28, 10, 'font-size', 1, 1),
(29, 10, 'font-style', 0, 2),
(30, 10, 'font-family', 0, 3),
(31, 11, 'text-align: center;', 1, 1),
(32, 11, 'justify-content: center;', 0, 2),
(33, 11, 'align-items: center;', 0, 3),
(370, 16, 'href chứa URL cần liên kết', 1, 1),
(371, 16, 'src chứa nội dung văn bản', 0, 2),
(372, 16, 'alt mô tả hình ảnh', 0, 3),
(373, 17, 'Chọn đối tượng trong menu', 1, 1),
(374, 17, 'Định dạng màu nền', 0, 2),
(375, 17, 'Tạo hiệu ứng chuyển động', 0, 3),
(376, 18, 'Bo góc phần tử', 1, 1),
(377, 18, 'Tăng kích thước chữ', 0, 2),
(378, 18, 'Tạo khoảng cách dòng', 0, 3),
(379, 19, 'Nhấn mạnh nội dung', 1, 1),
(380, 19, 'Ẩn phần tử HTML', 0, 2),
(381, 19, 'Căn giữa khối', 0, 3),
(382, 20, 'Thay đổi màu nền', 1, 1),
(383, 20, 'Tạo viền khối', 0, 2),
(384, 20, 'Xác định font chữ', 0, 3),
(385, 21, 'Căn chỉnh theo trục chính', 1, 1),
(386, 21, 'Tạo bảng dữ liệu', 0, 2),
(387, 21, 'Thiết lập kích thước ảnh', 0, 3),
(388, 22, 'Xác định khu vực nội dung', 1, 1),
(389, 22, 'Căn giữa văn bản', 0, 2),
(390, 22, 'Ẩn phần tử khỏi trang', 0, 3),
(391, 23, 'Chia bố cục grid', 1, 1),
(392, 23, 'Thay đổi màu chữ', 0, 2),
(393, 23, 'Tạo menu xổ xuống', 0, 3),
(394, 24, 'margin và padding', 1, 1),
(395, 24, 'border và outline', 0, 2),
(396, 24, 'height và width', 0, 3),
(397, 25, 'absolute phụ thuộc phần tử cha', 1, 1),
(398, 25, 'relative cố định toàn trang', 0, 2),
(399, 25, 'fixed di chuyển theo cha', 0, 3),
(400, 26, 'Thân thiện SEO', 1, 1),
(401, 26, 'Dùng thay thế script', 0, 2),
(402, 26, 'Tạo hiệu ứng hover', 0, 3),
(403, 27, 'Kích hoạt khi hover', 1, 1),
(404, 27, 'Dùng để tạo Bộ chọn ID', 0, 2),
(405, 27, 'Cho phép đổi font chữ', 0, 3),
(406, 28, 'Tạo quy tắc kế thừa', 1, 1),
(407, 28, 'Tăng tốc độ xử lý', 0, 2),
(408, 28, 'Tạo hiệu ứng animation', 0, 3),
(409, 29, 'Cải thiện cấu trúc HTML', 1, 1),
(410, 29, 'Tăng tốc độ GPU', 0, 2),
(411, 29, 'Thay đổi giao diện phím', 0, 3),
(412, 30, 'Xác định tầng hiển thị', 1, 1),
(413, 30, 'Thay đổi màu chữ', 0, 2),
(414, 30, 'Căn chỉnh chữ in nghiêng', 0, 3),
(415, 31, 'Định dạng kiểu đường viền', 1, 1),
(416, 31, 'Tạo bóng cho chữ', 0, 2),
(417, 31, 'Đặt kích thước đoạn văn', 0, 3),
(418, 32, 'Căn trái hoặc phải phần tử', 1, 1),
(419, 32, 'Tăng độ đậm chữ', 0, 2),
(420, 32, 'Đổi màu chữ', 0, 3),
(421, 33, 'Tạo bố cục linh hoạt', 1, 1),
(422, 33, 'Tạo hiệu ứng xoay', 0, 2),
(423, 33, 'Tạo hiệu ứng mờ', 0, 3),
(424, 34, 'Đếm số phần tử con', 1, 1),
(425, 34, 'Căn giữa văn bản', 0, 2),
(426, 34, 'Xóa viền phần tử', 0, 3),
(484, 12, 'Tạo liên kết', 1, 1),
(485, 12, 'Chèn hình ảnh', 0, 2),
(486, 12, 'Tạo bảng', 0, 3),
(487, 13, 'margin, border, padding, content', 1, 1),
(488, 13, 'font, color, background', 0, 2),
(489, 13, 'grid, flex, block', 0, 3),
(490, 14, 'width', 1, 1),
(491, 14, 'height', 0, 2),
(492, 14, 'padding', 0, 3),
(493, 15, 'Sắp xếp phần tử theo trục', 1, 1),
(494, 15, 'Tạo bảng dữ liệu', 0, 2),
(495, 15, 'Tạo hiệu ứng animation', 0, 3);

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
(2, 11, 'Ngân hàng câu hỏi trung bình', 'Các câu hỏi mức độ trung bình, yêu cầu hiểu và vận dụng.', 2),
(3, 11, 'Ngân hàng câu hỏi nâng cao', 'Các câu hỏi mức độ khó, dành cho học viên muốn thử sức.', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `NguoiDungId` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
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

INSERT INTO `nguoidung` (`NguoiDungId`, `Email`, `MatKhauHash`, `HoTen`, `AvatarUrl`, `TrangThai`, `TaoLuc`, `CapNhatLuc`) VALUES
(1, 'admin@example.com', 'password123', 'Admin Hệ Thống', NULL, 1, '2025-12-05 17:42:44', '2025-12-06 00:54:36'),
(2, 'nhutthien@example.com', '123456', 'Nhựt Thiên', NULL, 1, '2025-12-05 17:42:44', '2025-12-05 17:45:51'),
(3, 'danthanh@example.com', '123456', 'Đan Thanh', NULL, 1, '2025-12-05 17:42:44', '2025-12-05 17:45:51');

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
(3, 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `noidungbaihoc`
--

CREATE TABLE `noidungbaihoc` (
  `NoiDungId` int(11) NOT NULL,
  `BaiHocId` int(11) NOT NULL,
  `LoaiNoiDung` enum('heading','subheading','paragraph','image','video','quote','list') NOT NULL,
  `NoiDung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ThuTu` int(11) DEFAULT 1,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `noidungbaihoc`
--

INSERT INTO `noidungbaihoc` (`NoiDungId`, `BaiHocId`, `LoaiNoiDung`, `NoiDung`, `ThuTu`, `TaoLuc`, `CapNhatLuc`) VALUES
(1, 1, 'heading', 'Giới thiệu về khóa học', 1, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(2, 1, 'paragraph', 'Khóa học giúp bạn nắm vững các kiến thức nền tảng và chuẩn bị cho các chương tiếp theo.', 2, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(3, 1, 'image', '/uploads/baihoc11_intro.png', 3, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(4, 1, 'subheading', 'Mục tiêu bài học', 4, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(5, 1, 'list', '- Hiểu tổng quan khóa học\n- Nắm mục tiêu học tập\n- Chuẩn bị môi trường học', 5, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(6, 2, 'heading', 'Cài đặt các công cụ cần thiết', 1, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(7, 2, 'paragraph', 'Trong bài này bạn sẽ được hướng dẫn cài đặt phần mềm và công cụ hỗ trợ.', 2, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(8, 2, 'image', '/uploads/baihoc11_setup.png', 3, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(9, 2, 'subheading', 'Các bước cài đặt', 4, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(10, 2, 'list', '- Tải phần mềm\n- Cài đặt môi trường\n- Kiểm tra phiên bản', 5, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(11, 3, 'heading', 'Các khái niệm cơ bản', 1, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(12, 3, 'paragraph', 'Bạn sẽ học các khái niệm trọng tâm và áp dụng vào ví dụ đầu tiên.', 2, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(13, 3, 'image', '/uploads/baihoc11_basic.png', 3, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(14, 3, 'subheading', 'Ví dụ đầu tiên', 4, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(15, 3, 'list', '- Khởi tạo dự án\n- Viết đoạn mã đầu tiên\n- Chạy thử chương trình', 5, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(16, 4, 'heading', 'Thực hành: Xây dựng ứng dụng nhỏ', 1, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(17, 4, 'paragraph', 'Bạn sẽ áp dụng kiến thức đã học để xây dựng một ứng dụng thực tế.', 2, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(18, 4, 'image', '/uploads/baihoc11_app.png', 3, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(19, 4, 'subheading', 'Các bước thực hiện', 4, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(20, 4, 'list', '- Phân tích yêu cầu\n- Thiết kế giao diện\n- Hoàn thiện chức năng', 5, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(21, 5, 'heading', 'Tổng kết bài học', 1, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(22, 5, 'paragraph', 'Bài học tổng kết toàn bộ kiến thức và định hướng cho chương tiếp theo.', 2, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(23, 5, 'image', '/uploads/baihoc11_summary.png', 3, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(24, 5, 'subheading', 'Bạn đã học được gì?', 4, '2025-12-05 17:34:22', '2025-12-05 17:34:22'),
(25, 5, 'list', '- Nắm kiến thức nền tảng\n- Biết cách cài đặt môi trường\n- Thực hành xây dựng ứng dụng nhỏ', 5, '2025-12-05 17:34:22', '2025-12-05 17:34:22');

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
(2, 'App\\Models\\NguoiDung', 1, 'api_token', '65133252c066b2ed4debf8bbb4b0b57ef44d27c8a7e5a36cb1c4b8e157056cef', '[\"*\"]', '2025-12-05 11:32:10', NULL, '2025-12-05 09:35:22', '2025-12-05 11:32:10'),
(3, 'App\\Models\\NguoiDung', 1, 'api_token', '4c78a087b2f5b7f9c51a47bab6913a5f5942e37f440cd794491806a21c7ae396', '[\"*\"]', '2025-12-05 11:30:09', NULL, '2025-12-05 10:55:00', '2025-12-05 11:30:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `traloi`
--

CREATE TABLE `traloi` (
  `TraLoiId` int(11) NOT NULL,
  `LanLamBaiId` int(11) NOT NULL,
  `CauHoiId` int(11) NOT NULL,
  `LuaChonIds` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`LuaChonIds`)),
  `DungHaySai` tinyint(1) DEFAULT NULL,
  `ThoiGianGiay` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `traloi`
--

INSERT INTO `traloi` (`TraLoiId`, `LanLamBaiId`, `CauHoiId`, `LuaChonIds`, `DungHaySai`, `ThoiGianGiay`) VALUES
(3, 9, 25, '[397]', NULL, 5),
(4, 10, 23, '[392,393,391]', NULL, 0),
(5, 10, 1, '[3]', NULL, 0);

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
  ADD UNIQUE KEY `Email` (`Email`);

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
  MODIFY `BaiHocId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `baikiemtra`
--
ALTER TABLE `baikiemtra`
  MODIFY `BaiKiemTraId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `cauhoi`
--
ALTER TABLE `cauhoi`
  MODIFY `CauHoiId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT cho bảng `khoahoc`
--
ALTER TABLE `khoahoc`
  MODIFY `KhoaHocId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `kynang`
--
ALTER TABLE `kynang`
  MODIFY `KyNangId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `lanlambai`
--
ALTER TABLE `lanlambai`
  MODIFY `LanLamBaiId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `luachon`
--
ALTER TABLE `luachon`
  MODIFY `LuaChonId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=496;

--
-- AUTO_INCREMENT cho bảng `nganhangcauhoi`
--
ALTER TABLE `nganhangcauhoi`
  MODIFY `NganHangId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `NguoiDungId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `noidungbaihoc`
--
ALTER TABLE `noidungbaihoc`
  MODIFY `NoiDungId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `traloi`
--
ALTER TABLE `traloi`
  MODIFY `TraLoiId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
