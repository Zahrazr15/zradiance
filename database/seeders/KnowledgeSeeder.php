<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\KnowledgeArticle;
use App\Models\Product;
use Illuminate\Database\Seeder;

class KnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $this->faqs();
        $this->articles();

        // Hanya data yang tertulis jelas di deskripsi produk.
        Product::where('name', 'Hyalucera Moisturizer')
            ->whereNull('ingredients')
            ->update(['ingredients' => 'Hyaluronic acid, ceramide']);
    }

    private function faqs(): void
    {
        $faqs = [
            [
                'question' => 'Bagaimana cara memesan produk?',
                'answer'   => "1. Login atau daftar akun terlebih dahulu.\n2. Buka menu Kategori, pilih kategori, lalu klik \"Tambah ke Keranjang\" pada produk yang kamu mau.\n3. Buka menu Keranjang untuk mengatur jumlah produk.\n4. Lanjut ke checkout, isi data penerima, lalu buat pesanan.\n5. Pantau pesananmu di menu Pesanan.",
                'keywords' => 'memesan, pemesanan, cara pesan, cara order, cara beli, membeli, belanja',
            ],
            [
                'question' => 'Bagaimana cara checkout dan data apa yang harus diisi?',
                'answer'   => "Di halaman checkout: 1) pilih alamat pengiriman (atau tambahkan alamat lengkap di Profil), 2) klik hitung ongkir lalu pilih kurir dan layanan, 3) pilih pembayaran Online atau COD, 4) buat pesanan. Pastikan produk sudah dicentang di keranjang. Pesanan online dibayar lewat tombol Bayar di detail pesanan.",
                'keywords' => 'checkout, data penerima, alamat pengiriman, nomor telepon, isi alamat',
            ],
            [
                'question' => 'Bagaimana cara melihat status pesanan?',
                'answer'   => 'Buka menu Pesanan, lalu pilih pesanan untuk melihat detail dan statusnya. Status pesanan yang digunakan toko: menunggu (pending), diproses (processing), dikirim (shipped), selesai (completed), dan dibatalkan (cancelled).',
                'keywords' => 'status pesanan, lacak pesanan, melacak, cek pesanan, pesanan saya, riwayat pesanan, sudah dikirim',
            ],
            [
                'question' => 'Apakah pesanan bisa dibatalkan?',
                'answer'   => 'Bisa, selama status pesanan masih menunggu (pending) dan belum dibayar. Buka menu Pesanan, pilih pesanan, lalu klik Batalkan. Pesanan yang sudah dibayar tidak bisa dibatalkan langsung, hubungi admin untuk proses refund.',
                'keywords' => 'batal, dibatalkan, membatalkan, pembatalan, cancel',
            ],
            [
                'question' => 'Bagaimana cara mengubah isi keranjang?',
                'answer'   => 'Buka menu Keranjang. Di sana kamu bisa menambah atau mengurangi jumlah produk, dan menghapus produk dari keranjang. Jika jumlah dikurangi sampai habis, produk otomatis keluar dari keranjang.',
                'keywords' => 'ubah keranjang, hapus dari keranjang, kurangi jumlah, tambah jumlah, isi keranjang, mengubah keranjang',
            ],
            [
                'question' => 'Kenapa produk tidak bisa dipesan atau stoknya habis?',
                'answer'   => 'Jika stok produk 0, tombol tambah ke keranjang berubah menjadi "Stok Habis". Stok berkurang otomatis ketika pesanan berhasil dibuat. Jika stok tidak cukup saat checkout, pesanan tidak dapat diproses dan kamu perlu menyesuaikan jumlahnya.',
                'keywords' => 'stok habis, stok kosong, tidak bisa dipesan, stok tidak cukup, sold out',
            ],
            [
                'question' => 'Bagaimana cara mendaftar akun?',
                'answer'   => 'Klik menu Registrasi di halaman utama, isi formulir pendaftaran, lalu login dengan akun yang sudah dibuat.',
                'keywords' => 'daftar akun, mendaftar, registrasi, buat akun, membuat akun, register',
            ],
            [
                'question' => 'Bagaimana cara membayar pesanan online?',
                'answer'   => 'Setelah pesanan dibuat, buka menu Pesanan, pilih pesanan, lalu klik Bayar. Kamu bisa membayar dengan QRIS, transfer/virtual account, atau e-wallet. Status pembayaran biasanya berubah otomatis dalam beberapa menit setelah pembayaran berhasil.',
                'keywords' => 'cara bayar, membayar, pembayaran, bayar pesanan, qris, virtual account, e-wallet, gopay, transfer',
            ],
            [
                'question' => 'Kenapa ongkir tidak muncul atau lokasi tidak ditemukan?',
                'answer'   => "Ongkir dihitung otomatis dari alamat dan berat produk. Penyebab yang paling sering: provinsi, kota, kecamatan, kelurahan, atau kode pos di alamat belum tepat; alamat belum lengkap; atau layanan ongkir sedang membatasi permintaan (coba lagi beberapa menit). Periksa dan perbarui alamat di Profil > Alamat.",
                'keywords' => 'ongkir tidak muncul, ongkir gagal, lokasi tidak ditemukan, ongkos kirim error, kurir tidak muncul',
            ],
            [
                'question' => 'Pembayaran saya kedaluwarsa atau gagal, apa yang harus dilakukan?',
                'answer'   => 'Pesanan dengan pembayaran kedaluwarsa atau gagal tidak bisa dibayar ulang. Buka detail pesanan, klik Batalkan, lalu checkout ulang produknya. Kalau saldo kamu sudah terpotong, simpan bukti pembayaran dan hubungi admin.',
                'keywords' => 'pembayaran kedaluwarsa, pembayaran kadaluarsa, pembayaran gagal, expired, bayar ulang, saldo terpotong',
            ],
            [
                'question' => 'Bagaimana jika saya lupa password?',
                'answer'   => 'Klik "Lupa password" di halaman login, isi email dan password baru. Permintaanmu akan dicek dan disetujui admin dulu, setelah itu kamu bisa login dengan password baru. Kalau lama tidak diproses, hubungi admin.',
                'keywords' => 'lupa password, reset password, ganti password, tidak bisa login, lupa kata sandi',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(['question' => $faq['question']], $faq + ['is_active' => true]);
        }
    }

    private function articles(): void
    {
        $articles = [
            // ---------------- PANDUAN ----------------
            [
                'title'    => 'Perbedaan serum dan moisturizer',
                'slug'     => 'perbedaan-serum-dan-moisturizer',
                'category' => 'panduan',
                'keywords' => 'serum moisturizer, serum vs moisturizer, perbedaan serum, serum dan pelembap, fungsi serum dan moisturizer',
                'summary'  => 'Serum bertekstur ringan dengan bahan aktif terkonsentrasi untuk target tertentu (misalnya hidrasi atau mencerahkan). Moisturizer bertugas melembapkan dan mengunci kelembapan di permukaan kulit. Keduanya saling melengkapi, bukan saling menggantikan.',
                'content'  => "Serum\nBertekstur ringan dan biasanya mengandung bahan aktif dalam konsentrasi lebih tinggi untuk menargetkan masalah tertentu, misalnya hidrasi, kulit kusam, atau noda.\n\nMoisturizer\nBertugas melembapkan kulit dan membantu mengunci kelembapan agar tidak mudah menguap. Teksturnya umumnya lebih kaya dibanding serum.\n\nUrutan pakai\nSerum biasanya dipakai lebih dulu, lalu ditutup dengan moisturizer.\n\nInformasi ini bersifat umum. Selalu baca petunjuk pada kemasan produk.",
            ],
            [
                'title'    => 'Urutan penggunaan skincare',
                'slug'     => 'urutan-penggunaan-skincare',
                'category' => 'panduan',
                'keywords' => 'urutan skincare, urutan pemakaian skincare, urutan pakai skincare, skincare routine, rutinitas skincare, step skincare, mana dulu',
                'summary'  => 'Urutan umum: pembersih wajah, toner (opsional), serum, moisturizer, lalu sunscreen di pagi hari. Prinsipnya dari tekstur paling ringan ke paling kental.',
                'content'  => "Pagi\n1. Pembersih wajah\n2. Toner (opsional)\n3. Serum\n4. Moisturizer\n5. Sunscreen\n\nMalam\n1. Pembersih wajah (dan makeup remover jika memakai makeup)\n2. Toner (opsional)\n3. Serum\n4. Moisturizer\n\nPrinsip umum: pakai dari tekstur paling ringan ke paling kental. Kenalkan produk baru satu per satu agar mudah mengenali jika ada produk yang tidak cocok.",
            ],
            [
                'title'    => 'Memahami jenis kulit',
                'slug'     => 'memahami-jenis-kulit',
                'category' => 'panduan',
                'keywords' => 'jenis kulit, tipe kulit, kulit normal kering berminyak, kulit kombinasi, kulit sensitif, mengenali kulit, cek jenis kulit',
                'summary'  => 'Jenis kulit umum: normal, kering, berminyak, kombinasi (berminyak di area T, normal atau kering di pipi), dan sensitif (mudah merah atau perih). Jenis kulit dapat berubah mengikuti cuaca, usia, dan kebiasaan.',
                'content'  => "Normal: tidak terlalu berminyak maupun kering.\nKering: terasa kencang, kadang bersisik.\nBerminyak: tampak mengilap, terutama di dahi, hidung, dan dagu.\nKombinasi: berminyak di area T, normal atau kering di area pipi.\nSensitif: mudah kemerahan, perih, atau gatal saat memakai produk tertentu.\n\nJenis kulit bisa berubah. Jika punya masalah kulit yang menetap, konsultasikan ke dokter kulit.",
            ],
            [
                'title'    => 'Mengenal kategori produk Beauty Store',
                'slug'     => 'mengenal-kategori-produk',
                'category' => 'panduan',
                'keywords' => 'kategori produk, jenis kategori, apa saja kategori, kategori beauty store, ada kategori apa',
                'summary'  => 'Beauty Store memiliki empat kategori: Skincare (perawatan wajah), Makeup (riasan), Body Care (perawatan tubuh), dan Hair Care (perawatan rambut).',
                'content'  => "Skincare: produk perawatan wajah.\nMakeup: produk riasan seperti lip cream dan cushion.\nBody Care: perawatan tubuh.\nHair Care: perawatan rambut.\n\nBuka menu Kategori untuk melihat produk di tiap kategori.",
            ],
            [
                'title'    => 'Apa itu patch test?',
                'slug'     => 'apa-itu-patch-test',
                'category' => 'panduan',
                'keywords' => 'patch test, uji coba produk, tes alergi produk, coba produk baru, uji tempel',
                'summary'  => 'Patch test adalah mencoba sedikit produk di area kecil kulit (misalnya lengan bagian dalam atau belakang telinga) lalu mengamati reaksinya selama sekitar 24 jam sebelum dipakai ke seluruh wajah.',
                'content'  => "Cara umum\n1. Oleskan produk sedikit saja di area kecil, misalnya lengan bagian dalam atau belakang telinga.\n2. Tunggu dan amati sekitar 24 jam.\n3. Jika muncul kemerahan, gatal, atau perih, hentikan pemakaian.\n\nPatch test membantu mengurangi risiko, tetapi tidak menjamin produk 100% cocok. Jika reaksi berat, hubungi dokter.",
            ],

            // ---------------- BAHAN ----------------
            [
                'title'    => 'Niacinamide',
                'slug'     => 'niacinamide',
                'category' => 'bahan',
                'keywords' => 'niacinamide, vitamin b3',
                'summary'  => 'Niacinamide (vitamin B3) umumnya dipakai untuk membantu menjaga skin barrier, mengontrol tampilan minyak berlebih, dan meratakan tampilan warna kulit. Pada sebagian kulit sensitif, konsentrasi tinggi bisa menimbulkan kemerahan atau rasa perih.',
                'content'  => "Niacinamide adalah bentuk vitamin B3.\n\nFungsi umum: membantu menjaga skin barrier, membantu mengontrol tampilan minyak berlebih, dan meratakan tampilan warna kulit.\n\nPerhatian: sebagian orang dengan kulit sensitif bisa mengalami kemerahan atau perih, terutama pada konsentrasi tinggi. Lakukan patch test dan mulai bertahap.",
            ],
            [
                'title'    => 'Hyaluronic acid',
                'slug'     => 'hyaluronic-acid',
                'category' => 'bahan',
                'keywords' => 'hyaluronic acid, hyaluronic, asam hialuronat, ha',
                'summary'  => 'Hyaluronic acid adalah humektan yang menarik dan menahan air di kulit sehingga membantu hidrasi. Umumnya dapat dipakai berbagai jenis kulit, termasuk yang berminyak.',
                'content'  => "Hyaluronic acid adalah humektan: bahan yang menarik dan menahan air.\n\nFungsi umum: membantu melembapkan (hidrasi) dan membuat kulit terasa lebih kenyal.\n\nTips: pakai pada kulit yang masih sedikit lembap, lalu tutup dengan moisturizer agar kelembapan terkunci.",
            ],
            [
                'title'    => 'Ceramide',
                'slug'     => 'ceramide',
                'category' => 'bahan',
                'keywords' => 'ceramide, seramide',
                'summary'  => 'Ceramide adalah lipid yang secara alami ada di lapisan terluar kulit. Pada skincare, ceramide membantu menjaga skin barrier dan mengunci kelembapan.',
                'content'  => "Ceramide adalah lipid alami di lapisan terluar kulit.\n\nFungsi umum: membantu menjaga skin barrier dan mengunci kelembapan, sehingga cocok untuk kulit yang mudah kering atau barrier-nya terganggu.",
            ],
            [
                'title'    => 'Salicylic acid (BHA)',
                'slug'     => 'salicylic-acid',
                'category' => 'bahan',
                'keywords' => 'salicylic acid, salicylic, bha, asam salisilat',
                'summary'  => 'Salicylic acid (BHA) larut dalam minyak sehingga sering dipakai untuk membantu membersihkan pori pada kulit berminyak atau berjerawat. Bisa menyebabkan kering atau perih; mulai bertahap. Ibu hamil atau menyusui sebaiknya konsultasi ke dokter dulu.',
                'content'  => "Salicylic acid adalah BHA yang larut dalam minyak.\n\nFungsi umum: membantu membersihkan pori dan sering dipakai pada kulit berminyak atau berjerawat.\n\nPerhatian: dapat menyebabkan kering, perih, atau mengelupas. Mulai pemakaian bertahap. Ibu hamil atau menyusui sebaiknya berkonsultasi dengan dokter sebelum memakai.",
            ],
            [
                'title'    => 'Retinol',
                'slug'     => 'retinol',
                'category' => 'bahan',
                'keywords' => 'retinol, vitamin a, retinoid',
                'summary'  => 'Retinol adalah turunan vitamin A yang dipakai untuk membantu memperbaiki tekstur kulit dan tanda penuaan. Bisa menyebabkan kering dan kemerahan serta membuat kulit lebih sensitif terhadap matahari, jadi pakai bertahap di malam hari dan gunakan sunscreen. Ibu hamil atau menyusui perlu konsultasi dokter.',
                'content'  => "Retinol adalah turunan vitamin A.\n\nFungsi umum: membantu memperbaiki tekstur kulit dan tanda penuaan.\n\nPerhatian: dapat menyebabkan kering, kemerahan, dan kulit lebih sensitif terhadap matahari. Mulai bertahap, pakai malam hari, dan gunakan sunscreen pada pagi hari. Ibu hamil atau menyusui sebaiknya berkonsultasi dengan dokter.",
            ],
            [
                'title'    => 'Vitamin C',
                'slug'     => 'vitamin-c',
                'category' => 'bahan',
                'keywords' => 'vitamin c, ascorbic acid, asam askorbat',
                'summary'  => 'Vitamin C adalah antioksidan yang dipakai untuk membantu mencerahkan tampilan kulit dan melindungi dari radikal bebas. Beberapa bentuknya mudah rusak dan bisa terasa perih pada kulit sensitif; tetap gunakan sunscreen di pagi hari.',
                'content'  => "Vitamin C (misalnya ascorbic acid) adalah antioksidan.\n\nFungsi umum: membantu mencerahkan tampilan kulit dan melindungi kulit dari radikal bebas.\n\nPerhatian: beberapa bentuknya tidak stabil terhadap cahaya dan udara, dan bisa terasa perih pada kulit sensitif. Tetap gunakan sunscreen pada pagi hari.",
            ],
            [
                'title'    => 'SPF dan PA pada sunscreen',
                'slug'     => 'spf-dan-pa',
                'category' => 'bahan',
                'keywords' => 'spf, pa+, sunscreen, tabir surya, sunblock, uv',
                'summary'  => 'SPF menunjukkan tingkat perlindungan terhadap sinar UVB, sedangkan PA menunjukkan perlindungan terhadap UVA (semakin banyak tanda +, semakin kuat). Sunscreen perlu dipakai ulang secara berkala jika terpapar matahari langsung.',
                'content'  => "SPF (Sun Protection Factor): ukuran perlindungan terhadap sinar UVB.\nPA: label perlindungan terhadap sinar UVA; semakin banyak tanda +, semakin tinggi perlindungannya.\n\nSunscreen sebaiknya dipakai ulang secara berkala bila terpapar matahari langsung. Ikuti petunjuk pada kemasan.",
            ],
        ];

        foreach ($articles as $article) {
            KnowledgeArticle::updateOrCreate(['slug' => $article['slug']], $article + ['is_active' => true]);
        }
    }
}