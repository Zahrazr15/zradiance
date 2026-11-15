@extends('layouts.user')

@section('title', 'Beranda Pelanggan - Beauty Store')

@push('styles')
<style>
    .user-home-hero {
        max-width: 1240px;
        margin: 36px auto 28px;
        padding: 50px 40px;
        background: linear-gradient(135deg, rgba(250, 172, 191, 0.25) 0%, rgba(255, 234, 187, 0.35) 100%);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 32px;
        box-shadow: var(--shadow-sm);
        flex-wrap: wrap;
    }

    .user-home-hero h1 {
        font-size: 34px;
        margin-bottom: 10px;
        color: var(--c-ink-heading);
    }

    .user-home-hero p {
        font-size: 16px;
        color: var(--c-ink-soft);
        max-width: 580px;
        line-height: 1.6;
    }

    .category-section {
        max-width: 1240px;
        margin: auto;
        padding: 20px 32px 80px;
    }

    .section-title {
        text-align: center;
        margin-bottom: 40px;
    }

    .section-title h2 {
        font-size: 28px;
        color: var(--c-ink-heading);
        margin-bottom: 8px;
    }

    .section-title p {
        color: var(--c-ink-soft);
        font-size: 15px;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }

    .category-card {
        background: var(--c-surface);
        padding: 36px 24px;
        border-radius: var(--radius-lg);
        text-align: center;
        text-decoration: none;
        color: var(--c-ink);
        transition: var(--transition);
        border: 1px solid var(--c-border-subtle);
        box-shadow: var(--shadow-xs);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .category-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-md);
        border-color: var(--c-blush);
    }

    .category-icon {
        margin-bottom: 18px;
        transition: var(--transition);
    }

    .category-card:hover .category-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .category-card h3 {
        font-size: 19px;
        margin-bottom: 6px;
        color: var(--c-ink-heading);
    }

    .category-card p {
        color: var(--c-ink-soft);
        font-size: 13.5px;
    }

    .empty-category {
        text-align: center;
        background: var(--c-surface);
        padding: 50px 20px;
        border-radius: var(--radius-lg);
        color: var(--c-ink-soft);
        border: 1px dashed var(--c-border);
    }

    @media (max-width: 1040px) {
        .category-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 600px) {
        .user-home-hero { margin: 20px 16px; padding: 30px 20px; }
        .user-home-hero h1 { font-size: 26px; }
        .category-section { padding: 10px 16px 50px; }
        .category-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

    <!-- HERO -->
    <section class="user-home-hero">
        <div>
            <h1>Selamat Datang, {{ Auth::user()->name }}! ✨</h1>
            <p>
                Temukan pilihan produk perawatan wajah, kosmetik estetik, dan perawatan tubuh yang terkurasi khusus untuk Anda.
            </p>
        </div>
        <div>
            <a href="{{ route('products.index') }}" class="btn btn-primary">
                Buka Katalog Lengkap 🛍️
            </a>
        </div>
    </section>

    <!-- CATEGORY -->
    <section class="category-section">
        <div class="section-title">
            <h2>Kategori Perawatan Pilihan</h2>
            <p>Jelajahi produk kecantikan sesuai dengan rutinitas harianmu.</p>
        </div>

        @if ($categories->count() > 0)
            <div class="category-grid">
                @foreach ($categories as $category)
                    <a href="{{ route('categories.show', $category->cat_id) }}" class="category-card">
                        <div class="category-icon">
                            <x-category-icon :title="$category->cat_title" />
                        </div>

                        <h3>{{ $category->cat_title }}</h3>
                        <p>Lihat koleksi {{ $category->cat_title }}</p>
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
