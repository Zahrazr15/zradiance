@extends('layouts.user')

@section('title', 'Kategori Produk - Beauty Store')

@push('styles')
<style>
    .category-page {
        max-width: 1240px;
        margin: 40px auto;
        padding: 0 32px 80px;
    }

    .category-header {
        text-align: center;
        max-width: 700px;
        margin: 0 auto 48px;
    }

    .category-header h1 {
        font-size: 34px;
        margin-bottom: 12px;
        color: var(--c-ink-heading);
    }

    .category-header p {
        color: var(--c-ink-soft);
        font-size: 15.5px;
        line-height: 1.6;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }

    .category-card {
        background: var(--c-surface);
        padding: 38px 24px;
        border-radius: var(--radius-lg);
        text-align: center;
        text-decoration: none;
        color: var(--c-ink);
        border: 1px solid var(--c-border-subtle);
        transition: var(--transition);
        box-shadow: var(--shadow-xs);
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    .category-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-md);
        border-color: var(--c-blush);
    }

    .category-icon-box {
        margin-bottom: 20px;
        transition: var(--transition);
    }

    .category-card:hover .category-icon-box {
        transform: scale(1.1) rotate(4deg);
    }

    .category-card h2 {
        font-size: 20px;
        margin-bottom: 8px;
        color: var(--c-ink-heading);
    }

    .category-card p {
        color: var(--c-ink-soft);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 16px;
        flex: 1;
    }

    .category-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--c-pink-strong);
        background: var(--c-pink-subtle);
        padding: 6px 14px;
        border-radius: var(--radius-pill);
        border: 1px solid rgba(250, 172, 191, 0.4);
    }

    .empty-categories {
        background: var(--c-surface);
        padding: 60px 24px;
        text-align: center;
        border-radius: var(--radius-lg);
        color: var(--c-ink-soft);
        border: 1px dashed var(--c-border);
    }

    @media (max-width: 1040px) {
        .category-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media (max-width: 600px) {
        .category-page { padding: 0 20px 50px; margin-top: 24px; }
        .category-grid { grid-template-columns: 1fr; }
        .category-header h1 { font-size: 28px; }
    }
</style>
@endpush

@section('content')
<div class="category-page">

    <div class="category-header">
        <h1>Kategori Pilihan</h1>
        <p>Jelajahi berbagai kategori perawatan wajah, tata rias, dan perawatan tubuh yang sesuai dengan tipe kulitmu.</p>
    </div>

    @if ($categories->count() > 0)
        <div class="category-grid">
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category->cat_id) }}" class="category-card">
                    <div class="category-icon-box">
                        <x-category-icon :title="$category->cat_title" />
                    </div>

                    <h2>{{ $category->cat_title }}</h2>

                    <p>{{ $category->description ?: 'Temukan produk ' . $category->cat_title . ' terbaik untuk perawatanmu.' }}</p>

                    <span class="category-pill">
                        ✨ {{ $category->products_count }} Koleksi Produk
                    </span>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-categories">
            <h3>Belum Ada Kategori</h3>
            <p>Kategori produk akan segera ditambahkan oleh admin.</p>
        </div>
    @endif

</div>
@endsection
