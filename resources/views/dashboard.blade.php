@extends('layouts.user')

@section('title', 'Beranda Pelanggan - Beauty Store')

@push('styles')
<style>
    .home-wrapper {
        max-width: 1240px;
        margin: 0 auto;
        padding: 24px 24px 80px;
    }

    .dashboard-welcome {
        display: grid;
        grid-template-columns: minmax(0, 1.3fr) minmax(240px, 0.7fr);
        align-items: center;
        gap: 24px;
        margin-bottom: 22px;
        padding: 30px;
        border: 1px solid rgba(255, 255, 255, 0.72);
        border-radius: var(--radius-lg);
        background: linear-gradient(125deg, #FFF1F4, #FFF9EF 70%, #FBE8ED);
        box-shadow: var(--shadow-sm);
    }

    .dashboard-eyebrow {
        margin-bottom: 8px;
        color: var(--c-pink-strong);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.3px;
        text-transform: uppercase;
    }

    .dashboard-welcome h1 {
        margin-bottom: 8px;
        color: var(--c-ink-heading);
        font-size: clamp(26px, 3vw, 36px);
    }

    .dashboard-welcome p {
        max-width: 580px;
        margin: 0;
        color: var(--c-ink-soft);
    }

    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .dashboard-stat {
        padding: 16px;
        border: 1px solid rgba(243, 217, 214, 0.75);
        border-radius: var(--radius-md);
        background: rgba(255, 255, 255, 0.78);
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

    .hero-carousel-container {
        position: relative;
        border-radius: var(--radius-lg);
        overflow: hidden;
        margin-bottom: 44px;
        box-shadow: var(--shadow-md);
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
    }

    .hero-carousel-track {
        display: flex;
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        width: 100%;
    }

    .carousel-slide {
        min-width: 100%;
        position: relative;
        min-height: 440px;
        display: flex;
        align-items: center;
        padding: 60px 50px;
        background-size: cover;
        background-position: center;
        box-sizing: border-box;
    }

    .carousel-slide::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(36, 20, 29, 0.85) 0%, rgba(36, 20, 29, 0.45) 55%, rgba(36, 20, 29, 0.15) 100%);
    }

    .slide-content {
        position: relative;
        z-index: 2;
        max-width: 580px;
        color: #fff;
    }

    .slide-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: var(--radius-pill);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        margin-bottom: 16px;
        color: #fff;
    }

    .slide-content h2 {
        font-size: 40px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 14px;
        color: #fff;
    }

    .slide-content p {
        font-size: 15.5px;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 26px;
    }

    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.8);
        color: var(--c-ink);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 5;
        font-size: 20px;
        font-weight: bold;
        transition: var(--transition);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .carousel-btn:hover {
        background: #fff;
        transform: translateY(-50%) scale(1.08);
    }
    .carousel-prev { left: 16px; }
    .carousel-next { right: 16px; }

    .carousel-dots {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 8px;
        z-index: 5;
    }

    .dot {
        width: 10px;
        height: 10px;
        border-radius: var(--radius-pill);
        background: rgba(255, 255, 255, 0.45);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .dot.active {
        width: 28px;
        background: var(--c-pink);
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
        .dashboard-welcome { grid-template-columns: 1fr; padding: 24px; }
        .dashboard-shortcuts { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .dashboard-order-grid { grid-template-columns: 1fr; }
        .dashboard-product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .dashboard-section-heading { align-items: flex-start; flex-direction: column; }
        .carousel-slide { min-height: 380px; padding: 40px 24px; }
        .slide-content h2 { font-size: 28px; }
        .slide-content p { font-size: 14px; margin-bottom: 20px; }
        .carousel-btn { display: none; }
    }

    @media (max-width: 420px) {
        .dashboard-welcome { padding: 20px; }
        .dashboard-stats { gap: 8px; }
        .dashboard-stat { padding: 12px; }
        .dashboard-shortcut { padding: 12px; font-size: 13px; }
        .dashboard-product-info { padding: 12px; }
    }

    @media (prefers-reduced-motion: reduce) {
        .hero-carousel-track { transition: none !important; }
        .carousel-btn { transition: none !important; }
    }
</style>
@endpush

@section('content')
<div class="home-wrapper">

    <section class="dashboard-welcome" aria-labelledby="dashboard-welcome-title">
        <div>
            <div class="dashboard-eyebrow">Area Pelanggan</div>
            <h1 id="dashboard-welcome-title">Selamat datang, {{ auth()->user()->name }}</h1>
            <p>Temukan pilihan perawatan terbaru dan pantau aktivitas belanja Anda dari satu tempat.</p>
        </div>
        <div class="dashboard-stats" aria-label="Ringkasan pesanan">
            <div class="dashboard-stat">
                <strong>{{ $orderCount }}</strong>
                <span>Total pesanan</span>
            </div>
            <div class="dashboard-stat">
                <strong>{{ $activeOrderCount }}</strong>
                <span>Pesanan berjalan</span>
            </div>
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

    {{-- HERO CAROUSEL BANNER --}}
    <div class="hero-carousel-container" id="heroCarousel">
        <div class="hero-carousel-track" id="carouselTrack">

            <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1600&q=80');">
                <div class="slide-content">
                    <span class="slide-badge">✨ NEW COLLECTION 2026</span>
                    <h2>Glow Beyond Expectations</h2>
                    <p>
                        Perawatan intensif dengan formula aktif alami untuk mengembalikan rona cerah, kekenyalan, dan kelembapan optimal kulitmu setiap hari.
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        Jelajahi Produk Pilihan 🛍️
                    </a>
                </div>
            </div>

            <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=1600&q=80');">
                <div class="slide-content">
                    <span class="slide-badge">🌿 100% BPOM & CLINICALLY TESTED</span>
                    <h2>Kelembutan Murni untuk Semua Jenis Kulit</h2>
                    <p>
                        Diformulasikan secara lembut tanpa alkohol keras & paraben. Melindungi skin barrier tetap sehat dan ternutrisi menyeluruh.
                    </p>
                    <button type="button" class="btn btn-ghost" onclick="document.getElementById('bsChat').querySelector('.bs-fab').click()">
                        💬 Konsultasi Bersama AI Chatbot
                    </button>
                </div>
            </div>

            <div class="carousel-slide" style="background-image: url('https://images.unsplash.com/photo-1608248597359-00e9095f9c54?auto=format&fit=crop&w=1600&q=80');">
                <div class="slide-content">
                    <span class="slide-badge">🚚 EKSPEDISI RAJAONGKIR</span>
                    <h2>Katalog Lengkap & Kirim Cepat se-Indonesia</h2>
                    <p>
                        Pengecekan tarif ongkos kirim real-time, pilihan kurir fleksibel, dan pembayaran digital otomatis tanpa repot konfirmasi manual.
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        Beli Sekarang ⚡
                    </a>
                </div>
            </div>

        </div>

        <button type="button" class="carousel-btn carousel-prev" id="carouselPrev" aria-label="Slide sebelumnya">‹</button>
        <button type="button" class="carousel-btn carousel-next" id="carouselNext" aria-label="Slide selanjutnya">›</button>

        <div class="carousel-dots" id="carouselDots">
            <span class="dot active" data-index="0"></span>
            <span class="dot" data-index="1"></span>
            <span class="dot" data-index="2"></span>
        </div>
    </div>

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
                <h2 id="dashboard-products-title">Produk Pilihan Terbaru</h2>
                <p>Produk tersedia yang baru ditambahkan ke katalog.</p>
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
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary btn-sm">Lihat Produk</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="dashboard-empty-state">
                <strong>Belum ada produk tersedia</strong>
                Produk terbaru akan muncul di sini setelah tersedia.
            </div>
        @endif
    </section>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var track = document.getElementById('carouselTrack');
    var slides = track ? track.querySelectorAll('.carousel-slide') : [];
    var prevBtn = document.getElementById('carouselPrev');
    var nextBtn = document.getElementById('carouselNext');
    var dots = document.querySelectorAll('#carouselDots .dot');
    var currentIndex = 0;
    var autoPlayTimer = null;
    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function goToSlide(index) {
        if (!track || slides.length === 0) return;
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;

        currentIndex = index;
        track.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';

        dots.forEach(function (d, i) {
            d.classList.toggle('active', i === currentIndex);
        });
    }

    function startAutoPlay() {
        if (prefersReducedMotion || slides.length <= 1) return;
        stopAutoPlay();
        autoPlayTimer = setInterval(function () {
            goToSlide(currentIndex + 1);
        }, 5000);
    }

    function stopAutoPlay() {
        if (autoPlayTimer) {
            clearInterval(autoPlayTimer);
            autoPlayTimer = null;
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            goToSlide(currentIndex - 1);
            startAutoPlay();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            goToSlide(currentIndex + 1);
            startAutoPlay();
        });
    }

    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            var idx = parseInt(dot.getAttribute('data-index'));
            goToSlide(idx);
            startAutoPlay();
        });
    });

    var container = document.getElementById('heroCarousel');
    if (container) {
        container.addEventListener('mouseenter', stopAutoPlay);
        container.addEventListener('mouseleave', startAutoPlay);
    }

    startAutoPlay();
});
</script>
@endpush
