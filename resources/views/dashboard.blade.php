@extends('layouts.user')

@section('title', 'Beranda Pelanggan - ZRadiance')

@push('styles')
<style>
    .home-wrapper {
        max-width: 1240px;
        margin: 0 auto;
        padding: 24px 24px 80px;
    }

    .dashboard-welcome {
        position: relative;
        isolation: isolate;
        display: flex;
        align-items: center;
        min-height: 270px;
        margin-bottom: 20px;
        padding: 38px;
        overflow: hidden;
        border-radius: var(--radius-lg);
        background: var(--c-ink);
        box-shadow: var(--shadow-md);
    }

    .dashboard-welcome-slides,
    .dashboard-welcome-slide {
        position: absolute;
        inset: 0;
    }

    .dashboard-welcome-slides {
        z-index: 0;
        background: var(--c-ink);
    }

    .dashboard-welcome-slide {
        overflow: hidden;
        opacity: 0;
        transform: scale(1.02);
        transition: opacity 650ms ease, transform 6s linear;
    }

    .dashboard-welcome-slide.is-active {
        opacity: 1;
        transform: scale(1);
    }

    .dashboard-welcome-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .dashboard-welcome::before {
        position: absolute;
        inset: 0;
        z-index: 1;
        background: linear-gradient(90deg, rgba(36, 20, 29, 0.9), rgba(36, 20, 29, 0.68) 58%, rgba(36, 20, 29, 0.22));
        content: '';
        pointer-events: none;
    }

    .dashboard-welcome-copy {
        position: relative;
        z-index: 2;
        max-width: 620px;
    }

    .dashboard-eyebrow {
        margin-bottom: 8px;
        color: #ffdbe6;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.3px;
        text-transform: uppercase;
    }

    .dashboard-welcome h1 {
        margin-bottom: 8px;
        color: #fff;
        font-size: clamp(26px, 3vw, 36px);
    }

    .dashboard-welcome p {
        max-width: 540px;
        margin: 0 0 20px;
        color: rgba(255, 255, 255, 0.9);
    }

    .dashboard-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .dashboard-stat {
        display: flex;
        align-items: center;
        gap: 14px;
        min-height: 86px;
        padding: 18px;
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        background: #fff;
        box-shadow: var(--shadow-xs);
    }

    .dashboard-stat strong {
        display: block;
        color: var(--c-ink-heading);
        font-size: 24px;
        line-height: 1.2;
    }

    .dashboard-stat span {
        color: var(--c-ink-soft);
        font-size: 12px;
        font-weight: 700;
    }

    .dashboard-stat .dashboard-stat-icon {
        display: grid;
        width: 42px;
        height: 42px;
        flex: none;
        place-items: center;
        border-radius: 50%;
        background: var(--c-pink-subtle);
        font-size: 19px;
    }

    .dashboard-shortcuts {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 32px;
    }

    .dashboard-shortcut {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 72px;
        padding: 14px 18px;
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        background: #fff;
        box-shadow: var(--shadow-xs);
        color: var(--c-ink-heading);
        font-weight: 800;
        text-decoration: none;
        transition: var(--transition);
    }

    .dashboard-shortcut:hover {
        transform: translateY(-2px);
        border-color: var(--c-blush);
        box-shadow: var(--shadow-sm);
    }

    .dashboard-shortcut span:first-child {
        display: grid;
        width: 38px;
        height: 38px;
        flex: none;
        place-items: center;
        border-radius: 50%;
        background: var(--c-pink-subtle);
        font-size: 18px;
    }

    .dashboard-section {
        margin-bottom: 38px;
    }

    .dashboard-section-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }

    .dashboard-section-heading h2 {
        margin-bottom: 4px;
        color: var(--c-ink-heading);
        font-size: 23px;
    }

    .dashboard-section-heading p {
        margin: 0;
        color: var(--c-ink-soft);
        font-size: 13.5px;
    }

    .dashboard-order-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .dashboard-order-card {
        padding: 18px;
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        background: #fff;
        box-shadow: var(--shadow-xs);
    }

    .dashboard-order-top,
    .dashboard-order-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .dashboard-order-top strong {
        color: var(--c-ink-heading);
    }

    .dashboard-order-date {
        margin: 12px 0;
        color: var(--c-ink-soft);
        font-size: 12.5px;
    }

    .dashboard-order-bottom {
        padding-top: 12px;
        border-top: 1px solid var(--c-border-subtle);
    }

    .dashboard-order-total {
        color: var(--c-pink-strong);
        font-weight: 800;
    }

    .dashboard-product-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
    }

    .dashboard-product-card {
        overflow: hidden;
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        background: #fff;
        box-shadow: var(--shadow-xs);
        transition: var(--transition);
    }

    .dashboard-product-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
    }

    .dashboard-product-image {
        display: grid;
        width: 100%;
        aspect-ratio: 1 / 1;
        place-items: center;
        object-fit: cover;
        background: linear-gradient(135deg, #FFF6EC, var(--c-cream));
        font-size: 36px;
    }

    .dashboard-product-info {
        padding: 15px;
    }

    .dashboard-product-info small {
        color: var(--c-ink-light);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .dashboard-product-info h3 {
        min-height: 40px;
        margin: 5px 0 8px;
        color: var(--c-ink-heading);
        font-size: 14px;
    }

    .dashboard-product-price {
        margin-bottom: 12px;
        color: var(--c-ink-heading);
        font-weight: 800;
    }

    .dashboard-product-rating {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 12px;
        color: var(--c-ink-soft);
        font-size: 12px;
        font-weight: 700;
    }

    .dashboard-product-rating .rating-score {
        color: var(--c-pink-strong);
    }

    .dashboard-empty-state {
        padding: 28px 20px;
        border: 1px dashed var(--c-border);
        border-radius: var(--radius-md);
        background: rgba(255, 255, 255, 0.72);
        color: var(--c-ink-soft);
        text-align: center;
    }

    .dashboard-empty-state strong {
        display: block;
        margin-bottom: 5px;
        color: var(--c-ink-heading);
    }

    .category-pills-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-top: 18px;
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
    .category-pill-title {
        font-weight: 800;
        font-size: 14.5px;
        color: var(--c-ink-heading);
    }

    @media (max-width: 768px) {
        .home-wrapper { padding: 20px 16px 60px; }
        .dashboard-welcome { min-height: 250px; padding: 26px 22px; }
        .dashboard-shortcuts { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .dashboard-order-grid { grid-template-columns: 1fr; }
        .dashboard-summary-grid { gap: 10px; }
        .dashboard-product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .dashboard-section-heading { align-items: flex-start; flex-direction: column; }
    }

    @media (max-width: 420px) {
        .dashboard-welcome { padding: 20px; }
        .dashboard-summary-grid { grid-template-columns: 1fr; }
        .dashboard-stat { min-height: 74px; padding: 14px; }
        .dashboard-shortcut { padding: 12px; font-size: 13px; }
        .dashboard-product-info { padding: 12px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .dashboard-welcome-slide {
            transition: none;
            transform: none;
        }
    }

</style>
@endpush

@section('content')
<div class="home-wrapper">

    <section class="dashboard-welcome" aria-labelledby="dashboard-welcome-title" data-dashboard-hero>
        <div class="dashboard-welcome-slides" aria-hidden="true">
            <div class="dashboard-welcome-slide is-active">
                <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1600&q=80" alt="" fetchpriority="high">
            </div>
            <div class="dashboard-welcome-slide">
                <img src="https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1600&q=80" alt="" loading="lazy">
            </div>
            <div class="dashboard-welcome-slide">
                <img src="https://images.unsplash.com/photo-1556229010-6c3f2c9ca5f8?auto=format&fit=crop&w=1600&q=80" alt="" loading="lazy">
            </div>
        </div>
        <div class="dashboard-welcome-copy">
            <div class="dashboard-eyebrow">Area Pelanggan</div>
            <h1 id="dashboard-welcome-title">Selamat datang, {{ auth()->user()->name }}</h1>
            <p>Temukan pilihan perawatan terbaru dan pantau aktivitas belanja Anda dari satu tempat.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary">Jelajahi katalog produk →</a>
        </div>
    </section>

    <section class="dashboard-summary-grid" aria-label="Ringkasan pesanan">
        <div class="dashboard-stat">
            <span class="dashboard-stat-icon" aria-hidden="true">📦</span>
            <div><strong>{{ $orderCount }}</strong><span>Total pesanan</span></div>
        </div>
        <div class="dashboard-stat">
            <span class="dashboard-stat-icon" aria-hidden="true">⏳</span>
            <div><strong>{{ $activeOrderCount }}</strong><span>Pesanan berjalan</span></div>
        </div>
    </section>
    <nav class="dashboard-shortcuts" aria-label="Akses cepat">
        <a href="{{ route('products.index') }}" class="dashboard-shortcut"><span>🛍️</span><span>Katalog produk</span></a>
        <a href="{{ route('cart.index') }}" class="dashboard-shortcut"><span>🛒</span><span>Keranjang</span></a>
        <a href="{{ route('orders.index') }}" class="dashboard-shortcut"><span>📦</span><span>Pesanan saya</span></a>
        <a href="{{ route('profile') }}" class="dashboard-shortcut"><span>👤</span><span>Profil</span></a>
    </nav>

    <section class="dashboard-section" aria-labelledby="dashboard-orders-title">
        <div class="dashboard-section-heading">
            <div>
                <h2 id="dashboard-orders-title">Pesanan Terbaru</h2>
                <p>Ringkasan pesanan dari akun Anda.</p>
            </div>
            <a href="{{ route('orders.index') }}" class="btn btn-outline btn-sm">Semua pesanan →</a>
        </div>

        @if ($orders->isNotEmpty())
            <div class="dashboard-order-grid">
                @foreach ($orders as $order)
                    <article class="dashboard-order-card">
                        <div class="dashboard-order-top">
                            <strong>Pesanan #{{ $order->id }}</strong>
                            @include('user.orders._status', ['status' => $order->status])
                        </div>
                        <div class="dashboard-order-date">
                            {{ $order->created_at->format('d M Y') }} · {{ $order->items_count }} item
                        </div>
                        <div class="dashboard-order-bottom">
                            <span class="dashboard-order-total">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline btn-sm">Detail</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="dashboard-empty-state">
                <strong>Belum ada pesanan</strong>
                Pesanan Anda akan tampil di sini setelah berbelanja.
            </div>
        @endif
    </section>

    {{-- KATEGORI PILIHAN --}}
    <section class="dashboard-section" aria-labelledby="dashboard-categories-title">
        <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:18px;">
            <div>
                <h2 id="dashboard-categories-title" style="font-size:24px;color:var(--c-ink-heading);margin-bottom:4px;">Pilihan Kategori Perawatan</h2>
                <p style="color:var(--c-ink-soft);font-size:14px;margin:0;">Jelajahi produk kecantikan sesuai dengan rutinitas harianmu.</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">Lihat Semua Produk →</a>
        </div>

        @if ($categories->isNotEmpty())
            <div class="category-pills-grid">
                @foreach ($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->cat_id]) }}" class="category-pill-card">
                        <x-category-icon :title="$category->cat_title" />
                        <div>
                            <div class="category-pill-title">{{ $category->cat_title }}</div>
                            <small style="color:var(--c-ink-soft);font-size:12px;">Lihat koleksi →</small>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="dashboard-empty-state">
                <strong>Kategori belum tersedia</strong>
                Kategori produk akan muncul di sini setelah tersedia.
            </div>
        @endif
    </section>

    <section class="dashboard-section" aria-labelledby="dashboard-products-title">
        <div class="dashboard-section-heading">
            <div>
                <h2 id="dashboard-products-title">Top Products</h2>
                <p>Produk berperingkat tertinggi berdasarkan ulasan pelanggan.</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">Jelajahi katalog →</a>
        </div>

        @if ($products->isNotEmpty())
            <div class="dashboard-product-grid">
                @foreach ($products as $product)
                    <article class="dashboard-product-card">
                        @if ($product->image)
                            <img class="dashboard-product-image" src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy">
                        @else
                            <div class="dashboard-product-image" role="img" aria-label="Gambar produk belum tersedia">✨</div>
                        @endif
                        <div class="dashboard-product-info">
                            <small>{{ $product->brand }}</small>
                            <h3>{{ $product->name }}</h3>
                            <div class="dashboard-product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                            @if ($product->reviews_count > 0)
                                <div class="dashboard-product-rating" aria-label="Rating {{ number_format($product->reviews_avg_rating, 1, ',', '.') }} dari 5 dari {{ $product->reviews_count }} ulasan">
                                    <span class="rating-score" aria-hidden="true">★ {{ number_format($product->reviews_avg_rating, 1, ',', '.') }}</span>
                                    <span>({{ $product->reviews_count }} ulasan)</span>
                                </div>
                            @else
                                <div class="dashboard-product-rating">Belum ada ulasan</div>
                            @endif
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary btn-sm">Lihat Produk</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="dashboard-empty-state">
                <strong>Belum ada produk dengan ulasan</strong>
                Produk yang sudah memiliki ulasan akan ditampilkan di sini.
            </div>
        @endif
    </section>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var hero = document.querySelector('[data-dashboard-hero]');
    if (!hero) return;

    var slides = Array.prototype.slice.call(hero.querySelectorAll('.dashboard-welcome-slide'));
    var current = 0;
    var timer = null;
    var motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    var reducedMotion = motionQuery.matches;

    function showNextSlide() {
        slides[current].classList.remove('is-active');
        current = (current + 1) % slides.length;
        slides[current].classList.add('is-active');
    }

    function stopSlideshow() {
        if (timer) {
            window.clearInterval(timer);
            timer = null;
        }
    }

    function startSlideshow() {
        stopSlideshow();
        if (!reducedMotion && !document.hidden && slides.length > 1) {
            timer = window.setInterval(showNextSlide, 5000);
        }
    }

    if (slides.length < 2) return;

    hero.addEventListener('focusin', stopSlideshow);
    hero.addEventListener('focusout', function (event) {
        if (!hero.contains(event.relatedTarget)) startSlideshow();
    });
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) stopSlideshow();
        else startSlideshow();
    });
    function onMotionPreferenceChange(event) {
        reducedMotion = event.matches;
        if (reducedMotion) stopSlideshow();
        else startSlideshow();
    }
    if (typeof motionQuery.addEventListener === 'function') {
        motionQuery.addEventListener('change', onMotionPreferenceChange);
    } else if (typeof motionQuery.addListener === 'function') {
        motionQuery.addListener(onMotionPreferenceChange);
    }

    startSlideshow();
});
</script>
@endpush
