@extends('layouts.user')

@section('title', 'Home - Beauty Store')

@push('styles')
<style>
    /* HERO */

    .hero {
        max-width: 1200px;
        margin: 50px auto 40px;
        padding: 70px 40px;
        background: var(--c-surface);
        border-radius: 18px;
        text-align: center;
    }

    .hero h1 {
        font-size: 42px;
        margin-bottom: 15px;
    }

    .hero p {
        font-size: 18px;
        color: var(--c-ink-soft);
        max-width: 600px;
        margin: auto;
        line-height: 1.6;
    }

    /* CATEGORY */

    .category-section {
        max-width: 1200px;
        margin: auto;
        padding: 0 30px 60px;
    }

    .section-title {
        text-align: center;
        margin-bottom: 30px;
    }

    .section-title h2 {
        font-size: 28px;
        margin-bottom: 8px;
    }

    .section-title p {
        color: var(--c-ink-soft);
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .category-card {
        background: var(--c-surface);
        padding: 35px 20px;
        border-radius: 15px;
        text-align: center;
        text-decoration: none;
        color: var(--c-ink);
        transition: 0.2s;
        border: 1px solid var(--c-border);
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .category-icon {
        margin-bottom: 15px;
    }

    .category-card h3 {
        font-size: 20px;
        margin-bottom: 8px;
    }

    .category-card p {
        color: var(--c-ink-soft);
        font-size: 14px;
    }

    /* EMPTY */

    .empty-category {
        text-align: center;
        background: var(--c-surface);
        padding: 40px;
        border-radius: 15px;
        color: var(--c-ink-soft);
    }

    /* RESPONSIVE */

    @media (max-width: 900px) {
        .category-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .hero {
            margin: 30px 20px;
            padding: 50px 20px;
        }

        .hero h1 {
            font-size: 30px;
        }

        .category-section {
            padding: 0 20px 40px;
        }

        .category-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <!-- HERO -->

    <section class="hero">

        <h1>
            Selamat Datang, {{ Auth::user()->name }}! 👋
        </h1>

        <p>
            Temukan berbagai produk kecantikan yang sesuai
            dengan kebutuhanmu.
        </p>

    </section>


    <!-- CATEGORY -->

    <section class="category-section">

        <div class="section-title">

            <h2>
                Pilih Kategori
            </h2>

            <p>
                Jelajahi produk berdasarkan kategori yang kamu inginkan.
            </p>

        </div>


        @if ($categories->count() > 0)

            <div class="category-grid">

                @foreach ($categories as $category)

                    <a
                        href="{{ route('categories.show', $category->cat_id) }}"
                        class="category-card"
                    >

                        <div class="category-icon">
                            <x-category-icon :title="$category->cat_title" />
                        </div>

                        <h3>
                            {{ $category->cat_title }}
                        </h3>

                        <p>
                            Lihat produk {{ $category->cat_title }}
                        </p>

                    </a>

                @endforeach

            </div>

        @else

            <div class="empty-category">
                Belum ada kategori produk.
            </div>

        @endif

    </section>
@endsection