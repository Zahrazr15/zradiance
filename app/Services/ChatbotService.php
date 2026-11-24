<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Faq;
use App\Models\KnowledgeArticle;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;

class ChatbotService
{
    private const STOPWORDS = [
        'yang','dan','atau','dari','untuk','dengan','ini','itu','apa',
        'apakah','bagaimana','gimana','dong','nih','aku','saya','kamu',
        'adalah','pada','tolong','mohon','bisa','boleh','cara','berapa',
        'the','and','for','ada'
    ];

    private const GENERIC_WORDS = [
        'moisturizer','serum','cushion','lip','cream','mousse','matte',
        'velvet','ink','toner','sunscreen','cleanser','foundation',
        'lotion','shampoo','body','hair','face','wash','gel','mask',
        'oil','balm','tint','powder','pelembap','series','the','and',
        'dan','of','for'
    ];

    private const TYPE_MAP = [
        'moisturizer' => ['moisturizer','pelembap','pelembab'],
        'serum' => ['serum'],
        'cushion' => ['cushion'],
        'lip' => ['lip cream','lipcream','lipstik','lipstick','lip mousse','lip tint','lip balm'],
        'sunscreen' => ['sunscreen','tabir surya','sunblock'],
        'cleanser' => ['cleanser','pembersih wajah','facial wash','face wash'],
        'toner' => ['toner'],
        'foundation' => ['foundation','bedak'],
        'shampoo' => ['shampoo','sampo'],
        'lotion' => ['body lotion','lotion'],
    ];

    private const SKIN_MAP = [
        'berminyak' => ['berminyak','oily'],
        'kering' => ['kering','dry'],
        'sensitif' => ['sensitif','sensitive'],
        'kombinasi' => ['kombinasi','combination'],
        'berjerawat' => ['berjerawat','jerawat','acne'],
        'normal' => ['kulit normal','normal skin'],
    ];

    private const ASPECT_MAP = [
        'ingredients' => ['kandungan','ingredient','ingredients','bahan','komposisi','mengandung'],
        'benefits' => [
            'fungsi','fungsinya','manfaat','manfaatnya','khasiat',
            'kegunaan','gunanya','berguna','buat apa','apa gunanya'
        ],
        'how_to_use' => [
            'cara pakai','cara menggunakan','cara penggunaan','pakai',
            'dipakai','pemakaian','menggunakan','gunakan','digunakan',
            'oleskan','aplikasikan'
        ],
        'warnings' => [
            'perhatian','efek samping','aman','hamil','menyusui',
            'alergi','iritasi','bahaya','peringatan','waspada'
        ],
        'skin_type' => [
            'jenis kulit','cocok untuk kulit','cocok kulit','untuk kulit'
        ],
        'price' => ['harga','berapa'],
        'stock' => ['stok','tersedia','ready'],
    ];

    private const ASPECT_LABEL = [
        'ingredients' => 'Kandungan',
        'benefits' => 'Fungsi / manfaat',
        'how_to_use' => 'Cara penggunaan',
        'warnings' => 'Hal yang perlu diperhatikan',
        'skin_type' => 'Jenis kulit yang disarankan',
    ];

    private const INGREDIENT_ALIASES = [
        'ceramide' => [
            'ceramide','cerimide','ceramid','ceramides','ceramide complex'
        ],
        'niacinamide' => [
            'niacinamide','niacinamid','niaciamide','niasinamide',
            'niacinamida','niaciamida'
        ],
        'retinol' => ['retinol','retinoll'],
        'hyaluronic acid' => [
            'hyaluronic acid','hyaluronik acid','hyaluronate'
        ],
        'salicylic acid' => ['salicylic acid','salicilic acid'],
        'glycolic acid' => ['glycolic acid','glikolik acid'],
        'lactic acid' => ['lactic acid','laktik acid'],
        'vitamin c' => ['vitamin c','vit c','ascorbic acid'],
        'panthenol' => ['panthenol'],
        'centella asiatica' => ['centella asiatica','centella','cica'],
        'alpha arbutin' => ['alpha arbutin','arbutin'],
        'benzoyl peroxide' => ['benzoyl peroxide','benzoil peroxide'],
        'peptide' => ['peptide','peptides'],
        'squalane' => ['squalane','squalene'],
        'kojic acid' => ['kojic acid'],
        'azelaic acid' => ['azelaic acid'],
        'tea tree' => ['tea tree','tea tree oil'],
        'zinc oxide' => ['zinc oxide'],
        'aha' => ['aha','alpha hydroxy acid'],
        'bha' => ['bha','beta hydroxy acid'],
    ];

    public function __construct(
        private SupportDiagnosisService $support
    ) {}

    /**
     * @param  User|null  $user  Pelanggan yang login, dipakai untuk diagnosis kendala sistem.
     * @param  string|null  $previousSupportTopic  Topik bantuan pada giliran sebelumnya (untuk "masih gagal").
     */
    public function reply(
        string $message,
        ?int $contextProductId = null,
        ?string $userName = null,
        ?User $user = null,
        ?string $previousSupportTopic = null
    ): array {
        $text = $this->normalize($message);

        if ($text === '') {
            return $this->fallback();
        }

        if ($this->isGreeting($text)) {
            return $this->greeting($userName);
        }

        if ($this->isThanks($text)) {
            return $this->response(
                'Sama-sama! Kalau masih ada yang ingin ditanyakan soal produk atau pesananmu, tulis saja ya.',
                ['suggestions' => $this->defaultSuggestions()]
            );
        }

        /*
         * Kendala sistem (checkout, ongkir, pembayaran, pesanan, akun)
         * ditangani sebelum pencarian produk.
         */
        $supportIntent = $this->support->detectTopic(
            $text,
            $previousSupportTopic
        );

        if ($supportIntent !== null) {
            return $this->supportReply($supportIntent, $user);
        }

        $products = Product::with('category')->get();

        $context = $contextProductId
            ? $products->firstWhere('id', $contextProductId)
            : null;

        $matched = $this->matchProducts($text, $products);
        $aspects = $this->detectAspects($text);

        $usesContext = $context
            && $matched->isEmpty()
            && !empty($aspects)
            && $this->containsWord($text, ['ini','itu']);

        $candidates = $usesContext
            ? collect([$context])
            : $matched;

        $criteria = $this->extractCriteria($text, $products);
        $hasIngredient = !empty($criteria['ingredients']);

        /*
         * Pisahkan pencarian produk dari pertanyaan pengetahuan kandungan.
         */
        $ingredientSearch = $hasIngredient && $this->containsWord($text, [
            'produk',
            'cari',
            'carikan',
            'rekomendasi',
            'rekomen',
            'mengandung',
            'dengan kandungan',
            'yang ada',
            'yang punya',
            'di sini',
            'disini',
        ]);

        $ingredientQuestion = $hasIngredient && $this->containsWord($text, [
            'fungsi',
            'fungsinya',
            'manfaat',
            'manfaatnya',
            'khasiat',
            'kegunaan',
            'gunanya',
            'apa itu',
            'untuk apa',
            'buat apa',
            'apa gunanya',
            'bagaimana',
            'jelaskan',
            'kandungan',
            'cocok untuk',
            'cara kerja',
            'berguna',
            'mengatasi',
            'bagus untuk',
            'efek',
        ]);

        /*
         * Contoh:
         * "Ada produk niasinamide?"
         * "Cari moisturizer yang mengandung ceramide"
         */
        if ($ingredientSearch) {
            return $this->recommend($criteria, $products);
        }

        /*
         * Contoh:
         * "Cerimide apa fungsinya?"
         * "Niacinamide buat apa?"
         *
         * Pertanyaan umum tidak perlu meminta nama produk.
         */
        if (
            $matched->isEmpty()
            && !$usesContext
            && $ingredientQuestion
        ) {
            $related = $this->productsForIngredients(
                $products,
                $criteria['ingredients']
            );

            $articles = KnowledgeArticle::where('category', 'bahan')
                ->where('is_active', true)
                ->get()
                ->filter(function ($article) use ($criteria) {
                    $haystack = $this->normalize(
                        ($article->title ?? '') . ' ' .
                        ($article->summary ?? '') . ' ' .
                        ($article->keywords ?? '')
                    );

                    foreach ($criteria['ingredients'] as $term) {
                        if (str_contains($haystack, $term)) {
                            return true;
                        }
                    }

                    return false;
                })
                ->take(4)
                ->map(fn ($article) => [
                    'title' => $article->title,
                    'summary' => $article->summary,
                    'keywords' => $article->keywords,
                ])
                ->values()
                ->all();

            return $this->response(
                'Aku bantu jelaskan kandungan tersebut ya.',
                [
                    '_use_ai' => true,
                    '_ai_context' => [
                        'intent' => 'ingredient_explanation',
                        'ingredients' => $criteria['ingredients'],
                        'database_products' => $related
                            ->take(8)
                            ->map(fn ($product) => $this->aiProductData($product))
                            ->values()
                            ->all(),
                        'knowledge_articles' => $articles,
                    ],
                    'suggestions' => $this->defaultSuggestions(),
                ]
            );
        }

        $compare = $this->containsWord($text, [
            'bandingkan',
            'perbandingan',
            'banding',
            'vs',
            'versus',
            'lebih bagus mana',
            'lebih baik mana',
            'bedanya',
        ]);

        $conjunction = $this->containsWord(
            $text,
            ['dan','atau','vs','versus']
        );

        if ($compare || ($candidates->count() >= 2 && $conjunction)) {
            $set = $candidates;

            if (
                $set->count() === 1
                && $context
                && $set->first()->id !== $context->id
            ) {
                $set = $set->push($context);
            }

            if ($set->count() >= 2) {
                return $this->compare($set->take(3)->values());
            }

            if ($set->count() === 1) {
                return $this->response(
                    "Mau dibandingkan dengan produk apa? Sebutkan nama produk lainnya, misalnya \"bandingkan {$set->first()->name} dengan ...\".",
                    ['suggestions' => $this->productSuggestions($products, 3)]
                );
            }
        }

        $knowledge = $this->searchKnowledge($text);

        if ($candidates->count() >= 2) {
            return $this->response(
                'Aku menemukan beberapa produk yang cocok dengan kata kuncimu. Maksudmu yang mana?',
                [
                    'products' => $candidates
                        ->take(4)
                        ->map(fn ($product) => $this->productCard($product))
                        ->values()
                        ->all(),

                    'suggestions' => $candidates
                        ->take(4)
                        ->map(fn ($product) => "Ceritakan tentang {$product->brand} {$product->name}")
                        ->values()
                        ->all(),
                ]
            );
        }

        if (
            $candidates->count() === 1
            && (!empty($aspects) || !$knowledge)
        ) {
            return $this->productAnswer(
                $candidates->first(),
                $text,
                $aspects
            );
        }

        $hasPrice = $criteria['max'] !== null
            || $criteria['min'] !== null;

        $recWord = $this->containsWord($text, [
            'rekomendasi',
            'rekomendasikan',
            'rekomen',
            'cari',
            'carikan',
            'butuh',
            'saran',
            'sarankan',
            'cocok',
            'paling bagus',
            'terbaik',
            'yang bagus',
        ]);

        if (
            $recWord
            || $hasPrice
            || (
                $criteria['skin']
                && (
                    !empty($criteria['types'])
                    || !empty($criteria['categoryIds'])
                )
            )
        ) {
            return $this->recommend($criteria, $products);
        }

        if ($knowledge) {
            return $this->knowledgeAnswer($knowledge, $products);
        }

        if (!empty($aspects)) {
            return $this->response(
                'Produk yang mana yang kamu maksud? Sebutkan nama produknya ya, atau buka lewat tombol "Tanya Chatbot" di kartu produk.',
                ['suggestions' => $this->productSuggestions($products, 3)]
            );
        }

        return $this->fallback();
    }

    private function supportReply(array $intent, ?User $user): array
    {
        $topic = $intent['topic'];
        $escalate = (bool) $intent['escalate'];

        $diagnosis = $this->support->diagnose($topic, $user);
        $needsHuman = $escalate || $diagnosis['needs_human'];

        $links = $this->support->actionLinks($diagnosis);

        if ($needsHuman) {
            $links[] = $this->support->contactLink(
                $user,
                $topic,
                $diagnosis['problems']
            );

            $this->support->logEscalation(
                $user,
                $topic,
                $diagnosis['problems'],
                'diagnosis'
            );
        }

        return $this->response(
            $this->support->fallbackReply($diagnosis, $escalate),
            [
                'links' => $links,
                'suggestions' => $needsHuman ? [] : $this->supportSuggestions($topic),
                '_use_ai' => true,
                '_ai_context' => [
                    'intent' => 'support_' . $topic,
                    'diagnosis' => [
                        'topic' => $diagnosis['topic_label'],
                        'problems' => $this->aiChecks($diagnosis['problems']),
                        'checks_ok' => $this->aiChecks(array_filter(
                            $diagnosis['checks'],
                            fn ($check) => $check['status'] === 'ok'
                        )),
                        'tips' => $diagnosis['tips'],
                        'recent_orders' => $diagnosis['orders'],
                        'needs_admin' => $needsHuman,
                    ],
                ],
                '_support_topic' => $topic,
                '_support_problems' => $diagnosis['problems'],
                '_support_needs_human' => $needsHuman,
            ]
        );
    }

    private function aiChecks(array $checks): array
    {
        return array_values(array_map(
            fn ($check) => [
                'issue' => $check['detail'],
                'owner' => $check['owner'],
                'fix' => $check['fix'],
            ],
            $checks
        ));
    }

    private function supportSuggestions(string $topic): array
    {
        return match ($topic) {
            'checkout', 'shipping' => ['Ongkir tidak muncul', 'Cara mengisi alamat dengan benar'],
            'payment' => ['Pembayaran saya kedaluwarsa', 'Bagaimana cara membatalkan pesanan?'],
            'order' => ['Bagaimana cara melihat status pesanan?'],
            default => [],
        };
    }

    private function productAnswer(
        Product $product,
        string $text,
        array $aspects
    ): array {
        $title = trim(($product->brand ?? '') . ' ' . $product->name);
        $lines = ["Tentang {$title}:"];

        if (empty($aspects)) {
            if (trim((string) $product->description) !== '') {
                $lines[] = $product->description;
            }

            $lines[] = 'Harga: ' . $this->rupiah($product->price)
                . ' | '
                . ($product->stock > 0
                    ? "Stok: {$product->stock}"
                    : 'Stok habis');

            foreach (self::ASPECT_LABEL as $field => $label) {
                $value = trim((string) $product->{$field});

                if ($value !== '') {
                    $lines[] = "{$label}: {$value}";
                }
            }
        } else {
            foreach ($aspects as $aspect) {
                if ($aspect === 'price') {
                    $lines[] = 'Harga: ' . $this->rupiah($product->price);
                } elseif ($aspect === 'stock') {
                    $lines[] = $product->stock > 0
                        ? "Stok tersedia: {$product->stock}."
                        : 'Stok sedang habis.';
                } else {
                    $label = self::ASPECT_LABEL[$aspect] ?? $aspect;
                    $value = trim((string) $product->{$aspect});

                    $lines[] = $value !== ''
                        ? "{$label}: {$value}"
                        : "{$label}: datanya belum tersedia di sistem kami, jadi aku tidak bisa memastikannya.";
                }
            }

            if (
                in_array('warnings', $aspects, true)
                && trim((string) $product->warnings) === ''
            ) {
                $lines[] = 'Kalau kamu sedang hamil, menyusui, atau punya kondisi kulit tertentu, sebaiknya konsultasikan dulu ke dokter.';
            }
        }

        return $this->response(
            implode("\n", $lines),
            [
                'products' => [$this->productCard($product)],
                '_use_ai' => true,
                '_ai_context' => [
                    'intent' => 'product_question',
                    'products' => [$this->aiProductData($product)],
                ],
                'suggestions' => [
                    "Kandungan {$title}",
                    "Cara pakai {$title}",
                ],
            ]
        );
    }

    
    private function compare(Collection $set): array
    {
        $headers = [''];

        foreach ($set as $product) {
            $headers[] = trim(
                ($product->brand ?? '') . ' ' . $product->name
            );
        }

        $rows = [];

        $fields = [
            ['Harga', fn ($p) => $this->rupiah($p->price)],
            ['Stok', fn ($p) => $p->stock > 0 ? (string) $p->stock : 'Habis'],
            ['Kategori', fn ($p) => optional($p->category)->cat_title],
            ['Kandungan', fn ($p) => $p->ingredients],
            ['Fungsi', fn ($p) => $p->benefits],
            ['Jenis kulit', fn ($p) => $p->skin_type],
            ['Cara pakai', fn ($p) => $p->how_to_use],
            ['Perhatian', fn ($p) => $p->warnings],
        ];

        foreach ($fields as [$label, $getter]) {
            $rows[] = $this->compareRow($label, $set, $getter);
        }

        $lines = [
            'Ini perbandingan berdasarkan data yang tersimpan di ZRadiance:'
        ];

        $sorted = $set->sortBy('price')->values();

        if ($sorted->first()->price != $sorted->last()->price) {
            $cheapest = $sorted->first();

            $lines[] = "- Harga paling terjangkau: {$cheapest->brand} {$cheapest->name} (" .
                $this->rupiah($cheapest->price) . ').';
        }

        $sold = $set->filter(fn ($p) => $p->stock <= 0);

        if ($sold->isNotEmpty()) {
            $lines[] = '- Stok habis: ' . $sold
                ->map(fn ($p) => "{$p->brand} {$p->name}")
                ->implode(', ') . '.';
        }

        $lines[] = '"Belum ada data" berarti informasi tersebut belum diisi di sistem.';

        $aiProducts = $set
            ->map(fn ($product) => $this->aiProductData($product))
            ->values()
            ->all();

        return $this->response(
            implode("\n", $lines),
            [
                'table' => [
                    'headers' => $headers,
                    'rows' => $rows,
                ],

                'products' => $set
                    ->map(fn ($product) => $this->productCard($product))
                    ->values()
                    ->all(),

                '_use_ai' => true,

                '_ai_context' => [
                    'intent' => 'product_comparison',
                    'products' => $aiProducts,
                ],
            ]
        );
    }

    private function compareRow(
        string $label,
        Collection $set,
        callable $getter
    ): array {
        $row = [$label];

        foreach ($set as $product) {
            $value = trim((string) $getter($product));
            $row[] = $value !== '' ? $value : 'Belum ada data';
        }

        return $row;
    }

    private function recommend(array $criteria, Collection $products): array
    {
        $hasAny = $criteria['max'] !== null
            || $criteria['min'] !== null
            || $criteria['skin']
            || $criteria['types']
            || $criteria['categoryIds']
            || $criteria['ingredients'];

        if (!$hasAny) {
            return $this->response(
                'Biar rekomendasinya pas, kasih tahu jenis produk, jenis kulit, budget, atau kandungan yang dicari.',
                ['suggestions' => $this->defaultSuggestions()]
            );
        }

        $base = $products->filter(function ($product) use ($criteria) {
            if (
                $criteria['categoryIds']
                && !in_array($product->product_cat, $criteria['categoryIds'])
            ) {
                return false;
            }

            if (
                $criteria['types']
                && !$this->productMatchesTypes($product, $criteria['types'])
            ) {
                return false;
            }

            foreach ($criteria['ingredients'] as $ingredient) {
                if (!$this->productContainsIngredient($product, $ingredient)) {
                    return false;
                }
            }

            return true;
        });

        $inStock = $base->filter(fn ($p) => $p->stock > 0);
        $out = $base->count() - $inStock->count();

        $pool = $inStock->filter(fn ($p) =>
            ($criteria['max'] === null || (float) $p->price <= $criteria['max'])
            && ($criteria['min'] === null || (float) $p->price >= $criteria['min'])
        );

        $understood = [];

        if ($criteria['types']) {
            $understood[] = 'jenis: ' . implode(', ', $criteria['types']);
        }

        if ($criteria['categoryTitles']) {
            $understood[] = 'kategori: ' . implode(', ', $criteria['categoryTitles']);
        }

        if ($criteria['skin']) {
            $understood[] = 'kulit ' . $criteria['skin'];
        }

        if ($criteria['ingredients']) {
            $understood[] = 'kandungan: ' . implode(', ', $criteria['ingredients']);
        }

        if ($criteria['max'] !== null) {
            $understood[] = 'harga maks. ' . $this->rupiah($criteria['max']);
        }

        if ($criteria['min'] !== null) {
            $understood[] = 'harga min. ' . $this->rupiah($criteria['min']);
        }

        $lines = [
            'Kriteria yang kutangkap: ' . implode(' | ', $understood) . '.'
        ];

        if ($pool->isEmpty()) {
            if ($inStock->isNotEmpty()) {
                $near = $inStock->sortBy('price')->take(2);

                $lines[] = 'Belum ada produk yang pas di rentang harga itu. Ini produk dengan kriteria lain yang sama tetapi di luar rentang harga:';

                return $this->response(
                    implode("\n", $lines),
                    [
                        'products' => $near
                            ->map(fn ($p) => $this->productCard($p))
                            ->values()
                            ->all(),
                        '_use_ai' => true,
                        '_ai_context' => [
                            'intent' => 'recommendation',
                            'criteria' => $understood,
                            'products' => $near
                                ->map(fn ($p) => $this->aiProductData($p))
                                ->values()
                                ->all(),
                        ],
                    ]
                );
            }

            $lines[] = $out > 0
                ? 'Produk yang sesuai ada, tetapi stoknya sedang habis.'
                : 'Belum ada produk di ZRadiance yang sesuai kriteria tersebut.';

            if ($criteria['ingredients']) {
                $lines[] = 'Produk yang belum memiliki data kandungan tidak bisa kusaring.';
            }

            return $this->response(
                implode("\n", $lines),
                ['suggestions' => $this->defaultSuggestions()]
            );
        }

        $verified = collect();
        $unverified = collect();

        if ($criteria['skin']) {
            foreach ($pool as $product) {
                if (trim((string) $product->skin_type) === '') {
                    $unverified->push($product);
                } elseif ($this->skinMatches($product, $criteria['skin'])) {
                    $verified->push($product);
                }
            }
        } else {
            $verified = $pool;
        }

        $cards = [];

        foreach ($verified->sortBy('price')->take(3) as $product) {
            $why = ['Harga ' . $this->rupiah($product->price)];

            if ($criteria['skin']) {
                $why[] = "Sesuai untuk kulit {$criteria['skin']} (menurut data toko: {$product->skin_type})";
            }

            if (trim((string) $product->ingredients) !== '') {
                $why[] = 'Kandungan: ' . $this->shorten($product->ingredients, 110);
            }

            if (trim((string) $product->benefits) !== '') {
                $why[] = 'Fungsi: ' . $this->shorten($product->benefits, 110);
            }

            $cards[] = $this->productCard(
                $product,
                $why,
                $this->missingNotes($product)
            );
        }

        if ($criteria['skin'] && $verified->isEmpty()) {
            $lines[] = "Belum ada produk dengan data jenis kulit \"{$criteria['skin']}\" yang terverifikasi. Berikut produk yang cocok di kriteria lain, tetapi kesesuaian jenis kulitnya belum bisa kupastikan:";
        } else {
            $lines[] = 'Ini pilihan yang sesuai (urut dari harga termurah):';
        }

        if ($criteria['skin']) {
            foreach (
                $unverified->sortBy('price')
                    ->take($verified->isEmpty() ? 3 : 2)
                as $product
            ) {
                $cards[] = $this->productCard(
                    $product,
                    ['Harga ' . $this->rupiah($product->price)],
                    array_merge(
                        [
                            "Data jenis kulit belum tersedia, jadi kecocokan untuk kulit {$criteria['skin']} belum bisa dipastikan"
                        ],
                        $this->missingNotes($product, ['skin_type'])
                    )
                );
            }
        }

        if (empty($cards)) {
            return $this->response(
                implode("\n", $lines) . "\nTidak ada produk dengan kecocokan jenis kulit yang dapat dipastikan.",
                ['suggestions' => $this->defaultSuggestions()]
            );
        }

        if ($out > 0) {
            $lines[] = "Ada {$out} produk lain yang sesuai tetapi stoknya habis.";
        }

        $lines[] = 'Rekomendasi memakai data yang tersimpan di toko. Kecocokan yang datanya belum tersedia tidak akan diasumsikan.';

        $selected = $verified
            ->sortBy('price')
            ->take(5);

        return $this->response(
            implode("\n", $lines),
            [
                'products' => $cards,
                'suggestions' => count($cards) >= 2
                    ? ['Bandingkan ' . $cards[0]['name'] . ' dan ' . $cards[1]['name']]
                    : [],
                '_use_ai' => true,
                '_ai_context' => [
                    'intent' => 'recommendation',
                    'criteria' => $understood,
                    'products' => $selected
                        ->map(fn ($p) => $this->aiProductData($p))
                        ->values()
                        ->all(),
                ],
            ]
        );
    }

    /**
     * Periksa kandungan dari kolom ingredients, description,
     * name, dan benefits.
     *
     * Alias dipakai agar variasi ejaan tetap bisa dicari.
     */
    private function productContainsIngredient(
        Product $product,
        string $ingredient
    ): bool {
        $haystack = $this->normalize(
            (string) $product->ingredients . ' ' .
            (string) $product->description . ' ' .
            (string) $product->name . ' ' .
            (string) $product->benefits
        );

        $aliases = self::INGREDIENT_ALIASES[$ingredient] ?? [$ingredient];
        $aliases[] = $ingredient;

        foreach (array_unique($aliases) as $alias) {
            $alias = $this->normalize($alias);

            if ($alias === '') {
                continue;
            }

            $pattern = '/(?<![\p{L}\p{N}])'
                . preg_quote($alias, '/')
                . '(?![\p{L}\p{N}])/u';

            if (preg_match($pattern, $haystack) === 1) {
                return true;
            }
        }

        return false;
    }

    private function productsForIngredients(
        Collection $products,
        array $ingredients
    ): Collection {
        return $products->filter(function ($product) use ($ingredients) {
            foreach ($ingredients as $ingredient) {
                if ($this->productContainsIngredient($product, $ingredient)) {
                    return true;
                }
            }

            return false;
        })->values();
    }

    private function aiProductData(Product $product): array
    {
        return [
            'brand' => $product->brand,
            'name' => $product->name,
            'category' => optional($product->category)->cat_title,
            'price' => (float) $product->price,
            'stock' => (int) $product->stock,
            'description' => $product->description,
            'ingredients' => $product->ingredients,
            'benefits' => $product->benefits,
            'skin_type' => $product->skin_type,
            'how_to_use' => $product->how_to_use,
            'warnings' => $product->warnings,
        ];
    }

    private function missingNotes(Product $product, array $skip = []): array
    {
        $notes = [];

        $fields = [
            'ingredients' => 'Data kandungan belum tersedia',
            'benefits' => 'Data fungsi belum tersedia',
            'skin_type' => 'Data jenis kulit belum tersedia',
        ];

        foreach ($fields as $field => $message) {
            if (
                !in_array($field, $skip, true)
                && trim((string) $product->{$field}) === ''
            ) {
                $notes[] = $message;
            }
        }

        return $notes;
    }

    private function knowledgeAnswer(
        array $hits,
        Collection $products
    ): array {
        $best = $hits[0];
        $links = [];
        $cards = [];

        if ($best['type'] === 'faq') {
            $reply = $best['model']->answer;

            $links[] = [
                'label' => 'Lihat di Pusat Bantuan',
                'url' => route('knowledge.index') . '#faq-' . $best['model']->id,
            ];
        } else {
            $article = $best['model'];

            $reply = "{$article->title}\n{$article->summary}";

            $links[] = [
                'label' => 'Baca selengkapnya',
                'url' => route('knowledge.index') . '#artikel-' . $article->slug,
            ];

            if ($article->category === 'bahan') {
                $terms = array_map(
                    fn ($term) => $this->canonicalIngredient($term),
                    $this->splitKeywords($article->keywords)
                );

                $found = $products->filter(function ($product) use ($terms) {
                    foreach ($terms as $term) {
                        if ($this->productContainsIngredient($product, $term)) {
                            return true;
                        }
                    }

                    return false;
                });

                if ($found->isNotEmpty()) {
                    $reply .= "\n\nProduk di ZRadiance yang datanya mencantumkan bahan ini:";

                    $cards = $found
                        ->take(3)
                        ->map(fn ($p) => $this->productCard($p))
                        ->values()
                        ->all();
                } else {
                    $reply .= "\n\nSaat ini belum ada produk di ZRadiance yang datanya mencantumkan bahan ini.";
                }
            }
        }

        foreach (array_slice($hits, 1, 2) as $other) {
            $model = $other['model'];

            $links[] = $other['type'] === 'faq'
                ? [
                    'label' => $model->question,
                    'url' => route('knowledge.index') . '#faq-' . $model->id,
                ]
                : [
                    'label' => $model->title,
                    'url' => route('knowledge.index') . '#artikel-' . $model->slug,
                ];
        }

        return $this->response(
            $reply,
            [
                'products' => $cards,
                'links' => $links,
            ]
        );
    }

    private function searchKnowledge(string $text): ?array
    {
        $tokens = array_values(array_unique(array_filter(
            explode(' ', $text),
            fn ($word) => mb_strlen($word) >= 5
                && !in_array($word, self::STOPWORDS, true)
        )));

        $hits = [];

        foreach (Faq::where('is_active', true)->get() as $faq) {
            $score = $this->scoreEntry(
                $text,
                $tokens,
                $faq->keywords,
                $faq->question
            );

            if ($score >= 2) {
                $hits[] = [
                    'type' => 'faq',
                    'model' => $faq,
                    'score' => $score,
                ];
            }
        }

        foreach (
            KnowledgeArticle::where('is_active', true)->get()
            as $article
        ) {
            $score = $this->scoreEntry(
                $text,
                $tokens,
                $article->keywords,
                $article->title
            );

            if ($score >= 2) {
                $hits[] = [
                    'type' => 'article',
                    'model' => $article,
                    'score' => $score,
                ];
            }
        }

        if (!$hits) {
            return null;
        }

        usort($hits, fn ($a, $b) => $b['score'] <=> $a['score']);

        return $hits;
    }

    private function scoreEntry(
        string $text,
        array $tokens,
        ?string $keywords,
        string $title
    ): float {
        $score = 0;

        foreach ($this->splitKeywords($keywords) as $keyword) {
            $words = preg_split('/\s+/u', $keyword, -1, PREG_SPLIT_NO_EMPTY);

            if (!$words) {
                continue;
            }

            $all = true;

            foreach ($words as $word) {
                if (!str_contains($text, $word)) {
                    $all = false;
                    break;
                }
            }

            if ($all) {
                $score += 2 * count($words);
            }
        }

        foreach ($tokens as $token) {
            if (str_contains(mb_strtolower($title), $token)) {
                $score++;
            }
        }

        return $score;
    }

    private function splitKeywords(?string $keywords): array
    {
        return array_values(array_filter(
            array_map(
                fn ($keyword) => trim(mb_strtolower($keyword)),
                explode(',', (string) $keywords)
            )
        ));
    }

    private function matchProducts(
        string $text,
        Collection $products
    ): Collection {
        $words = array_unique(explode(' ', $text));
        $compact = str_replace(' ', '', $text);
        $scores = [];

        foreach ($products as $product) {
            $score = 0;
            $name = $this->normalize((string) $product->name);

            if ($name !== '' && str_contains($text, $name)) {
                $score += 5;
            }

            foreach ($this->distinctiveTokens($product) as $token) {
                if (
                    in_array($token, $words, true)
                    || (
                        mb_strlen($token) >= 6
                        && str_contains($compact, $token)
                    )
                ) {
                    $score++;
                }
            }

            if ($score > 0) {
                $scores[$product->id] = $score;
            }
        }

        if (!$scores) {
            return collect();
        }

        $max = max($scores);

        return $products
            ->filter(fn ($p) => ($scores[$p->id] ?? 0) === $max)
            ->values();
    }

    private function distinctiveTokens(Product $product): array
    {
        $tokens = explode(
            ' ',
            $this->normalize(
                ($product->brand ?? '') . ' ' . $product->name
            )
        );

        return array_values(array_unique(array_filter(
            $tokens,
            fn ($token) => mb_strlen($token) >= 3
                && !in_array($token, self::STOPWORDS, true)
                && !in_array($token, self::GENERIC_WORDS, true)
        )));
    }

    private function detectAspects(string $text): array
    {
        $found = [];

        foreach (self::ASPECT_MAP as $aspect => $words) {
            if ($this->containsWord($text, $words)) {
                $found[] = $aspect;
            }
        }

        return $found;
    }

    private function extractCriteria(
        string $text,
        Collection $products
    ): array {
        $max = null;
        $min = null;

        if (preg_match(
            '/\b(?:di ?bawah|kurang dari|maksimal|maksimum|maks|max|under|tidak lebih dari|budget|sampai|hingga)\s*(?:rp\s*)?(\d+)\s*(rb|ribu|k|jt|juta)?\b/u',
            $text,
            $match
        )) {
            $max = $this->toRupiah(
                (int) $match[1],
                $match[2] ?? null
            );
        }

        if (preg_match(
            '/\b(?:di ?atas|lebih dari|minimal|minimum|min)\s*(?:rp\s*)?(\d+)\s*(rb|ribu|k|jt|juta)?\b/u',
            $text,
            $match
        )) {
            $min = $this->toRupiah(
                (int) $match[1],
                $match[2] ?? null
            );
        }

        $skin = null;

        if (preg_match('/\b(kulit|skin|wajah)\b/u', $text)) {
            foreach (self::SKIN_MAP as $label => $aliases) {
                if ($this->containsWord($text, $aliases)) {
                    $skin = $label;
                    break;
                }
            }
        }

        $types = [];

        foreach (self::TYPE_MAP as $canonical => $synonyms) {
            if ($this->containsWord($text, $synonyms)) {
                $types[] = $canonical;
            }
        }

        $categoryIds = [];
        $categoryTitles = [];
        $compactText = str_replace(' ', '', $text);

        foreach (Category::all() as $category) {
            $title = $this->normalize($category->cat_title);
            $compact = str_replace(' ', '', $title);

            if (
                $title !== ''
                && (
                    str_contains($text, $title)
                    || (
                        mb_strlen($compact) >= 5
                        && str_contains($compactText, $compact)
                    )
                )
            ) {
                $categoryIds[] = $category->cat_id;
                $categoryTitles[] = $category->cat_title;
            }
        }

        $ingredients = [];

        foreach (self::INGREDIENT_ALIASES as $canonical => $aliases) {
            foreach ($aliases as $alias) {
                if ($this->fuzzyPhraseMatch($text, $alias)) {
                    $ingredients[] = $canonical;
                    break;
                }
            }
        }

        foreach (
            KnowledgeArticle::where('category', 'bahan')
                ->where('is_active', true)
                ->get()
            as $article
        ) {
            foreach ($this->splitKeywords($article->keywords) as $term) {
                if ($this->fuzzyPhraseMatch($text, $term)) {
                    $ingredients[] = $this->canonicalIngredient($term);
                }
            }
        }

        return [
            'max' => $max,
            'min' => $min,
            'skin' => $skin,
            'types' => $types,
            'categoryIds' => array_values(array_unique($categoryIds)),
            'categoryTitles' => array_values(array_unique($categoryTitles)),
            'ingredients' => array_values(array_unique($ingredients)),
        ];
    }

    private function canonicalIngredient(string $term): string
    {
        $term = $this->normalize($term);

        foreach (self::INGREDIENT_ALIASES as $canonical => $aliases) {
            if ($term === $canonical || in_array($term, $aliases, true)) {
                return $canonical;
            }
        }

        return $term;
    }

    private function fuzzyPhraseMatch(
        string $text,
        string $phrase
    ): bool {
        $phrase = $this->normalize($phrase);

        if ($phrase === '' || str_contains($text, $phrase)) {
            return $phrase !== '';
        }

        $targets = explode(' ', $phrase);
        $words = explode(' ', $text);

        foreach ($targets as $target) {
            $found = false;

            foreach ($words as $word) {
                if ($target === $word) {
                    $found = true;
                    break;
                }

                if (
                    mb_strlen($target) >= 6
                    && abs(mb_strlen($word) - mb_strlen($target)) <= 2
                    && levenshtein($word, $target) <= 2
                ) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return false;
            }
        }

        return true;
    }

    private function toRupiah(int $number, ?string $unit): float
    {
        if (in_array($unit, ['rb','ribu','k'], true)) {
            return $number * 1000;
        }

        if (in_array($unit, ['jt','juta'], true)) {
            return $number * 1000000;
        }

        return $number < 1000
            ? $number * 1000
            : $number;
    }

    private function productMatchesTypes(
        Product $product,
        array $types
    ): bool {
        $haystack = mb_strtolower(
            (string) $product->name . ' ' .
            (string) $product->description . ' ' .
            (string) optional($product->category)->cat_title
        );

        foreach ($types as $type) {
            foreach (self::TYPE_MAP[$type] as $synonym) {
                if (str_contains($haystack, $synonym)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function skinMatches(Product $product, string $skin): bool
    {
        $stored = mb_strtolower((string) $product->skin_type);

        if (str_contains($stored, 'semua')) {
            return true;
        }

        foreach (self::SKIN_MAP[$skin] as $alias) {
            if (str_contains($stored, str_replace('kulit ', '', $alias))) {
                return true;
            }
        }

        return false;
    }

    private function normalize(string $text): string
    {
        $text = mb_strtolower($text);

        $text = str_replace(
            ['<=','<'],
            ' dibawah ',
            $text
        );

        $text = str_replace(
            ['>=','>'],
            ' diatas ',
            $text
        );

        $text = preg_replace(
            '/(\d)[.,](?=\d{3}\b)/',
            '$1',
            $text
        );

        $text = preg_replace(
            '/[^\p{L}\p{N}\s]/u',
            ' ',
            $text
        );

        $text = preg_replace('/\s+/u', ' ', $text);

        return trim($text);
    }

    private function containsWord(string $text, array $words): bool
    {
        foreach ($words as $word) {
            $word = $this->normalize((string) $word);

            if (
                $word !== ''
                && preg_match(
                    '/\b' . preg_quote($word, '/') . '\b/u',
                    $text
                ) === 1
            ) {
                return true;
            }
        }

        return false;
    }

    private function isGreeting(string $text): bool
    {
        return mb_strlen($text) <= 25
            && preg_match(
                '/^(halo|hallo|hai|hi|hello|selamat \w+|permisi|assalamualaikum)\b/u',
                $text
            ) === 1;
    }

    private function isThanks(string $text): bool
    {
        return mb_strlen($text) <= 40
            && $this->containsWord(
                $text,
                ['terima kasih','terimakasih','makasih','thanks','thank you']
            );
    }

    private function rupiah($number): string
    {
        return 'Rp' . number_format((float) $number, 0, ',', '.');
    }

    private function shorten(string $text, int $limit): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text));

        return mb_strlen($text) > $limit
            ? mb_substr($text, 0, $limit - 1) . '…'
            : $text;
    }

    private function productCard(
        Product $product,
        array $reasons = [],
        array $notes = []
    ): array {
        return [
            'name' => $product->name,
            'brand' => $product->brand,
            'price' => $this->rupiah($product->price),
            'stock' => (int) $product->stock,
            'url' => $product->product_cat
                ? route('categories.show', $product->product_cat)
                : null,
            'reasons' => $reasons,
            'notes' => $notes,
        ];
    }

    private function productSuggestions(
        Collection $products,
        int $number
    ): array {
        return $products
            ->take($number)
            ->map(fn ($product) =>
                "Ceritakan tentang {$product->brand} {$product->name}"
            )
            ->values()
            ->all();
    }

    private function defaultSuggestions(): array
    {
        return [
            'Cari moisturizer untuk kulit berminyak di bawah Rp100.000',
            'Apa fungsi niacinamide?',
            'Bagaimana cara memesan produk?',
            'Kenapa aku tidak bisa checkout?',
        ];
    }

    private function greeting(?string $userName): array
    {
        $first = $userName
            ? ' ' . explode(' ', trim($userName))[0]
            : '';

        return $this->response(
            "Halo{$first}! Aku asisten ZRadiance. Aku bisa bantu soal produk dan skincare, pemesanan, sampai kendala seperti tidak bisa checkout, ongkir, atau pembayaran. Mau tanya apa?",
            ['suggestions' => $this->defaultSuggestions()]
        );
    }

    private function fallback(): array
    {
        return $this->response(
            'Maaf, aku belum punya informasi yang cukup untuk menjawab itu. Aku akan mencoba memahami pertanyaanmu, tetapi jawaban tentang produk harus tetap mengacu pada data ZRadiance.',
            [
                'suggestions' => $this->defaultSuggestions(),
                'links' => [
                    [
                        'label' => 'Buka Pusat Bantuan',
                        'url' => route('knowledge.index'),
                    ],
                ],
                '_use_ai' => true,
                '_ai_context' => [
                    'intent' => 'general_fallback',
                    'database_products' => [],
                ],
            ]
        );
    }

    private function response(string $reply, array $extra = []): array
    {
        return array_merge(
            [
                'reply' => $reply,
                'products' => [],
                'table' => null,
                'links' => [],
                'suggestions' => [],
            ],
            $extra
        );
    }
}
