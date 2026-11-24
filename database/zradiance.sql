-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260920.b442fbfd85
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2026 at 08:40 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `getcat` (IN `cid` INT)   BEGIN
    SELECT *
    FROM categories
    WHERE cat_id = cid;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_products_by_category` (IN `p_cat_id` BIGINT)   SELECT *
FROM products
WHERE product_cat = p_cat_id$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--
CREATE TABLE `addresses` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Rumah',
  `recipient_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `village_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `village_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `detail` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `label`, `recipient_name`, `phone`, `province_code`, `province_name`, `city_code`, `city_name`, `district_code`, `district_name`, `village_code`, `village_name`, `postal_code`, `detail`, `is_default`, `created_at`, `updated_at`) VALUES
(2, 2, 'Rumah', 'Dani', '081269611488', '32', 'Jawa Barat', '32.78', 'Kota Tasikmalaya', '32.78.07', 'Tamansari', '32.78.07.1003', 'Tamanjaya', '55223', 'JL alang2', 0, '2026-11-15 17:58:20', '2026-11-24 12:11:06'),
(3, 2, 'Rumah', 'Zahratur Ramadhani', '081269611488', '11', 'Aceh', '11.71', 'Kota Banda Aceh', '11.71.04', 'Syiah Kuala', '11.71.04.2002', 'Tibang', '23114', 'jl tengku meurah Lr Usman', 1, '2026-11-24 12:10:48', '2026-11-24 12:11:06');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--
CREATE TABLE `brands` (
  `brand_id` bigint UNSIGNED NOT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_name`, `brand_slug`, `brand_logo`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Azarine', 'azarine', 'brands/T4oUeX0mcjtR327gYMcTFHMZlcb0rgQbWNw5XXuh.png', NULL, 1, '2026-11-20 08:51:56', '2026-11-20 09:46:45'),
(2, 'Nivea', 'nivea', 'brands/SRCQbp6XLlmK7JE0VyqEnXyphL0DfzfHpwqBBbd8.webp', NULL, 1, '2026-11-20 08:51:56', '2026-11-24 09:26:54'),
(3, 'Scarlett', 'scarlett', 'brands/t3EK1SMrFDqDetfJaNFn9SZZj6FfAPYJgjXR5F94.png', NULL, 1, '2026-11-20 08:51:57', '2026-11-24 09:27:39'),
(4, 'Skin Aqua', 'skin-aqua', 'brands/46GX5EPyFyQiAVPzOu0D4ZmKkKsWw1ZGmEH5TIqc.webp', NULL, 1, '2026-11-20 08:51:57', '2026-11-24 09:34:15'),
(5, 'Wardah', 'wardah', 'brands/yXoazT97Mw1m4ct2aHi0vOI69h3F3PElYFrac35O.png', NULL, 1, '2026-11-23 11:36:25', '2026-11-24 09:37:45'),
(7, 'Emina', 'emina', 'brands/LzrWHO75o9PDNOZE5VWmlUTniJBx8F1EV5BB9oED.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:20:28'),
(8, 'Somethinc', 'somethinc', 'brands/bhoscABDu3dfVB7GfGOSVFFQ6hKy5JOph1R4t6fV.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:35:11'),
(9, 'Skintific', 'skintific', 'brands/YZajfNkGOBKd19LUhjvehcqGcpC4sLXDPi8QB0eE.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:34:53'),
(10, 'Avoskin', 'avoskin', 'brands/2vytUUFZgtj9tWKU9TCSJyFkDM4NQ0IA5u073aIU.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:10:42'),
(11, 'Cetaphil', 'cetaphil', 'brands/EXfwYZ8QHbM1ujVUo1AneAIVeiwGMVJs7WTpozmn.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:16:12'),
(12, 'CeraVe', 'cerave', 'brands/ZWeo4QLtrQjpBOXLSllhSs6fxlzV0kCRA8WInCNt.jpg', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:16:01'),
(13, 'The Ordinary', 'the-ordinary', 'brands/w7kh11NuhWBVDi08bwDI7whvEwgqXkbQgebnDQ6K.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:35:47'),
(14, 'COSRX', 'cosrx', 'brands/EEc7hLXeAKdTVFouvZfhbsGC6Q6BxsQRP7TKqQNX.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:16:26'),
(15, 'Maybelline', 'maybelline', 'brands/bB0mvtBy1GYy4SZSITvPyvJpS1K6ysyXkU5yOw7O.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:25:03'),
(16, 'Make Over', 'make-over', 'brands/hxss2zsmfHvLL5MKwkgsXIxLoDoKO6aDIexoJP39.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:24:47'),
(17, 'ESQA', 'esqa', 'brands/058zTtItN7WUdDFeh7VpFujp7m2wQUZWvY19hPmU.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:20:42'),
(18, 'Focallure', 'focallure', 'brands/uzbrbfiNAw5yLwghkZq6A0UCCXXsM5aqSxPSzTDV.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:20:56'),
(19, 'Hanasui', 'hanasui', 'brands/LLpcHOrp5OAdDIFyL2Ecf1vYgl567OvQyw4H5WpF.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:22:45'),
(20, 'L\'Oreal Paris', 'loreal-paris', 'brands/jSenV3t0WapCSsZAe9zsAChZMFoytkPdkhk6h1gG.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:23:53'),
(21, 'Makarizo', 'makarizo', 'brands/uldHiInD0amJ4xSKeLuoWGG7G7jvo56ppms0GDav.jpg', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:24:31'),
(22, 'Pantene', 'pantene', 'brands/h03YUqTfBjvxJrATnlmrWuNMi0vXly7x4gfHyxTH.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:27:18'),
(23, 'TRESemme', 'tresemme', 'brands/JyfOshL3rn5zxgYkzj0WzS9wRtDgNbGaUzvcIjWf.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:36:03'),
(24, 'Dove', 'dove', 'brands/0Cxr4YtwKiTphVKVWgo1R9jTp4SB6RFuRkp3LBqD.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:20:02'),
(25, 'Ellips', 'ellips', 'brands/blIJ6mqXoZdbxP7RK2KszPMkB9G6G2d6yf903Xma.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:20:14'),
(26, 'Mise en Scene', 'mise-en-scene', 'brands/yPd61uLus0Tlg0Qe66k3687nV1n0oQ90YGp2ATo2.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:25:42'),
(27, 'Natur', 'natur', 'brands/xR3fbdIDCqtP7rsyFJcKawlJirrJaAMDcAY9AKmr.jpg', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:26:27'),
(28, 'Vaseline', 'vaseline', 'brands/MaUjl4pBvjdrJs6Zps9DohKIyL1GI4iiMrEP9XH1.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:36:19'),
(29, 'Herborist', 'herborist', 'brands/inlMRFeciI875ChH14gbV5bCmzXAWDNvBFp1PlDi.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:23:05'),
(30, 'The Body Shop', 'the-body-shop', 'brands/V1Q27J9A1pNCzK47wLJbJtCfzYpRZWPZRMR8zWVS.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:35:29'),
(31, 'Shinzui', 'shinzui', 'brands/aRs2QkKK1u0gpiMCtIFsoIgq6a9rSPzJ30jGoFyK.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:33:53'),
(32, 'Lifebuoy', 'lifebuoy', 'brands/pC0NiwjFo7xnbNZvKe8qV6y31htbdhfc8mD1QocU.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:24:12'),
(33, 'Mykonos', 'mykonos', 'brands/Uc4WNstGTgZCgvlCJLVXB981oOdpLIRpbLhvEbtv.jpg', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:26:00'),
(34, 'HMNS', 'hmns', 'brands/nGLyYCaa9sccmUXy1bg1S5taOy3kxd5OqHMik3PK.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:23:26'),
(35, 'Zara', 'zara', 'brands/fGLYcShPaFfUTyumPDBKs2XSEUkveGT7E6QwgzB2.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:38:00'),
(36, 'Miniso', 'miniso', 'brands/rhZ4ZovzrGucoC1IgDMQ6azjctfHGF4dGWX9tegi.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:25:22'),
(37, 'Victoria\'s Secret', 'victorias-secret', 'brands/P7bLwjclHUwzfZe9feVx9OG7DImO4wnQ1kO7eBC1.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:37:26'),
(38, 'Bath & Body Works', 'bath-body-works', 'brands/NkuePRkiFORWpuV3bl2Kv9ZxfeAD4UCdnIb7FiYV.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:10:54'),
(39, 'Carl & Claire', 'carl-claire', 'brands/K7x5ODniT6XSuuQgt0YuvS88oHaEhPJy7yY314LA.jpg', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:19:50'),
(40, 'Biore', 'biore', 'brands/7ErKZrx0BsB4VWHg5aZky5cqnYhwGAry8Ik2Uh5z.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:15:17'),
(41, 'Beauty of Joseon', 'beauty-of-joseon', 'brands/blul96BL5dEPSqqK8ZcqVqllc9hBCD0Mu7oe3CJI.webp', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:11:06'),
(42, 'Skin1004', 'skin1004', 'brands/veJdsP4LBZRwHNFxZsxceybp1eJ7msn47RNsxOvB.jpg', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 09:34:32'),
(43, 'Anessa', 'anessa', 'brands/iL4uDZcug64zom5m24xDyK3taHOCCtTrfjXq5XzW.png', NULL, 1, '2026-11-23 11:41:16', '2026-11-24 08:10:30');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:1;', 1795548815),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1795548815;', 1795548815);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--
CREATE TABLE `cart_items` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(25, 2, 68, 1, '2026-11-24 12:31:21', '2026-11-24 12:31:21');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--
CREATE TABLE `categories` (
  `cat_id` bigint UNSIGNED NOT NULL,
  `cat_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_title`, `description`, `created_at`, `updated_at`) VALUES
(5, 'Skincare', NULL, '2026-11-15 16:54:45', '2026-11-15 16:54:45'),
(6, 'Makeup', NULL, '2026-11-15 16:54:45', '2026-11-15 16:54:45'),
(7, 'Hair Care', NULL, '2026-11-15 16:54:45', '2026-11-15 16:54:45'),
(8, 'Body Care', NULL, '2026-11-15 16:54:45', '2026-11-15 16:54:45'),
(9, 'Fragrance', NULL, '2026-11-15 16:54:45', '2026-11-15 16:54:45'),
(10, 'Sunscreen', NULL, '2026-11-15 16:54:45', '2026-11-15 16:54:45');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--
CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--
CREATE TABLE `faqs` (
  `id` bigint UNSIGNED NOT NULL,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `keywords`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Bagaimana cara memesan produk?', '1. Login atau daftar akun terlebih dahulu.\n2. Buka menu Kategori, pilih kategori, lalu klik \"Tambah ke Keranjang\" pada produk yang kamu mau.\n3. Buka menu Keranjang untuk mengatur jumlah produk.\n4. Lanjut ke checkout, isi data penerima, lalu buat pesanan.\n5. Pantau pesananmu di menu Pesanan.', 'memesan, pemesanan, cara pesan, cara order, cara beli, membeli, belanja', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(2, 'Bagaimana cara checkout dan data apa yang harus diisi?', 'Di halaman checkout: 1) pilih alamat pengiriman (atau tambahkan alamat lengkap di Profil), 2) klik hitung ongkir lalu pilih kurir dan layanan, 3) pilih pembayaran Online atau COD, 4) buat pesanan. Pastikan produk sudah dicentang di keranjang. Pesanan online dibayar lewat tombol Bayar di detail pesanan.', 'checkout, data penerima, alamat pengiriman, nomor telepon, isi alamat', 1, '2026-11-01 18:38:24', '2026-11-24 13:28:13'),
(3, 'Bagaimana cara melihat status pesanan?', 'Buka menu Pesanan, lalu pilih pesanan untuk melihat detail dan statusnya. Status pesanan yang digunakan toko: menunggu (pending), diproses (processing), dikirim (shipped), selesai (completed), dan dibatalkan (cancelled).', 'status pesanan, lacak pesanan, melacak, cek pesanan, pesanan saya, riwayat pesanan, sudah dikirim', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(4, 'Apakah pesanan bisa dibatalkan?', 'Bisa, selama status pesanan masih menunggu (pending) dan belum dibayar. Buka menu Pesanan, pilih pesanan, lalu klik Batalkan. Pesanan yang sudah dibayar tidak bisa dibatalkan langsung, hubungi admin untuk proses refund.', 'batal, dibatalkan, membatalkan, pembatalan, cancel', 1, '2026-11-01 18:38:24', '2026-11-24 13:28:13'),
(5, 'Bagaimana cara mengubah isi keranjang?', 'Buka menu Keranjang. Di sana kamu bisa menambah atau mengurangi jumlah produk, dan menghapus produk dari keranjang. Jika jumlah dikurangi sampai habis, produk otomatis keluar dari keranjang.', 'ubah keranjang, hapus dari keranjang, kurangi jumlah, tambah jumlah, isi keranjang, mengubah keranjang', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(6, 'Kenapa produk tidak bisa dipesan atau stoknya habis?', 'Jika stok produk 0, tombol tambah ke keranjang berubah menjadi \"Stok Habis\". Stok berkurang otomatis ketika pesanan berhasil dibuat. Jika stok tidak cukup saat checkout, pesanan tidak dapat diproses dan kamu perlu menyesuaikan jumlahnya.', 'stok habis, stok kosong, tidak bisa dipesan, stok tidak cukup, sold out', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(7, 'Bagaimana cara mendaftar akun?', 'Klik menu Registrasi di halaman utama, isi formulir pendaftaran, lalu login dengan akun yang sudah dibuat.', 'daftar akun, mendaftar, registrasi, buat akun, membuat akun, register', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(8, 'Bagaimana cara membayar pesanan online?', 'Setelah pesanan dibuat, buka menu Pesanan, pilih pesanan, lalu klik Bayar. Kamu bisa membayar dengan QRIS, transfer/virtual account, atau e-wallet. Status pembayaran biasanya berubah otomatis dalam beberapa menit setelah pembayaran berhasil.', 'cara bayar, membayar, pembayaran, bayar pesanan, qris, virtual account, e-wallet, gopay, transfer', 1, '2026-11-24 13:28:13', '2026-11-24 13:28:13'),
(9, 'Kenapa ongkir tidak muncul atau lokasi tidak ditemukan?', 'Ongkir dihitung otomatis dari alamat dan berat produk. Penyebab yang paling sering: provinsi, kota, kecamatan, kelurahan, atau kode pos di alamat belum tepat; alamat belum lengkap; atau layanan ongkir sedang membatasi permintaan (coba lagi beberapa menit). Periksa dan perbarui alamat di Profil > Alamat.', 'ongkir tidak muncul, ongkir gagal, lokasi tidak ditemukan, ongkos kirim error, kurir tidak muncul', 1, '2026-11-24 13:28:13', '2026-11-24 13:28:13'),
(10, 'Pembayaran saya kedaluwarsa atau gagal, apa yang harus dilakukan?', 'Pesanan dengan pembayaran kedaluwarsa atau gagal tidak bisa dibayar ulang. Buka detail pesanan, klik Batalkan, lalu checkout ulang produknya. Kalau saldo kamu sudah terpotong, simpan bukti pembayaran dan hubungi admin.', 'pembayaran kedaluwarsa, pembayaran kadaluarsa, pembayaran gagal, expired, bayar ulang, saldo terpotong', 1, '2026-11-24 13:28:13', '2026-11-24 13:28:13'),
(11, 'Bagaimana jika saya lupa password?', 'Klik \"Lupa password\" di halaman login, isi email dan password baru. Permintaanmu akan dicek dan disetujui admin dulu, setelah itu kamu bisa login dengan password baru. Kalau lama tidak diproses, hubungi admin.', 'lupa password, reset password, ganti password, tidak bisa login, lupa kata sandi', 1, '2026-11-24 13:28:13', '2026-11-24 13:28:13');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--
CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `knowledge_articles`
--
CREATE TABLE `knowledge_articles` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'panduan',
  `keywords` text COLLATE utf8mb4_unicode_ci,
  `summary` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `knowledge_articles`
--

INSERT INTO `knowledge_articles` (`id`, `title`, `slug`, `category`, `keywords`, `summary`, `content`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Perbedaan serum dan moisturizer', 'perbedaan-serum-dan-moisturizer', 'panduan', 'serum moisturizer, serum vs moisturizer, perbedaan serum, serum dan pelembap, fungsi serum dan moisturizer', 'Serum bertekstur ringan dengan bahan aktif terkonsentrasi untuk target tertentu (misalnya hidrasi atau mencerahkan). Moisturizer bertugas melembapkan dan mengunci kelembapan di permukaan kulit. Keduanya saling melengkapi, bukan saling menggantikan.', 'Serum\nBertekstur ringan dan biasanya mengandung bahan aktif dalam konsentrasi lebih tinggi untuk menargetkan masalah tertentu, misalnya hidrasi, kulit kusam, atau noda.\n\nMoisturizer\nBertugas melembapkan kulit dan membantu mengunci kelembapan agar tidak mudah menguap. Teksturnya umumnya lebih kaya dibanding serum.\n\nUrutan pakai\nSerum biasanya dipakai lebih dulu, lalu ditutup dengan moisturizer.\n\nInformasi ini bersifat umum. Selalu baca petunjuk pada kemasan produk.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(2, 'Urutan penggunaan skincare', 'urutan-penggunaan-skincare', 'panduan', 'urutan skincare, urutan pemakaian skincare, urutan pakai skincare, skincare routine, rutinitas skincare, step skincare, mana dulu', 'Urutan umum: pembersih wajah, toner (opsional), serum, moisturizer, lalu sunscreen di pagi hari. Prinsipnya dari tekstur paling ringan ke paling kental.', 'Pagi\n1. Pembersih wajah\n2. Toner (opsional)\n3. Serum\n4. Moisturizer\n5. Sunscreen\n\nMalam\n1. Pembersih wajah (dan makeup remover jika memakai makeup)\n2. Toner (opsional)\n3. Serum\n4. Moisturizer\n\nPrinsip umum: pakai dari tekstur paling ringan ke paling kental. Kenalkan produk baru satu per satu agar mudah mengenali jika ada produk yang tidak cocok.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(3, 'Memahami jenis kulit', 'memahami-jenis-kulit', 'panduan', 'jenis kulit, tipe kulit, kulit normal kering berminyak, kulit kombinasi, kulit sensitif, mengenali kulit, cek jenis kulit', 'Jenis kulit umum: normal, kering, berminyak, kombinasi (berminyak di area T, normal atau kering di pipi), dan sensitif (mudah merah atau perih). Jenis kulit dapat berubah mengikuti cuaca, usia, dan kebiasaan.', 'Normal: tidak terlalu berminyak maupun kering.\nKering: terasa kencang, kadang bersisik.\nBerminyak: tampak mengilap, terutama di dahi, hidung, dan dagu.\nKombinasi: berminyak di area T, normal atau kering di area pipi.\nSensitif: mudah kemerahan, perih, atau gatal saat memakai produk tertentu.\n\nJenis kulit bisa berubah. Jika punya masalah kulit yang menetap, konsultasikan ke dokter kulit.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(4, 'Mengenal kategori produk Beauty Store', 'mengenal-kategori-produk', 'panduan', 'kategori produk, jenis kategori, apa saja kategori, kategori beauty store, ada kategori apa', 'Beauty Store memiliki empat kategori: Skincare (perawatan wajah), Makeup (riasan), Body Care (perawatan tubuh), dan Hair Care (perawatan rambut).', 'Skincare: produk perawatan wajah.\nMakeup: produk riasan seperti lip cream dan cushion.\nBody Care: perawatan tubuh.\nHair Care: perawatan rambut.\n\nBuka menu Kategori untuk melihat produk di tiap kategori.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(5, 'Apa itu patch test?', 'apa-itu-patch-test', 'panduan', 'patch test, uji coba produk, tes alergi produk, coba produk baru, uji tempel', 'Patch test adalah mencoba sedikit produk di area kecil kulit (misalnya lengan bagian dalam atau belakang telinga) lalu mengamati reaksinya selama sekitar 24 jam sebelum dipakai ke seluruh wajah.', 'Cara umum\n1. Oleskan produk sedikit saja di area kecil, misalnya lengan bagian dalam atau belakang telinga.\n2. Tunggu dan amati sekitar 24 jam.\n3. Jika muncul kemerahan, gatal, atau perih, hentikan pemakaian.\n\nPatch test membantu mengurangi risiko, tetapi tidak menjamin produk 100% cocok. Jika reaksi berat, hubungi dokter.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(6, 'Niacinamide', 'niacinamide', 'bahan', 'niacinamide, vitamin b3', 'Niacinamide (vitamin B3) umumnya dipakai untuk membantu menjaga skin barrier, mengontrol tampilan minyak berlebih, dan meratakan tampilan warna kulit. Pada sebagian kulit sensitif, konsentrasi tinggi bisa menimbulkan kemerahan atau rasa perih.', 'Niacinamide adalah bentuk vitamin B3.\n\nFungsi umum: membantu menjaga skin barrier, membantu mengontrol tampilan minyak berlebih, dan meratakan tampilan warna kulit.\n\nPerhatian: sebagian orang dengan kulit sensitif bisa mengalami kemerahan atau perih, terutama pada konsentrasi tinggi. Lakukan patch test dan mulai bertahap.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(7, 'Hyaluronic acid', 'hyaluronic-acid', 'bahan', 'hyaluronic acid, hyaluronic, asam hialuronat, ha', 'Hyaluronic acid adalah humektan yang menarik dan menahan air di kulit sehingga membantu hidrasi. Umumnya dapat dipakai berbagai jenis kulit, termasuk yang berminyak.', 'Hyaluronic acid adalah humektan: bahan yang menarik dan menahan air.\n\nFungsi umum: membantu melembapkan (hidrasi) dan membuat kulit terasa lebih kenyal.\n\nTips: pakai pada kulit yang masih sedikit lembap, lalu tutup dengan moisturizer agar kelembapan terkunci.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(8, 'Ceramide', 'ceramide', 'bahan', 'ceramide, seramide', 'Ceramide adalah lipid yang secara alami ada di lapisan terluar kulit. Pada skincare, ceramide membantu menjaga skin barrier dan mengunci kelembapan.', 'Ceramide adalah lipid alami di lapisan terluar kulit.\n\nFungsi umum: membantu menjaga skin barrier dan mengunci kelembapan, sehingga cocok untuk kulit yang mudah kering atau barrier-nya terganggu.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(9, 'Salicylic acid (BHA)', 'salicylic-acid', 'bahan', 'salicylic acid, salicylic, bha, asam salisilat', 'Salicylic acid (BHA) larut dalam minyak sehingga sering dipakai untuk membantu membersihkan pori pada kulit berminyak atau berjerawat. Bisa menyebabkan kering atau perih; mulai bertahap. Ibu hamil atau menyusui sebaiknya konsultasi ke dokter dulu.', 'Salicylic acid adalah BHA yang larut dalam minyak.\n\nFungsi umum: membantu membersihkan pori dan sering dipakai pada kulit berminyak atau berjerawat.\n\nPerhatian: dapat menyebabkan kering, perih, atau mengelupas. Mulai pemakaian bertahap. Ibu hamil atau menyusui sebaiknya berkonsultasi dengan dokter sebelum memakai.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(10, 'Retinol', 'retinol', 'bahan', 'retinol, vitamin a, retinoid', 'Retinol adalah turunan vitamin A yang dipakai untuk membantu memperbaiki tekstur kulit dan tanda penuaan. Bisa menyebabkan kering dan kemerahan serta membuat kulit lebih sensitif terhadap matahari, jadi pakai bertahap di malam hari dan gunakan sunscreen. Ibu hamil atau menyusui perlu konsultasi dokter.', 'Retinol adalah turunan vitamin A.\n\nFungsi umum: membantu memperbaiki tekstur kulit dan tanda penuaan.\n\nPerhatian: dapat menyebabkan kering, kemerahan, dan kulit lebih sensitif terhadap matahari. Mulai bertahap, pakai malam hari, dan gunakan sunscreen pada pagi hari. Ibu hamil atau menyusui sebaiknya berkonsultasi dengan dokter.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(11, 'Vitamin C', 'vitamin-c', 'bahan', 'vitamin c, ascorbic acid, asam askorbat', 'Vitamin C adalah antioksidan yang dipakai untuk membantu mencerahkan tampilan kulit dan melindungi dari radikal bebas. Beberapa bentuknya mudah rusak dan bisa terasa perih pada kulit sensitif; tetap gunakan sunscreen di pagi hari.', 'Vitamin C (misalnya ascorbic acid) adalah antioksidan.\n\nFungsi umum: membantu mencerahkan tampilan kulit dan melindungi kulit dari radikal bebas.\n\nPerhatian: beberapa bentuknya tidak stabil terhadap cahaya dan udara, dan bisa terasa perih pada kulit sensitif. Tetap gunakan sunscreen pada pagi hari.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24'),
(12, 'SPF dan PA pada sunscreen', 'spf-dan-pa', 'bahan', 'spf, pa+, sunscreen, tabir surya, sunblock, uv', 'SPF menunjukkan tingkat perlindungan terhadap sinar UVB, sedangkan PA menunjukkan perlindungan terhadap UVA (semakin banyak tanda +, semakin kuat). Sunscreen perlu dipakai ulang secara berkala jika terpapar matahari langsung.', 'SPF (Sun Protection Factor): ukuran perlindungan terhadap sinar UVB.\nPA: label perlindungan terhadap sinar UVA; semakin banyak tanda +, semakin tinggi perlindungannya.\n\nSunscreen sebaiknya dipakai ulang secara berkala bila terpapar matahari langsung. Ikuti petunjuk pada kemasan.', 1, '2026-11-01 18:38:24', '2026-11-01 18:38:24');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--
CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_09_07_000001_create_categories_table', 1),
(5, '2026_09_07_000002_create_products_table', 1),
(6, '2026_09_07_110809_add_product_cat_to_products_table', 2),
(7, '2026_09_07_115159_create_orders_table', 3),
(8, '2026_09_07_115206_create_order_items_table', 3),
(9, '2026_09_07_124200_add_role_to_users_table', 4),
(10, '2026_11_02_012122_add_knowledge_fields_to_products_table', 5),
(11, '2026_11_02_012123_create_faqs_table', 5),
(12, '2026_11_02_012124_create_knowledge_articles_table', 5),
(13, '2026_11_02_150115_create_cart_items_table', 6),
(14, '2026_11_02_150209_add_profile_fields_to_users_table', 6),
(15, '2026_11_02_150326_add_description_to_categories_table', 6),
(16, '2026_11_02_150357_create_password_reset_requests_table', 6),
(17, '2026_11_13_184649_add_shipping_address_fields_to_orders_table', 7),
(18, '2026_11_14_160432_create_addresses_table', 8),
(19, '2026_11_14_165524_add_shipping_and_payment_fields_to_orders_table', 9),
(20, '2026_11_14_165936_add_stock_deducted_at_to_orders_table', 9),
(21, '2026_11_14_180739_add_snap_token_to_orders_table', 9),
(22, '2026_11_15_062153_create_shipping_settings_table', 10),
(23, '2026_11_15_064332_add_weight_to_products_table', 11),
(24, '2026_11_15_065916_add_shipping_details_to_orders_table', 12),
(25, '2026_11_15_085142_create_notifications_table', 13),
(26, '2026_11_15_092424_create_reviews_table', 14),
(27, '2026_11_20_152441_create_brands_table', 15),
(28, '2026_11_20_152656_add_brand_id_to_products_table', 15),
(29, '2026_11_23_183842_standardize_product_categories', 16),
(30, '2026_11_23_183922_make_product_price_nullable', 17),
(31, '2026_11_23_184058_make_product_weight_nullable', 18);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('0754db62-52d0-496a-b770-e4617538b70b', 'App\\Notifications\\AdminOrderNotification', 'App\\Models\\User', 3, '{\"event\":\"new_order\",\"title\":\"Pesanan Baru\",\"message\":\"Pesanan #24 telah dibuat oleh pelanggan.\",\"order_id\":24,\"recipient_name\":\"Dani\",\"total\":\"85900.00\",\"status\":\"pending\",\"payment_status\":\"unpaid\",\"url\":\"https:\\/\\/thirteen-antacid-mankind.ngrok-free.dev\\/admin\\/orders\\/24\"}', '2026-11-24 13:02:20', '2026-11-22 21:03:52', '2026-11-24 13:02:20'),
('22968212-120d-4f43-a753-f87e17aaa7c9', 'App\\Notifications\\AdminOrderNotification', 'App\\Models\\User', 3, '{\"event\":\"new_order\",\"title\":\"Pesanan Baru\",\"message\":\"Pesanan #25 telah dibuat oleh pelanggan.\",\"order_id\":25,\"recipient_name\":\"Dani\",\"total\":\"53000.00\",\"status\":\"pending\",\"payment_status\":\"unpaid\",\"url\":\"https:\\/\\/thirteen-antacid-mankind.ngrok-free.dev\\/admin\\/orders\\/25\"}', '2026-11-24 13:02:20', '2026-11-24 12:23:51', '2026-11-24 13:02:20'),
('22ee5405-c4f4-4239-9f1a-7201313e8361', 'App\\Notifications\\AdminOrderNotification', 'App\\Models\\User', 3, '{\"event\":\"payment_success\",\"title\":\"Pembayaran Berhasil\",\"message\":\"Pembayaran pesanan #23 telah berhasil dikonfirmasi.\",\"order_id\":23,\"recipient_name\":\"Dani\",\"total\":\"77000.00\",\"status\":\"pending\",\"payment_status\":\"paid\",\"url\":\"https:\\/\\/thirteen-antacid-mankind.ngrok-free.dev\\/admin\\/orders\\/23\"}', '2026-11-16 20:03:51', '2026-11-16 20:02:46', '2026-11-16 20:03:51'),
('60558498-d456-41db-9a75-88e0ba99fb97', 'App\\Notifications\\AdminOrderNotification', 'App\\Models\\User', 3, '{\"event\":\"new_order\",\"title\":\"Pesanan Baru\",\"message\":\"Pesanan #26 telah dibuat oleh pelanggan.\",\"order_id\":26,\"recipient_name\":\"Dani\",\"total\":\"213750.00\",\"status\":\"pending\",\"payment_status\":\"unpaid\",\"url\":\"https:\\/\\/thirteen-antacid-mankind.ngrok-free.dev\\/admin\\/orders\\/26\"}', '2026-11-24 13:02:20', '2026-11-24 12:24:32', '2026-11-24 13:02:20'),
('b6e6b73c-97ca-47f7-baf6-0e9747b88aa1', 'App\\Notifications\\AdminOrderNotification', 'App\\Models\\User', 3, '{\"event\":\"new_order\",\"title\":\"Pesanan Baru\",\"message\":\"Pesanan #23 telah dibuat oleh pelanggan.\",\"order_id\":23,\"recipient_name\":\"Dani\",\"total\":\"77000.00\",\"status\":\"pending\",\"payment_status\":\"unpaid\",\"url\":\"https:\\/\\/thirteen-antacid-mankind.ngrok-free.dev\\/admin\\/orders\\/23\"}', '2026-11-16 20:03:51', '2026-11-16 20:00:04', '2026-11-16 20:03:51');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--
CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `recipient_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `shipping_cost` int UNSIGNED NOT NULL DEFAULT '0',
  `shipping_weight` int UNSIGNED NOT NULL DEFAULT '0',
  `shipping_courier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_service` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_etd` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `province` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_detail` text COLLATE utf8mb4_unicode_ci,
  `delivery_note` text COLLATE utf8mb4_unicode_ci,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `snap_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `stock_deducted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `recipient_name`, `phone`, `address`, `total`, `shipping_cost`, `shipping_weight`, `shipping_courier`, `shipping_service`, `shipping_etd`, `status`, `created_at`, `updated_at`, `province`, `city`, `district`, `postal_code`, `address_detail`, `delivery_note`, `payment_method`, `payment_status`, `payment_reference`, `snap_token`, `paid_at`, `stock_deducted_at`) VALUES
(23, 2, 'Dani', '081269611488', 'JL alang2, Tamanjaya, Tamansari, Kota Tasikmalaya, Jawa Barat, 55223', 77000.00, 20000, 330, 'jne', 'REG', '3 day', 'completed', '2026-11-16 20:00:00', '2026-11-16 20:04:21', 'Jawa Barat', 'Kota Tasikmalaya', 'Tamansari', '55223', 'JL alang2', NULL, 'qris', 'paid', '35a00d5f-6ec1-4f78-96d1-659994622519', '71302a3c-55f5-4d9c-aa67-08f06da7d0f8', '2026-11-16 20:02:45', '2026-11-16 20:02:45'),
(24, 2, 'Dani', '081269611488', 'JL alang2, Tamanjaya, Tamansari, Kota Tasikmalaya, Jawa Barat, 55223', 85900.00, 20000, 200, 'jne', 'REG', '3 day', 'pending', '2026-11-22 21:03:48', '2026-11-22 21:20:06', 'Jawa Barat', 'Kota Tasikmalaya', 'Tamansari', '55223', 'JL alang2', NULL, 'qris', 'expired', NULL, '7493d873-2b1a-4062-83c4-9ce31226b551', NULL, NULL),
(25, 2, 'Dani', '081269611488', 'JL alang2, Tamanjaya, Tamansari, Kota Tasikmalaya, Jawa Barat, 55223', 53000.00, 8000, 100, 'wahana', 'Express', '6 day', 'pending', '2026-11-24 12:23:47', '2026-11-24 12:23:52', 'Jawa Barat', 'Kota Tasikmalaya', 'Tamansari', '55223', 'JL alang2', NULL, 'online', 'unpaid', NULL, '7abfada9-96b8-4eba-9e12-220bbb11353b', NULL, NULL),
(26, 2, 'Dani', '081269611488', 'JL alang2, Tamanjaya, Tamansari, Kota Tasikmalaya, Jawa Barat, 55223', 213750.00, 28750, 100, 'sicepat', 'GOKIL', '6-8 day', 'pending', '2026-11-24 12:24:32', '2026-11-24 12:24:32', 'Jawa Barat', 'Kota Tasikmalaya', 'Tamansari', '55223', 'JL alang2', NULL, 'online', 'unpaid', NULL, '2af06725-b4e4-416c-bacd-400621e050f6', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--
CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `created_at`, `updated_at`) VALUES
(27, 23, 18, 'Scarlett Loving Body Scrub 250ml', 57000.00, 1, '2026-11-16 20:00:00', '2026-11-16 20:00:00'),
(28, 24, 19, 'Nivea Body Intensive Lotion 380ml', 65900.00, 1, '2026-11-22 21:03:48', '2026-11-22 21:03:48'),
(29, 25, 67, 'UV Shield Aqua Fresh Essence SPF 50 PA++++', 45000.00, 1, '2026-11-24 12:23:47', '2026-11-24 12:23:47'),
(30, 26, 31, 'Niacinamide 10% + Zinc 1%', 185000.00, 1, '2026-11-24 12:24:32', '2026-11-24 12:24:32');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_requests`
--
CREATE TABLE `password_reset_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `new_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `handled_by` bigint UNSIGNED DEFAULT NULL,
  `handled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_requests`
--

INSERT INTO `password_reset_requests` (`id`, `user_id`, `new_password`, `note`, `status`, `handled_by`, `handled_at`, `created_at`, `updated_at`) VALUES
(1, 2, NULL, NULL, 'approved', 3, '2026-11-02 09:02:36', '2026-11-02 09:01:58', '2026-11-02 09:02:36'),
(2, 2, '$2y$12$awQfJjxVbiAY5SRs0hNvJeuztKJcOlmFSJaMGIKVDbxcTsdPBsd92', NULL, 'pending', NULL, NULL, '2026-11-02 11:08:13', '2026-11-02 11:08:13');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--
CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `product_cat` bigint UNSIGNED DEFAULT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `skin_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ingredients` text COLLATE utf8mb4_unicode_ci,
  `benefits` text COLLATE utf8mb4_unicode_ci,
  `how_to_use` text COLLATE utf8mb4_unicode_ci,
  `warnings` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(12,2) DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `weight` int DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_cat`, `brand`, `brand_id`, `name`, `description`, `skin_type`, `ingredients`, `benefits`, `how_to_use`, `warnings`, `price`, `stock`, `weight`, `image`, `created_at`, `updated_at`) VALUES
(17, 8, 'Scarlett', 3, 'Scarlett Fragrance Brightening Body Lotion Bright Mood Booster Jolly 300ml', 'Body lotion Scarlett varian Jolly dalam kemasan 300 ml, dengan aroma manis dan formula Bright Booster.', 'All skin types (klaim pada halaman produk)', 'Glutathione; Niacinamide; Kojic Acid; Vitamin E; +4 Essential Oils (kandungan utama yang dicantumkan brand).', 'Membantu melembapkan kulit tubuh dan memberikan aroma wangi; brand mencantumkan Bright Booster untuk perawatan tampilan kulit.', 'Oleskan secara merata pada tangan, kaki, dan bagian tubuh lainnya. Gunakan secara rutin sesuai petunjuk kemasan.', 'Hanya untuk pemakaian luar. Hindari area mata dan kulit yang luka. Hentikan penggunaan jika timbul iritasi.', 59000.00, 20, 380, 'https://medias.watsons.co.id/publishing/WTCID-43613-side-zoom.jpg?version=1731683425', '2026-11-15 17:12:48', '2026-11-20 08:51:57'),
(18, 8, 'Scarlett', 3, 'Scarlett Loving Body Scrub 250ml', 'Body scrub Scarlett varian Loving dalam kemasan 250 ml untuk perawatan eksfoliasi kulit tubuh.', 'All skin types (klaim pada halaman produk)', 'Glutathione; Vitamin E; 7X Ceramide (kandungan utama yang dicantumkan brand).', 'Membantu membersihkan dan menghaluskan permukaan kulit melalui eksfoliasi; brand juga mencantumkan manfaat brightening.', 'Oleskan pada tubuh secara merata, diamkan 2–3 menit, gosok perlahan, lalu bilas hingga bersih. Gunakan sekitar 2 kali seminggu sesuai toleransi kulit.', 'Jangan gunakan pada kulit luka atau sedang iritasi. Hindari menggosok terlalu kuat. Hentikan pemakaian jika terjadi iritasi.', 57000.00, 19, 330, 'https://down-id.img.susercontent.com/file/id-11134207-7r98v-llpw7rtuh1fy55', '2026-11-15 17:12:48', '2026-11-20 08:51:57'),
(19, 8, 'Nivea', 2, 'Nivea Body Intensive Lotion 380ml', 'Body lotion Nivea Intensive Moisture ukuran 380 ml untuk perawatan kulit tubuh, khususnya kulit yang membutuhkan kelembapan ekstra.', NULL, 'Grape Seed Oil; Avocado Oil (bahan yang disebut pada deskripsi Watsons).', 'Membantu melembapkan kulit tubuh secara intensif.', 'Oleskan secara merata pada kulit tubuh, terutama setelah mandi atau saat kulit terasa kering.', 'Hanya untuk pemakaian luar. Hindari kontak dengan mata. Hentikan pemakaian jika terjadi iritasi.', 65900.00, 20, 200, 'https://medias.watsons.co.id/publishing/WTCID-51913-front-zoom.jpg?version=1751556642', '2026-11-15 17:12:48', '2026-11-20 08:51:56'),
(20, 9, 'Scarlett', 3, 'Scarlett Extrait De Parfum Biru Samudra 30ml', 'Parfum Scarlett Extrait De Parfum varian Biru Samudra dalam kemasan 30 ml.', 'Semua Jenis Kulit', 'Bergamot; Mandarin; Rose; Freesia; Orris; Musk (notasi aroma yang dicantumkan brand, bukan daftar INCI).', 'Memberikan aroma parfum; brand mencantumkan ketahanan hingga 16 jam sebagai klaim produk.', 'Semprotkan pada titik nadi dari jarak sekitar 10–15 cm. Hindari menggosok area setelah penyemprotan.', 'Hindari kontak dengan mata dan kulit yang iritasi. Jauhkan dari panas/api. Hentikan penggunaan jika timbul reaksi kulit.', 60900.00, 20, 120, 'https://tse3.mm.bing.net/th/id/OIP.5tmsgOOs5tr0QLHy2duTTgAAAA?r=0&rs=1&pid=ImgDetMain&o=7&rm=3', '2026-11-15 17:12:48', '2026-11-20 08:51:57'),
(21, 9, 'Scarlett', 3, 'Scarlett Eau De Parfum La Foret Fairy 30ml', 'Parfum Scarlett Eau De Parfum varian La Foret Fairy dalam kemasan 30 ml.', 'Semua Jenis Kulit', 'Mandarin; Raspberry; Guava; Cassis; Rose; Peony; Peach; Cardamom; Petitgrain; Musk; Amber; Moss; Benzoin (notasi aroma yang dicantumkan brand, bukan daftar INCI).', 'Memberikan aroma parfum; brand mencantumkan ketahanan hingga 8 jam sebagai klaim produk.', 'Semprotkan pada titik nadi dari jarak sekitar 10–15 cm. Jangan menggosok area setelah penyemprotan.', 'Hindari kontak dengan mata dan kulit yang iritasi. Jauhkan dari panas/api. Hentikan penggunaan jika timbul reaksi kulit.', 59900.00, 20, 120, 'https://tse1.mm.bing.net/th/id/OIP.Qq6qLzDwCRD3Ume4YYBrVQHaHa?r=0&rs=1&pid=ImgDetMain&o=7&rm=3', '2026-11-15 17:12:48', '2026-11-20 08:51:57'),
(22, 10, 'Azarine', 1, 'Azarine Hydrasoothe Sunscreen Gel SPF45 PA++++ 50ml', 'Tabir surya wajah bertekstur gel dengan SPF45 PA++++ dalam kemasan 50 ml.', 'Semua Jenis Kulit', 'Aqua, Glycerin, Propanediol, Ethylhexyl Methoxycinnamate, Butyl Methoxydibenzoylmethane, Aloe Barbadensis Juice Extract, Portulaca Oleracea Extract, Sodium Hyaluronate, Niacinamide, Cucumis Sativus Fruit Extract, Camellia Sinensis Extract, Punica Granatum Fruit Extract, Phenoxyethanol, Octocrylene, Butylene Glycol, Sodium Acrylates/C10-30 Alkyl Acrylates Crosspolymer, Propolis, Picea Abies Extract, Allantoin, Xanthan Gum, Panthenol, Disodium EDTA, Lecithin', 'Membantu melindungi kulit dari paparan sinar UV sesuai tingkat perlindungan SPF45 PA++++ yang tertera pada produk.', 'Aplikasikan merata pada wajah dan leher sebagai tahap terakhir perawatan pagi, sebelum paparan matahari. Aplikasikan ulang secara berkala, terutama setelah berkeringat, berenang, atau mengeringkan wajah.', 'Hindari kontak dengan mata. Pemakaian tabir surya tidak menggantikan perlindungan fisik dari matahari. Hentikan pemakaian jika terjadi iritasi.', 53300.00, 20, 95, 'https://medias.watsons.co.id/publishing/WTCID-36163-side-zoom.jpg?version=1728581511', '2026-11-15 17:12:48', '2026-11-23 11:55:45'),
(23, 10, 'Skin Aqua', 4, 'Skin Aqua Moisture Milk SPF 50 40ml', 'Tabir surya Skin Aqua Moisture Milk dengan SPF 50 dalam kemasan 40 ml.', 'Semua Jenis Kulit', 'Water, Cyclopentasiloxane, Zinc Oxide, Silica, Lauryl PEG-9 Polydimethylsiloxyethyl Dimethicone, Methicone, Triethoxysilylethyl Polydimethylsiloxyethyl Hexyl Dimethicone, Ethylhexyl Methoxycinnamate, Polymethylsilsesquioxane, Butylene Glycol, Triethylhexanoin, Trimethylsiloxysilicate, Lauroyl Lysine, Diethylamino Hydroxybenzoyl Hexyl Benzoate, Methylparaben, Disodium EDTA, Propylparaben, Tocopheryl Acetate, Dipotassium Glycyrrhizate, Magnesium Ascorbyl Phosphate, Hydrolyzed Collagen, Sodium Hyaluronate, Panthenol', 'Membantu melindungi kulit dari paparan sinar UV dengan SPF 50.', 'Kocok bila petunjuk kemasan meminta demikian. Aplikasikan merata pada wajah dan leher sebelum beraktivitas di luar ruangan; ulangi pemakaian secara berkala.', 'Hindari kontak dengan mata. Hentikan pemakaian jika terjadi iritasi. Ikuti petunjuk pemakaian pada kemasan.', 55500.00, 20, 85, 'https://down-id.img.susercontent.com/file/id-11134207-7r98z-lzahi43eddmt86', '2026-11-15 17:12:48', '2026-11-23 11:54:56'),
(24, 5, 'Wardah', 5, 'Lightening Micellar Gentle Wash', 'Pembersih wajah dengan micellar technology untuk membantu membersihkan kotoran dan menjaga kelembapan kulit.', 'All Skin Types', 'Niacinamide, Glycerin, Licorice Root Extract', 'Membersihkan wajah dan membantu menjaga kelembapan kulit.', 'Basahi wajah, tuangkan produk pada telapak tangan, busakan, pijat lembut, lalu bilas.', 'Hentikan penggunaan apabila terjadi iritasi.', 26700.00, 20, 80, 'https://wardah-mainsite.s3-ap-southeast-1.amazonaws.com/medias/products/variant-1645422362.webp', '2026-11-23 11:36:25', '2026-11-23 11:41:16'),
(25, 5, 'Emina', 7, 'Bright Stuff Face Wash', 'Pembersih wajah dari rangkaian Bright Stuff untuk membersihkan wajah.', 'All Skin Types', 'Aqua, Glycerin, Myristic Acid, Propylene Glycol, Stearic Acid, Potassium Hydroxide, Lauric Acid, Decyl Glucoside, Glyceryl Stearate, Xylitol, Phenoxyethanol, Niacinamide, Fragrance, Panthenol, Allantoin, Chlorphenesin, Potassium Cocoyl Glycinate, Polyquaternium-7, Disodium EDTA, Potassium Cocoate, Ethylhexylglycerin, Hydroxypropyl Methylcellulose, Glycyrrhiza Glabra (Licorice) Root Extract, Xylitylglucoside, Anhydroxylitol, Sodium Benzoate, Glucose, Caprylic/Capric Triglyceride, Deoxyphytantriyl Palmitamide MEA, Hydrogenated Lecithin, Saccharomyces Lysate, Ceramide NP, Cholesterol, Disodium Succinate, Glutamic Acid, Glycine, Threonine, Valine', 'Membantu membersihkan kotoran pada wajah.', 'Gunakan pada wajah yang telah dibasahi, pijat lembut, lalu bilas.', 'Hindari kontak langsung dengan mata.', 35000.00, 20, 120, 'https://www.eminacosmetics.com/cfind/source/thumb/images/product/toner/bright-stuff/cover_w236_h255_face-wash-new.png', '2026-11-23 11:41:16', '2026-11-23 11:44:22'),
(26, 5, 'Somethinc', 8, 'Low pH Gentle Jelly Cleanser', 'Pembersih wajah bertekstur jelly dengan formula low pH.', 'All Skin Types', 'Water, Potassium Laureth Phosphate, Glycerin, Potassium Cocoyl Glutamate, Acrylates/C10-30 Alkyl Acrylate Crosspolymer, Butylene Glycol, Centella Asiatica Leaf Water, Caprylic/Capric Triglyceride, Aspartic Acid, Caprylyl Glycol, Sodium Cocoyl Glutamate, 1,2-Hexanediol, Lauryl Hydroxysultaine, Coco-Glucoside, Polyglyceryl-10 Laurate, Polyglyceryl-10 Myristate, Glyceryl Caprylate, Melaleuca Alternifolia (Tea Tree) Leaf Oil, Sodium Cocoyl Isethionate, Disodium EDTA, Calendula Officinalis Flower Extract, Mentha Piperita (Peppermint) Oil, Quillaja Saponaria Bark Extract, Artemisia Capillaris Extract', 'Membantu membersihkan wajah tanpa membuat kulit terasa terlalu kering.', 'Aplikasikan pada wajah basah, pijat lembut, kemudian bilas.', 'Hentikan pemakaian jika muncul reaksi iritasi.', 124000.00, 20, 50, 'https://www.beautyhaul.com/assets/uploads/products/thumbs/800x800/SOMETHINC_Low_pH_Gentle_Jelly_Cleanser_50ml.png', '2026-11-23 11:41:16', '2026-11-23 11:44:27'),
(27, 5, 'Skintific', 9, '5X Ceramide Barrier Repair Moisture Gel', 'Pelembap wajah yang diformulasikan untuk membantu menjaga skin barrier.', 'Dry Skin, Combination Skin', 'Ceramide', 'Membantu menjaga kelembapan dan skin barrier.', 'Oleskan secara merata pada wajah setelah menggunakan serum.', 'Lakukan patch test sebelum penggunaan.', 213100.00, 20, 100, 'https://id.skintific.com/cdn/shop/files/sg-11134201-8258r-mv5e3owg0b2h7a.jpg?v=1794195148&width=768', '2026-11-23 11:41:16', '2026-11-23 12:29:28'),
(28, 5, 'Avoskin', 10, 'Miraculous Refining Toner', 'Toner eksfoliasi untuk membantu merawat tekstur dan tampilan kulit wajah.', 'Normal Skin, Oily Skin', 'AHA, BHA, PHA', 'Membantu mengangkat sel kulit mati dan merawat tekstur kulit.', 'Aplikasikan menggunakan kapas atau telapak tangan sesuai petunjuk kemasan.', 'Gunakan sunscreen pada pagi hari dan hindari penggunaan berlebihan.', 226000.00, 20, 180, 'https://matahari.com/cdn/shop/files/65590234_1.jpg?v=1741898757', '2026-11-23 11:41:16', '2026-11-23 12:30:06'),
(29, 5, 'Cetaphil', 11, 'Gentle Skin Cleanser', 'Pembersih wajah lembut untuk membersihkan kulit sehari-hari.', 'Sensitive Skin, Dry Skin', 'Aqua, Glycerin, Cetearyl Alcohol, Panthenol, Niacinamide, Pantolactone, Xanthan Gum, Sodium Cocoyl Isethionate, Sodium Benzoate, Citric Acid', 'Membantu membersihkan kulit dengan lembut.', 'Gunakan pada kulit dan bilas atau ikuti petunjuk pada kemasan.', 'Hentikan penggunaan jika terjadi iritasi.', 98500.00, 20, 200, 'https://www.cetaphil.co.id/dw/image/v2/bggn_prd/on/demandware.static/-/Sites-galderma-id-m-catalog/default/dwf2153c2b/Rebranding/product_images/Gentle%20Skin%20Cleanser/Core_GSC%201000.png?q=85&sh=900&sm=fit&sw=900', '2026-11-23 11:41:16', '2026-11-23 12:29:28'),
(30, 5, 'CeraVe', 12, 'Moisturizing Cream', 'Krim pelembap untuk membantu menjaga kelembapan kulit.', 'Dry Skin', 'Ceramides, Hyaluronic Acid', 'Membantu menjaga kelembapan dan lapisan pelindung kulit.', 'Oleskan pada kulit sesuai kebutuhan.', 'Hentikan penggunaan jika muncul reaksi yang tidak diinginkan.', 119600.00, 20, 100, 'https://www.cerave.co.id/-/media/project/loreal/brand-sites/cerave/americas/id/scx/product/pdp/packshots/moisturising-cream/moisturising-cream-454g-lg.jpg?hash=692200795579E0C75B8343AF3AF642FF&rev=-1%3Fw%3D667', '2026-11-23 11:41:16', '2026-11-23 12:29:28'),
(31, 5, 'The Ordinary', 13, 'Niacinamide 10% + Zinc 1%', 'Serum wajah dengan niacinamide dan zinc.', 'Oily Skin, Combination Skin', 'Niacinamide, Zinc PCA', 'Membantu merawat tampilan pori-pori dan minyak berlebih.', 'Aplikasikan beberapa tetes pada wajah setelah pembersih.', 'Hindari area mata dan lakukan patch test.', 185000.00, 20, 100, 'https://theordinary.com/dw/image/v2/BFKJ_PRD/on/demandware.static/-/Sites-deciem-master/default/dwce8a7cdf/Images/products/The%20Ordinary/rdn-niacinamide-10pct-zinc-1pct-30ml.png?sh=800&sm=fit&sw=800', '2026-11-23 11:41:16', '2026-11-23 12:29:28'),
(32, 5, 'COSRX', 14, 'Advanced Snail 96 Mucin Power Essence', 'Essence dengan snail secretion filtrate untuk perawatan kelembapan kulit.', 'All Skin Types', 'Snail Secretion Filtrate', 'Membantu menjaga kelembapan dan membuat kulit terasa lebih halus.', 'Tepuk perlahan pada wajah setelah toner.', 'Hindari penggunaan apabila memiliki sensitivitas terhadap kandungannya.', 221000.00, 20, 180, 'https://www.cosrx.com/cdn/shop/files/Snail_96_Promo-Image-1_1024x1024.png?v=1763519441', '2026-11-23 11:41:16', '2026-11-23 12:29:28'),
(33, 6, 'Maybelline', 15, 'Fit Me Matte + Poreless Foundation', 'Foundation dengan hasil akhir matte untuk tampilan complexion sehari-hari.', 'Normal Skin, Oily Skin', 'Aqua/Water, Cyclohexasiloxane, Nylon-12, Isododecane, Alcohol Denat., Cyclopentasiloxane, PEG-10 Dimethicone, Cetyl PEG/PPG-10/1 Dimethicone, PEG-20, Polyglyceryl-4 Isostearate, Disteardimonium Hectorite, Phenoxyethanol, Magnesium Sulfate, Disodium Stearoyl Glutamate, HDI/Trimethylol Hexyllactone Crosspolymer, Methylparaben, Acrylonitrile/Methyl Methacrylate/Vinylidene Chloride Copolymer, Tocopherol, Butylparaben, Aluminum Hydroxide, Isobutane, Silica, Pentaerythrityl Tetra-Di-T-Butyl Hydroxyhydrocinnamate, May Contain CI 77891 Titanium Dioxide, CI 77491, CI 77492, CI 77499 Iron Oxides', 'Membantu meratakan tampilan warna kulit.', 'Aplikasikan secara merata menggunakan jari, spons, atau brush.', 'Pilih warna yang sesuai dengan warna kulit.', 171900.00, 20, 100, 'https://down-id.img.susercontent.com/file/id-11134207-822wj-mn0wke0clxc3f4', '2026-11-23 11:41:16', '2026-11-23 11:45:17'),
(34, 6, 'Wardah', 5, 'Exclusive Matte Lip Cream', 'Lip cream dengan hasil akhir matte untuk melengkapi riasan bibir.', 'All Skin Types', 'Cyclopentasiloxane, Isododecane, Trisiloxane, Trimethylsiloxysilicate, Diisostearyl Malate, Disteardimonium Hectorite, Synthetic Beeswax, Aluminum Starch Octenylsuccinate, Caprylyl Methicone, Propylene Carbonate, Silica Dimethyl Silylate, Tocopheryl Acetate, Cetyl Dimethicone, Propylparaben, Fragrance, Aluminum Hydroxide, Triethoxycaprylylsilane, Talc, CI 45410:2, CI 15850:1, CI 42090, CI 77891, CI 15850:2, CI 77491, CI 77492, CI 77499', 'Memberikan warna pada bibir dengan hasil akhir matte.', 'Aplikasikan secara merata pada bibir.', 'Hentikan penggunaan jika terjadi iritasi.', 72500.00, 20, 35, 'https://down-id.img.susercontent.com/file/68a705cb24b67c1b4f332fa0792c891d', '2026-11-23 11:41:16', '2026-11-23 11:57:26'),
(35, 6, 'Emina', 7, 'Creamatte Lip Cream', 'Lip cream dengan tekstur creamy dan hasil akhir matte.', 'All Skin Types', 'Cyclopentasiloxane, Isododecane, Butyrospermum Parkii (Shea Butter), Tridecyl Trimellitate, Aluminum Starch Octenylsuccinate, Trimethylsiloxysilicate, Titanium Dioxide, Disteardimonium Hectorite, Phytosteryl/Isostearyl/Cetyl/Stearyl/Behenyl Dimer Dilinoleate, Dimethicone, Jojoba Esters, Propylene Carbonate, Silica Dimethyl Silylate, Helianthus Annuus Seed Extract, Phenoxyethanol, Cetyl Dimethicone, Polyhydroxystearic Acid, Tocopheryl Acetate, Divinyldimethicone/Dimethicone Crosspolymer, Fragrance, Aluminum Hydroxide, Acacia Decurrens Flower Wax, Polyglycerin-3, Triethoxycaprylylsilane, Ethylhexylglycerin, May Contain CI 77492, CI 77891, CI 15850:2, CI 77491, CI 77499, CI 15850, CI 15850:1', 'Memberikan warna pada bibir.', 'Aplikasikan pada bibir secara merata.', 'Hentikan penggunaan jika muncul iritasi.', 52000.00, 20, 35, 'https://down-id.img.susercontent.com/file/sg-11134201-7rbmn-ll8ughb4ptp770', '2026-11-23 11:41:16', '2026-11-23 12:31:05'),
(36, 6, 'Make Over', 16, 'Powerstay Matte Powder Foundation', 'Bedak foundation untuk membantu memberikan tampilan complexion yang lebih merata.', 'Normal Skin, Oily Skin', 'Talc, Titanium Dioxide, Silica, Ethylhexyl Methoxycinnamate, Nylon-12, Octyldodecanol, Triethylhexanoin, Zinc Oxide, PCA Dimethicone, Phenoxyethanol, Hydrogen Dimethicone, Zinc Gluconate, Dimethicone, Ethylhexylglycerin, Triethoxycaprylylsilane, Aluminum Hydroxide, HDI/Trimethylol Hexyllactone Crosspolymer, Polymethyl Methacrylate, Dimethiconol Stearate, Methicone, Fragrance, Niacinamide, Tocopheryl Acetate, BHT, Tocopherol, Glycyrrhiza Glabra (Licorice) Root Extract', 'Membantu meratakan tampilan kulit wajah.', 'Aplikasikan menggunakan spons secara merata.', 'Pilih shade sesuai warna kulit.', 225000.00, 20, 100, 'https://cf.shopee.co.id/file/04ab479a40aac3fd3894a80d3f35edf2', '2026-11-23 11:41:16', '2026-11-23 12:31:05'),
(37, 6, 'ESQA', 17, 'Flawless Cushion Serum', 'Cushion complexion untuk membantu meratakan warna kulit.', 'All Skin Types', 'Water, Titanium Dioxide, Cyclopentasiloxane, Cyclohexasiloxane, Ethylhexyl Methoxycinnamate, Butylene Glycol, Lauryl PEG-9 Polydimethylsiloxyethyl Dimethicone, Niacinamide, Cetyl Ethylhexanoate, Silica, Pentylene Glycol, Glycerin, PEG-10 Dimethicone, Synthetic Fluorphlogopite, Magnesium Sulfate, Disteardimonium Hectorite, Phenoxyethanol, Triethoxycaprylylsilane, Stearic Acid, Betaine, Alumina, Centella Asiatica Extract, Vinyl Dimethicone/Methicone Silsesquioxane Crosspolymer, Sodium Hyaluronate, Ethylhexylglycerin, Disodium EDTA, 1,2-Hexanediol, BHT, Allantoin, Caffeine, Tocopherol, CI 77492, CI 77491, CI 77499', 'Memberikan coverage dan tampilan complexion yang merata.', 'Tekan puff pada cushion dan tepuk perlahan pada wajah.', 'Sesuaikan shade dengan warna kulit.', 229000.00, 20, 120, 'https://medias.watsons.co.id/publishing/WTCID-BP_42427-front-zoom.jpg?version=1750864824', '2026-11-23 11:41:16', '2026-11-23 12:31:05'),
(38, 6, 'Somethinc', 8, 'Copy Paste Breathable Mesh Cushion', 'Cushion untuk membantu memberikan hasil complexion yang merata.', 'All Skin Types', 'Water, Titanium Dioxide, Cyclopentasiloxane, Diphenylsiloxy Phenyl Trimethicone, Butylene Glycol, Ethylhexyl Methoxycinnamate, Lauryl PEG-9 Polydimethylsiloxyethyl Dimethicone, Dicaprylyl Carbonate, Cyclohexasiloxane, Glycerin, Caprylic/Capric Triglyceride, Trimethylsiloxysilicate, Phenyl Trimethicone, Pentylene Glycol, Synthetic Fluorphlogopite, Polypropylsilsesquioxane, PEG-10 Dimethicone, Zinc Oxide, Phenoxyethanol, Magnesium Sulfate, Dipentaerythrityl Hexa C5-9 Acid Esters, Dimethicone, Triethoxycaprylylsilane, Betaine, Polyhydroxystearic Acid, Aluminum Hydroxide, Disteardimonium Hectorite, Dimethicone Crosspolymer, Dimethicone/Vinyl Dimethicone Crosspolymer, Stearic Acid, Fragrance (Parfum), Ethylhexylglycerin, Lecithin, Ethylhexyl Palmitate, Isostearic Acid, Isopropyl Myristate, Hydrogenated Castor Oil Isostearate, Polyglyceryl-3 Polyricinoleate, Anthemis Nobilis Flower Extract, Disodium EDTA, 1,2-Hexanediol, BHT, Tocopherol, Allantoin, Caffeine', 'Membantu menyamarkan tampilan ketidaksempurnaan kulit.', 'Aplikasikan menggunakan puff dengan gerakan menepuk.', 'Gunakan shade yang sesuai.', 208000.00, 20, 120, 'https://medias.watsons.co.id/publishing/WTCID-19850-back-zoom.jpg?version=1728560625', '2026-11-23 11:41:16', '2026-11-23 12:31:05'),
(39, 6, 'Focallure', 18, 'Staymax Matte Liquid Lipstick', 'Lipstik cair untuk memberikan warna pada bibir.', 'All Skin Types', 'Isododecane, Aqua/Water, Hydrogenated C6-20 Polyolefin, Mineral Oil/Paraffinum Liquidum, Mica, Beeswax/Cera Alba, Hydrogenated Polyisobutene, Cetyl PEG/PPG-10/1 Dimethicone, Butylene Glycol, Glycerin, PEG-10 Dimethicone, Alcohol/Ethanol, Dimethicone, Trifluoropropyl Dimethicone/Trifluoropropyl Divinyldimethicone Crosspolymer, Bis-PEG-15 Dimethicone/IPDI Copolymer, Simmondsia Chinensis (Jojoba) Seed Oil, Daucus Carota Sativa (Carrot) Root Extract, Simethicone, Tocopherol, Phenoxyethanol, CI 77861, Tin Oxide, Fragrance/Parfum/Aroma, Titanium Dioxide/CI 77891, CI 77491, CI 15985, CI 77499, CI 77492, CI 15850', 'Memberikan tampilan warna bibir.', 'Aplikasikan pada bibir dan biarkan mengering.', 'Hentikan penggunaan jika terjadi iritasi.', 55000.00, 20, 35, 'https://down-id.img.susercontent.com/file/sg-11134201-82256-mhls6siv2f45ab', '2026-11-23 11:41:16', '2026-11-23 12:31:05'),
(40, 6, 'Maybelline', 15, 'Hypercurl Mascara', 'Maskara untuk membantu memberikan tampilan bulu mata yang lebih lentik.', 'All Skin Types', 'Isododecane, Cera Alba/Beeswax, Copernicia Cerifera Cera/Carnauba Wax, Disteardimonium Hectorite, Paraffin, Aqua/Water, Oryza Sativa Cera/Rice Bran Wax, Alcohol Denat., VP/Eicosene Copolymer, Propylene Carbonate, Allyl Stearate/VA Copolymer, Polyvinyl Laurate, Talc, Ethylenediamine/Stearyl Dimer Dilinoleate Copolymer, PEG-30 Glyceryl Stearate, Poly C10-30 Alkyl Acrylate, Parfum/Fragrance, Trimethylsiloxysilicate, Hydrogenated Jojoba Oil, Rayon, Polyacrylate Crosspolymer-6, Pentaerythrityl Tetra-Di-T-Butyl Hydroxyhydrocinnamate, BHT, May Contain CI 77491, CI 77492, CI 77499/Iron Oxides', 'Membantu mempertegas tampilan bulu mata.', 'Sapukan aplikator dari pangkal hingga ujung bulu mata.', 'Hindari kontak langsung dengan mata.', 65450.00, 20, 50, 'https://www.maybelline.co.id/-/media/project/loreal/brand-sites/mny/apac/id/products/eye-makeup/mascara/hypercurl-mascara/hypercurl-mascara-5.webp?ch=0&cw=0&cx=0&cy=0&hash=65ECFDE5C3010DC3429B00475B0541D5&rev=6e7d403e724e4079ae6d2d4ad9455869', '2026-11-23 11:41:16', '2026-11-23 12:31:05'),
(41, 6, 'Hanasui', 19, 'Mattedorable Lip Cream', 'Lip cream untuk memberikan warna pada bibir dengan tampilan matte.', 'All Skin Types', 'Isododecane, Cyclopentasiloxane, Aluminium Starch Octenylsuccinate, Kaolin, Microcrystalline Wax, Polyglyceryl-2 Triisostearate, Trimethylsiloxysilicate, Disteardimonium Hectorite, Propylene Carbonate, Dimethicone/Vinyl Dimethicone Crosspolymer, Phenoxyethanol, Fragrance, Olea Europaea Husk Oil, Tocopheryl Acetate, Aluminum Hydroxide, Isononyl Isononanoate, Isopropyl Myristate, Isopropyl Titanium Triisostearate, Polyhydroxystearic Acid, Stearalkonium Hectorite, Titanium Dioxide, Iron Oxides, CI 19140, CI 15850, CI 45410, CI 42090', 'Memberikan warna dan mempercantik tampilan bibir.', 'Aplikasikan secara merata pada bibir.', 'Hentikan pemakaian jika terjadi iritasi.', 33300.00, 20, 35, 'https://image.idntimes.com/post/20250913/hanasui-mattedorable-lip-cream-12_ad9436d3-bcf4-45dd-a28c-074a36e2b526.png?tr=w-1200', '2026-11-23 11:41:16', '2026-11-23 12:31:05'),
(42, 7, 'L\'Oreal Paris', 20, 'Elseve Fall Resist Shampoo', 'Sampo untuk membantu merawat rambut dan kulit kepala.', 'All Hair Types', 'Aqua/Water, Sodium Laureth Sulfate, Cocamidopropyl Betaine, Dimethicone, Sodium Chloride, CI 77891/Titanium Dioxide, Guar Hydroxypropyltrimonium Chloride, Mica, Coco-Betaine, Sodium Benzoate, Sodium Hydroxide, Phenoxyethanol, Steareth-6, Acetic Acid, Arginine, PEG-100 Stearate, Trideceth-10, Trideceth-3, Salicylic Acid, Limonene, Fumaric Acid, Linalool, Benzyl Alcohol, Benzyl Salicylate, Amodimethicone, Alpha-Isomethyl Ionone, Parfum/Fragrance, Carbomer, Citric Acid, Citronellol, Hexylene Glycol, Glycerin, Glycol Distearate', 'Membersihkan rambut dan membantu merawat rambut rapuh.', 'Aplikasikan pada rambut basah, pijat kulit kepala, lalu bilas.', 'Hindari kontak dengan mata.', 29900.00, 20, 400, 'https://medias.watsons.com.my/publishing/WTCMY-27185-side-zoom.jpg?version=1724790021', '2026-11-23 11:41:16', '2026-11-23 11:47:57'),
(43, 7, 'Makarizo', 21, 'Hair Energy Fibertherapy Shampoo', 'Sampo untuk membersihkan dan merawat rambut.', 'All Hair Types', 'Water (Aqua), Sodium Laureth Sulfate, Glycerin, Cocamidopropyl Betaine, Cocamide DEA, Fragrance (Parfum), Sodium Chloride, Dimethiconol, Triethanolamine, Bis-Cetearyl Amodimethicone, Glycol Distearate, Carbomer, Guar Hydroxypropyltrimonium Chloride, Panthenyl Ethyl Ether, DMDM Hydantoin, Amodimethicone, Bisamino PEG/PPG-41/3 Aminoethyl PG-Propyl Dimethicone, Sodium Bisulfite, Tetrasodium EDTA, Aloe Vera Leaf Extract, Polyquaternium-47, Sodium Metabisulfite, Formic Acid, Iodopropynyl Butylcarbamate, Oryza Sativa Bran Oil, Passiflora Edulis Seed Oil, Hydrolyzed Keratin, Sodium Benzoate, Euterpe Oleracea Fruit Oil, Potassium Sorbate, Cucumis Melo Fruit Extract, Persea Gratissima Fruit Extract, Trigonella Foenum-Graecum Seed Extract, CI 47005, Citric Acid, CI 42090, Citrus Aurantifolia Fruit Extract', 'Membantu membersihkan rambut dan kulit kepala.', 'Gunakan pada rambut basah, pijat, kemudian bilas.', 'Hindari kontak dengan mata.', 48400.00, 20, 350, 'https://down-id.img.susercontent.com/file/id-11134207-7quky-lh2x6h01ktb3ff', '2026-11-23 11:41:16', '2026-11-23 12:32:00'),
(44, 7, 'Pantene', 22, 'Pro-V Miracles Hair Fall Control Shampoo', 'Sampo untuk membantu merawat rambut yang mudah patah dan rontok akibat patah.', 'All Hair Types', 'Water, Sodium Laureth Sulfate, Dimethicone, Cocamidopropyl Betaine, Sodium Citrate, Sodium Xylenesulfonate, Glycol Distearate, Sodium Chloride, Fragrance, Stearyl Alcohol, Cetyl Alcohol, Guar Hydroxypropyltrimonium Chloride, Citric Acid, Sodium Benzoate, Tetrasodium EDTA, Polyquaternium-6, Histidine, Panthenol, Panthenyl Ethyl Ether, Oryza Sativa (Rice Bran) Oil, Methylchloroisothiazolinone, Methylisothiazolinone', 'Membersihkan rambut dan membantu merawat kekuatan rambut.', 'Aplikasikan pada rambut basah dan bilas hingga bersih.', 'Hindari kontak dengan mata.', 59750.00, 20, 400, 'https://aapharmacy.com.my/cdn/shop/files/Slide15_054236dc-8518-4966-ae00-431614750abe.jpg?v=1732156450&width=3780', '2026-11-23 11:41:16', '2026-11-23 12:32:00'),
(45, 7, 'TRESemme', 23, 'Keratin Smooth Shampoo', 'Sampo untuk membantu merawat rambut agar terasa lebih halus.', 'All Hair Types', 'Keratin', 'Membantu membersihkan dan merawat rambut.', 'Gunakan pada rambut basah, pijat, lalu bilas.', 'Hindari kontak langsung dengan mata.', 85400.00, 20, 400, 'https://cdn.mafrservices.com/pim-content/SAU/media/product/646909/1779626400/646909_main.jpg', '2026-11-23 11:41:16', '2026-11-23 12:32:00'),
(46, 7, 'Dove', 24, 'Total Hair Fall Treatment Shampoo', 'Sampo untuk membersihkan rambut dan membantu merawat rambut rapuh.', 'All Hair Types', 'Aqua, Sodium Laureth Sulfate, Dimethiconol, Cocamidopropyl Betaine, Sodium Chloride, Perfume, Glycerin, TEA-Dodecylbenzenesulfonate, Guar Hydroxypropyltrimonium Chloride, Lysine HCl, Helianthus Annuus (Sunflower) Seed Oil, Argania Spinosa Kernel Oil, Sodium Benzoate, Citric Acid', 'Membantu merawat rambut agar terasa lebih kuat.', 'Aplikasikan pada rambut basah dan bilas.', 'Hindari kontak dengan mata.', 46500.00, 20, 350, 'https://guardianindonesia.co.id/media/catalog/product/3/0/3026916.jpg?auto=webp&fit=cover&format=pjpg&height=800&width=640', '2026-11-23 11:41:16', '2026-11-23 12:32:00'),
(47, 7, 'Ellips', 25, 'Hair Vitamin Smooth and Silky', 'Vitamin rambut untuk membantu merawat rambut agar terasa halus.', 'All Hair Types', 'Cyclopentasiloxane, Dimethiconol, Fragrance, Tocopheryl Acetate, Squalene, Glycine Soja (Soybean) Oil, CI 47000, CI 26100, Argania Spinosa Kernel Oil, Aloe Barbadensis Leaf Extract, Honey, Panthenol, Cocos Nucifera (Coconut) Oil, Carica Papaya (Papaya) Fruit Extract, BHT', 'Membantu merawat rambut dan memberikan tampilan berkilau.', 'Aplikasikan isi kapsul pada batang rambut.', 'Hindari area kulit kepala jika tidak sesuai petunjuk kemasan.', 16000.00, 20, 35, 'https://medias.watsons.co.id/publishing/WTCID-70244-front-zoom.jpg?version=1755529814', '2026-11-23 11:41:16', '2026-11-23 12:32:00'),
(48, 7, 'Mise en Scene', 26, 'Perfect Serum Original', 'Serum rambut untuk membantu merawat rambut kering dan kusut.', 'Dry Hair', 'Cyclopentasiloxane, Dimethiconol, Disiloxane, C12-15 Alkyl Benzoate, Fragrance/Parfum, Argania Spinosa Kernel Oil, Simmondsia Chinensis (Jojoba) Seed Oil, Cocos Nucifera (Coconut) Oil, Prunus Armeniaca (Apricot) Kernel Oil, Olea Europaea (Olive) Fruit Oil, Sclerocarya Birrea Seed Oil, Camellia Japonica Seed Oil, Coco-Caprylate/Caprate, Caprylic/Capric Triglyceride, Capsicum Annuum Extract, Beta-Carotene, Tocopherol, Glycolipids', 'Membantu membuat rambut terasa lebih lembut dan mudah diatur.', 'Gunakan secukupnya pada batang hingga ujung rambut.', 'Hindari kontak dengan mata.', 117500.00, 20, 150, 'https://medias.watsons.com.sg/publishing/WTCSG-35669-side-zoom.jpg?version=1762190426', '2026-11-23 11:41:16', '2026-11-23 12:32:00'),
(49, 7, 'Natur', 27, 'Natural Extract Shampoo Aloe Vera', 'Sampo dengan ekstrak aloe vera untuk perawatan rambut.', 'All Hair Types', 'Aloe Vera Extract', 'Membantu membersihkan rambut dan kulit kepala.', 'Gunakan pada rambut basah, pijat, lalu bilas.', 'Hindari kontak dengan mata.', 17500.00, 20, 250, 'https://favo.id/cdn/shop/products/NTURAM1004_Natur-Shampoo-Aloevera-Extract---270-mL.jpg?v=1619491752', '2026-11-23 11:41:16', '2026-11-23 12:32:00'),
(50, 8, 'Scarlett', 3, 'Jolly Body Lotion', 'Body lotion untuk membantu menjaga kelembapan kulit tubuh.', 'All Skin Types', 'Aqua (Water), Glycerin, Niacinamide, Glutathione, Cetearyl Alcohol, Glyceryl Stearate, Caprylic/Capric Triglyceride, Dimethicone, Tocopheryl Acetate, Carbomer, Triethanolamine, Phenoxyethanol, Fragrance', 'Membantu melembapkan kulit tubuh.', 'Oleskan secara merata pada kulit tubuh.', 'Hentikan penggunaan jika terjadi iritasi.', 44500.00, 20, 400, 'https://cdn.beautyhaul.com/assets/uploads/products/thumbs/800x800/WEBSITE-SLIDE_1.-JOLLY_7.png', '2026-11-23 11:41:16', '2026-11-23 11:51:18'),
(51, 8, 'Nivea', 2, 'Extra White Repair and Protect Body Lotion', 'Lotion tubuh untuk membantu melembapkan dan merawat kulit.', 'All Skin Types', 'Aqua, C12-15 Alkyl Benzoate, Glycerin, Octocrylene, Butyl Methoxydibenzoylmethane, Phenylbenzimidazole Sulfonic Acid, Cetyl Alcohol, Dimethicone, Palmitic Acid, Stearic Acid, Phenoxyethanol, Cetyl Palmitate, Glyceryl Stearate, Tapioca Starch, Sodium Hydroxide, Carbomer, Glyceryl Glucoside, Trisodium EDTA, Myristic Acid, Sodium Ascorbyl Phosphate, Propylene Glycol, Arachidic Acid, Oleic Acid, Vitis Vinifera Seed Oil, Alcohol Denat, Methylisothiazolinone, Citric Acid, Polysorbate 20, Glycyrrhiza Glabra Root Extract, Acerola (Malpighia Glabra) Extract, Trideceth-9, Myrciaria Dubia Fruit Juice, Bisabolol, Parfum', 'Membantu menjaga kelembapan kulit tubuh.', 'Gunakan secara merata pada kulit tubuh.', 'Hindari penggunaan pada kulit yang mengalami iritasi.', 33000.00, 20, 400, 'https://down-id.img.susercontent.com/file/a1b43160fff0f01e40ae644988d4fc15', '2026-11-23 11:41:16', '2026-11-23 11:56:38'),
(52, 8, 'Vaseline', 28, 'Healthy Bright UV Extra Brightening Lotion', 'Lotion tubuh untuk membantu melembapkan dan merawat tampilan kulit.', 'All Skin Types', 'Water, Isopropyl Myristate, Stearic Acid, Glyceryl Stearate, Mineral Oil, Ethylhexyl Methoxycinnamate, Niacinamide, Glycerin, Petrolatum, Butyl Methoxydibenzoylmethane, Tocopheryl Acetate, Sodium Ascorbyl Phosphate, Titanium Dioxide, Dimethicone, Triethanolamine, Carbomer, Perfume, Cetyl Alcohol, Disodium EDTA, Hydrated Silica, Aluminum Hydroxide, Alginic Acid, Phenoxyethanol, Methylparaben, Propylparaben, BHT', 'Membantu melembapkan kulit tubuh.', 'Oleskan secara merata pada kulit tubuh.', 'Hentikan penggunaan jika terjadi reaksi yang tidak diinginkan.', 23900.00, 20, 300, 'https://medias.watsons.com.ph/publishing/WTCPH-50035758-back-zoom.jpg?version=1721921226', '2026-11-23 11:41:16', '2026-11-23 11:51:23'),
(53, 8, 'Dove', 24, 'Deeply Nourishing Body Wash', 'Sabun mandi cair untuk membersihkan kulit tubuh.', 'All Skin Types', 'Water, Cocamidopropyl Betaine, Sodium Laureth Sulfate, Sodium Cocoyl, Sodium Chloride, Glycerin, Fragrance, Sodium Hydroxide, Stearic Acid, Acrylates/C10-30 Alkyl Acrylate Crosspolymer, PPG-9, Lauric Acid, DMDM Hydantoin, BHT, Sodium Palmitate, Tetrasodium EDTA, Coconut Acid, Glycerin Sodium Stearate, Citric Acid, Sodium Palm Kernelate, Methylisothiazolinone', 'Membersihkan kulit tubuh dan membantu menjaga kelembapan.', 'Tuangkan pada tangan atau spons, busakan, lalu bilas.', 'Hindari kontak langsung dengan mata.', 32175.00, 20, 350, 'https://cdn.mafrservices.com/pim-content/KEN/media/product/47314/1773910804/47314_main.jpg', '2026-11-23 11:41:16', '2026-11-23 12:32:33'),
(54, 8, 'Herborist', 29, 'Body Butter Zaitun', 'Body butter untuk membantu menjaga kelembapan kulit tubuh.', 'Dry Skin', 'Olive Oil', 'Membantu melembapkan kulit yang terasa kering.', 'Oleskan pada kulit tubuh dan pijat hingga meresap.', 'Hentikan penggunaan apabila terjadi iritasi.', 27500.00, 20, 150, 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/89/MTA-4602247/herborist_herborist_body_butter_zaitun_-_80gr_full02_fgetg459.jpg', '2026-11-23 11:41:16', '2026-11-23 12:32:33'),
(55, 8, 'The Body Shop', 30, 'Shea Body Butter', 'Body butter dengan shea butter untuk perawatan kulit tubuh.', 'Dry Skin', 'Shea Butter', 'Membantu melembapkan kulit tubuh.', 'Pijatkan pada kulit tubuh hingga meresap.', 'Hindari area kulit yang mengalami iritasi.', 199000.00, 20, 350, 'https://cdn.shopify.com/s/files/1/0840/6679/5785/files/RESIZED_1097370_SHEA_BODY_BUTTER_200ml_BRONZE_INECMPS090_7c48c3ba-6611-483b-bb8e-dfce53db150a.jpg?v=1762430733', '2026-11-23 11:41:16', '2026-11-23 12:32:33'),
(56, 8, 'Shinzui', 31, 'Skin Lightening Body Cleanser', 'Pembersih tubuh untuk penggunaan sehari-hari.', 'All Skin Types', 'Aqua/Water, Lauric Acid, Sodium Laureth Sulfate, Lauryl Hydroxysultaine, Glycol Stearate, Glycerin, Potassium Hydroxide, Stearic Acid, Beta-Glucan, PEG-40 Hydrogenated Castor Oil, Fragrance/Parfum/Aroma, Sodium Chloride, Tocopheryl Acetate, Tetrasodium EDTA, Hydroxypropyl Methylcellulose, Dextrin, Tricholoma Matsutake Extract, Hexamidine Diisethionate, Prunus Lannesiana Flower Extract, Pentaerythrityl Tetra-Di-T-Butyl Hydroxyhydrocinnamate, Disodium Distyrylbiphenyl Disulfonate, Ascorbic Acid, Malic Acid', 'Membersihkan kulit tubuh.', 'Gunakan saat mandi dan bilas hingga bersih.', 'Hindari kontak dengan mata.', 38000.00, 20, 300, 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/103/MTA-20961786/shinzui_shinzui-hana-body-cleanser--250-ml-_full04.jpg', '2026-11-23 11:41:16', '2026-11-23 12:32:33'),
(57, 8, 'Lifebuoy', 32, 'Total 10 Body Wash', 'Sabun mandi cair untuk membersihkan kulit tubuh.', 'All Skin Types', 'Water, Sodium Laureth Sulfate, Sodium Chloride, Glycol Distearate, Cocamide MEA, Perfume, Citric Acid, Acrylate Copolymer, Sodium Benzoate, PPG-9, Tetrasodium EDTA, Sodium Hydroxide, VP/VA Copolymer, Terpineol, Pentasodium Pentate, Sodium Carbonate, Thymol, Silver Oxide, Lauric Acid, CI 45100', 'Membantu membersihkan kulit tubuh.', 'Gunakan pada tubuh yang basah, busakan, lalu bilas.', 'Hindari kontak dengan mata.', 23300.00, 20, 550, 'https://down-id.img.susercontent.com/file/90a66eddf2a9ece6bba13ef2ca541dfe', '2026-11-23 11:41:16', '2026-11-23 12:32:33'),
(58, 9, 'Mykonos', 33, 'Luminos Eau de Parfum', 'Produk parfum untuk memberikan aroma pada tubuh.', 'All Skin Types', 'Alcohol Denat., Fragrance, Dipropylene Glycol, Linalyl Acetate, Limonene, Methylenedioxyphenyl Methylpropanal, Isoamyl Allylglycolate', 'Memberikan aroma wangi pada tubuh.', 'Semprotkan secukupnya pada area tubuh yang diinginkan.', 'Hindari kontak dengan mata dan kulit yang mengalami iritasi.', 149000.00, 20, 150, 'https://officialmykonos.com/cdn/shop/files/luminos50mlsku_1_2.jpg?v=1783073874&width=1946', '2026-11-23 11:41:16', '2026-11-23 12:33:25'),
(59, 9, 'HMNS', 34, 'Orgasm Eau de Parfum', 'Parfum dengan aroma untuk penggunaan sehari-hari.', 'All Skin Types', 'Alcohol, Fragrance, CI 14700, CI 40820', 'Memberikan aroma wangi pada tubuh.', 'Semprotkan secukupnya pada kulit atau pakaian.', 'Jauhkan dari api dan hindari kontak dengan mata.', 289000.00, 20, 200, 'https://hmnsperfume.com/cdn/shop/files/ORGSM_eefefeb9-220f-4c67-8116-887618b630f7.png?v=1789546324&width=800', '2026-11-23 11:41:16', '2026-11-23 12:33:25'),
(60, 9, 'The Body Shop', 30, 'White Musk Eau de Toilette', 'Parfum dengan aroma musk untuk penggunaan sehari-hari.', 'All Skin Types', 'Alcohol (69.9%), Aqua/Water/Eau, Parfum/Fragrance, t-Butyl Alcohol, Hydroxycitronellal, Cinnamyl Alcohol, Linalool, Alpha-Isomethyl Ionone, Benzyl Benzoate, Benzyl Alcohol, Denatonium Benzoate, Coumarin, Isoeugenol', 'Memberikan aroma wangi pada tubuh.', 'Semprotkan secukupnya pada area tubuh.', 'Hindari kontak dengan mata.', 399000.00, 20, 150, 'https://us.thebodyshop.com/cdn/shop/files/WHITE_MUSK_EAU_DE_TOILETTE_60ml_1_INECOPS044_4fdaf2e5-0a32-4111-9ae6-dd1a8c07b28f.jpg?v=1776166150&width=1512', '2026-11-23 11:41:16', '2026-11-23 12:36:16'),
(61, 9, 'Zara', 35, 'Red Temptation Eau de Parfum', 'Parfum untuk memberikan aroma pada tubuh.', 'All Skin Types', 'Alcohol Denat., Water, Parfum, Ethylhexyl Methoxycinnamate, BHT, Butyl Methoxydibenzoylmethane, Ethylhexyl Salicylate, Limonene, Benzyl Benzoate, Linalool, CI 19140, Acid Red 33', 'Memberikan aroma wangi.', 'Semprotkan pada area tubuh yang diinginkan.', 'Jauhkan dari panas dan api.', 399000.00, 20, 150, 'https://maulux.mu/cdn/shop/files/Zara_Red_Temptation_EDP_80ML2.jpg?v=1779629314', '2026-11-23 11:41:16', '2026-11-23 12:36:16'),
(62, 9, 'Miniso', 36, 'Perfume Eau de Toilette', 'Parfum untuk penggunaan sehari-hari.', 'All Skin Types', NULL, 'Memberikan aroma wangi pada tubuh.', 'Semprotkan secukupnya pada tubuh.', 'Periksa varian dan komposisi pada kemasan sebelum penggunaan.', 99000.00, 20, 150, 'https://down-id.img.susercontent.com/file/sg-11134201-7rffj-m9vcue0udpi405', '2026-11-23 11:41:16', '2026-11-23 12:36:16'),
(63, 9, 'Victoria\'s Secret', 37, 'Bare Vanilla Fragrance Mist', 'Fragrance mist untuk memberikan aroma pada tubuh.', 'All Skin Types', 'Alcohol Denat., Water (Aqua, Eau), Fragrance (Parfum), Hexamethylindanopyran, Propylene Glycol, Glycerin, Vanillin, Tetramethyl Acetyloctahydronaphthalenes, Linalyl Acetate, Limonene, Linalool, Pinene, Citrus Aurantium Peel Oil, Benzyl Alcohol, Beta-Caryophyllene, PPG-26-Buteth-26, PEG-40 Hydrogenated Castor Oil, Ethylhexyl Methoxycinnamate, Butyl Methoxydibenzoylmethane, Ethylhexyl Salicylate', 'Memberikan aroma wangi yang ringan.', 'Semprotkan pada tubuh sesuai kebutuhan.', 'Hindari kontak dengan mata dan sumber api.', 299000.00, 20, 350, 'https://www.victoriassecretbeauty.co.id/on/demandware.static/-/Sites-vs-master-beauty-catalog/default/dwca2fe3c4/images/large/112592038527_OM_F.jpg', '2026-11-23 11:41:16', '2026-11-23 12:33:25'),
(64, 9, 'Bath & Body Works', 38, 'Japanese Cherry Blossom Fine Fragrance Mist', 'Fragrance mist dengan aroma Japanese Cherry Blossom.', 'All Skin Types', 'Alcohol Denat., Water (Aqua, Eau), Fragrance (Parfum), Propylene Glycol, Alpha-Isomethyl Ionone, Benzyl Salicylate, Linalool, Coumarin, Citronellol, Eugenol, Red 40 (CI 16035), Cinnamyl Alcohol, Benzyl Benzoate, Red 33 (CI 17200), Ethylhexyl Methoxycinnamate, Ethylhexyl Salicylate, Butyl Methoxydibenzoylmethane, Yellow 5 (CI 19140), Ext. Violet 2 (CI 60730)', 'Memberikan aroma wangi pada tubuh.', 'Semprotkan secukupnya pada tubuh.', 'Hindari kontak dengan mata dan api.', 299000.00, 20, 350, 'https://www.bathandbodyworks.co.id/dw/image/v2/BLFZ_PRD/on/demand.static/-/Sites-bbw-master-catalog/default/dwb5941b71/hires/28009133.jpg?sh=600&sm=fit&sw=350', '2026-11-23 11:41:16', '2026-11-23 12:33:30'),
(65, 9, 'Carl & Claire', 39, 'Eau de Parfum', 'Parfum untuk melengkapi aktivitas sehari-hari.', 'All Skin Types', NULL, 'Memberikan aroma wangi pada tubuh.', 'Semprotkan secukupnya pada area tubuh.', 'Pastikan varian dan komposisi sesuai dengan produk yang dipilih.', 149000.00, 20, 150, 'https://images.soco.id/615-Review-Carl-%26-Claire-Perfumery---Beauty-Journal-cherish.jpg.jpeg', '2026-11-23 11:41:16', '2026-11-23 12:36:16'),
(66, 10, 'Azarine', 1, 'Hydrasoothe Sunscreen Gel SPF 45 PA++++', 'Sunscreen gel untuk membantu melindungi kulit dari paparan sinar matahari.', 'All Skin Types', 'Aqua, Glycerin, Propanediol, Ethylhexyl Methoxycinnamate, Butyl Methoxydibenzoylmethane, Aloe Barbadensis Juice Extract, Portulaca Oleracea Extract, Sodium Hyaluronate, Niacinamide, Cucumis Sativus Fruit Extract, Tea Camellia Sinensis Extract, Punica Granatum Fruit Extract, Phenoxyethanol, Octocrylene, Butylene Glycol, Sodium Acrylates/C10-30 Alkyl Acrylates Crosspolymer, Propolis, Picea Abies Extract, Allantoin, Xanthan Gum, Panthenol, Disodium EDTA, Lecithin', 'Membantu melindungi kulit dari paparan sinar UVA dan UVB.', 'Aplikasikan merata sebelum beraktivitas di luar ruangan dan gunakan ulang secara berkala.', 'Hindari kontak dengan mata.', 53500.00, 20, 100, 'https://www.gopicky.com/_next/image?q=75&url=https%3A%2F%2Fmedia.gopicky.com%2Fproducts%2Fimages%2FAzarine-HydrasootheSunscreenGelSPF45-2278792817&w=3840', '2026-11-23 11:41:16', '2026-11-23 11:57:27'),
(67, 10, 'Wardah', 5, 'UV Shield Aqua Fresh Essence SPF 50 PA++++', 'Sunscreen wajah untuk membantu melindungi kulit dari paparan sinar matahari.', 'All Skin Types', 'Aqua, Ethylhexyl Methoxycinnamate, Ethylhexyl Salicylate, Homosalate, Butyl Methoxydibenzoylmethane, Octocrylene, Polymethyl Methacrylate, Butylene Glycol, Butyloctyl Salicylate, Bis-Ethylhexyloxyphenol Methoxyphenyl Triazine, Cyclopentasiloxane, Polyurethane-79, Arachidyl Alcohol, Polyester-7, Behenyl Alcohol, Bisabolol, Nylon-12, Phenoxyethanol, Polyacrylate Crosspolymer-6, Caprylic/Capric Triglycerides, Neopentyl Glycol Diheptanoate, Chlorphenesin, Arachidyl Glucoside, Propanediol, Polyacrylate-13, Disodium EDTA, Fragrance, Polyisobutene, Pongamia Glabra Seed Oil, Polysorbate 20, Artemisia Capillaris Flower Extract', 'Membantu melindungi kulit dari sinar UVA dan UVB.', 'Gunakan secara merata sebelum terpapar sinar matahari dan aplikasikan ulang.', 'Hindari area mata.', 45000.00, 20, 100, 'https://www.static-src.com/wcsstore/Indraprastha/images/catalog/full/catalog-image/115/MTA-138988537/wardah_wardah_uv_shield_aqua_fresh_essence_-spf_50-_30ml_full02_cv8drx8d.jpg', '2026-11-23 11:41:16', '2026-11-23 12:36:16'),
(68, 10, 'Skin Aqua', 4, 'UV Moisture Milk SPF 50+ PA++++', 'Sunscreen berbentuk milk untuk membantu melindungi kulit dari sinar matahari.', 'All Skin Types', 'Water, Cyclopentasiloxane, Zinc Oxide, Silica, Lauryl PEG-9 Polydimethylsiloxyethyl Dimethicone, Methicone, Triethoxysilylethyl Polydimethylsiloxyethyl Hexyl Dimethicone, Ethylhexyl Methoxycinnamate, Polymethylsilsesquioxane, Butylene Glycol, Triethylhexanoin, Trimethylsiloxysilicate, Lauroyl Lysine, Diethylamino Hydroxybenzoyl Hexyl Benzoate, Methylparaben, Disodium EDTA, Propylparaben, Tocopheryl Acetate, Dipotassium Glycyrrhizate, Magnesium Ascorbyl Phosphate, Hydrolyzed Collagen, Sodium Hyaluronate, Panthenol', 'Membantu memberikan perlindungan terhadap paparan sinar UV.', 'Kocok jika diarahkan pada kemasan, lalu aplikasikan merata pada kulit.', 'Gunakan ulang secara berkala.', 49900.00, 20, 100, 'https://rohto.co.id/assets/uploads/product/20251013041805.png', '2026-11-23 11:41:16', '2026-11-23 12:36:16'),
(69, 10, 'Biore', 40, 'UV Aqua Rich Watery Essence SPF 50+ PA++++', 'Sunscreen bertekstur essence untuk membantu melindungi kulit dari sinar UV.', 'All Skin Types', 'Water, Alcohol, Ethylhexyl Methoxycinnamate, Lauryl Methacrylate/Sodium Methacrylate Crosspolymer, Diethylamino Hydroxybenzoyl Hexyl Benzoate, Dimethicone, Acrylates/C10-30 Acrylate Crosspolymer, Glyceryl Stearate, Agar, Polyvinyl Alcohol, Sodium Hyaluronate, Citrus Grandis Extract, Citrus Medica Limonum Extract, Citrus Aurantium Dulcis Extract, Xylitol, Butylene Glycol, Propylene Glycol, Sodium Hydroxide, Potassium Hydroxide, Phenoxyethanol, Disodium EDTA, BHT, Fragrance', 'Membantu melindungi kulit dari paparan sinar UVA dan UVB.', 'Aplikasikan merata pada wajah dan leher sebelum beraktivitas.', 'Hindari kontak dengan mata.', 66000.00, 20, 100, 'https://down-id.img.susercontent.com/file/d915cb50f4c65ac85f00e0f9c11411e0', '2026-11-23 11:41:16', '2026-11-23 12:36:16'),
(70, 10, 'Beauty of Joseon', 41, 'Relief Sun Rice + Probiotics SPF 50+ PA++++', 'Sunscreen wajah dengan rice extract dan probiotics.', 'All Skin Types', 'Rice Extract, Probiotics', 'Membantu melindungi kulit dari paparan sinar UV.', 'Aplikasikan merata pada wajah dan leher sebagai tahap akhir skincare pagi.', 'Gunakan ulang secara berkala.', 129000.00, 20, 100, 'https://lloydspharmacy.com/cdn/shop/files/0159211_1024x.jpg?v=1764323619', '2026-11-23 11:41:16', '2026-11-23 12:36:16'),
(71, 10, 'Skin1004', 42, 'Madagascar Centella Hyalu-Cica Water-Fit Sun Serum SPF 50+ PA++++', 'Sunscreen serum untuk membantu melindungi kulit dari sinar matahari.', 'All Skin Types', 'Centella Asiatica, Hyaluronic Acid', 'Membantu melindungi kulit dari sinar UV dan menjaga kelembapan.', 'Aplikasikan merata pada wajah dan leher sebelum terpapar sinar matahari.', 'Hindari kontak langsung dengan mata.', 135000.00, 20, 100, 'https://kbeauty.sale/cdn/shop/files/1719555662660_28dd7332ae7049119d88d650ae1a0172.png?v=1771818647', '2026-11-23 11:41:16', '2026-11-23 12:36:16'),
(72, 10, 'Nivea', 2, 'Sun Protect and Moisture SPF 50', 'Produk sunscreen untuk membantu melindungi kulit dari paparan sinar matahari.', 'All Skin Types', 'Aqua, Alcohol Denat., Glycerin, Butyl Methoxydibenzoylmethane, C12-15 Alkyl Benzoate, Dibutyl Adipate, Isopropyl Palmitate, Bis-Ethylhexyloxyphenol Methoxyphenyl Triazine, Diethylamino Hydroxybenzoyl Hexyl Benzoate, Dipropylene Glycol Dibenzoate, Ethylhexyl Triazone, Cetyl Alcohol, Copernicia Cerifera Cera, Silica Dimethyl Silylate, Hectorite, Phenylbenzimidazole Sulfonic Acid, Sodium Ascorbyl Phosphate, Tocopheryl Acetate, Sodium Hyaluronate, Polyglyceryl-3 Methylglucose Distearate, Glyceryl Stearate Citrate, Glyceryl Stearate, C18-38 Alkyl Hydroxystearoyl Stearate, Xanthan Gum, Cetyl Palmitate, Trisodium Ethylenediamine Disuccinate, Sodium Hydroxide, Phenoxyethanol, Hydroxyacetophenone, Linalool, Benzyl Alcohol, Linalyl Acetate, Alpha-Isomethyl Ionone, Citronellol, Vanillin, Parfum', 'Membantu melindungi kulit dari paparan sinar UV.', 'Aplikasikan merata pada kulit sebelum beraktivitas di luar ruangan.', 'Gunakan ulang secara berkala sesuai petunjuk kemasan.', 45000.00, 20, 150, 'https://down-id.img.susercontent.com/file/sg-11134201-22100-kg9ey9kymviva1', '2026-11-23 11:41:16', '2026-11-23 12:36:16'),
(73, 10, 'Anessa', 43, 'Perfect UV Sunscreen Skincare Milk SPF 50+ PA++++', 'Sunscreen berbentuk milk untuk membantu melindungi kulit dari sinar UV.', 'All Skin Types', 'Dimethicone, Water (Aqua), Zinc Oxide, Alcohol, Diisopropyl Sebacate, Isododecane, Octocrylene, Ethylhexyl Salicylate, C12-15 Alkyl Benzoate, PEG/PPG-9/2 Dimethyl Ether, Zea Mays (Corn) Starch, Talc, Silica, Isopropyl Myristate, Diethylamino Hydroxybenzoyl Hexyl Benzoate, Titanium Dioxide, PEG-9 Polydimethylsiloxyethyl Dimethicone, Dextrin Palmitate, Glycerin, Homosalate, Bis-Ethylhexyloxyphenol Methoxyphenyl Triazine, Sodium Chloride, PEG/PPG-14/7 Dimethyl Ether, Trimethylsiloxysilicate, Dipotassium Glycyrrhizate, Camellia Sinensis Leaf Extract, Potentilla Erecta Root Extract, Citrus Unshiu Peel Extract, Sodium Acetylated Hyaluronate, Lauryl Betaine, Soluble Collagen, Ethylhexyl Triazone, Disteardimonium Hectorite, PEG-10 Dimethicone, Isostearic Acid, Triethoxycaprylylsilane, Polyglyceryl-6 Polyricinoleate, Aluminum Hydroxide, Stearic Acid, Trisodium EDTA, PEG-6, Tocopherol, BHT, Butylene Glycol, Bis-Butyldimethicone Polyglyceryl-3, Sodium Metabisulfite, Synthetic Fluorphlogopite, Phenoxyethanol, Sodium Benzoate, Fragrance', 'Membantu memberikan perlindungan terhadap sinar UVA dan UVB.', 'Kocok sesuai petunjuk kemasan dan aplikasikan merata.', 'Hindari kontak dengan mata.', 175000.00, 20, 150, 'https://asianbestsellers.com/cdn/shop/files/61FR4t1h_CL._AC_SL1080.png?v=1736341063', '2026-11-23 11:41:16', '2026-11-23 12:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--
CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('3W0oE8QcTX6iYK8TaUrZvvDn9YbD98xsmsIxHq6G', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36 Edg/158.0.0.0', 'eyJfdG9rZW4iOiJyNHdiYmJTakhtV1I2UW14MkRTSGtRaEQ4c04zNGVhc0lKaWVidnlkIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3RoaXJ0ZWVuLWFudGFjaWQtbWFua2luZC5uZ3Jvay1mcmVlLmRldiIsInJvdXRlIjoiaG9tZSJ9LCJfZmxhc2giOnsib2xkIjpbXSwibmV3IjpbXX19', 1795547329),
('hIVEwqX882aF521OrpqUBSbNZoHBBl7tf64VcpSm', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36 Edg/158.0.0.0', 'eyJfdG9rZW4iOiI5eGg2Zk9DZmVSOGxkOWxRRURXSDMwbWVOOHlHZUJRRjg1Q3pRRG5FIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cL3RoaXJ0ZWVuLWFudGFjaWQtbWFua2luZC5uZ3Jvay1mcmVlLmRldlwvbG9naW4iLCJyb3V0ZSI6ImxvZ2luIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=', 1795547331),
('pHBy3TBxIHgApQ3wCpPBT22egpfs8W4Gh946gLAy', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/158.0.0.0 Safari/537.36 Edg/158.0.0.0', 'eyJfdG9rZW4iOiJJaVBtUGtqdXdVdkhIZGt2RDZIVnNYU0tucWdtTHJ5eVNQODl1dXNyIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvdGhpcnRlZW4tYW50YWNpZC1tYW5raW5kLm5ncm9rLWZyZWUuZGV2XC9hZG1pblwvZGFzaGJvYXJkIiwicm91dGUiOiJhZG1pbi5kYXNoYm9hcmQifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjN9', 1795550557);

-- --------------------------------------------------------

--
-- Table structure for table `shipping_settings`
--
CREATE TABLE `shipping_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `store_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ZRadiance',
  `province_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rajaongkir_origin_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shipping_settings`
--

INSERT INTO `shipping_settings` (`id`, `store_name`, `province_name`, `province_code`, `city_name`, `city_code`, `district_name`, `district_code`, `rajaongkir_origin_id`, `address`, `postal_code`, `created_at`, `updated_at`) VALUES
(1, 'ZRadiance', 'DI YOGYAKARTA', NULL, 'YOGYAKARTA', NULL, 'GONDOKUSUMAN', NULL, '31413', 'Jl. Kartini Jl. Sagan Utara No.1A, Terban, Kec. Gondokusuman, Kota Yogyakarta, Daerah Istimewa Yogyakarta 55223', '55223', '2026-11-14 23:40:36', '2026-11-14 23:40:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `avatar`, `phone`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', '2026-09-07 03:47:57', '$2y$12$FxJkq8EhrXVe0kvzaDEYK.85HrFiWq3zg.N6X2qFJD6Dp26zzaDOq', 'user', NULL, NULL, 'hFRhZH0fzW', '2026-09-07 03:47:57', '2026-09-07 03:47:57'),
(2, 'zahra', 'zahra@gmail.com', NULL, '$2y$12$RxdWQG/wpEO7C4KK.gLEnOtoEWA79rJxJWX6i6E0pCVnUfodtrUxK', 'user', NULL, NULL, 'RrvEewRcelJNsSgROa7hKmV8WddkPB2xhAWHWmGqsNU3OtwjwTX6D0jl9Ghi', '2026-09-07 03:53:34', '2026-11-02 09:02:36'),
(3, 'Admin', 'admin@gmail.com', NULL, '$2y$12$IzX3sNMaoemgOWmkQ19k7OQw/mXRT1zKSKn9Qyhfwd4WcBuPSKfQO', 'admin', NULL, NULL, NULL, '2026-09-07 05:42:58', '2026-09-30 07:22:24');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_users_insert` AFTER INSERT ON `users` FOR EACH ROW INSERT INTO users_backup (
    user_id,
    name,
    email,
    role
)
VALUES (
    NEW.id,
    NEW.name,
    NEW.email,
    NEW.role
)
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users_backup`
--
CREATE TABLE `users_backup` (
  `backup_id` int NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `backup_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `addresses_user_id_is_default_index` (`user_id`,`is_default`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `brands_brand_name_unique` (`brand_name`),
  ADD UNIQUE KEY `brands_brand_slug_unique` (`brand_slug`);

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
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_items_user_id_product_id_unique` (`user_id`,`product_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  ADD KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `knowledge_articles_slug_unique` (`slug`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_read_at_index` (`notifiable_type`,`notifiable_id`,`read_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_reset_requests_user_id_foreign` (`user_id`),
  ADD KEY `password_reset_requests_handled_by_foreign` (`handled_by`);

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
  ADD KEY `products_product_cat_foreign` (`product_cat`),
  ADD KEY `products_brand_id_foreign` (`brand_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reviews_user_product_unique` (`user_id`,`product_id`),
  ADD KEY `reviews_product_id_rating_index` (`product_id`,`rating`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shipping_settings`
--
ALTER TABLE `shipping_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `users_backup`
--
ALTER TABLE `users_backup`
  ADD PRIMARY KEY (`backup_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `knowledge_articles`
--
ALTER TABLE `knowledge_articles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `shipping_settings`
--
ALTER TABLE `shipping_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users_backup`
--
ALTER TABLE `users_backup`
  MODIFY `backup_id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD CONSTRAINT `password_reset_requests_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `password_reset_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_product_cat_foreign` FOREIGN KEY (`product_cat`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
