# Laporan Pengujian ZRadiance

## Ringkasan

- **Tujuan:** memeriksa perilaku utama pengunjung, customer, administrator, validasi, otorisasi, error handling, dan integrasi pengiriman.
- **Alat:** PHPUnit melalui `php artisan test`, Laravel Feature Test, `Http::fake()`, serta mock service.
- **Isolasi data:** `phpunit.xml` mengatur `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:`. Tes memakai `RefreshDatabase` pada database SQLite in-memory; tidak ada migration, seeder, atau query tulis pada database development/production.
- **Integrasi eksternal:** RajaOngkir, Wilayah.id, dan Midtrans tidak dipanggil sungguhan. Respons RajaOngkir/Wilayah dimock; Midtrans dimock dan tidak dipanggil pada checkout COD.
- **Hasil suite penuh:** 40 PASS, 0 FAIL, 0 SKIPPED; 212 assertions.
- **Cakupan suite:** 27 Feature Test dan 13 Unit Test. Tes RajaOngkir yang sudah ada dipertahankan dan ditambah satu kasus pemisahan cache per offset.

## Hasil otomatis yang benar-benar dijalankan

| ID | Jenis Pengujian | Skenario | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| F01 | Feature | `ExampleTest`: halaman utama | HTTP 200 | Berhasil; HTTP 200 setelah tes memakai SQLite in-memory | PASS |
| F02 | Feature | Pengunjung membuka halaman utama, login, dan registrasi | Semua halaman publik dapat dibuka | Ketiga halaman merespons sukses | PASS |
| F03 | Feature | Pengunjung membuka katalog produk | Akses diarahkan ke login sesuai middleware aplikasi | Dialihkan ke login | PASS |
| F04 | Feature | Customer mencari, memfilter kategori, dan mengurutkan produk | Hasil sesuai kata kunci, kategori, dan harga | ID/jumlah produk yang dikembalikan sesuai data uji sintetis | PASS |
| F05 | Feature | Customer membuka detail produk | Detail produk benar dan belum memiliki hak review | Produk tepat ditampilkan; `canReview` bernilai false | PASS |
| F06 | Feature | Registrasi valid dan email duplikat | Customer tercatat; email duplikat ditolak | Customer tersimpan dengan role `user`; duplikat menghasilkan error validasi | PASS |
| F07 | Feature | Registrasi dengan field wajib/email/password confirmation tidak valid | Validasi menolak data | Error untuk `name`, `email`, dan `password`; user tidak dibuat | PASS |
| F08 | Feature | Login dengan password salah dan benar | Password salah ditolak; kredensial valid masuk | Kegagalan menghasilkan error; login valid mengautentikasi customer | PASS |
| F09 | Feature | Logout customer | Session berakhir dan diarahkan ke beranda | Customer menjadi guest; redirect ke beranda | PASS |
| F10 | Feature | Melihat dan memperbarui profil | Profil tersimpan sesuai input valid | Nama, email, dan telepon berubah sesuai data uji | PASS |
| F11 | Feature | Membuat/memperbarui alamat dan akses alamat pengguna lain | Alamat tersimpan; akses lintas pengguna ditolak | Alamat default terbentuk dan dapat diperbarui; pengguna lain menerima 403 | PASS |
| F12 | Feature | Riwayat dan detail pesanan customer | Hanya pemilik dapat melihat pesanan | Daftar berisi pesanan sendiri; akses pesanan pengguna lain menerima 403 | PASS |
| F13 | Feature | Tambah/naikkan/hapus produk keranjang | Kuantitas dibatasi stok dan penghapusan berhasil | Kuantitas tidak melewati stok; item dapat dihapus | PASS |
| F14 | Feature | Checkout tanpa login dan alamat yang bukan milik customer | Login diwajibkan; alamat tidak sah divalidasi tanpa API | Guest diarahkan ke login; alamat tidak sah menghasilkan error dan layanan ongkir tidak dipanggil | PASS |
| F15 | Feature | Dashboard admin dan proteksi role | Admin diizinkan, customer ditolak | Admin mendapat HTTP 200; customer mendapat HTTP 403 | PASS |
| F16 | Feature | Daftar, tambah, ubah, dan hapus produk admin | CRUD berjalan untuk data valid | Daftar tampil dan perubahan database uji sesuai | PASS |
| F17 | Feature | Validasi harga, stok, dan berat produk | Harga nonnumerik, stok negatif, dan berat nol ditolak | Error validasi pada ketiga field; produk tidak dibuat | PASS |
| F18 | Feature | CRUD kategori dan pencegahan cascade penghapusan produk | Kategori berproduk tidak menghapus produk secara cascade | Penghapusan kategori ditolak saat masih memiliki produk; CRUD kategori berhasil | PASS |
| F19 | Feature | Daftar/detail pesanan admin, proses COD, dan transisi status | Status mengikuti aturan; stok hanya dipotong sekali | COD masuk `processing`; stok berkurang sekali; transisi terlarang ditolak | PASS |
| F20 | Feature | Pesanan online belum dibayar masuk `processing` | Transisi ditolak | Error validasi; status tetap `pending` | PASS |
| F21 | Feature | Pengaturan pengiriman admin | Data asal pengiriman dapat disimpan dan dibaca | Pengaturan tersimpan dan halaman edit berhasil dibuka | PASS |
| F22 | Feature | Rute tidak dikenal | Menghasilkan 404 | HTTP 404 | PASS |
| F23 | Feature | Error server tanpa detail sensitif | Respons tidak menampilkan exception detail | HTTP 500 tanpa teks sintetis rahasia | PASS |
| F24 | Feature | Kegagalan API Wilayah.id | Error upstream disajikan secara generik | HTTP 502; body upstream sintetis tidak terekspos | PASS |
| F25 | Feature | Perhitungan ongkir memakai response tiruan | Format request domestic-cost dan hasil dipetakan benar | Form request berisi origin, destination, berat, courier, dan `price=lowest`; hasil tiruan diterima | PASS |
| F26 | Feature | HTTP 429 pada hitung ongkir checkout | Rate limit dibedakan dari lokasi tidak ditemukan; cooldown mencegah request ulang | Respons 429 memuat retry-after; dua pemanggilan hanya membuat satu request HTTP | PASS |
| F27 | Feature | Checkout COD dengan layanan ongkir tiruan | Order terbentuk tanpa pembayaran nyata; Midtrans tidak dipanggil | Order dan item tersimpan di SQLite in-memory, keranjang dibersihkan, stok belum dipotong | PASS |
| U01 | Unit | Pencarian destination mengambil halaman offset berikutnya | Hasil halaman berikutnya ditemukan tanpa mengulang offset | Dua halaman diminta dengan limit 10 dan offset berikutnya yang benar | PASS |
| U02 | Unit | Cache destination memakai kata kunci terpisah | Pencarian yang sama memakai cache; kata kunci lain tidak tertukar | Request kedua untuk kata kunci pertama memakai cache; kata kunci berbeda mendapat hasilnya sendiri | PASS |
| U03 | Unit | Cache satu kata kunci memisahkan offset halaman | Tiap offset dicache sendiri dan dapat dipakai ulang | Dua halaman diambil sekali; pencarian kedua memakai kedua halaman dari cache | PASS |
| U04 | Unit | Pagination berhenti setelah halaman yang berisi kandidat cocok | Halaman sesudah kecocokan tidak diminta | Pencarian berhenti setelah satu request | PASS |
| U05 | Unit | HTTP 429 membaca header dan menerapkan cooldown | Status/header rate limit terklasifikasi dan request ulang diblokir | Header terdeteksi; body disaring; request berikutnya tidak mengirim HTTP | PASS |
| U06 | Unit | Kuota API habis | Dibedakan dari rate limit biasa | Exception diklasifikasikan sebagai kuota habis | PASS |
| U07 | Unit | Error API tidak membocorkan body/key/query | Log dan exception tersaring | Data sintetis rahasia tidak muncul pada log/exception yang diperiksa | PASS |
| U08 | Unit | Kegagalan koneksi RajaOngkir | Exception koneksi aman tanpa detail request | Kategori koneksi gagal dan konteks log minimal terverifikasi | PASS |
| U09 | Unit | API mengulang halaman yang sama | Pagination berhenti dengan error API yang aman | Halaman berulang terdeteksi setelah dua request | PASS |
| U10 | Unit | Kandidat cocok berada di halaman berikutnya dengan variasi awalan wilayah | Kandidat dengan hierarki tepat tetap ditemukan | Kandidat sintetis halaman kedua ditemukan | PASS |
| U11 | Unit | Nama kelurahan/kecamatan mirip tetapi hierarki berbeda | Kandidat yang salah ditolak | Kandidat lintas provinsi/kota ditolak; log tidak berisi lokasi | PASS |
| U12 | Unit | Pesan checkout untuk rate limit, kuota, koneksi, dan API error | Pesan/status dibedakan tanpa membocorkan detail | Keempat kategori dan header retry terverifikasi | PASS |
| U13 | Unit | Contoh assertion PHPUnit bawaan | Assertion dasar tetap berjalan | Assertion bernilai benar | PASS |

## Eksekusi dan validasi

| Perintah | Hasil aktual | Status |
|---|---|---|
| `php artisan test` | 40 tests, 40 passed, 212 assertions | PASS |
| `php artisan test tests\Feature` | 27 tests, 27 passed, 159 assertions | PASS |
| `php artisan test tests\Unit` | 13 tests, 13 passed, 53 assertions | PASS |
| `php -l` pada seluruh file tes yang dibuat/diubah | Semua file melaporkan “No syntax errors detected” | PASS |
| `git diff --check` | Exit code 0; tidak ada whitespace error. Git menampilkan peringatan normalisasi CRLF pada beberapa file lain yang sudah berubah di worktree | PASS |
| Pemeriksaan `git status --short` | Perubahan UI, data, aset, dan integrasi yang sudah ada tetap terlihat; tidak diubah oleh tugas ini | PASS |

## Skenario belum tersedia / belum dijalankan

Berikut bukan hasil PHPUnit; ditandai `SKIPPED` karena memerlukan layanan nyata, antarmuka manual, atau cakupan tes tambahan. Baris ini tidak termasuk hitungan 40 tes di atas.

| ID | Jenis Pengujian | Skenario | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| S01 | Integrasi eksternal | Menghitung ongkir melalui API RajaOngkir live | Respons ongkir valid dari provider | Sengaja tidak dipanggil; pengujian hanya memakai mock dan tidak mengklaim ongkir live berhasil | SKIPPED |
| S02 | Integrasi pembayaran | Membayar transaksi Midtrans sungguhan | Provider menerima transaksi uji | Sengaja tidak dilakukan; tidak ada transaksi pembayaran nyata | SKIPPED |
| S03 | Manual | Memeriksa tampilan responsif dan interaksi visual di browser | Alur UI bekerja pada ukuran layar target | Belum dijalankan dalam PHPUnit/browser | SKIPPED |
| S04 | Feature tambahan | Upload/hapus foto profil dan validasi format/ukuran file | File dan profil tersimpan/terhapus dengan aman | Belum ada tes otomatis untuk alur ini | SKIPPED |
| S05 | Feature tambahan | Permintaan/reset password | Token dan alur reset sesuai kebijakan aplikasi | Belum ada tes otomatis | SKIPPED |
| S06 | Feature tambahan | Ulasan produk dan pembatasan hanya untuk pembeli berhak | Ulasan mengikuti syarat pesanan | Belum ada tes otomatis | SKIPPED |
| S07 | Feature tambahan | Pembatalan pesanan oleh customer dan seluruh cabang refund/stok | Kepemilikan, status, dan stok terjaga | Belum ada tes otomatis pada rangkaian tersebut | SKIPPED |
| S08 | Feature tambahan | Pencarian produk dengan filter rentang harga dan validasi parameter katalog tidak sah | Hasil dan pesan validasi sesuai filter | Tes saat ini mencakup pencarian, kategori, dan urutan harga; rentang harga belum diuji khusus | SKIPPED |

## Masalah dan batasan

- Tes beranda bawaan sebelumnya belum memigrasikan skema tes sehingga bergantung pada keberadaan tabel `categories`. `RefreshDatabase` ditambahkan pada tes tersebut; hasilnya kini konsisten memakai SQLite in-memory.
- Katalog produk pada implementasi saat ini berada di grup autentikasi/role customer, bukan halaman katalog pengunjung. Tes memverifikasi akses aktual tersebut dan tidak mengubah routing.
- Tes integrasi menggunakan data sintetis serta mock; ini tidak membuktikan ketersediaan layanan provider, kuota akun, keberhasilan pengiriman, atau transaksi pembayaran live.
- Tidak ada perubahan pada controller/model/service aplikasi, route, cart, checkout, migration, dependency, maupun database development/production dalam pekerjaan pengujian ini.

## Kesimpulan

Semua 40 tes otomatis yang dijalankan lulus (27 feature dan 13 unit). Cakupan menguji fitur publik/customer/admin yang dipilih, validasi, proteksi akses dan kepemilikan pesanan, error handling, cache/pagination/cooldown RajaOngkir, perhitungan ongkir dengan mock, serta checkout COD tanpa Midtrans. Skenario yang membutuhkan layanan live dan beberapa fitur di luar cakupan yang diuji tetap dicatat sebagai belum dijalankan.
