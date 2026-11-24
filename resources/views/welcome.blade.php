
@extends('layouts.landing')

@section('title', 'ZRADIANCE | Beauty & Skincare')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/homepage-refresh.css') }}">
@endpush

@section('content')

{{-- HERO --}}
<section class="home-hero" id="home">
    <div class="home-hero-bg"></div>

    <div class="home-hero-content">
        <span class="home-eyebrow">YOUR BEAUTY DESTINATION</span>

        <h1>
            Temukan Kecantikanmu,
            <span>Rawat dengan Pilihan Terbaik.</span>
        </h1>

        <p>
            Jelajahi koleksi skincare dan makeup pilihan
            untuk menemani rutinitas kecantikanmu.
        </p>

        <a href="{{ route('login') }}" class="home-primary-btn">
            Jelajahi Produk
            <span>→</span>
        </a>
    </div>
</section>



{{-- BRAND MARQUEE --}}
<section class="home-brand-section">
    <div class="home-section-container">

        <div class="home-section-heading home-heading-row">
            <div>
                <span class="home-eyebrow home-eyebrow-dark">OUR COLLECTION</span>
                <h2>Brand Pilihan</h2>
                <p>Temukan produk dari berbagai brand kecantikan favorit.</p>
            </div>
        </div>

        @if(isset($brands) && $brands->isNotEmpty())
            <div class="home-brand-marquee">
                <div class="home-brand-track">

                    {{-- Brand pertama --}}
                    <div class="home-brand-group">
                        @foreach($brands as $brand)
                            <a href="{{ route('login') }}"
                               class="home-brand-item"
                               title="{{ $brand->brand_name }}">

                                @if($brand->brand_logo)
                                    <img
                                        src="{{ asset('storage/' . $brand->brand_logo) }}"
                                        alt="{{ $brand->brand_name }}"
                                        loading="lazy">
                                @else
                                    <span class="home-brand-name">
                                        {{ $brand->brand_name }}
                                    </span>
                                @endif

                            </a>
                        @endforeach
                    </div>

                    {{-- Duplikat untuk animasi tanpa jeda --}}
                    <div class="home-brand-group" aria-hidden="true">
                        @foreach($brands as $brand)
                            <div class="home-brand-item">

                                @if($brand->brand_logo)
                                    <img
                                        src="{{ asset('storage/' . $brand->brand_logo) }}"
                                        alt=""
                                        loading="lazy">
                                @else
                                    <span class="home-brand-name">
                                        {{ $brand->brand_name }}
                                    </span>
                                @endif

                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        @else
            <div class="home-empty">
                Brand belum tersedia.
            </div>
        @endif

    </div>
</section>


{{-- PRODUCT --}}
<section class="home-product-section" id="products">
    <div class="home-section-container">

        <div class="home-section-heading home-heading-row">
            <div>
                <span class="home-eyebrow home-eyebrow-dark">CURATED FOR YOU</span>
                <h2>Produk Pilihan</h2>
                <p>Produk kecantikan pilihan untuk kebutuhanmu.</p>
            </div>

            <a href="{{ route('login') }}" class="home-text-link">
                Lihat semua produk <span>→</span>
            </a>
        </div>

        @if(isset($products) && $products->isNotEmpty())
            <div class="home-product-grid">

                @foreach($products->take(4) as $product)
                    <article class="home-product-card">

                        <a href="{{ auth()->check() ? route('products.show', $product->id) : route('login') }}"
                           class="home-product-image-wrap">

                            @if($product->image)
                                <img
                                    src="{{ $product->image }}"
                                    alt="{{ $product->name }}"
                                    class="home-product-image"
                                    loading="lazy"
                                >
                            @else
                                <div class="home-product-no-image">
                                    Gambar tidak tersedia
                                </div>
                            @endif

                            <span class="home-product-view">
                                Lihat Produk
                            </span>
                        </a>

                        <div class="home-product-details">

                            <span class="home-product-brand">
                                {{ $product->brandRelation->brand_name ?? $product->brand }}
                            </span>

                            <h3>
                                <a href="{{ auth()->check() ? route('products.show', $product->id) : route('login') }}">
                                    {{ $product->name }}
                                </a>
                            </h3>

                            <p class="home-product-price">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>

                        </div>
                    </article>
                @endforeach

            </div>
        @else
            <div class="home-empty">
                Produk belum tersedia.
            </div>
        @endif

    </div>
</section>


{{-- FEATURES --}}
<section class="home-feature-section" id="info">
    <div class="home-section-container">

        <div class="home-section-heading home-heading-centered">
            <span class="home-eyebrow home-eyebrow-dark">SHOP WITH CONFIDENCE</span>
            <h2>Belanja Lebih Mudah</h2>
            <p>Pengalaman berbelanja kecantikan yang praktis dalam satu platform.</p>
        </div>

        <div class="home-feature-grid">

            <article class="home-feature-card">
                <div class="home-feature-icon">
                    <i class="fas fa-spa"></i>
                </div>
                <h3>Produk Beragam</h3>
                <p>
                    Jelajahi berbagai pilihan skincare, makeup,
                    dan produk perawatan dari brand favorit.
                </p>
            </article>

            <article class="home-feature-card">
                <div class="home-feature-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Asisten Belanja</h3>
                <p>
                    Dapatkan bantuan melalui chatbot untuk
                    menemukan produk dan informasi yang kamu butuhkan.
                </p>
            </article>

            <article class="home-feature-card">
                <div class="home-feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Transaksi Praktis</h3>
                <p>
                    Nikmati proses checkout, pilihan pengiriman,
                    dan pembayaran yang terintegrasi.
                </p>
            </article>

        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    // 1. DYNAMIC HERO SIZING (MEASURE ACTUAL NAVBAR POSITION)
    function fitHeroToScreen() {
        if (window.innerWidth <= 600) return;

        const navWrapper = document.querySelector('.navbar-floating-wrapper');
        const navbar = navWrapper ? navWrapper.querySelector('.navbar') : document.querySelector('.navbar');
        const hero = document.querySelector('.home-hero');
        if (!navbar || !hero || !navWrapper) return;

        // Visual top offset of the sticky/floating navbar
        const navComputed = window.getComputedStyle(navWrapper);
        const navTop = parseFloat(navComputed.top) || 18;
        const navHeight = navbar.offsetHeight || 72;
        const navBottom = navTop + navHeight;

        // Proportional gap below the floating navbar (balanced with top gap)
        const gap = 16;
        const targetHeroTop = navBottom + gap;

        // Flow bottom of navbar-floating-wrapper
        const flowBottom = navWrapper.offsetTop + navWrapper.offsetHeight;
        const neededMarginTop = Math.max(0, targetHeroTop - flowBottom);

        // Height to fit screen down to bottom edge
        const availableHeight = Math.max(380, Math.round(window.innerHeight - targetHeroTop));

        hero.style.setProperty('--hero-margin-top', neededMarginTop + 'px');
        hero.style.setProperty('--hero-height', availableHeight + 'px');
        hero.style.marginTop = neededMarginTop + 'px';
        hero.style.height = availableHeight + 'px';
    }

    window.addEventListener('resize', fitHeroToScreen);
    window.addEventListener('orientationchange', fitHeroToScreen);
    document.addEventListener('DOMContentLoaded', fitHeroToScreen);
    fitHeroToScreen();

    // 2. HERO BACKGROUND SLIDESHOW (EVERY 6 SECONDS)
    const heroBg = document.querySelector('.home-hero-bg');
    if (!heroBg) return;

    const slides = [
        'https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=2200&q=85',
        'https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=2200&q=85',
        'https://images.unsplash.com/photo-1556229010-6c3f2c9ca5f8?auto=format&fit=crop&w=2200&q=85'
    ];

    // Preload slide images
    slides.forEach(function (url) {
        const img = new Image();
        img.src = url;
    });

    let currentSlide = 0;
    let timer = null;
    const gradient = 'linear-gradient(90deg, rgba(255, 250, 247, .97) 0%, rgba(255, 250, 247, .90) 39%, rgba(255, 250, 247, .35) 67%, rgba(255, 250, 247, .08) 100%)';
    const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

    function nextSlide() {
        if (document.hidden) return;
        currentSlide = (currentSlide + 1) % slides.length;
        heroBg.classList.add('fade-out');
        setTimeout(function () {
            heroBg.style.backgroundImage = gradient + ', url("' + slides[currentSlide] + '")';
            heroBg.classList.remove('fade-out');
        }, 600);
    }

    function startSlideshow() {
        stopSlideshow();
        if (!motionQuery.matches && slides.length > 1) {
            timer = setInterval(nextSlide, 6000);
        }
    }

    function stopSlideshow() {
        if (timer) {
            clearInterval(timer);
            timer = null;
        }
    }

    startSlideshow();

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            stopSlideshow();
        } else {
            startSlideshow();
        }
    });

    if (motionQuery.addEventListener) {
        motionQuery.addEventListener('change', function (e) {
            if (e.matches) stopSlideshow();
            else startSlideshow();
        });
    }
})();
</script>
@endpush
