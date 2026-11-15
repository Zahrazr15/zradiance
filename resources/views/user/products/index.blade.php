@extends('layouts.user')

@section('title', 'Katalog Produk Kecantikan - Beauty Store')

@push('styles')
<style>
    .catalog {
        max-width: 1280px;
        margin: 36px auto;
        padding: 0 32px 80px;
    }

    .catalog-header {
        margin-bottom: 28px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 20px;
        flex-wrap: wrap;
    }

    .catalog-header h1 {
        font-size: 32px;
        margin-bottom: 6px;
        color: var(--c-ink-heading);
    }

    .catalog-header p {
        color: var(--c-ink-soft);
        font-size: 15px;
    }

    /* Filter Glass Panel */
    .filter-panel {
        background: var(--glass-bg-elevated);
        backdrop-filter: blur(var(--glass-blur));
        -webkit-backdrop-filter: blur(var(--glass-blur));
        border: 1px solid var(--glass-border);
        border-radius: var(--radius-lg);
        padding: 22px 24px;
        margin-bottom: 32px;
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
        box-shadow: 0 4px 14px rgba(233, 129, 158, 0.45);
    }

    .reset-filter-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 14px;
        color: var(--c-pink-strong);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: var(--transition);
    }
    .reset-filter-link:hover { text-decoration: underline; }

    .catalog-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--c-border-subtle);
    }

    .catalog-toolbar strong {
        font-size: 18px;
        color: var(--c-ink-heading);
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 24px;
    }

    /* Product Card Luxury */
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

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

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
        font-size: 11.5px;
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

    .product-name a {
        color: var(--c-ink-heading);
        text-decoration: none;
        transition: color 0.2s;
    }

    .product-name a:hover {
        color: var(--c-pink-strong);
    }

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

    .product-rating span {
        color: var(--c-ink-soft);
        font-size: 12px;
    }

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
        box-shadow: 0 4px 12px rgba(233, 129, 158, 0.45);
    }

    /* Ask Bot Quick Pill */
    .btn-ask-bot {
        grid-column: span 2;
        background: rgba(250, 172, 191, 0.15);
        color: var(--c-pink-strong);
        border: 1px dashed var(--c-pink-strong) !important;
        margin-top: 4px;
        padding: 6px 10px !important;
        font-size: 11.5px !important;
    }
    .btn-ask-bot:hover {
        background: var(--c-pink);
        color: var(--c-ink);
    }

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

    @media(max-width: 1040px) {
        .filter-form { grid-template-columns: repeat(3, 1fr); }
        .product-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media(max-width: 768px) {
        .catalog { padding: 0 18px 60px; margin-top: 24px; }
        .catalog-header h1 { font-size: 26px; }
        .filter-form { grid-template-columns: 1fr 1fr; }
        .product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .product-info { padding: 14px; }
        .product-name { font-size: 14px; min-height: 40px; }
        .product-price { font-size: 15.5px; }
        .product-actions { grid-template-columns: 1fr; }
        .btn-ask-bot { grid-column: span 1; }
    }

    @media(max-width: 420px) {
        .filter-form { grid-template-columns: 1fr; }
        .product-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="catalog">

    <div class="catalog-header">
        <div>
            <h1>Katalog Produk Kecantikan</h1>
            <p>Jelajahi berbagai pilihan kosmetik & perawatan kulit terkurasi.</p>
        </div>
    </div>

    {{-- FILTER PANEL --}}
    <div class="filter-panel">
        <form action="{{ route('products.index') }}" method="GET" class="filter-form">

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
                <option value="latest" @selected(request('sort', 'latest') === 'latest')>
                    Terbaru
                </option>
                <option value="price_low" @selected(request('sort') === 'price_low')>
                    Harga terendah
                </option>
                <option value="price_high" @selected(request('sort') === 'price_high')>
                    Harga tertinggi
                </option>
            </select>

            <button type="submit" class="filter-button">
                Terapkan
            </button>
        </form>

        @if(request()->hasAny(['q', 'category', 'min_price', 'max_price', 'sort']))
            <a href="{{ route('products.index') }}" class="reset-filter-link">
                ✕ Reset semua filter
            </a>
        @endif
    </div>

    <div class="catalog-toolbar">
        <strong>Semua Produk</strong>
        <span class="product-meta">{{ $products->total() }} produk ditemukan</span>
    </div>

    @if($products->count())
        <div class="product-grid">
            @foreach($products as $product)
                <article class="product-card">

                    <a href="{{ route('products.show', $product->id) }}" class="product-image-link">
                        @if($product->image)
                            <img
                                src="{{ $product->image }}"
                                alt="{{ $product->name }}"
                                class="product-image"
                                loading="lazy"
                            >
                        @else
                            <div class="image-placeholder">✨</div>
                        @endif
                    </a>

                    <div class="product-info">
                        <div class="product-brand">{{ $product->brand }}</div>

                        <h2 class="product-name">
                            <a href="{{ route('products.show', $product->id) }}">
                                {{ $product->name }}
                            </a>
                        </h2>

                        <div class="product-price">
                            Rp{{ number_format($product->price, 0, ',', '.') }}
                        </div>

                        {{-- Rating dan ulasan --}}
                        <div class="product-rating">
                            @if($product->reviews_count > 0)
                                ★ {{ number_format($product->reviews_avg_rating, 1) }}
                                <span>({{ $product->reviews_count }} ulasan)</span>
                            @else
                                <span>Belum ada ulasan</span>
                            @endif
                        </div>

                        <div class="product-meta">
                            {{ $product->category->cat_title ?? 'Tanpa kategori' }} · Stok: {{ $product->stock }}
                        </div>

                        <div class="product-actions">
                            <a href="{{ route('products.show', $product->id) }}" class="detail-button">
                                Detail
                            </a>

                            @if($product->stock > 0)
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="add-to-cart-form">
                                    @csrf
                                    <button type="submit" class="cart-button">
                                        + Keranjang
                                    </button>
                                </form>
                            @else
                                <button type="button" disabled style="background:#ddd;color:#888;cursor:not-allowed;">
                                    Habis
                                </button>
                            @endif

                            {{-- Tombol interaktif tanya chatbot --}}
                            <button
                                type="button"
                                class="btn-ask-bot"
                                data-chat-ask="{{ $product->brand }} {{ $product->name }}"
                                data-chat-product="{{ $product->id }}"
                                title="Konsultasi produk ini dengan chatbot"
                            >
                                💬 Tanya Asisten AI
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
            <p>Cobalah mengganti kata kunci atau menghapus beberapa filter pencarian.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // FLYING PRODUCT ANIMATION FUNCTION
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

    // Intercept form add-to-cart submissions
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
                // Berhasil: status 200 atau redirect
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
                form.submit(); // fallback to standard submit if AJAX fails
            });
        });
    });
});
</script>
@endpush
