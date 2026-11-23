@extends('layouts.landing')

@section('title', 'ZRADIANCE - Temukan Produk Kecantikan Pilihanmu')

@push('styles')
<style>
    .navbar-floating-wrapper { position: absolute; top: 0; left: 0; right: 0; }
    .navbar-floating-wrapper .navbar-container { min-height: var(--nav-h); padding: 0 24px; gap: 16px; }
    .navbar-floating-wrapper .nav-links a,
    .navbar-floating-wrapper .nav-links button { padding: 9px 16px; font-size: 14px; }

    /* HERO */
    .hero {
        position: relative; isolation: isolate; min-height: 100vh; min-height: 100svh;
        display: flex; align-items: center; background: var(--c-ink); overflow: hidden;
    }
    .hero-slides { position: absolute; inset: 0; z-index: 0; background: var(--c-ink); }
    .hero-slide {
        position: absolute; inset: 0; overflow: hidden; opacity: 0; transform: scale(1.025);
        transition: opacity 650ms ease, transform 6s linear;
    }
    .hero-slide.is-active { opacity: 1; transform: scale(1); }
    .hero-slide img { width: 100%; height: 100%; object-fit: cover; }
    .hero-slide::after {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(90deg, rgba(36,20,29,.82), rgba(36,20,29,.46) 58%, rgba(36,20,29,.18));
    }
    .hero::before {
        content: ''; position: absolute; inset: 0; z-index: 1;
        background: radial-gradient(circle at 20% 50%, rgba(250,172,191,.28), transparent 60%);
        pointer-events: none;
    }
    .hero-container {
        position: relative; z-index: 2; max-width: 1240px; width: 100%; margin: auto;
        padding: 80px 32px; display: grid; grid-template-columns: 1.2fr .8fr;
        align-items: center; gap: 40px;
    }
    .hero-carousel-controls { display: flex; align-items: center; gap: 12px; margin: -18px 0 30px; }
    .hero-carousel-controls button {
        display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px;
        border: 1px solid rgba(255,255,255,.55); border-radius: 50%; background: rgba(255,255,255,.12);
        color: #fff; font: 700 18px var(--font); cursor: pointer; transition: var(--transition);
    }
    .hero-carousel-controls button:hover { background: rgba(255,255,255,.28); transform: translateY(-1px); }
    .hero-carousel-indicators { display: flex; align-items: center; gap: 8px; }
    .hero-carousel-indicators button { width: 9px; height: 9px; padding: 0; border: 0; background: rgba(255,255,255,.55); }
    .hero-carousel-indicators button[aria-current="true"] { width: 26px; border-radius: var(--radius-pill); background: var(--c-pink); }
    .hero-content { color: #fff; animation: slideUpFade .7s cubic-bezier(.16,1,.3,1) forwards; }
    .hero-content h1, .hero-content p { transition: opacity 180ms ease, transform 180ms ease; }
    .hero-content.is-changing h1, .hero-content.is-changing p { opacity: .35; transform: translateY(6px); }
    .hero-badge {
        display: inline-flex; align-items: center; gap: 8px; margin-bottom: 22px; padding: 8px 18px;
        background: rgba(255,255,255,.16); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255,255,255,.4); border-radius: var(--radius-pill);
        font-size: 13px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #fff;
        box-shadow: 0 4px 16px rgba(0,0,0,.1);
    }
    .hero-badge span { width: 8px; height: 8px; border-radius: 50%; background: var(--c-pink-strong); box-shadow: 0 0 10px var(--c-pink-strong); }
    .hero h1 { font-size: 56px; line-height: 1.15; margin-bottom: 24px; color: #fff; font-weight: 800; letter-spacing: -.03em; }
    .hero h1 span.gradient-text {
        background: linear-gradient(135deg, #FFEABB 0%, #FAACBF 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .hero p { font-size: 18px; line-height: 1.8; color: rgba(255,255,255,.9); margin-bottom: 36px; max-width: 580px; }
    .hero-buttons { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 40px; }
    .hero-buttons .btn { padding: 14px 30px; font-size: 15px; }
    .hero-stats { display: flex; gap: 28px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,.2); }
    .hero-stat-item h4 { font-size: 24px; color: #fff; margin-bottom: 2px; }
    .hero-stat-item p { font-size: 13px; color: rgba(255,255,255,.75); margin-bottom: 0; }
    .hero-glass-card {
        background: rgba(255,255,255,.12); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,.3); border-radius: var(--radius-lg); padding: 32px; color: #fff;
        box-shadow: 0 20px 50px rgba(0,0,0,.25); animation: fadeInScale .9s cubic-bezier(.16,1,.3,1) forwards;
    }
    .hero-glass-card h3 { color: #fff; font-size: 20px; margin-bottom: 12px; }
    .hero-feature-list { list-style: none; display: flex; flex-direction: column; gap: 16px; margin-top: 20px; }
    .hero-feature-list li { display: flex; align-items: center; gap: 14px; font-size: 14.5px; color: rgba(255,255,255,.9); }
    .hero-feature-list .icon-wrap {
        width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,.2);
        display: flex; align-items: center; justify-content: center; font-size: 16px; flex: none;
    }

    /* SECTIONS & FEATURES */
    .section { max-width: 1240px; margin: auto; padding: 64px 32px; }
    .section-header { text-align: center; max-width: 720px; margin: 0 auto 40px; }
    .section-header .section-tag {
        display: inline-block; font-size: 12.5px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 1.5px; color: var(--c-pink-strong); background: var(--c-pink-subtle);
        padding: 6px 14px; border-radius: var(--radius-pill); margin-bottom: 12px;
    }
    .section-header h2 { font-size: 34px; color: var(--c-ink-heading); margin-bottom: 16px; letter-spacing: -.02em; }
    .section-header p { color: var(--c-ink-soft); line-height: 1.7; font-size: 16px; }
    .features-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; }
    .feature-card {
        background: var(--c-surface); border: 1px solid var(--c-border-subtle); border-radius: var(--radius-lg);
        padding: 28px 24px; text-align: center; transition: var(--transition); box-shadow: var(--shadow-sm);
        position: relative; overflow: hidden;
    }
    .feature-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-lg); border-color: var(--c-blush); }
    .feature-icon {
        width: 68px; height: 68px; margin: 0 auto 16px; border-radius: 50%;
        background: linear-gradient(135deg,#FFF6EC,var(--c-cream)); display: flex; align-items: center;
        justify-content: center; font-size: 30px; border: 2px solid rgba(251,195,193,.4);
        box-shadow: 0 4px 14px rgba(255,234,187,.4); transition: var(--transition);
    }
    .feature-card:hover .feature-icon { transform: scale(1.1) rotate(5deg); }
    .feature-card h3 { margin-bottom: 12px; color: var(--c-ink-heading); font-size: 20px; }
    .feature-card p { color: var(--c-ink-soft); line-height: 1.7; font-size: 14.5px; }

    /* LANDING BRAND + PRODUCT */
    .landing-section { max-width: 1240px; margin: auto; padding: 56px 32px; }
    .landing-section-heading {
        display: flex; align-items: flex-end; justify-content: space-between; gap: 20px; margin-bottom: 20px;
    }
    .landing-section-heading h2 { margin-bottom: 6px; font-size: 28px; color: var(--c-ink-heading); }
    .landing-section-heading p { font-size: 15px; color: var(--c-ink-soft); margin: 0; }

    .landing-brands { background: #fff; border-block: 1px solid var(--c-border-subtle); overflow: hidden; }
    .brand-marquee { overflow: hidden; width: 100%; padding: 14px 0 24px; }
    .brand-marquee-track { display: flex; width: max-content; animation: brand-marquee 34s linear infinite; will-change: transform; }
    .brand-marquee:hover .brand-marquee-track, .brand-marquee:focus-within .brand-marquee-track { animation-play-state: paused; }
    .brand-marquee-group { display: flex; align-items: center; flex-shrink: 0; gap: clamp(38px, 6vw, 90px); padding: 0 clamp(19px, 3vw, 45px); }
    .brand-logo-link { width: clamp(110px, 14vw, 180px); height: 90px; display: flex; align-items: center; justify-content: center; flex: 0 0 auto; opacity: .82; transition: opacity .2s ease, transform .2s ease; }
    .brand-logo-link:hover, .brand-logo-link:focus-visible { opacity: 1; transform: scale(1.06); outline: none; }
    .brand-logo { max-width: 100%; max-height: 100%; width: 100%; height: 100%; object-fit: contain; }
    .brand-fallback { font-size: 32px; font-weight: 900; color: var(--c-pink-strong); }
    @keyframes brand-marquee { from { transform: translateX(0); } to { transform: translateX(-50%); } }

    .landing-products { background: linear-gradient(180deg,#FFFDFB,#FCF5F5); border-block: 1px solid var(--c-border-subtle); }
    .landing-product-grid { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 20px; }
    .landing-product-card { min-width: 0; overflow: hidden; border: 1px solid var(--c-border-subtle); border-radius: var(--radius-md); background: #fff; box-shadow: var(--shadow-sm); transition: var(--transition); }
    .landing-product-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
    .landing-product-image { display: block; width: 100%; aspect-ratio: 1/1; object-fit: cover; background: #FFF6EC; }
    .landing-product-info { padding: 18px; }
    .landing-product-brand { margin-bottom: 5px; color: var(--c-ink-light); font-size: 11px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
    .landing-product-info h3 { min-height: 42px; margin-bottom: 10px; font-size: 15px; }
    .landing-product-info h3 a { color: inherit; text-decoration: none; }
    .landing-product-info h3 a:hover { color: var(--c-pink-strong); }
    .landing-product-price { margin-bottom: 14px; color: var(--c-ink-heading); font-size: 16px; font-weight: 800; }
    .landing-empty-state { padding: 38px 22px; border: 1px dashed var(--c-border); border-radius: var(--radius-lg); background: rgba(255,255,255,.75); color: var(--c-ink-soft); text-align: center; }
    .landing-empty-state strong { display: block; margin-bottom: 6px; color: var(--c-ink-heading); font-size: 17px; }

    /* ABOUT */
    .about-section { background: linear-gradient(180deg,rgba(255,234,187,.3),rgba(251,195,193,.2)); padding: 64px 32px; border-block: 1px solid var(--c-border-subtle); }
    .about-container { max-width: 1200px; margin: auto; display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: center; }
    .about-image {
        min-height: 360px; background: linear-gradient(rgba(61,34,48,.1),rgba(61,34,48,.2)),
        url('https://images.unsplash.com/photo-1556229010-6c3f2c9ca5f8?auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;
        border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); position: relative;
    }
    .about-image .glass-stat {
        position: absolute; bottom: -20px; right: -20px; background: rgba(255,255,255,.92);
        backdrop-filter: blur(14px); padding: 20px 26px; border-radius: var(--radius-md);
        border: 1px solid rgba(255,255,255,.8); box-shadow: var(--shadow-md); max-width: 220px;
    }
    .glass-stat h4 { color: var(--c-pink-strong); font-size: 26px; font-weight: 800; }
    .glass-stat p { font-size: 12.5px; color: var(--c-ink-soft); margin: 0; }
    .about-content h2 { color: var(--c-ink-heading); font-size: 34px; margin-bottom: 20px; line-height: 1.25; }
    .about-content p { color: var(--c-ink-soft); line-height: 1.8; font-size: 15.5px; margin-bottom: 18px; }

    /* CTA */
    .cta-section { padding: 64px 32px; text-align: center; }
    .cta-card {
        max-width: 1000px; margin: auto; background: linear-gradient(135deg,var(--c-ink),#4D2B3D);
        border-radius: 28px; padding: 70px 40px; color: #fff; position: relative; overflow: hidden; box-shadow: var(--shadow-lg);
    }
    .cta-card::after {
        content: ''; position: absolute; top: -50%; right: -20%; width: 400px; height: 400px;
        border-radius: 50%; background: radial-gradient(circle,rgba(233,129,158,.3),transparent 70%); pointer-events: none;
    }
    .cta-card h2 { font-size: 36px; margin-bottom: 16px; color: #fff; }
    .cta-card p { max-width: 620px; margin: 0 auto 32px; line-height: 1.8; color: rgba(255,255,255,.85); font-size: 16px; }
    .cta-card .btn { padding: 15px 36px; font-size: 16px; }

    /* RESPONSIVE */
    @media(max-width:960px) {
        .navbar-floating-wrapper { top: 0; }
        .hero-container { grid-template-columns: 1fr; padding: 96px 24px 60px; }
        .hero-glass-card { display: none; }
        .hero h1 { font-size: 42px; }
        .features-grid { grid-template-columns: 1fr; }
        .landing-product-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
        .about-container { grid-template-columns: 1fr; gap: 40px; }
        .about-image .glass-stat { bottom: 10px; right: 10px; }
        .cta-card { padding: 50px 24px; }
        .cta-card h2 { font-size: 28px; }
    }
    @media(max-width:600px) {
        .hero { min-height: 100vh; min-height: 100svh; }
        .navbar-floating-wrapper { padding: 0 12px; }
        .navbar-floating-wrapper .navbar-container { min-height: 64px; padding: 8px 18px; }
        .navbar-floating-wrapper .nav-links a, .navbar-floating-wrapper .nav-links button { padding: 12px 16px; }
        .hero-container { padding: 88px 20px 40px; }
        .hero-carousel-controls { margin: -10px 0 26px; }
        .hero h1 { font-size: clamp(34px,10vw,40px); margin-bottom: 16px; }
        .hero p { font-size: 15px; line-height: 1.65; margin-bottom: 20px; }
        .hero-buttons { gap: 10px; margin-bottom: 22px; }
        .hero-buttons .btn { width: 100%; padding: 12px 20px; }
        .hero-stats { gap: 16px; flex-wrap: wrap; padding-top: 16px; }
        .hero-stat-item { flex: 1 1 112px; }
        .hero-stat-item h4 { font-size: 19px; }
        .hero-stat-item p { font-size: 12px; line-height: 1.5; }
        .landing-section { padding: 44px 20px; }
        .landing-section-heading { align-items: flex-start; flex-direction: column; }
        .landing-section-heading h2 { font-size: 25px; }
        .brand-logo-link { width: 112px; height: 68px; }
        .landing-product-grid { gap: 12px; }
        .landing-product-info { padding: 13px; }
        .landing-product-info h3 { font-size: 14px; min-height: 38px; }
        .landing-product-price { font-size: 14px; }
        .section { padding: 48px 20px; }
        .section-header { margin-bottom: 28px; }
        .section-header h2, .about-content h2 { font-size: 28px; }
        .section-header p { font-size: 15px; }
        .feature-card { padding: 24px 20px; }
        .about-section { padding: 48px 20px; }
        .about-container { gap: 28px; }
        .about-image { min-height: 260px; }
        .about-image .glass-stat { padding: 14px 18px; max-width: 190px; }
        .glass-stat h4 { font-size: 22px; }
        .cta-section { padding: 48px 20px; }
        .cta-card { padding: 42px 22px; border-radius: var(--radius-lg); }
        .cta-card h2 { font-size: 26px; }
        .cta-card p { font-size: 15px; margin-bottom: 26px; }
        .cta-card .btn { width: 100%; padding: 13px 20px; font-size: 15px; }
    }
    @media(prefers-reduced-motion:reduce) {
        .hero-slide, .brand-marquee-track { transition: none !important; animation: none !important; }
        .brand-logo-link, .hero-carousel-controls button { transition: none !important; }
    }
</style>
@endpush

@section('content')
    {{-- HERO / BANNER --}}
    <section class="hero" id="home" data-landing-hero>
        <div class="hero-slides" aria-hidden="true">
            <div class="hero-slide is-active" aria-hidden="true" data-title="Temukan Kilau Alami &" data-highlight="Pesona Cantikmu" data-description="Skincare, makeup, dan perawatan pilihan untuk rutinitas kecantikanmu.">
                <img src="https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=2200&q=85" alt="" fetchpriority="high">
            </div>
            <div class="hero-slide" aria-hidden="true" data-title="Rawat Kulit dengan" data-highlight="Pilihan Terpercaya" data-description="Temukan produk perawatan yang sesuai untuk kebutuhan kulitmu.">
                <img src="https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=2200&q=80" alt="" loading="lazy">
            </div>
            <div class="hero-slide" aria-hidden="true" data-title="Temukan Ritual" data-highlight="Kecantikanmu" data-description="Jelajahi koleksi kecantikan pilihan dalam satu tempat.">
                <img src="https://images.unsplash.com/photo-1556229010-6c3f2c9ca5f8?auto=format&fit=crop&w=2200&q=80" alt="" loading="lazy">
            </div>
        </div>

        <div class="hero-container">
            <div class="hero-content">
                <h1 aria-live="polite">
                    <span data-hero-title>Temukan Kilau Alami &</span>
                    <span class="gradient-text" data-hero-highlight>Pesona Cantikmu</span>
                </h1>
                <p data-hero-description>Skincare, makeup, dan perawatan pilihan untuk rutinitas kecantikanmu.</p>
                <div class="hero-buttons">
                    <a href="{{ route('login') }}" class="btn btn-primary">Mulai Belanja Sekarang 🛍️</a>
                    <a href="#info" class="btn btn-ghost">Pelajari Sistem →</a>
                </div>
                <div class="hero-carousel-controls" role="group" aria-label="Kontrol gambar utama">
                    <button type="button" data-hero-previous aria-label="Gambar sebelumnya">‹</button>
                    <div class="hero-carousel-indicators">
                        <button type="button" data-hero-slide="0" aria-label="Tampilkan gambar 1" aria-current="true"></button>
                        <button type="button" data-hero-slide="1" aria-label="Tampilkan gambar 2"></button>
                        <button type="button" data-hero-slide="2" aria-label="Tampilkan gambar 3"></button>
                    </div>
                    <button type="button" data-hero-next aria-label="Gambar berikutnya">›</button>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat-item"><h4>100% Original</h4><p>Produk terjamin & BPOM</p></div>
                    <div class="hero-stat-item"><h4>Smart AI</h4><p>Asisten belanja cerdas</p></div>
                    <div class="hero-stat-item"><h4>RajaOngkir</h4><p>Tarif kirim akurat</p></div>
                </div>
            </div>

            <div class="hero-glass-card">
                <h3>Keunggulan Berbelanja di Sini</h3>
                <p style="color:rgba(255,255,255,0.8);font-size:14px;">Rasakan kemudahan berbelanja produk kecantikan dengan pengalaman digital terbaik.</p>
                <ul class="hero-feature-list">
                    <li><div class="icon-wrap">✨</div><span>Katalog produk lengkap & update stok langsung</span></li>
                    <li><div class="icon-wrap">🤖</div><span>Konsultasi interaktif bersama Chatbot ZRadiance</span></li>
                    <li><div class="icon-wrap">💳</div><span>Pembayaran aman instan terintegrasi Midtrans</span></li>
                    <li><div class="icon-wrap">🚚</div><span>Pilihan ekspedisi JNE, POS, TIKI otomatis</span></li>
                </ul>
            </div>
        </div>
    </section>

    {{-- BRAND LOGO MARQUEE --}}
    <section class="landing-brands" aria-labelledby="landing-brands-title">
        <div class="landing-section">
            <div class="landing-section-heading">
                <div><h2 id="landing-brands-title">Brand Pilihan</h2><p>Brand skincare pilihan untuk kebutuhan perawatanmu.</p></div>
            <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk untuk berbelanja →</a>
        </div>

        @if(isset($brands) && $brands->isNotEmpty())
            <div class="brand-marquee" aria-label="Logo brand pilihan, bergerak horizontal">
                <div class="brand-marquee-track">
                    @for($copy = 0; $copy < 2; $copy++)
                        <div class="brand-marquee-group" @if($copy === 1) aria-hidden="true" @endif>
                            @foreach($brands as $brand)
                                <a href="{{ route('login') }}" class="brand-logo-link" title="{{ $brand->brand_name }}" aria-label="Login untuk melihat brand {{ $brand->brand_name }}" @if($copy === 1) tabindex="-1" @endif>
                                    @if($brand->brand_logo)
                                        <img src="{{ asset('storage/' . $brand->brand_logo) }}" alt="{{ $copy === 1 ? '' : 'Logo ' . $brand->brand_name }}" class="brand-logo" loading="lazy">
                                    @else
                                        <span class="brand-fallback">{{ strtoupper(mb_substr($brand->brand_name, 0, 1)) }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endfor
                </div>
            </div>
        @else
            <div class="landing-empty-state"><strong>Brand belum tersedia</strong>Brand pilihan akan ditampilkan di sini setelah tersedia.</div>
        @endif
    </section>

    {{-- PILIHAN PRODUK --}}
    <section class="landing-products">
        <div class="landing-section">
            <div class="landing-section-heading">
                <div><h2>Pilihan Produk</h2><p>Produk tersedia yang dipilih langsung dari katalog ZRadiance.</p></div>
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk untuk melihat katalog →</a>
            </div>
            @if($products->isNotEmpty())
                <div class="landing-product-grid">
                    @foreach($products as $product)
                        <article class="landing-product-card">
                            @if($product->image)
                                <img class="landing-product-image" src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="landing-product-image" role="img" aria-label="Gambar produk belum tersedia"></div>
                            @endif
                            <div class="landing-product-info">
                                <div class="landing-product-brand">{{ $product->brandRelation->brand_name ?? $product->brand }}</div>
                                <h3><a href="{{ auth()->check() ? route('products.show', $product->id) : route('login') }}">{{ $product->name }}</a></h3>
                                <div class="landing-product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                <a href="{{ auth()->check() ? route('products.show', $product->id) : route('login') }}" class="btn btn-primary btn-sm">Lihat Produk</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="landing-empty-state"><strong>Produk belum tersedia</strong>Koleksi pilihan akan tampil di sini setelah produk tersedia.</div>
            @endif
        </div>
    </section>

    {{-- FITUR SISTEM --}}
    <section class="section" id="info">
        <div class="section-header">
            <span class="section-tag">Pengalaman Baru</span>
            <h2>Mengapa Memilih ZRadiance?</h2>
            <p>Platform yang dirancang khusus untuk kenyamanan Anda dalam menjelajahi dan merawat kecantikan setiap hari dengan aman dan transparan.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card"><div class="feature-icon">🛍️</div><h3>Koleksi Terkurasi</h3><p>Pilihan produk kecantikan premium meliputi skincare, makeup, body care, dan hair care dari brand tepercaya.</p></div>
            <div class="feature-card"><div class="feature-icon">💬</div><h3>Asisten AI Responsif</h3><p>Bingung memilih produk? Asisten pintar kami siap memberi rekomendasi kandungan dan cara pakai secara real-time.</p></div>
            <div class="feature-card"><div class="feature-icon">🛡️</div><h3>Transaksi Cepat & Aman</h3><p>Manajemen keranjang cerdas, kalkulasi ongkos kirim real-time, dan status pembayaran otomatis tanpa ribet konfirmasi manual.</p></div>
        </div>
    </section>

    {{-- ABOUT --}}
    <section class="about-section">
        <div class="about-container">
            <div class="about-image">
                <div class="glass-stat"><h4>ZRADIANCE</h4><p>Temukan pilihan untuk rutinitas kecantikanmu.</p></div>
            </div>
            <div class="about-content">
                <h2>Satu Platform untuk Seluruh Kebutuhan Estetika Anda</h2>
                <p>Kami memahami bahwa setiap kulit memiliki cerita unik. Oleh karena itu, ZRadiance hadir dengan sistem e-commerce modern yang memudahkan Anda menemukan produk yang tepat sesuai kebutuhan personal.</p>
                <p>Mulai dari kemudahan navigasi kategori, detail produk yang transparan, perhitungan ongkos kirim dinamis hingga ke pelosok kecamatan, hingga pelacakan status pesanan secara live.</p>
                <div style="margin-top:28px;"><a href="{{ route('register') }}" class="btn btn-primary">Daftar Akun Gratis</a></div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta-section">
        <div class="cta-card">
            <h2>Siap Menemukan Produk Impianmu?</h2>
            <p>Bergabunglah bersama ribuan pelanggan lainnya. Daftar akun sekarang untuk menikmati promo eksklusif dan kemudahan belanja kosmetik kapan pun Anda butuhkan.</p>
            <a href="{{ route('register') }}" class="btn btn-primary">Buat Akun Sekarang</a>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // HERO SLIDES
    var hero = document.querySelector('[data-landing-hero]');
    if (hero) {
        var slides = Array.prototype.slice.call(hero.querySelectorAll('.hero-slide'));
        var indicators = Array.prototype.slice.call(hero.querySelectorAll('[data-hero-slide]'));
        var previous = hero.querySelector('[data-hero-previous]');
        var next = hero.querySelector('[data-hero-next]');
        var heroContent = hero.querySelector('.hero-content');
        var title = hero.querySelector('[data-hero-title]');
        var highlight = hero.querySelector('[data-hero-highlight]');
        var description = hero.querySelector('[data-hero-description]');
        var current = 0, timer = null, copyTimer = null;
        var motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
        var reducedMotion = motionQuery.matches;

        function showSlide(index) {
            if (slides.length < 2) return;
            current = (index + slides.length) % slides.length;
            slides.forEach(function (slide, i) { slide.classList.toggle('is-active', i === current); });
            indicators.forEach(function (dot, i) {
                if (i === current) dot.setAttribute('aria-current', 'true');
                else dot.removeAttribute('aria-current');
            });
            if (!title || !highlight || !description) return;
            if (copyTimer) clearTimeout(copyTimer);
            if (reducedMotion) {
                title.textContent = slides[current].dataset.title || '';
                highlight.textContent = slides[current].dataset.highlight || '';
                description.textContent = slides[current].dataset.description || '';
                heroContent.classList.remove('is-changing');
                return;
            }
            heroContent.classList.add('is-changing');
            copyTimer = setTimeout(function () {
                title.textContent = slides[current].dataset.title || '';
                highlight.textContent = slides[current].dataset.highlight || '';
                description.textContent = slides[current].dataset.description || '';
                heroContent.classList.remove('is-changing');
            }, 180);
        }
        function stopHero() { if (timer) { clearInterval(timer); timer = null; } }
        function startHero() {
            stopHero();
            if (!reducedMotion && !document.hidden && slides.length > 1) timer = setInterval(function () { showSlide(current + 1); }, 5000);
        }
        if (previous) previous.addEventListener('click', function () { showSlide(current - 1); startHero(); });
        if (next) next.addEventListener('click', function () { showSlide(current + 1); startHero(); });
        indicators.forEach(function (dot) { dot.addEventListener('click', function () { showSlide(Number(dot.dataset.heroSlide)); startHero(); }); });
        hero.addEventListener('focusin', stopHero);
        hero.addEventListener('focusout', function (e) { if (!hero.contains(e.relatedTarget)) startHero(); });
        document.addEventListener('visibilitychange', function () { if (document.hidden) stopHero(); else startHero(); });
        function motionChange(e) { reducedMotion = e.matches; if (reducedMotion) stopHero(); else startHero(); }
        if (motionQuery.addEventListener) motionQuery.addEventListener('change', motionChange);
        else if (motionQuery.addListener) motionQuery.addListener(motionChange);
        startHero();
    }


});
</script>
@endpush
