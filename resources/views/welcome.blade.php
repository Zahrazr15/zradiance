@extends('layouts.landing')

@section('title', 'Beauty Store - Beranda')

@push('styles')
    <style>
        /* =========================
           HERO / BANNER
        ========================= */

        .hero {
            min-height: 540px;

            background:
                linear-gradient(rgba(61, 34, 48, 0.55), rgba(61, 34, 48, 0.55)),
                url('{{ asset('images/hero.jpg') }}'),
                url('https://images.unsplash.com/photo-1596462502278-27bfdc403348?auto=format&fit=crop&w=1600&q=80');

            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
        }

        .hero-container {
            max-width: 1200px;
            width: 100%;
            margin: auto;
            padding: 60px 30px;
        }

        .hero-content {
            max-width: 650px;
            color: white;
        }

        .hero-label {
            display: inline-block;
            margin-bottom: 18px;
            padding: 8px 16px;

            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;

            font-size: 13px;
            letter-spacing: 1px;
        }

        .hero h1 {
            font-size: 52px;
            line-height: 1.15;
            margin-bottom: 20px;
        }

        .hero h1 span {
            color: var(--c-pink);
        }

        .hero p {
            font-size: 17px;
            line-height: 1.8;
            color: #f7eee8;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }


        /* =========================
           INFORMASI SISTEM
        ========================= */

        .section {
            max-width: 1100px;
            margin: auto;
            padding: 75px 30px;
        }

        .section-header {
            text-align: center;
            max-width: 700px;
            margin: 0 auto 45px;
        }

        .section-header h2 {
            font-size: 30px;
            color: var(--c-ink);
            margin-bottom: 14px;
        }

        .section-header p {
            color: var(--c-ink-soft);
            line-height: 1.7;
            font-size: 15px;
        }


        /* =========================
           FEATURE CARDS
        ========================= */

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 22px;
        }

        .feature-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 14px;
            padding: 30px 25px;

            text-align: center;
        }

        .feature-icon {
            width: 58px;
            height: 58px;

            margin: 0 auto 18px;

            border-radius: 50%;
            background: var(--c-cream);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 25px;
        }

        .feature-card h3 {
            margin-bottom: 10px;
            color: var(--c-ink);
            font-size: 18px;
        }

        .feature-card p {
            color: var(--c-ink-soft);
            line-height: 1.7;
            font-size: 14px;
        }


        /* =========================
           ABOUT / INFO
        ========================= */

        .about {
            background: var(--c-cream);
        }

        .about-container {
            max-width: 1100px;
            margin: auto;
            padding: 70px 30px;

            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .about-image {
            min-height: 320px;

            background:
                url('{{ asset('images/about.jpg') }}'),
                url('https://images.unsplash.com/photo-1556229010-6c3f2c9ca5f8?auto=format&fit=crop&w=900&q=80');

            background-size: cover;
            background-position: center;

            border-radius: 16px;
        }

        .about-content h2 {
            color: var(--c-ink);
            font-size: 30px;
            margin-bottom: 18px;
        }

        .about-content p {
            color: var(--c-ink-soft);
            line-height: 1.8;
            font-size: 15px;
            margin-bottom: 15px;
        }


        /* =========================
           CTA
        ========================= */

        .cta {
            text-align: center;
            padding: 75px 30px;
            background: var(--c-ink);
            color: white;
        }

        .cta h2 {
            font-size: 30px;
            margin-bottom: 15px;
        }

        .cta p {
            max-width: 650px;
            margin: 0 auto 25px;
            line-height: 1.7;
            color: var(--c-border);
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 800px) {

            .hero {
                min-height: 500px;
            }

            .hero h1 {
                font-size: 38px;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .about-container {
                grid-template-columns: 1fr;
            }

            .about-image {
                min-height: 250px;
            }
        }
    </style>
@endpush

@section('content')

    {{-- =========================
         HERO / BANNER
    ========================== --}}

    <section class="hero" id="home">

        <div class="hero-container">

            <div class="hero-content">

                <span class="hero-label">
                    BEAUTY STORE
                </span>

                <h1>
                    Temukan Produk
                    <span>Kecantikan</span>
                    Pilihanmu
                </h1>

                <p>
                    Beauty Store merupakan sistem e-commerce yang
                    menyediakan berbagai produk kecantikan seperti
                    skincare, makeup, body care, dan hair care.
                    Temukan produk yang sesuai dengan kebutuhanmu
                    dengan mudah dalam satu platform.
                </p>

                <div class="hero-buttons">

                    <a
                        href="{{ route('login') }}"
                        class="btn btn-primary"
                    >
                        Mulai Belanja
                    </a>

                    {{-- di .hero-buttons --}}
                    <a href="#info" class="btn btn-ghost">Informasi Sistem</a>

                </div>

            </div>

        </div>

    </section>


    {{-- =========================
         PENJELASAN SISTEM
    ========================== --}}

    <section class="section" id="info">

        <div class="section-header">

            <h2>
                Tentang Beauty Store
            </h2>

            <p>
                Beauty Store dirancang sebagai sistem e-commerce
                untuk membantu pengguna mencari dan membeli berbagai
                produk kecantikan secara lebih mudah dan terorganisir.
            </p>

        </div>


        <div class="features">

            <div class="feature-card">

                <div class="feature-icon">
                    🛍️
                </div>

                <h3>
                    Produk Kecantikan
                </h3>

                <p>
                    Menyediakan berbagai pilihan produk kecantikan
                    berdasarkan kategori seperti skincare, makeup,
                    body care, dan hair care.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    🔎
                </div>

                <h3>
                    Mudah Digunakan
                </h3>

                <p>
                    Pengguna dapat melihat kategori, memilih produk,
                    memasukkan produk ke keranjang, dan melakukan
                    checkout melalui sistem.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    🛒
                </div>

                <h3>
                    Pemesanan Online
                </h3>

                <p>
                    Sistem menyediakan fitur keranjang dan checkout
                    untuk membantu pengguna melakukan pemesanan
                    produk kecantikan.
                </p>

            </div>

        </div>

    </section>


    {{-- =========================
         INFORMASI TAMBAHAN
    ========================== --}}

    <section class="about">

        <div class="about-container">

            <div class="about-image"></div>

            <div class="about-content">

                <h2>
                    Satu Platform untuk Kebutuhan Beauty
                </h2>

                <p>
                    Beauty Store membantu pengguna menemukan berbagai
                    produk kecantikan dalam satu sistem yang sederhana
                    dan mudah digunakan.
                </p>

                <p>
                    Pengguna dapat melakukan registrasi, login,
                    menjelajahi kategori produk, memilih produk,
                    memasukkannya ke keranjang, dan melakukan checkout.
                </p>

                <p>
                    Sistem juga menyediakan halaman administrasi untuk
                    membantu admin mengelola produk, kategori, dan
                    pesanan.
                </p>

            </div>

        </div>

    </section>


    {{-- =========================
         CTA
    ========================== --}}

    <section class="cta">

        <h2>
            Mulai Jelajahi Beauty Store
        </h2>

        <p>
            Daftar atau login untuk melihat berbagai produk
            kecantikan yang tersedia dan melakukan pemesanan.
        </p>

        {{-- di section CTA --}}
        <a href="{{ route('register') }}" class="btn btn-primary">Buat Akun</a>

    </section>

@endsection