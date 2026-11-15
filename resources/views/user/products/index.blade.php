@extends('layouts.user')

@section('title', 'Katalog & Rekomendasi Kecantikan - Beauty Store')

@push('styles')
<style>
    .home-wrapper {
        max-width: 1240px;
        margin: 0 auto;
        padding: 0 24px 80px;
    }

    .catalog-intro {
        margin: 20px 0 30px;
    }

    .catalog-intro h1 {
        margin-bottom: 6px;
        color: var(--c-ink-heading);
        font-size: clamp(28px, 4vw, 38px);
    }

    .catalog-intro p {
        max-width: 620px;
        margin: 0;
        color: var(--c-ink-soft);
    }

    /* ===================================================
       2. KATEGORI PILIHAN (CATEGORY CARDS)
    =================================================== */
    .section-block {
        margin-bottom: 48px;
    }

    .section-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 22px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .section-header-row h2 {
        font-size: 24px;
        color: var(--c-ink-heading);
        margin-bottom: 4px;
    }
    .section-header-row p {
        color: var(--c-ink-soft);
        font-size: 14px;
        margin: 0;
    }

    .category-pills-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
    }

    .category-pill-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        text-decoration: none;
        color: var(--c-ink);
        transition: var(--transition);
        box-shadow: var(--shadow-xs);
    }
    .category-pill-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-sm);
        border-color: var(--c-blush);
        background: #FFFDF9;
    }
    .category-pill-card.active {
        border-color: var(--c-pink-strong);
        background: var(--c-pink-subtle);
    }
    .category-pill-title {
        font-weight: 800;
        font-size: 14.5px;
        color: var(--c-ink-heading);
    }

    /* ===================================================
       3. PRODUK UNGGULAN (FEATURED PRODUCTS)
    =================================================== */
    .featured-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 22px;
    }

    /* ===================================================
       4. FILTER PANEL & SEMUA PRODUK KATALOG
    =================================================== */
    .filter-panel {
        background: var(--glass-bg-elevated);
        backdrop-filter: blur(var(--glass-blur));
        -webkit-backdrop-filter: blur(var(--glass-blur));
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        margin-bottom: 30px;
        box-shadow: var(--shadow-sm);
    }

    .filter-form {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1fr 1fr 1fr auto;
        gap: 12px;
        align-items: center;
    }

    .filter-form input,
    .filter-form select {
        width: 100%;
        min-width: 0;
        padding: 12px 14px;
        border: 1.5px solid var(--c-border);
        border-radius: var(--radius-pill);
        background: #fff;
        color: var(--c-ink);
        font-size: 13.5px;
        transition: var(--transition);
    }

    .filter-form input:focus,
    .filter-form select:focus {
        outline: none;
        border-color: var(--c-pink-strong);
        box-shadow: 0 0 0 3px rgba(233, 129, 158, 0.15);
    }

    .filter-button {
        border: 0;
        border-radius: var(--radius-pill);
        padding: 12px 24px;
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        color: #fff;
        font: 800 13.5px var(--font);
        cursor: pointer;
        transition: var(--transition);
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(233, 129, 158, 0.35);
    }
    .filter-button:hover {
        background: linear-gradient(135deg, var(--c-pink-strong), #D46684);
        transform: translateY(-1px);
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 24px;
    }

    /* Product Card Styling */
    .product-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-xs);
        position: relative;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
        border-color: var(--c-blush);
    }

    .product-image-link {
        display: block;
        background: linear-gradient(135deg, #FFF6EC, var(--c-cream));
        position: relative;
        overflow: hidden;
        padding-top: 90%;
    }

    .product-image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .product-card:hover .product-image { transform: scale(1.05); }

    .image-placeholder {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        background: linear-gradient(135deg, #FFF9F2, #FFEABB);
    }

    .product-info {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .product-brand {
        color: var(--c-ink-light);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 4px;
    }

    .product-name {
        min-height: 44px;
        font-size: 15.5px;
        font-weight: 700;
        line-height: 1.4;
        margin-bottom: 8px;
    }
    .product-name a { color: var(--c-ink-heading); text-decoration: none; }
    .product-name a:hover { color: var(--c-pink-strong); }

    .product-price {
        font-size: 17.5px;
        font-weight: 800;
        color: var(--c-ink-heading);
        margin-bottom: 6px;
    }

    .product-rating {
        font-size: 13px;
        color: #E6A213;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .product-rating span { color: var(--c-ink-soft); font-size: 12px; }

    .product-meta {
        font-size: 12px;
        color: var(--c-ink-soft);
        margin-bottom: 14px;
    }

    .product-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
        margin-top: auto;
    }
    .product-actions button,
    .product-actions a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        text-align: center;
        padding: 9px 12px;
        border: 0;
        border-radius: var(--radius-pill);
        text-decoration: none;
        cursor: pointer;
        font: 700 12.5px var(--font);
        transition: var(--transition);
    }

    .detail-button {
        background: #FAF2E8;
        color: var(--c-ink);
        border: 1px solid rgba(243, 217, 214, 0.6) !important;
    }
    .detail-button:hover {
        background: var(--c-cream);
        color: var(--c-ink-heading);
    }

    .cart-button {
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        color: #fff;
        box-shadow: 0 2px 8px rgba(233, 129, 158, 0.3);
    }
    .cart-button:hover {
        background: linear-gradient(135deg, var(--c-pink-strong), #D46684);
        transform: translateY(-1px);
    }

    .btn-ask-bot {
        grid-column: span 2;
        background: rgba(250, 172, 191, 0.15);
        color: var(--c-pink-strong);
        border: 1px dashed var(--c-pink-strong) !important;
        margin-top: 4px;
        padding: 6px 10px !important;
        font-size: 11.5px !important;
    }
    .btn-ask-bot:hover { background: var(--c-pink); color: var(--c-ink); }

    .empty-products {
        padding: 60px 20px;
        text-align: center;
        background: var(--c-surface);
        border-radius: var(--radius-lg);
        color: var(--c-ink-soft);
        border: 1px dashed var(--c-border);
    }

    .pagination {
        margin-top: 36px;
        display: flex;
        justify-content: center;
    }

    /* ===================================================
       RESPONSIVE & PREFERS-REDUCED-MOTION
    =================================================== */
    @media (max-width: 1040px) {
        .filter-form { grid-template-columns: repeat(3, 1fr); }
        .product-grid { grid-template-columns: repeat(3, 1fr); }
        .featured-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 768px) {
        .home-wrapper { padding: 0 16px 60px; }
        .filter-form { grid-template-columns: 1fr 1fr; }
        .product-grid, .featured-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .product-info { padding: 14px; }
        .product-name { font-size: 14px; min-height: 40px; }
        .product-price { font-size: 15.5px; }
        .product-actions { grid-template-columns: 1fr; }
        .btn-ask-bot { grid-column: span 1; }
    }

    @media (max-width: 480px) {
        .filter-form { grid-template-columns: 1fr; }
        .product-grid, .featured-grid { grid-template-columns: 1fr; }
    }

    @media (prefers-reduced-motion: reduce) {
        .product-image { transition: none !important; }
    }
</style>
@endpush

@section('content')
<div class="home-wrapper">

    <header class="catalog-intro">
        <h1>Koleksi Produk</h1>
        <p>Temukan produk kecantikan sesuai kebutuhan, lalu gunakan pencarian dan filter untuk mempersempit pilihan.</p>
    </header>

    {{-- =====================================================
         2. KATEGORI PILIHAN (CATEGORY CARDS)
    ====================================================== --}}
    <div class="section-block">
        <div class="section-header-row">
            <div>
                <h2>Pilihan Kategori Perawatan</h2>
                <p>Temukan solusi yang pas sesuai kebutuhan dan rutinitas harianmu.</p>
            </div>
        </div>

        <div class="category-pills-grid">
            @foreach($categories as $cat)
                <a
                    href="{{ route('products.index', array_merge(request()->query(), ['category' => $cat->cat_id])) }}"
                    class="category-pill-card {{ (string) request('category') === (string) $cat->cat_id ? 'active' : '' }}"
                >
                    <x-category-icon :title="$cat->cat_title" />
                    <div>
                        <div class="category-pill-title">{{ $cat->cat_title }}</div>
                        <small style="color:var(--c-ink-soft);font-size:12px;">Lihat koleksi →</small>
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    {{-- =====================================================
         4. KATALOG SEMUA PRODUK & FILTER
    ====================================================== --}}
    <div class="section-block" id="catalog-section">
        <div class="section-header-row">
            <div>
                <h2>Semua Koleksi Produk Kecantikan</h2>
                <p>Filter berdasarkan kategori, rentang harga, atau urutkan sesuai kebutuhanmu.</p>
            </div>
            <span style="color:var(--c-ink-soft);font-size:13.5px;font-weight:700;">
                {{ $products->total() }} produk ditemukan
            </span>
        </div>

        {{-- FILTER BAR --}}
        <div class="filter-panel">
            <form action="{{ route('products.index') }}#catalog-section" method="GET" class="filter-form">
                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari nama produk / brand..."
                >

                <select name="category">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option
                            value="{{ $category->cat_id }}"
                            @selected((string) request('category') === (string) $category->cat_id)
                        >
                            {{ $category->cat_title }}
                        </option>
                    @endforeach
                </select>

                <input
                    type="number"
                    name="min_price"
                    min="0"
                    value="{{ request('min_price') }}"
                    placeholder="Harga min (Rp)"
                >

                <input
                    type="number"
                    name="max_price"
                    min="0"
                    value="{{ request('max_price') }}"
                    placeholder="Harga max (Rp)"
                >

                <select name="sort">
                    <option value="latest" @selected(request('sort', 'latest') === 'latest')>Terbaru</option>
                    <option value="price_low" @selected(request('sort') === 'price_low')>Harga terendah</option>
                    <option value="price_high" @selected(request('sort') === 'price_high')>Harga tertinggi</option>
                </select>

                <button type="submit" class="filter-button">Terapkan</button>
            </form>

            @if(request()->hasAny(['q', 'category', 'min_price', 'max_price', 'sort']))
                <a href="{{ route('products.index') }}#catalog-section" style="display:inline-block;margin-top:12px;color:var(--c-pink-strong);font-size:13px;font-weight:700;text-decoration:none;">
                    ✕ Reset semua filter pencarian
                </a>
            @endif
        </div>

        {{-- GRID PRODUK --}}
        @if($products->count())
            <div class="product-grid">
                @foreach($products as $product)
                    <article class="product-card">
                        <a href="{{ route('products.show', $product->id) }}" class="product-image-link">
                            @if($product->image)
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
                            @else
                                <div class="image-placeholder">✨</div>
                            @endif
                        </a>

                        <div class="product-info">
                            <div class="product-brand">{{ $product->brand }}</div>

                            <h3 class="product-name">
                                <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                            </h3>

                            <div class="product-price">
                                Rp{{ number_format($product->price, 0, ',', '.') }}
                            </div>

                            <div class="product-rating">
                                @if($product->reviews_count > 0)
                                    ★ {{ number_format($product->reviews_avg_rating, 1) }}
                                    <span>({{ $product->reviews_count }} ulasan)</span>
                                @else
                                    <span>Belum ada ulasan</span>
                                @endif
                            </div>

                            <div class="product-meta">
                                {{ $product->category->cat_title ?? 'Kategori Umum' }} · Stok: {{ $product->stock }}
                            </div>

                            <div class="product-actions">
                                <a href="{{ route('products.show', $product->id) }}" class="detail-button">Detail</a>

                                @if($product->stock > 0)
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form">
                                        @csrf
                                        <button type="submit" class="cart-button">+ Keranjang</button>
                                    </form>
                                @else
                                    <button type="button" disabled style="background:#ddd;color:#888;">Habis</button>
                                @endif

                                <button
                                    type="button"
                                    class="btn-ask-bot"
                                    data-chat-ask="{{ $product->brand }} {{ $product->name }}"
                                    data-chat-product="{{ $product->id }}"
                                >
                                    💬 Tanya Chatbot
                                </button>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="pagination">
                {{ $products->links() }}
            </div>
        @else
            <div class="empty-products">
                <h3 style="font-size:20px;color:var(--c-ink-heading);margin-bottom:8px;">Produk Tidak Ditemukan</h3>
                <p>Coba gunakan kata kunci pencarian yang lebih umum atau atur ulang filter Anda.</p>
            </div>
        @endif
    </div>

    {{-- =====================================================
         3. PRODUK UNGGULAN (FEATURED PRODUCTS)
    ====================================================== --}}
    @php
        $featuredProducts = $products->filter(fn($p) => $p->stock > 0)->sortByDesc('reviews_avg_rating')->take(4);
        if ($featuredProducts->isEmpty()) {
            $featuredProducts = $products->take(4);
        }
    @endphp

    @if($featuredProducts->isNotEmpty() && !request()->has('q'))
    <div class="section-block">
        <div class="section-header-row">
            <div>
                <h2>🌟 Produk Unggulan & Terpopuler</h2>
                <p>Pilihan terbaik dengan ulasan tertinggi dari para pelanggan setia.</p>
            </div>
            <a href="#catalog-section" class="btn btn-outline btn-sm">Buka Seluruh Katalog →</a>
        </div>

        <div class="featured-grid">
            @foreach($featuredProducts as $product)
                <article class="product-card">
                    <a href="{{ route('products.show', $product->id) }}" class="product-image-link">
                        @if($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
                        @else
                            <div class="image-placeholder">✨</div>
                        @endif
                    </a>

                    <div class="product-info">
                        <div class="product-brand">{{ $product->brand }}</div>
                        <h3 class="product-name">
                            <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                        </h3>

                        <div class="product-price">
                            Rp{{ number_format($product->price, 0, ',', '.') }}
                        </div>

                        <div class="product-rating">
                            @if($product->reviews_count > 0)
                                ★ {{ number_format($product->reviews_avg_rating, 1) }}
                                <span>({{ $product->reviews_count }})</span>
                            @else
                                <span>Belum ada ulasan</span>
                            @endif
                        </div>

                        <div class="product-actions">
                            <a href="{{ route('products.show', $product->id) }}" class="detail-button">Detail</a>
                            @if($product->stock > 0)
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    <button type="submit" class="cart-button">+ Keranjang</button>
                                </form>
                            @else
                                <button type="button" disabled style="background:#ddd;color:#888;">Habis</button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===================================================
    // FLYING PRODUCT ANIMATION CONTROLLER
    // ===================================================
    function flyProductToCart(sourceImg, onComplete) {
        var cartTarget = document.getElementById('navCartButton') || document.querySelector('.nav-cart');
        if (!cartTarget) {
            if (onComplete) onComplete();
            return;
        }

        var startRect = sourceImg ? sourceImg.getBoundingClientRect() : { left: window.innerWidth / 2, top: window.innerHeight / 2, width: 60, height: 60 };
        var endRect = cartTarget.getBoundingClientRect();

        var ghost = document.createElement('img');
        ghost.src = (sourceImg && sourceImg.src) ? sourceImg.src : 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="60" height="60"><circle cx="30" cy="30" r="30" fill="%23FAACBF"/></svg>';
        ghost.className = 'flying-product-ghost';
        ghost.style.left = startRect.left + 'px';
        ghost.style.top = startRect.top + 'px';
        ghost.style.width = Math.min(startRect.width, 90) + 'px';
        ghost.style.height = Math.min(startRect.height, 90) + 'px';
        document.body.appendChild(ghost);

        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                var targetX = endRect.left + (endRect.width / 2) - 15;
                var targetY = endRect.top + (endRect.height / 2) - 15;
                ghost.style.transform = 'translate(' + (targetX - startRect.left) + 'px, ' + (targetY - startRect.top) + 'px) scale(0.2)';
                ghost.style.opacity = '0.2';
            });
        });

        setTimeout(function () {
            ghost.remove();
            var badge = document.getElementById('navCartBadge') || document.querySelector('.cart-badge');
            if (badge) {
                badge.classList.remove('bounce');
                void badge.offsetWidth;
                badge.classList.add('bounce');
                var count = parseInt(badge.textContent.trim()) || 0;
                badge.textContent = (count + 1) > 99 ? '99+' : (count + 1);
            } else {
                var newBadge = document.createElement('span');
                newBadge.className = 'cart-badge bounce';
                newBadge.id = 'navCartBadge';
                newBadge.textContent = '1';
                cartTarget.appendChild(newBadge);
            }
            if (onComplete) onComplete();
        }, 750);
    }

    document.querySelectorAll('.add-to-cart-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            var card = form.closest('.product-card');
            var img = card ? card.querySelector('.product-image') : null;

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                }
            })
            .then(function (res) {
                if (res.ok || res.status === 302 || res.type === 'opaqueredirect') {
                    flyProductToCart(img, function () {
                        if (btn) btn.disabled = false;
                    });
                } else {
                    throw new Error('Gagal');
                }
            })
            .catch(function () {
                if (btn) btn.disabled = false;
                form.submit();
            });
        });
    });
});
</script>
@endpush
