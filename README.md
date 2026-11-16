# ZRadiance - Beauty E-Commerce

**ZRadiance** merupakan aplikasi e-commerce berbasis web yang berfokus pada penjualan produk kecantikan (*beauty products*). Aplikasi ini dikembangkan sebagai bagian dari pemenuhan tugas Ujian Akhir Semester (UAS) untuk mata kuliah Praktik Manajemen Sistem Informasi.

ZRadiance dirancang untuk memudahkan pelanggan dalam mencari produk kecantikan, mengelola keranjang belanja, melakukan pemesanan, serta memantau informasi pesanan. Aplikasi juga menyediakan halaman administrator untuk mengelola data dan aktivitas toko.

---

## Fitur Aplikasi

### 1. Customer
- Registrasi dan login pengguna.
- Melihat katalog dan detail produk.
- Pencarian, filter kategori, dan pengurutan produk.
- Menambahkan dan mengelola produk dalam keranjang.
- Mengelola profil dan alamat pengiriman.
- Melakukan checkout dan melihat ongkos kirim.
- Melihat riwayat serta detail pesanan.
- Melihat ulasan produk.

### 2. Administrator
- Dashboard administrator.
- Mengelola data produk (tambah, lihat, ubah, dan hapus).
- Mengelola kategori produk.
- Melihat dan mengelola pesanan.
- Mengelola informasi pelanggan.
- Mengatur konfigurasi pengiriman.

### 3. Integrasi dan Fitur Pendukung
- Integrasi RajaOngkir untuk pencarian tujuan dan perhitungan ongkos kirim.
- Integrasi Midtrans untuk mendukung proses pembayaran online.
- Pengelolaan status pesanan.
- Penanganan kegagalan API dan pembatasan permintaan (*rate limiting*).
- Cache pencarian destinasi pengiriman.

---

## Teknologi yang Digunakan

| Teknologi | Kegunaan |
|---|---|
| PHP 8.3 | Bahasa pemrograman backend |
| Laravel 13 | Framework aplikasi web |
| MySQL | Sistem manajemen basis data |
| HTML, CSS, JavaScript | Pengembangan antarmuka |
| Blade Template | Template engine Laravel |
| PHPUnit | Pengujian otomatis aplikasi |
| Laravel Feature Test | Pengujian fungsional aplikasi |
| RajaOngkir | Integrasi layanan pengiriman |
| Midtrans | Integrasi pembayaran online |
| Laragon | Lingkungan pengembangan lokal |

---

## Persyaratan Sistem

Sebelum menjalankan aplikasi, pastikan perangkat telah memiliki:

- PHP 8.3 atau versi yang kompatibel.
- Composer.
- MySQL.
- Node.js dan NPM.
- Laragon atau lingkungan server lokal sejenis.

---

## Instalasi dan Konfigurasi

### 1. Clone Repository

```bash
git clone <URL_REPOSITORY_GITHUB>
```

Masuk ke direktori project:

```bash
cd ecommerce
```

### 2. Install Dependency

```bash
composer install
```

Install dependency frontend:

```bash
npm install
```

### 3. Konfigurasi Environment

Salin file konfigurasi:

```bash
cp .env.example .env
```

Pada Windows PowerShell, dapat menggunakan:

```powershell
Copy-Item .env.example .env
```

Buat application key:

```bash
php artisan key:generate
```

### 4. Konfigurasi Database

Buat database MySQL dengan nama:

```text
ecommerce
```

Sesuaikan konfigurasi database pada file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan username dan password berdasarkan konfigurasi MySQL lokal.

### 5. Migrasi dan Seeder

Jalankan migrasi database:

```bash
php artisan migrate
```

Untuk mengisi data awal demonstrasi, jalankan seeder yang telah disediakan:

```bash
php artisan db:seed
```

Pastikan `DatabaseSeeder` sudah memanggil seeder yang diperlukan sebelum menjalankan perintah tersebut.

### 6. Menjalankan Aplikasi

Jalankan proses build frontend:

```bash
npm run build
```

Jalankan server Laravel:

```bash
php artisan serve
```

Buka aplikasi melalui browser:

```text
http://127.0.0.1:8000
```

---

## Konfigurasi Layanan Eksternal

Aplikasi menggunakan layanan eksternal untuk mendukung beberapa proses operasional.

- **RajaOngkir:** digunakan untuk pencarian destinasi dan perhitungan ongkos kirim.
- **Midtrans:** digunakan untuk mendukung proses pembayaran online.

Konfigurasi API harus disesuaikan dengan dokumentasi resmi masing-masing penyedia layanan.

Gunakan API Key milik sendiri dan simpan seluruh kredensial pada file `.env`. Jangan mengunggah API Key atau secret ke repository publik.

---

## Pengujian Sistem

Pengujian otomatis dilakukan menggunakan PHPUnit dan Laravel Testing.

Perintah menjalankan seluruh pengujian:

```bash
php artisan test
```

### Hasil Pengujian Otomatis

| Jenis Pengujian | Jumlah |
|---|---:|
| Feature Test | 27 |
| Unit Test | 13 |
| Total Test | **40** |
| Assertions | **212** |
| PASS | **40** |
| FAIL | **0** |
| SKIPPED dalam suite otomatis | **0** |

Pengujian mencakup autentikasi, hak akses pengguna, pengelolaan produk dan kategori, profil, alamat, keranjang, checkout, validasi data, penanganan error, serta integrasi pengiriman menggunakan simulasi API (*mock testing*).

**Catatan:** Pengujian otomatis menggunakan database SQLite in-memory dan simulasi layanan eksternal. Pengujian transaksi Midtrans sungguhan dan integrasi langsung RajaOngkir belum dilakukan.

Dokumentasi pengujian lebih lanjut dapat dilihat pada:

`docs/testing/LAPORAN-PENGUJIAN-ZRADIANCE.md`

---

## Struktur Direktori

```text
ecommerce/
├── app/
│   ├── Http/
│   ├── Models/
│   └── Services/
├── config/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── seed-data/
├── docs/
│   └── testing/
├── public/
├── resources/
│   └── views/
├── routes/
├── tests/
│   ├── Feature/
│   └── Unit/
├── .env.example
├── artisan
├── composer.json
└── README.md
```

---

## Tujuan Pengembangan

Pengembangan ZRadiance bertujuan untuk menerapkan konsep sistem informasi berbasis web dalam kegiatan perdagangan elektronik, khususnya pada bidang produk kecantikan.

Melalui aplikasi ini, diharapkan proses pengelolaan produk, pelanggan, pesanan, dan informasi pengiriman dapat dilakukan secara lebih terstruktur dan terintegrasi.

---

## Informasi Proyek

| Keterangan | Informasi |
|---|---|
| Nama Aplikasi | ZRadiance |
| Jenis Aplikasi | E-Commerce |
| Bidang | Beauty Products |
| Mata Kuliah | Praktik Manajemen Sistem Informasi |
| Keperluan | Tugas Ujian Akhir Semester (UAS) |
| Framework | Laravel 13 |
| Database | MySQL |

---

**ZRadiance - Beauty Store E-Commerce**

Dikembangkan untuk memenuhi kebutuhan pembelajaran dan penerapan sistem informasi berbasis web.