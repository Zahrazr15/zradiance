# ZRadiance | Beauty E-Commerce

ZRadiance merupakan aplikasi e-commerce berbasis web yang berfokus pada penjualan produk kecantikan (*beauty products*). Aplikasi menyediakan pengalaman belanja bagi pelanggan serta panel administrator untuk mengelola produk, kategori, brand, pesanan, pelanggan, dan aktivitas toko.

Project ini dikembangkan sebagai proyek UAS Praktik Manajemen Sistem Informasi.

## Fitur Aplikasi

### Customer
- Registrasi dan login pengguna.
- Melihat katalog dan detail produk.
- Pencarian, filter kategori, dan pengurutan produk.
- Mengelola keranjang belanja.
- Mengelola profil dan alamat pengiriman.
- Checkout dan perhitungan ongkos kirim.
- Melihat riwayat dan detail pesanan.
- Memberikan ulasan produk.
- Chatbot bantuan pelanggan.

### Administrator
- Dashboard admin.
- Manajemen produk (CRUD).
- Manajemen kategori.
- Manajemen brand.
- Manajemen pesanan.
- Manajemen pelanggan.
- Pengelolaan informasi toko dan pengiriman.

### Integrasi
- Midtrans untuk pembayaran online.
- RajaOngkir untuk pencarian tujuan dan perhitungan ongkos kirim.
- Gemini API untuk chatbot bantuan pelanggan.
- Validasi data dan penanganan error API.
- Cache dan pembatasan permintaan API.

## Teknologi

| Teknologi | Kegunaan |
|---|---|
| PHP 8.3 | Backend |
| Laravel 13 | Framework |
| MySQL | Database |
| Blade | Template |
| HTML, CSS, JavaScript | Frontend |
| Vite | Asset bundling |
| Node.js & NPM | Frontend dependencies |
| PHPUnit | Testing |
| Midtrans | Payment gateway |
| RajaOngkir | Shipping integration |
| Gemini API | Chatbot |
| Laragon | Local development |

## Persyaratan

- PHP 8.3
- Composer
- MySQL
- Node.js dan NPM
- Git
- Laragon (direkomendasikan untuk Windows)

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/Zahrazr15/zradiance.git
cd ecommerce
```

Jika ingin menggunakan branch pengembangan:

```bash
git switch redesign/full-ui
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Konfigurasi environment

Salin file `.env.example` menjadi `.env`.

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

### 4. Konfigurasi database

Buat database MySQL bernama:

```text
ecommerce
```

Atur koneksi database di file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan username dan password dengan konfigurasi MySQL masing-masing.

### 5. Import database

Jika file backup `database/zradiance.sql` tersedia:

1. Buka phpMyAdmin.
2. Pilih database `ecommerce`.
3. Klik menu **Import**.
4. Pilih file `database/zradiance.sql`.
5. Jalankan proses import.

Alternatif menggunakan terminal:

```bash
mysql -u root ecommerce < database/zradiance.sql
```

Jika database belum memiliki data dan tidak menggunakan backup, jalankan:

```bash
php artisan migrate
php artisan db:seed
```

### 6. Konfigurasi API

Tambahkan API key yang dimiliki pada file `.env`:

```env
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=

RAJAONGKIR_API_KEY=

GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.5-flash
```

Jangan mengunggah API key, password, atau token pribadi ke repository.

### 7. Build frontend

```bash
npm run build
```

### 8. Jalankan aplikasi

```bash
php artisan serve
```

Buka browser:

```text
http://127.0.0.1:8000
```

## Pengujian

Jalankan pengujian menggunakan:

```bash
php artisan test
```

Pengujian mencakup feature test dan unit test untuk berbagai fungsi aplikasi, termasuk autentikasi, pengelolaan data, validasi, dan proses bisnis.

Pengujian integrasi layanan eksternal dapat menggunakan simulasi (*mock testing*), sehingga tidak seluruh proses berarti telah diuji menggunakan transaksi live.

## Struktur Project

```text
app/
├── Http/
├── Models/
└── Services/

database/
├── migrations/
├── seeders/
└── backup/

public/
resources/
├── css/
├── js/
└── views/

routes/
tests/
├── Feature/
└── Unit/

docs/
```

## Keamanan

- File `.env` tidak boleh diunggah ke repository.
- API key dan secret harus disimpan secara lokal.
- Database backup yang dibagikan harus menggunakan data demo atau data yang telah disanitasi.
- Jangan menyertakan password, data pribadi pelanggan, atau informasi transaksi sensitif.

## Informasi Project

| Keterangan | Informasi |
|---|---|
| Nama aplikasi | ZRadiance |
| Jenis | Beauty E-Commerce |
| Framework | Laravel 13 |
| Database | MySQL |
| Mata kuliah | Praktik Manajemen Sistem Informasi |
| Tujuan | Proyek UAS |

---

**ZRadiance — Beauty Store E-Commerce**