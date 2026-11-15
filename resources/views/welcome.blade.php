@extends('layouts.landing')

@section('title', 'Beauty Store - Temukan Produk Kecantikan Pilihanmu')

@push('styles')
    <style>
        /* =========================
           HERO SECTION (FULL VIEWPORT LUXURY)
        ========================= */
        .hero {
            position: relative;
            min-height: calc(100vh - var(--nav-h));
            display: flex;
            align-items: center;
            background:
                linear-gradient(135deg, rgba(61, 34, 48, 0.72) 0%, rgba(43, 23, 35, 0.65) 100%),
                url('https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1920&q=85') center/cover no-repeat;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 20% 50%, rgba(250, 172, 191, 0.28), transparent 60%);
            pointer-events: none;
        }

        .hero-container {
            position: relative;
            z-index: 2;
            max-width: 1240px;
            width: 100%;
            margin: auto;
            padding: 80px 32px;
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            align-items: center;
            gap: 40px;
        }

        .hero-content {
            color: #fff;
            animation: slideUpFade 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            padding: 8px 18px;
            background: rgba(255, 255, 255, 0.16);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: var(--radius-pill);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: #fff;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .hero-badge span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--c-pink-strong);
            box-shadow: 0 0 10px var(--c-pink-strong);
        }

        .hero h1 {
            font-size: 56px;
            line-height: 1.15;
            margin-bottom: 24px;
            color: #fff;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .hero h1 span.gradient-text {
            background: linear-gradient(135deg, #FFEABB 0%, #FAACBF 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 18px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 36px;
            max-width: 580px;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .hero-buttons .btn {
            padding: 14px 30px;
            font-size: 15px;
        }

        .hero-stats {
            display: flex;
            gap: 28px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hero-stat-item h4 {
            font-size: 24px;
            color: #fff;
            margin-bottom: 2px;
        }

        .hero-stat-item p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 0;
        }

        /* Hero Right Glass Card */
        .hero-glass-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: var(--radius-lg);
            padding: 32px;
            color: #fff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
            animation: fadeInScale 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .hero-glass-card h3 {
            color: #fff;
            font-size: 20px;
            margin-bottom: 12px;
        }

        .hero-feature-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-top: 20px;
        }

        .hero-feature-list li {
            display: flex;
            align-items: center;
            gap: 14px;
            font-size: 14.5px;
            color: rgba(255, 255, 255, 0.9);
        }

        .hero-feature-list .icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex: none;
        }

        /* =========================
           FEATURES SECTION
        ========================= */
        .section {
            max-width: 1240px;
            margin: auto;
            padding: 90px 32px;
        }

        .section-header {
            text-align: center;
            max-width: 720px;
            margin: 0 auto 56px;
        }

        .section-header .section-tag {
            display: inline-block;
            font-size: 12.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--c-pink-strong);
            background: var(--c-pink-subtle);
            padding: 6px 14px;
            border-radius: var(--radius-pill);
            margin-bottom: 12px;
        }

        .section-header h2 {
            font-size: 34px;
            color: var(--c-ink-heading);
            margin-bottom: 16px;
            letter-spacing: -0.02em;
        }

        .section-header p {
            color: var(--c-ink-soft);
            line-height: 1.7;
            font-size: 16px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
        }

        .feature-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border-subtle);
            border-radius: var(--radius-lg);
            padding: 38px 30px;
            text-align: center;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
            border-color: var(--c-blush);
        }

        .feature-icon {
            width: 68px;
            height: 68px;
            margin: 0 auto 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFF6EC, var(--c-cream));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            border: 2px solid rgba(251, 195, 193, 0.4);
            box-shadow: 0 4px 14px rgba(255, 234, 187, 0.4);
            transition: var(--transition);
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .feature-card h3 {
            margin-bottom: 12px;
            color: var(--c-ink-heading);
            font-size: 20px;
        }

        .feature-card p {
            color: var(--c-ink-soft);
            line-height: 1.7;
            font-size: 14.5px;
        }

        /* =========================
           ABOUT SECTION
        ========================= */
        .about-section {
            background: linear-gradient(180deg, rgba(255, 234, 187, 0.3) 0%, rgba(251, 195, 193, 0.2) 100%);
            padding: 90px 32px;
            border-top: 1px solid var(--c-border-subtle);
            border-bottom: 1px solid var(--c-border-subtle);
        }

        .about-container {
            max-width: 1200px;
            margin: auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 64px;
            align-items: center;
        }

        .about-image {
            min-height: 420px;
            background:
                linear-gradient(rgba(61, 34, 48, 0.1), rgba(61, 34, 48, 0.2)),
                url('https://images.unsplash.com/photo-1556229010-6c3f2c9ca5f8?auto=format&fit=crop&w=1000&q=80') center/cover no-repeat;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            position: relative;
        }

        .about-image .glass-stat {
            position: absolute;
            bottom: -20px;
            right: -20px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            padding: 20px 26px;
            border-radius: var(--radius-md);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: var(--shadow-md);
            max-width: 220px;
        }

        .glass-stat h4 {
            color: var(--c-pink-strong);
            font-size: 26px;
            font-weight: 800;
        }

        .glass-stat p {
            font-size: 12.5px;
            color: var(--c-ink-soft);
            margin: 0;
        }

        .about-content h2 {
            color: var(--c-ink-heading);
            font-size: 34px;
            margin-bottom: 20px;
            line-height: 1.25;
        }

        .about-content p {
            color: var(--c-ink-soft);
            line-height: 1.8;
            font-size: 15.5px;
            margin-bottom: 18px;
        }

        /* =========================
           CTA SECTION
        ========================= */
        .cta-section {
            padding: 90px 32px;
            text-align: center;
        }

        .cta-card {
            max-width: 1000px;
            margin: auto;
            background: linear-gradient(135deg, var(--c-ink) 0%, #4D2B3D 100%);
            border-radius: 28px;
            padding: 70px 40px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .cta-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(233, 129, 158, 0.3), transparent 70%);
            pointer-events: none;
        }

        .cta-card h2 {
            font-size: 36px;
            margin-bottom: 16px;
            color: #fff;
        }

        .cta-card p {
            max-width: 620px;
            margin: 0 auto 32px;
            line-height: 1.8;
            color: rgba(255, 255, 255, 0.85);
            font-size: 16px;
        }

        .cta-card .btn {
            padding: 15px 36px;
            font-size: 16px;
        }

        /* =========================
           RESPONSIVE
        ========================= */
        @media (max-width: 960px) {
            .hero-container {
                grid-template-columns: 1fr;
                padding: 60px 24px;
            }
            .hero-glass-card { display: none; }
            .hero h1 { font-size: 42px; }
            .features-grid { grid-template-columns: 1fr; }
            .about-container { grid-template-columns: 1fr; gap: 40px; }
            .about-image .glass-stat { bottom: 10px; right: 10px; }
            .cta-card { padding: 50px 24px; }
            .cta-card h2 { font-size: 28px; }
        }

        @media (max-width: 600px) {
            .hero { min-height: auto; }
            .hero-container { padding: 56px 20px 48px; }
            .hero-badge { font-size: 11px; letter-spacing: 0.8px; padding: 7px 14px; }
            .hero h1 { font-size: clamp(34px, 10vw, 40px); margin-bottom: 18px; }
            .hero p { font-size: 15px; line-height: 1.7; margin-bottom: 26px; }
            .hero-buttons { gap: 10px; margin-bottom: 30px; }
            .hero-buttons .btn { width: 100%; padding: 12px 20px; }
            .hero-stats { gap: 16px; flex-wrap: wrap; padding-top: 20px; }
            .hero-stat-item { flex: 1 1 112px; }
            .hero-stat-item h4 { font-size: 19px; }
            .hero-stat-item p { font-size: 12px; line-height: 1.5; }
            .section { padding: 64px 20px; }
            .section-header { margin-bottom: 36px; }
            .section-header h2,
            .about-content h2 { font-size: 28px; }
            .section-header p { font-size: 15px; }
            .feature-card { padding: 30px 24px; }
            .about-section { padding: 64px 20px; }
            .about-container { gap: 32px; }
            .about-image { min-height: 300px; }
            .about-image .glass-stat { padding: 14px 18px; max-width: 190px; }
            .glass-stat h4 { font-size: 22px; }
            .cta-section { padding: 64px 20px; }
            .cta-card { padding: 42px 22px; border-radius: var(--radius-lg); }
            .cta-card h2 { font-size: 26px; }
            .cta-card p { font-size: 15px; margin-bottom: 26px; }
            .cta-card .btn { width: 100%; padding: 13px 20px; font-size: 15px; }
        }
    </style>
@endpush

@section('content')

    {{-- HERO / BANNER --}}
    <section class="hero" id="home">
        <div class="hero-container">
            <div class="hero-content">
                <div class="hero-badge">
                    <span></span> Official Beauty E-Commerce
                </div>

                <h1>
                    Temukan Kilau Alami &
                    <span class="gradient-text">Pesona Cantikmu</span>
                </h1>

                <p>
                    Beauty Store menghadirkan ragam pilihan produk kecantikan terkurasi — dari perawatan kulit wajah, riasan estetik, hingga relaksasi tubuh. Sistem belanja modern dengan pengiriman terpercaya ke seluruh Indonesia.
                </p>

                <div class="hero-buttons">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        Mulai Belanja Sekarang 🛍️
                    </a>
                    <a href="#info" class="btn btn-ghost">
                        Pelajari Sistem →
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat-item">
                        <h4>100% Original</h4>
                        <p>Produk terjamin & BPOM</p>
                    </div>
                    <div class="hero-stat-item">
                        <h4>Smart AI</h4>
                        <p>Asisten belanja cerdas</p>
                    </div>
                    <div class="hero-stat-item">
                        <h4>RajaOngkir</h4>
                        <p>Tarif kirim akurat</p>
                    </div>
                </div>
            </div>

            <div class="hero-glass-card">
                <h3>Keunggulan Berbelanja di Sini</h3>
                <p style="color:rgba(255,255,255,0.8);font-size:14px;">Rasakan kemudahan berbelanja produk kecantikan dengan pengalaman digital terbaik.</p>

                <ul class="hero-feature-list">
                    <li>
                        <div class="icon-wrap">✨</div>
                        <span>Katalog produk lengkap & update stok langsung</span>
                    </li>
                    <li>
                        <div class="icon-wrap">🤖</div>
                        <span>Konsultasi interaktif bersama Chatbot Beauty Store</span>
                    </li>
                    <li>
                        <div class="icon-wrap">💳</div>
                        <span>Pembayaran aman instan terintegrasi Midtrans</span>
                    </li>
                    <li>
                        <div class="icon-wrap">🚚</div>
                        <span>Pilihan ekspedisi JNE, POS, TIKI otomatis</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- FITUR SISTEM --}}
    <section class="section" id="info">
        <div class="section-header">
            <span class="section-tag">Pengalaman Baru</span>
            <h2>Mengapa Memilih Beauty Store?</h2>
            <p>
                Platform yang dirancang khusus untuk kenyamanan Anda dalam menjelajahi dan merawat kecantikan setiap hari dengan aman dan transparan.
            </p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🛍️</div>
                <h3>Koleksi Terkurasi</h3>
                <p>
                    Pilihan produk kecantikan premium meliputi skincare, makeup, body care, dan hair care dari brand tepercaya.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <h3>Asisten AI Responsif</h3>
                <p>
                    Bingung memilih produk? Asisten pintar kami siap memberi rekomendasi kandungan dan cara pakai secara real-time.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3>Transaksi Cepat & Aman</h3>
                <p>
                    Manajemen keranjang cerdas, kalkulasi ongkos kirim real-time, dan status pembayaran otomatis tanpa ribet konfirmasi manual.
                </p>
            </div>
        </div>
    </section>

    {{-- ABOUT SECTION --}}
    <section class="about-section">
        <div class="about-container">
            <div class="about-image">
                <div class="glass-stat">
                    <h4>4.9 / 5.0</h4>
                    <p>Kepuasan Pelanggan Beauty Store</p>
                </div>
            </div>

            <div class="about-content">
                <h2>Satu Platform untuk Seluruh Kebutuhan Estetika Anda</h2>
                <p>
                    Kami memahami bahwa setiap kulit memiliki cerita unik. Oleh karena itu, Beauty Store hadir dengan sistem e-commerce modern yang memudahkan Anda menemukan produk yang tepat sesuai kebutuhan personal.
                </p>
                <p>
                    Mulai dari kemudahan navigasi kategori, detail produk yang transparan, perhitungan ongkos kirim dinamis hingga ke pelosok kecamatan, hingga pelacakan status pesanan secara live.
                </p>
                <div style="margin-top:28px;">
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar Akun Gratis</a>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA SECTION --}}
    <section class="cta-section">
        <div class="cta-card">
            <h2>Siap Menemukan Produk Impianmu?</h2>
            <p>
                Bergabunglah bersama ribuan pelanggan lainnya. Daftar akun sekarang untuk menikmati promo eksklusif dan kemudahan belanja kosmetik kapan pun Anda butuhkan.
            </p>
            <a href="{{ route('register') }}" class="btn btn-primary">Buat Akun Sekarang</a>
        </div>
    </section>

@endsection
