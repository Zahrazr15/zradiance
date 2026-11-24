<?php

namespace App\Services;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\ShippingSetting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Mendiagnosis kendala sistem milik pelanggan yang sedang login
 * (tidak bisa checkout, ongkir tidak muncul, pembayaran gagal, dst).
 *
 * Semua pemeriksaan bersifat READ-ONLY: tidak memanggil API pihak ketiga
 * dan tidak mengubah data. Hasilnya dipakai dua kali:
 *  1. sebagai jawaban cadangan yang pasti jalan walau Gemini gagal, dan
 *  2. sebagai konteks faktual untuk Gemini agar jawabannya spesifik.
 */
class SupportDiagnosisService
{
    public const TOPIC_LABELS = [
        'checkout' => 'checkout',
        'shipping' => 'ongkos kirim',
        'payment'  => 'pembayaran',
        'order'    => 'pesanan',
        'cart'     => 'keranjang',
        'address'  => 'alamat pengiriman',
        'account'  => 'akun / login',
        'system'   => 'halaman atau sistem',
        'general'  => 'bantuan admin',
    ];

    /** Urutan = prioritas. */
    private const TOPIC_WORDS = [
        'checkout' => [
            'checkout', 'check out', 'chekout', 'chek out', 'proses pesanan',
            'buat pesanan', 'membuat pesanan', 'pesan sekarang',
        ],
        'shipping' => [
            'ongkir', 'ongkos kirim', 'biaya kirim', 'kurir', 'ekspedisi',
            'pengiriman', 'jne', 'jnt', 'sicepat', 'anteraja', 'rajaongkir',
        ],
        'payment' => [
            'bayar', 'pembayaran', 'membayar', 'midtrans', 'snap', 'qris',
            'virtual account', 'va', 'transfer', 'gopay', 'shopeepay', 'ovo',
            'e wallet', 'ewallet', 'cod',
        ],
        'order' => [
            'pesanan', 'order', 'dibatalkan', 'batalkan', 'pembatalan',
            'refund', 'pengembalian dana', 'lacak', 'tracking', 'resi',
            'belum sampai', 'belum datang',
        ],
        'cart' => ['keranjang', 'cart'],
        'address' => [
            'alamat', 'kecamatan', 'kelurahan', 'kode pos', 'provinsi',
            'kabupaten',
        ],
        'account' => [
            'login', 'log in', 'password', 'kata sandi', 'registrasi',
            'register', 'mendaftar', 'daftar akun', 'akun',
        ],
        'system' => [
            'website', 'situs', 'halaman', 'server', 'page expired',
            'sistem', 'loading', 'aplikasi',
        ],
    ];

    private const PROBLEM_SIGNALS = [
        'gagal', 'error', 'eror', 'tidak bisa', 'gak bisa', 'ga bisa',
        'nggak bisa', 'ngga bisa', 'enggak bisa', 'tdk bisa', 'g bisa',
        'tidak berhasil', 'gak berhasil', 'ga berhasil', 'masalah', 'kendala',
        'kenapa', 'mengapa', 'kok', 'macet', 'stuck', 'nyangkut', 'muter',
        'tidak muncul', 'gak muncul', 'ga muncul', 'tidak ada pilihan',
        'tidak ditemukan', 'tidak keluar', 'gak keluar', 'hilang',
        'bermasalah', 'rusak', 'tidak masuk', 'belum masuk', 'kedaluwarsa',
        'kadaluarsa', 'expired', 'expire', 'ditolak', 'blank', 'lemot',
        'tidak respon', 'tidak merespons', 'page expired', 'belum sampai',
        'belum datang', 'kepotong', 'terpotong', 'double', 'dua kali',
        'dibatalkan', 'padahal', 'sudah bayar', 'udah bayar', 'sudah dibayar',
        'belum berubah', 'tidak berubah', 'gak berubah', 'status belum',
    ];

    private const FOLLOW_UP = [
        'masih', 'tetap', 'tetep', 'belum bisa', 'belum berhasil',
        'sudah dicoba', 'udah dicoba', 'sudah coba', 'udah coba',
        'sama aja', 'sama saja', 'bisa juga',
    ];

    private const HUMAN_REQUEST = [
        'admin', 'cs', 'customer service', 'hubungi', 'whatsapp', 'wa',
        'telepon', 'komplain', 'complain', 'pengaduan', 'refund',
        'kembalikan dana', 'uang kembali', 'bicara dengan manusia',
        'orang asli',
    ];

    private const TIPS = [
        'checkout' => [
            'Pastikan produk sudah dicentang di keranjang, lalu klik Checkout.',
            'Di halaman checkout: pilih alamat, hitung ongkir, pilih kurir dan layanan, baru buat pesanan.',
            'Muat ulang halaman (Ctrl+F5), lalu ulangi dari keranjang.',
            'Kalau muncul "Page Expired", buka ulang halaman keranjang dan ulangi checkout.',
        ],
        'shipping' => [
            'Pastikan provinsi, kota/kabupaten, kecamatan, kelurahan, dan kode pos di alamat sudah benar dan ejaannya sesuai.',
            'Coba hitung ulang ongkir beberapa menit lagi, layanan ongkir kadang membatasi jumlah permintaan.',
            'Coba pilih alamat lain atau perbarui alamat utama.',
        ],
        'payment' => [
            'Pesanan online yang belum dibayar bisa dibayar lewat tombol Bayar di detail pesanan.',
            'Pembayaran QRIS, virtual account, atau e-wallet biasanya terkonfirmasi otomatis dalam beberapa menit. Muat ulang halaman detail pesanan.',
            'Kalau saldo sudah terpotong tapi status masih belum dibayar, simpan bukti bayarnya dan hubungi admin.',
        ],
        'order' => [
            'Buka menu Pesanan untuk melihat status terbaru.',
            'Pesanan bisa dibatalkan sendiri hanya saat statusnya masih menunggu dan belum dibayar.',
            'Pesanan yang sudah dibayar dibatalkan atau di-refund lewat admin.',
        ],
        'cart' => [
            'Jumlah di keranjang otomatis menyesuaikan stok terbaru.',
            'Produk yang stoknya habis otomatis keluar dari keranjang.',
            'Muat ulang halaman kalau tombol tidak merespons.',
        ],
        'address' => [
            'Isi alamat lengkap (provinsi, kota/kabupaten, kecamatan, kelurahan/desa, kode pos) lewat menu Profil > Alamat.',
            'Daftar wilayah diambil dari layanan data wilayah. Kalau pilihan tidak muncul, tunggu sebentar lalu muat ulang halaman.',
        ],
        'account' => [
            'Cek ejaan email dan password (huruf besar/kecil berpengaruh).',
            'Lupa password: pakai "Lupa password" di halaman login. Permintaan reset perlu disetujui admin dulu.',
            'Password minimal 6 karakter.',
        ],
        'system' => [
            'Muat ulang halaman (Ctrl+F5).',
            'Kalau muncul "Page Expired", login ulang atau buka ulang halamannya.',
            'Coba browser lain atau mode incognito.',
        ],
        'general' => [],
    ];

    public function __construct(
        private CartService $cart,
        private ShippingService $shipping
    ) {}

    /* ---------------------------------------------------------------
     | Deteksi topik
     |---------------------------------------------------------------*/

    /**
     * @param  string  $text  Teks yang sudah dinormalisasi (huruf kecil, tanpa tanda baca).
     * @return array{topic:string,escalate:bool}|null
     */
    public function detectTopic(string $text, ?string $previousTopic = null): ?array
    {
        $human = $this->hasAny($text, self::HUMAN_REQUEST);
        $problem = $this->hasAny($text, self::PROBLEM_SIGNALS);
        $followUp = $this->hasAny($text, self::FOLLOW_UP);

        $topic = null;

        foreach (self::TOPIC_WORDS as $candidate => $words) {
            if ($this->hasAny($text, $words)) {
                $topic = $candidate;
                break;
            }
        }

        // "masih gagal", "tetap tidak bisa" tanpa menyebut topik -> lanjutan topik sebelumnya.
        if ($topic === null && $previousTopic !== null && ($followUp || $human)) {
            return ['topic' => $previousTopic, 'escalate' => true];
        }

        if ($topic === null) {
            return $human
                ? ['topic' => 'general', 'escalate' => true]
                : null;
        }

        if (!$problem && !$human) {
            return null; // pertanyaan informatif biasa -> biarkan jalur FAQ.
        }

        return [
            'topic' => $topic,
            'escalate' => $human || ($previousTopic === $topic && $followUp),
        ];
    }

    /* ---------------------------------------------------------------
     | Diagnosis
     |---------------------------------------------------------------*/

    public function diagnose(string $topic, ?User $user = null): array
    {
        $checks = [];

        if ($user) {
            try {
                if (in_array($topic, ['checkout', 'shipping', 'cart', 'address'], true)) {
                    $checks = array_merge($checks, $this->cartChecks($user));
                }

                if (in_array($topic, ['checkout', 'shipping', 'address'], true)) {
                    $checks = array_merge($checks, $this->addressChecks($user));
                }

                if (in_array($topic, ['checkout', 'shipping'], true)) {
                    $checks = array_merge($checks, $this->shippingSystemChecks());
                }

                if (in_array($topic, ['checkout', 'payment'], true)) {
                    $checks = array_merge($checks, $this->paymentSystemChecks());
                }

                if (in_array($topic, ['checkout', 'payment', 'order'], true)) {
                    $checks = array_merge($checks, $this->orderChecks($user));
                }
            } catch (Throwable $e) {
                // Diagnosis tidak boleh membuat chatbot error.
                Log::warning('Diagnosis chatbot gagal sebagian.', [
                    'topic' => $topic,
                    'exception' => $e::class,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        $problems = array_values(array_filter(
            $checks,
            fn (array $check) => $check['status'] === 'problem'
        ));

        $adminProblems = array_filter(
            $problems,
            fn (array $check) => $check['owner'] === 'admin'
        );

        // Topik yang tidak punya pemeriksaan konkret (halaman error, dsb)
        // tidak bisa diselesaikan bot, jadi arahkan ke admin.
        $unverifiable = in_array($topic, ['system', 'general'], true);

        return [
            'topic' => $topic,
            'topic_label' => self::TOPIC_LABELS[$topic] ?? $topic,
            'checks' => $checks,
            'problems' => $problems,
            'tips' => self::TIPS[$topic] ?? [],
            'orders' => $user && in_array($topic, ['payment', 'order', 'checkout'], true)
                ? $this->recentOrders($user)
                : [],
            'needs_human' => !empty($adminProblems) || $unverifiable,
        ];
    }

    private function check(
        string $key,
        string $status,
        string $owner,
        string $detail,
        string $fix = ''
    ): array {
        return [
            'key' => $key,
            'status' => $status, // ok | problem | warn
            'owner' => $owner,   // user | admin | wait
            'detail' => $detail,
            'fix' => $fix,
        ];
    }

    private function cartChecks(User $user): array
    {
        $items = CartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($items->isEmpty()) {
            return [$this->check(
                'cart_empty', 'problem', 'user',
                'Keranjangmu masih kosong, jadi tidak ada yang bisa di-checkout.',
                'Tambahkan produk dari menu Kategori, lalu buka Keranjang.'
            )];
        }

        $checks = [];

        if (empty($this->cart->selectedItems($user))) {
            $checks[] = $this->check(
                'nothing_selected', 'problem', 'user',
                'Belum ada produk yang dicentang di keranjang. Checkout hanya memproses produk yang dipilih.',
                'Centang produk yang mau dibeli di Keranjang, lalu klik Checkout.'
            );
        }

        $bad = [
            'product_missing' => [],
            'stock_empty' => [],
            'stock_short' => [],
            'price_missing' => [],
            'weight_missing' => [],
        ];

        foreach ($items as $item) {
            $product = $item->product;

            if (!$product) {
                $bad['product_missing'][] = 'sebuah produk';
                continue;
            }

            if ((int) $product->stock < 1) {
                $bad['stock_empty'][] = $product->name;
            } elseif ((int) $item->quantity > (int) $product->stock) {
                $bad['stock_short'][] = "{$product->name} (stok {$product->stock})";
            }

            if ($product->price === null || (float) $product->price <= 0) {
                $bad['price_missing'][] = $product->name;
            }

            if ($product->weight === null || (int) $product->weight < 1) {
                $bad['weight_missing'][] = $product->name;
            }
        }

        $messages = [
            'product_missing' => ['user', 'Ada produk di keranjang yang sudah tidak tersedia.', 'Hapus produk itu dari keranjang.'],
            'stock_empty' => ['user', 'Stok habis untuk: %s.', 'Hapus produk itu dari keranjang atau tunggu restock.'],
            'stock_short' => ['user', 'Jumlah di keranjang melebihi stok: %s.', 'Kurangi jumlahnya sesuai stok.'],
            'price_missing' => ['admin', 'Harga belum diatur di sistem untuk: %s.', 'Produk ini belum bisa dibeli. Admin perlu mengisi harganya.'],
            'weight_missing' => ['admin', 'Berat produk belum diatur untuk: %s, sehingga ongkir tidak bisa dihitung.', 'Admin perlu mengisi berat produk itu.'],
        ];

        foreach ($bad as $key => $names) {
            if (!$names) {
                continue;
            }

            [$owner, $detail, $fix] = $messages[$key];

            $checks[] = $this->check(
                $key, 'problem', $owner,
                str_contains($detail, '%s')
                    ? sprintf($detail, implode(', ', array_unique($names)))
                    : $detail,
                $fix
            );
        }

        if (!$checks) {
            $checks[] = $this->check(
                'cart_ok', 'ok', 'user',
                "Keranjang berisi {$items->count()} jenis produk; stok, harga, dan berat terisi."
            );
        }

        return $checks;
    }

    private function addressChecks(User $user): array
    {
        $address = Address::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->latest()
            ->first();

        if (!$address) {
            return [$this->check(
                'no_address', 'problem', 'user',
                'Kamu belum punya alamat pengiriman.',
                'Tambahkan alamat lewat menu Profil > Alamat.'
            )];
        }

        $required = [
            'recipient_name' => 'nama penerima',
            'phone' => 'nomor telepon',
            'province_name' => 'provinsi',
            'city_name' => 'kota/kabupaten',
            'district_name' => 'kecamatan',
            'village_name' => 'kelurahan/desa',
            'postal_code' => 'kode pos',
            'detail' => 'detail alamat',
        ];

        $missing = [];

        foreach ($required as $field => $label) {
            if (trim((string) $address->{$field}) === '') {
                $missing[] = $label;
            }
        }

        if ($missing) {
            return [$this->check(
                'address_incomplete', 'problem', 'user',
                'Alamat utamamu belum lengkap: ' . implode(', ', $missing) . ' belum terisi.',
                'Lengkapi alamat lewat menu Profil > Alamat.'
            )];
        }

        return [$this->check(
            'address_ok', 'ok', 'user',
            'Alamat utama terisi lengkap. Ongkir dicocokkan otomatis dari provinsi, kota, kecamatan, kelurahan, dan kode pos, jadi ejaan dan kode pos harus tepat.'
        )];
    }

    private function shippingSystemChecks(): array
    {
        $checks = [];

        if (!$this->shipping->hasApiKey()) {
            $checks[] = $this->check(
                'shipping_key', 'problem', 'admin',
                'Layanan hitung ongkir dari sisi toko belum dikonfigurasi.',
                'Ini perlu diperbaiki admin.'
            );
        }

        $setting = ShippingSetting::first();

        if (!$setting || !$setting->rajaongkir_origin_id) {
            $checks[] = $this->check(
                'shipping_origin', 'problem', 'admin',
                'Lokasi asal pengiriman toko belum diatur.',
                'Ini perlu diperbaiki admin.'
            );
        }

        $cooldown = $this->shipping->cooldownStatus();

        if ($cooldown) {
            if ($cooldown['reason'] === 'quota_exhausted') {
                $checks[] = $this->check(
                    'shipping_quota', 'problem', 'admin',
                    'Kuota layanan ongkir toko sedang habis.',
                    'Ini perlu diperbaiki admin.'
                );
            } else {
                $checks[] = $this->check(
                    'shipping_rate_limited', 'problem', 'wait',
                    "Layanan ongkir sedang membatasi permintaan (sekitar {$cooldown['retry_after']} detik lagi).",
                    'Tunggu sebentar lalu hitung ongkir lagi.'
                );
            }
        }

        return $checks;
    }

    private function paymentSystemChecks(): array
    {
        if (
            empty(config('midtrans.server_key'))
            || empty(config('midtrans.client_key'))
        ) {
            return [$this->check(
                'payment_gateway', 'problem', 'admin',
                'Pembayaran online belum bisa disiapkan dari sisi toko.',
                'Sementara pilih COD kalau tersedia, atau hubungi admin.'
            )];
        }

        return [];
    }

    private function orderChecks(User $user): array
    {
        $checks = [];

        $orders = Order::where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get();

        foreach ($orders as $order) {
            $ref = '#' . $order->id;

            if ($order->status === 'cancelled' && $order->payment_status === 'paid') {
                $checks[] = $this->check(
                    'paid_but_cancelled', 'problem', 'admin',
                    "Pesanan {$ref} tercatat sudah dibayar tetapi berstatus dibatalkan.",
                    'Perlu penanganan admin (refund / pengecekan manual).'
                );

                continue;
            }

            if ($order->status !== 'pending' || $order->payment_method === 'cod') {
                continue;
            }

            if ($order->payment_status === 'unpaid' && empty($order->snap_token)) {
                $checks[] = $this->check(
                    'payment_token_missing', 'problem', 'user',
                    "Pesanan {$ref} sudah dibuat, tetapi pembayarannya belum disiapkan.",
                    "Buka detail pesanan {$ref} di menu Pesanan lalu klik Bayar."
                );
            } elseif (in_array($order->payment_status, ['expired', 'failed'], true)) {
                $state = $order->payment_status === 'expired' ? 'kedaluwarsa' : 'gagal';

                $checks[] = $this->check(
                    'payment_' . $order->payment_status, 'problem', 'user',
                    "Pembayaran pesanan {$ref} {$state}, dan pesanan ini tidak bisa dibayar ulang.",
                    "Batalkan pesanan {$ref} di detail pesanan, lalu checkout ulang."
                );
            }
        }

        return $checks;
    }

    /**
     * Ringkasan pesanan terbaru tanpa data pribadi (alamat/telepon).
     */
    public function recentOrders(User $user, int $limit = 3): array
    {
        return Order::withCount('items')
            ->where('user_id', $user->id)
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn (Order $order) => [
                'nomor' => '#' . $order->id,
                'status' => $order->status,
                'status_pembayaran' => $order->payment_status,
                'metode_pembayaran' => $order->payment_method,
                'total' => (int) round((float) $order->total),
                'jumlah_jenis_produk' => (int) $order->items_count,
                'dibuat' => optional($order->created_at)->format('d-m-Y H:i'),
            ])
            ->values()
            ->all();
    }

    /* ---------------------------------------------------------------
     | Jawaban cadangan (tanpa Gemini)
     |---------------------------------------------------------------*/

    public function fallbackReply(array $diagnosis, bool $escalate): string
    {
        $label = $diagnosis['topic_label'];
        $problems = $diagnosis['problems'];
        $lines = [];

        if ($diagnosis['topic'] === 'general') {
            return 'Tentu, kamu bisa langsung menghubungi admin lewat WhatsApp di bawah. Ceritakan kendalamu singkat saja, nanti admin bantu cek.';
        }

        if ($problems) {
            $lines[] = count($problems) === 1
                ? "Aku cek akunmu dan menemukan 1 hal yang menghambat {$label}:"
                : 'Aku cek akunmu dan menemukan ' . count($problems) . " hal yang menghambat {$label}:";

            foreach ($problems as $i => $problem) {
                $lines[] = ($i + 1) . '. ' . $problem['detail'] . ' ' . $problem['fix'];
            }
        } else {
            $lines[] = $diagnosis['checks']
                ? 'Aku sudah cek bagian yang bisa kulihat dari akun dan sistem, dan semuanya terlihat normal. Coba langkah ini:'
                : 'Coba langkah ini dulu ya:';

            foreach (array_slice($diagnosis['tips'], 0, 4) as $tip) {
                $lines[] = '- ' . $tip;
            }
        }

        $adminSide = array_filter($problems, fn ($p) => $p['owner'] === 'admin');

        if ($adminSide) {
            $lines[] = 'Bagian ini harus dicek admin toko, jadi hubungi admin lewat WhatsApp di bawah ya.';
        } elseif ($escalate || $diagnosis['needs_human']) {
            $lines[] = 'Kalau sudah dicoba tapi masih belum beres, admin bisa bantu cek langsung lewat WhatsApp di bawah.';
        }

        return implode("\n", $lines);
    }

    /* ---------------------------------------------------------------
     | Tautan bantuan
     |---------------------------------------------------------------*/

    /**
     * Tautan aksi cepat sesuai masalah yang ditemukan.
     */
    public function actionLinks(array $diagnosis): array
    {
        $keys = array_column($diagnosis['problems'], 'key');
        $links = [];

        if (array_intersect($keys, [
            'cart_empty', 'nothing_selected', 'stock_empty', 'stock_short',
            'product_missing',
        ])) {
            $links[] = ['label' => 'Buka Keranjang', 'url' => route('cart.index')];
        }

        if (array_intersect($keys, ['no_address', 'address_incomplete'])) {
            $links[] = ['label' => 'Lengkapi alamat', 'url' => route('addresses.create')];
        }

        if (array_intersect($keys, [
            'payment_token_missing', 'payment_expired', 'payment_failed',
        ])) {
            $links[] = ['label' => 'Buka daftar pesanan', 'url' => route('orders.index')];
        }

        return $links;
    }

    public function whatsappNumber(): string
    {
        $digits = preg_replace('/\D+/', '', (string) config('services.support.whatsapp')) ?? '';

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            return '62' . $digits;
        }

        return $digits;
    }

    public function whatsappDisplay(): string
    {
        $digits = $this->whatsappNumber();
        $local = str_starts_with($digits, '62') ? '0' . substr($digits, 2) : $digits;

        return preg_replace('/^(\d{4})(\d{4})(\d+)$/', '$1-$2-$3', $local) ?? $local;
    }

    /**
     * Tautan WhatsApp ke admin dengan pesan yang sudah terisi konteks.
     */
    public function contactLink(
        ?User $user,
        string $topic = 'general',
        array $problems = []
    ): array {
        $store = (string) config('services.support.store_name', 'ZRadiance');
        $name = $user ? trim(explode(' ', trim((string) $user->name))[0]) : '';
        $label = self::TOPIC_LABELS[$topic] ?? 'bantuan';

        $text = "Halo admin {$store}, saya" . ($name !== '' ? " {$name}" : '')
            . " butuh bantuan soal {$label}.";

        $details = array_values(array_filter(array_map(
            fn ($problem) => is_array($problem) ? ($problem['detail'] ?? '') : '',
            $problems
        )));

        if ($details) {
            $text .= ' Kendala yang terdeteksi: ' . implode(' ', array_slice($details, 0, 3));
        }

        return [
            'label' => 'Hubungi admin via WhatsApp (' . $this->whatsappDisplay() . ')',
            'url' => 'https://wa.me/' . $this->whatsappNumber()
                . '?text=' . rawurlencode($text),
        ];
    }

    public function logEscalation(?User $user, string $topic, array $problems, string $source): void
    {
        Log::notice('Chatbot mengarahkan pelanggan ke admin.', [
            'user_id' => $user?->id,
            'topic' => $topic,
            'problem_keys' => array_column($problems, 'key'),
            'source' => $source,
        ]);
    }

    /* ---------------------------------------------------------------
     | Util
     |---------------------------------------------------------------*/

    private function hasAny(string $text, array $words): bool
    {
        foreach ($words as $word) {
            if (
                $word !== ''
                && preg_match(
                    '/(?<![\p{L}\p{N}])' . preg_quote($word, '/') . '(?![\p{L}\p{N}])/u',
                    $text
                ) === 1
            ) {
                return true;
            }
        }

        return false;
    }
}
