-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 21, 2026 at 12:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sales_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
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
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_11_17_000001_add_role_to_users_table', 1),
(5, '2026_11_17_000002_create_products_table', 1),
(6, '2026_11_17_000003_create_orders_tables', 1),
(7, '2026_11_18_000001_add_payment_and_profile_columns', 2),
(8, '2026_11_18_000002_align_statuses_and_remove_cod', 3),
(9, '2026_11_19_000001_add_promotion_to_products_table', 4),
(10, '2026_11_21_000001_add_shipping_to_orders_table', 5);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_no` varchar(30) NOT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_carrier` varchar(50) DEFAULT NULL,
  `payment_method` varchar(20) NOT NULL DEFAULT 'bank_transfer',
  `payment_status` varchar(20) NOT NULL DEFAULT 'unpaid',
  `payment_slip` varchar(255) DEFAULT NULL,
  `payment_note` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `tracking_no` varchar(100) DEFAULT NULL,
  `shipment_created_at` timestamp NULL DEFAULT NULL,
  `shipping_name` varchar(255) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_no`, `total`, `shipping_fee`, `shipping_carrier`, `payment_method`, `payment_status`, `payment_slip`, `payment_note`, `paid_at`, `status`, `tracking_no`, `shipment_created_at`, `shipping_name`, `shipping_phone`, `shipping_address`, `note`, `created_at`, `updated_at`) VALUES
(6, 3, 'ORD-261120-YV7EJ', 195.00, 0.00, 'Flash Express', 'bank_transfer', 'paid', 'slips/yCICRaHVneUQy2e9nZei5RA4mfT0UWdk79fl8J69.jpg', NULL, '2026-11-20 07:17:40', 'shipped', 'Flash TH1169503999', '2026-11-21 01:34:34', 'ลูกค้า ใจดี', '0800000003', '19 หมู่ 2 ต.ชุมพล อ.สทิงพระ จ.สงขลา', NULL, '2026-11-20 07:09:06', '2026-11-21 01:34:34'),
(7, 5, 'ORD-261120-LLDQP', 538.00, 0.00, 'Flash Express', 'promptpay', 'paid', 'slips/L6qQurMmg8uiZFkV09PW94ED70OLnYCUDirGClLC.jpg', NULL, '2026-11-20 07:17:22', 'shipped', 'Flash TH119923945', '2026-11-21 01:30:37', 'ลูกค้า ทดสอบระบบ', '0800000890', '43/1 หมู่ 2 ต.ปากะเเระ อ.ยะหริ่ง จ.ยะลา 11990', NULL, '2026-11-20 07:12:34', '2026-11-21 01:30:37');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `subtotal`, `created_at`, `updated_at`) VALUES
(10, 6, 25, 'โปเต้ มันฝรั่งกรอบ 52ก', 20.00, 2, 40.00, '2026-11-20 07:09:06', '2026-11-20 07:09:06'),
(11, 6, 21, 'ลักส์ ครีมอาบน้ำ ดิวอี้ โกลว์ (ซากุระ) 190 มล. (แพ็ก 3 ชิ้น)', 155.00, 1, 155.00, '2026-11-20 07:09:06', '2026-11-20 07:09:06'),
(12, 7, 14, 'ปลากระป๋องสามแม่ครัว ขนาด 155 กรัม (แพ็ค 10 กระป๋อง)', 300.00, 1, 300.00, '2026-11-20 07:12:34', '2026-11-20 07:12:34'),
(13, 7, 13, 'มาม่าบะหมี่ ต้มยำกุ้งน้ำข้น 55ก.X6', 40.00, 1, 40.00, '2026-11-20 07:12:34', '2026-11-20 07:12:34'),
(14, 7, 12, 'เบญจรงค์ ข้าวหอมมะลิ 100% 5 กก.', 198.00, 1, 198.00, '2026-11-20 07:12:34', '2026-11-20 07:12:34');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `promo_name` varchar(100) DEFAULT NULL,
  `discount_type` varchar(10) DEFAULT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `promo_start` date DEFAULT NULL,
  `promo_end` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `category`, `description`, `price`, `stock`, `promo_name`, `discount_type`, `discount_value`, `promo_start`, `promo_end`, `image`, `is_active`, `created_at`, `updated_at`) VALUES
(12, 'SKU-001', 'เบญจรงค์ ข้าวหอมมะลิ 100% 5 กก.', 'อาหารแห้ง', 'เบญจรงค์ ข้าวหอมมะลิ 100% คัดสรรจากเมล็ดข้าวหอมมะลิคุณภาพดีที่มีลักษณะเรียวยาวและสม่ำเสมอ เมื่อนำไปหุงจะได้เมล็ดข้าวเรียงตัวสวย พร้อมกลิ่นหอมอันเป็นเอกลักษณ์ ให้เนื้อสัมผัสนุ่มกำลังดีและคงรูปหลังการหุง\r\n\r\n- ชื่อสินค้า: เบญจรงค์ ข้าวหอมมะลิ 100% 5 กก.\r\n- แบรนด์: เบญจรงค์\r\n- ปริมาณบรรจุ: 1 หน่วย\r\n\r\nข้อมูลการเก็บรักษา: ควรเก็บในที่แห้งและเย็น และหลีกเลี่ยงแสงแดด\r\nข้อมูลจำเพาะ  - น้ำหนักรวมสุทธิ  25 x 8 x 44 cm, 5.03kg', 198.00, 39, NULL, NULL, NULL, NULL, NULL, 'products/BuhjS8JFn25fntT16Oz7e9nJP6cpmytrottFQYAb.png', 1, '2026-11-20 05:56:25', '2026-11-20 07:12:34'),
(13, 'SKU-002', 'มาม่าบะหมี่ ต้มยำกุ้งน้ำข้น 55ก.X6', 'อาหารแห้ง', 'มาม่าบะหมี่กึ่งสำเร็จรูป รสต้มยำกุ้งน้ำข้น เป็นผลิตภัณฑ์ที่ได้รับความนิยมอย่างสูงในหมวดหมู่อาหารกึ่งสำเร็จรูป ด้วยรสชาติเข้มข้นที่ผสมผสานระหว่างความเผ็ดและเปรี้ยวของต้มยำกุ้งน้ำข้น ทำให้คุณสามารถเพลิดเพลินกับรสชาติไทยแท้ได้อย่างง่ายดาย ผลิตภัณฑ์นี้มีธาตุเหล็กและวิตามินบี 1 ที่ช่วยเสริมสร้างสุขภาพที่ดี และจัดชุดมาในบรรจุภัณฑ์ 6 ซอง เหมาะสำหรับการบริโภคในครอบครัวหรือเก็บไว้สำหรับมื้ออาหารที่รวดเร็ว', 40.00, 19, NULL, NULL, NULL, NULL, NULL, 'products/zeFAgA0qwBVGU3dfrvl28FOX5X4HgrPEdKI1ZaBq.jpg', 1, '2026-11-20 06:02:55', '2026-11-20 07:12:34'),
(14, 'SKU-003', 'ปลากระป๋องสามแม่ครัว ขนาด 155 กรัม (แพ็ค 10 กระป๋อง)', 'อาหารแห้ง', 'ปลากระป๋องสามแม่ครัว ขนาด 155 กรัม (แพ็ค 10 กระป๋อง)\r\n\r\nยี่ห้อ: สามแม่ครัว\r\n- ปลากระป๋องในซอสมะเขือเทศ\r\n- ผ่านกระบวนการผลิตที่ได้มาตราฐาน\r\n- ใช้โมโนโซเดียดกลูตาเมตเป็นวัตถุปรุงแต่งอาหาร\r\n- หอม อร่อยน่ารับประทาน\r\n- ขนาด 155 กรัม\r\n- แพ็คละ 10 กระป๋อง', 300.00, 19, NULL, NULL, NULL, NULL, NULL, 'products/87hLYboQVSwZTJZGOrPJY46tOqE5zPxmG4oPZgCk.png', 1, '2026-11-20 06:08:57', '2026-11-20 07:12:34'),
(15, 'SKU-004', 'มรกต น้ำมันปาล์ม 1 ลิตร( 3 ขวด )', 'อาหารแห้ง', 'สกัดจากเนื้อปาล์ม 100%\r\nเป็นน้ำมันที่สามารถทนความร้อนได้ดี\r\nจำกัดจำนวนการซื้อ 12 ชุด ต่อวันและต่อหมายเลขสมาชิก หากเกินจำนวนที่กำหนด บริษัทขอสงวนสิทธิ์ในการยกเลิกการสั่งซื้อ แต้มสะสม รวมถึงสิทธิ์หรือประโยชน์อื่นๆอันเกิดจากการสั่งซื้อดังกล่าว พร้อมทั้งจะทำการคืนเงิน หรือวงเงินบัตรเครดิตให้กับท่านต่อไป\r\nสินค้ารายการนี้ไม่เข้าร่วมสิทธิหรือประโยชน์บางประการตามที่บริษัทฯ กำหนด อาทิ การสะสมแต้ม ALL POINT การเข้าร่วมกิจกรรมภารกิจพิชิตรางวัล โค้ดส่วนลดทุกประเภท และการแลกส่วนลดในอัตราพิเศษ', 156.00, 10, NULL, NULL, NULL, NULL, NULL, 'products/UOYkSHeRYU1FVwMlMhrrGUhgjaN4KyF3PdShFJZF.jpg', 1, '2026-11-20 06:13:34', '2026-11-20 06:13:34'),
(16, 'SKU-005', 'ภูเขาทอง ซอสปรุงรสฝาเขียว', 'อาหารแห้ง', 'ผลิตจากถั่วเหลืองคุณภาพดีและกรรมวิธีที่ทันสมัย ซึ่งในระหว่างการหมักจะได้น้ำตาลและกรดอะมิโนชนิดต่าง ๆ ทำให้ซอสปรุงรสมีกลิ่นหอมและรสชาติกลมกล่อมเฉพาะตัว มีส่วนประกอบจากซอสถั่วเหลือง 75% น้ำตาล 5% และเกลือ 4% เหมาะสำหรับเมนูหลากหลาย', 35.00, 14, NULL, NULL, NULL, NULL, NULL, 'products/2SaASP9es332QkpIY6eHa0cSbKsGxE6X1j5Joo8m.jpg', 1, '2026-11-20 06:18:51', '2026-11-20 06:18:51'),
(17, 'SKU-006', 'น้ำดื่มคริสตัล 600 มล. (แพ็ก 12 ขวด)', 'เครื่องดื่ม', 'น้ำดื่มคริสตัล 600 มล. (แพ็ก 12 ขวด)\r\nแบรนด์ : Crystal (คริสตัล)\r\nขนาด : 600 ml.\r\nอายุสินค้า : 1 ปี (นับจากวันผลิต)', 120.00, 20, NULL, NULL, NULL, NULL, NULL, 'products/4uYDYOoQXSMvHHJdFOhSru0377vWXb0mmcuFZf3r.jpg', 1, '2026-11-20 06:23:23', '2026-11-20 06:23:23'),
(18, 'SKU-007', 'นมสด โฟร์โมสต์ 225 มล. แพ็ค 6 กล่อง', 'เครื่องดื่ม', 'ประเภทสินค้า: นมสด UHT\r\nแบรนด์: โฟร์โมสต์\r\nรุ่น: รสจืด\r\nขนาด: 225 มล. แพ็ค 6 กล่อง\r\nคุณสมบัติ: โฟร์โมสต์ ผลิตจากนมสด 100% จากฟาร์มโคนมคุณภาพและได้มาตรฐานที่ผ่านการควบคุมกว่า 100 ขั้นตอน, แคลเซียมสูงเสริมสร้างกระดูกและฟันที่แข็งแรง, กรดอะมิโนจำเป็น 9 ชนิดและโปรตีนที่จำเป็นต่อร่างกาย, โอเมก้า 369 วิตามินบี12 บี5 บี2 สูง', 100.00, 10, NULL, NULL, NULL, NULL, NULL, 'products/GBAxxnxEGcpo5BzavXwNFcuPuqSBQcmeLUfzWMAJ.jpg', 1, '2026-11-20 06:25:31', '2026-11-20 06:25:31'),
(19, 'SKU-008', 'โค้ก น้ำอัดลม รสออริจินัล สูตรน้ำตาลน้อยกว่า 1 ล.', 'เครื่องดื่ม', 'โคคา-โคล่า โค้ก น้ำอัดลม รสออริจินัล สูตรน้ำตาลน้อยกว่า\r\nเติมความสุข มอบความซ่าและความสดชื่นได้ทุกๆ วัน ด้วย โค้ก น้ำอัดลม รสออริจินัล สูตรน้ำตาลน้อยกว่า เครื่องดื่มที่จะทำให้สดชื่น สนุก และอร่อย เพลิดเพลินไปกับรสชาติที่จะทำให้ช่วงเวลารับประทานอาหารและเครื่องดื่มพิเศษยิ่งขึ้น\r\n- โคคา-โคล่า โค้ก น้ำอัดลม รสออริจินัล สูตรน้ำตาลน้อยกว่า\r\n- หวานซ่า อร่อย สดชื่น แต่น้ำตาลน้อยกว่า\r\n- เพลิดเพลินไปกับรสชาติของความซ่าและความสดชื่นได้ในทุกๆ วัน\r\n- ทำให้ช่วงเวลารับประทานอาหารและเครื่องดื่มพิเศษยิ่งขึ้น', 30.00, 10, NULL, NULL, NULL, NULL, NULL, 'products/TJ9rcJTWBxqnVIkAYuf8GYgGi5FM5OtiXFiv7PML.jpg', 1, '2026-11-20 06:29:37', '2026-11-20 06:29:37'),
(20, 'SKU-009', 'เมน แอคโนไฟท์ แอนติ-แอคเน่ สครับ อิน โฟม', 'ของใช้ส่วนตัว', 'โฟมล้างหน้าแก้ปัญหาสิวสำหรับผู้ชายจากการ์นิเย่ ต่อสู้ปัญหาผิว 12 ประการในหลอดเดียว มีไมโครบีดส์เม็ดละเอียด อ่อนโยนต่อผิว ซาลิไซลิคแอซิดลดการสะสมของแบคทีเรีย สาเหตุของการเกิดสิว ลดเลือนจุดด่างดำ ลดความหมองคล้ำ คืนความกระจ่างใสให้ผิว', 89.00, 20, NULL, NULL, NULL, NULL, NULL, 'products/ETPkhoj8gnqkqcFdZ06KsWAluUEEVcKan3SIwS4C.jpg', 1, '2026-11-20 06:33:41', '2026-11-20 06:33:41'),
(21, 'SKU-010', 'ลักส์ ครีมอาบน้ำ ดิวอี้ โกลว์ (ซากุระ) 190 มล. (แพ็ก 3 ชิ้น)', 'ของใช้ส่วนตัว', 'ฟองครีมเนียนนุ่ม\r\nเผยผิวใสอมชมพู\r\nกลิ่นหอมละมุนของซากุระจากญี่ปุ่น', 155.00, 28, NULL, NULL, NULL, NULL, NULL, 'products/YWpyjlJvfB0vvrzcoNb9DM4yWZNv9rsKFGnGJw9A.jpg', 1, '2026-11-20 06:38:12', '2026-11-20 07:09:06'),
(22, 'SKU-011', 'ยาสีฟัน หมอจุฬา สูตรดั้งเดิม 100 กรัม', 'ของใช้ส่วนตัว', 'ตัวช่วยสำคัญที่ทำให้คุณมีช่องปากที่สะอาด หมอจุฬา เป็นยาสีฟันสมุนไพร สูตรดั้งเดิม ยาสีฟันคุณภาพดีที่เกิดจากภูมิปัญญาไทย ที่มีประสิทธิภาพในการเข้าช่วยดูแลสุขภาพเหงือก และฟันของคุณแข็งแรงลดอาการเสียวฟัน แก้ปัญหาเรื่องกลิ่นปากได้เป็นอย่างดี', 59.00, 5, NULL, NULL, NULL, NULL, NULL, 'products/uT4QRX1gpMGUZtpPDgdwjm3o38n9ctQsA2I3zJL0.png', 1, '2026-11-20 06:43:31', '2026-11-20 06:43:31'),
(23, 'SKU-012', 'ซันซิลแชมพูสมูทแอนด์เมเนจ 350มล.', 'ของใช้ส่วนตัว', 'อาร์แกน ออยล์ + คามิเลีย เอสเซนส์ ผมมีน้ำหนัก จัดทรงง่าย ผมจัดทรงง่ายขึ้น และหอมสดชื่นตั้งแต่ครั้งแรกที่ใช้ ให้ทุกวันของคุณจัดการได้ง่ายสุดๆ ด้วยสูตรที่ดีที่สุดของซันซิลพร้อมอาร์แกนออยล์ และคามิเลีย เอสเซนส์ ซึมลึก ให้ผมนุ่ม มีน้ำหนัก พร้อมเพลิดเพลินกับความหอมติดผมแบบเส้นต่อเส้น ให้คุณสัมผัสกับเส้นผมที่จัดทรงง่ายขึ้น และกลิ่นหอมสดชื่นตั้งแต่ครั้งแรกที่ใช้', 129.00, 11, NULL, NULL, NULL, NULL, NULL, 'products/wHuSNKEioq56xYj1ScjOd1ui7f43GqW9MeyACdln.jpg', 1, '2026-11-20 06:46:06', '2026-11-21 01:49:35'),
(24, 'SKU-013', 'เลย์ มันฝรั่งแท้ รสโนริ สาหร่าย', 'ขนมขบเคี้ยว', 'เลย์ มันฝรั่งแท้ทอดกรอบ (น้ำหนักสุทธิ 42-50 กรัม x 6 ซอง)', 20.00, 15, NULL, NULL, NULL, NULL, NULL, 'products/aj2d4mzFFCAlmPMxUQaL9hMFgI6vi3bFGDvFDrE2.jpg', 1, '2026-11-20 06:50:29', '2026-11-20 06:50:29'),
(25, 'SKU-014', 'โปเต้ มันฝรั่งกรอบ 52ก', 'ขนมขบเคี้ยว', NULL, 20.00, 12, NULL, NULL, NULL, NULL, NULL, 'products/XfLkKFe6MyU2GI5T41HXuQ2a2lPZe1IbkQf56d9V.png', 1, '2026-11-20 06:56:11', '2026-11-21 01:46:11'),
(26, 'SKU-015', 'ฟาร์มเฮ้าส์ ขนมปังโฮสวีท 250 กรัม', 'เบเกอรี่', 'ข้อมูลทั่วไป\r\nฟาร์มเฮ้าส์ขนมปังรอยัลธัญพืช 12 ชนิดป็นขนมปังแผ่นทรงสี่เหลี่ยมมุมมนด้านบน ผสมด้วยโฮลวีต สีของขนมปังเหลืองอ่อน เนื้อขนมปังมีความนุ่ม รสชาติกลมกล่อม หอม อร่อย สุขภาพดี\r\nส่วนประกอบ\r\n(แป้งสาลี 42.0%, แป้งรำข้าวสาลี 21.0%, น้ำตาลทราย 4.5%, ไขมันพืช 2.5%, ยีสต์ 1.0%, เกลือบริโภคเสริมไอโอดีน 1.0%, นมผง 0.6%, วิตามิน 0.2%, วัตถุกันเสีย INS282, อิมัลซิไฟเออร์ INS471, INS481(i) สารปรับปรุงคุณภาพแป้ง INS101(i), INS300, INS 1100, INS1100(i))', 24.00, 4, NULL, NULL, NULL, NULL, NULL, 'products/nu56Vrf6Lt65DgaXTrIuLisr126ekvoPoohPJR8s.jpg', 1, '2026-11-20 06:59:15', '2026-11-20 06:59:15');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('KZQ9L97sonSbNoiodt4YUqcqJv4zsEBMU9WHOC3e', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiOWFEdlB0ZnN5M1A2YzhLQXVXWVlQWnVFRVVscjJkaG0zN2QwSWtSaiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fX0=', 1795261314),
('Nh4L22GYzaDafnJJeuKA1ssB10Jo1TD3iCI1FnvA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.147.0 Chrome/150.0.7871.250 Electron/43.8.9 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoia0M3a1JjM29zNHBjQ0JqNk50b29ST1hHVGk5NzZLeU1wSjh6S0NmNiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1795260501);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `phone`, `avatar`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'นายอำนาจ ระบบ', 'admin@gmail.com', 'admin', '0890900909', 'avatars/BUqBcSzqJuCk0FXQlobbS0366YxLIpbvfAP1qjyt.png', NULL, '$2y$12$6M1cT/W11QEIkaeKwnco7.NXJO8rqa7LxcBAPnecqGXj0wyZT9OYe', NULL, '2026-11-17 04:52:01', '2026-11-21 05:32:56'),
(2, 'นายเจ้าของร้าน ระบบ', 'owner@gmail.com', 'owner', '0800000002', 'avatars/7UVstz7mdRGnAJQ3C1czf7UqDGldJjt4xIgKDmnf.png', NULL, '$2y$12$fjOaaLJco1Xob7p.4R8DRuS/5R1WWW7NgeWWTwETHJlG9qcfPQUn.', NULL, '2026-11-17 04:52:02', '2026-11-21 05:35:45'),
(3, 'ลูกค้า ใจดี', 'customer@gmail.com', 'customer', '0831513998', 'avatars/bmbCJJxCo0d2pmgmgErDKrS1TVepXqT9TtkCl8DL.png', NULL, '$2y$12$oGj421YlQt5tKGS5lvHJgOESA17IXc04umTBa9fdiHsjDx4vZ2DKG', NULL, '2026-11-17 04:52:02', '2026-11-21 07:54:12'),
(5, 'ลูกค้า ทดสอบระบบ', 'users@gmail.com', 'customer', '0800000890', NULL, NULL, '$2y$12$bOr7nZKY0.LXD2x8Bh.mEeBI3ZiM4dUdjuAz4n35ljTC6b39ydZ1i', '6Hr0zhGQuseFim2Q0hI9MeLqU9erx95eRXhrKsKSsgqRIJILUB7vuU14AZWH', '2026-11-20 05:50:39', '2026-11-20 05:50:39'),
(8, 'นางสมมติ นามสกุล', 'sommut@gmail.com', 'customer', '0941238760', NULL, NULL, '$2y$12$SR9TE9vfe9M9F/W3oqCmV.al8Ec3uAxeqO4ki2bYG39i6sDKCw7eq', NULL, '2026-11-21 07:28:59', '2026-11-21 07:28:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_order_no_unique` (`order_no`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
