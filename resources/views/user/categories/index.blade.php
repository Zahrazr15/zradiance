@extends('layouts.user')

@section('title', 'Kategori - Beauty Store')

@push('styles')
<style>
    .content {
        max-width: 1200px;
        margin: 50px auto;
        padding: 0 30px 60px;
    }

    .page-header {
        text-align: center;
        margin-bottom: 35px;
    }

    .page-header h1 {
        font-size: 32px;
        margin-bottom: 10px;
    }

    .page-header p {
        color: var(--c-ink-soft);
        font-size: 16px;
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
        border: 1px solid var(--c-border);
        transition: 0.2s;
    }

    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .category-icon {
        margin-bottom: 15px;
    }

    .category-card h2 {
        font-size: 20px;
        margin-bottom: 8px;
    }

    .category-card p {
        color: var(--c-ink-soft);
        font-size: 14px;
        margin-bottom: 10px;
    }

    .product-count {
        display: inline-block;
        font-size: 13px;
        color: var(--c-ink-soft);
        background: var(--c-cream);
        padding: 6px 12px;
        border-radius: 20px;
    }

    .empty {
        background: var(--c-surface);
        padding: 40px;
        text-align: center;
        border-radius: 15px;
        color: var(--c-ink-soft);
    }

    @media (max-width: 900px) {
        .category-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .content {
            margin: 30px auto;
            padding: 0 20px 40px;
        }

        .category-grid {
            grid-template-columns: 1fr;
        }

        .page-header h1 {
            font-size: 27px;
        }
    }
</style>
@endpush

@section('content')
<div class="content">

    <div class="page-header">
        <h1>Kategori Produk</h1>

        <p>
            Jelajahi berbagai kategori produk kecantikan yang tersedia.
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

                    <h2>
                        {{ $category->cat_title }}
                    </h2>

                    <p>
                        {{ $category->description }}
                    </p>

                    <p>
                        Lihat produk {{ $category->cat_title }}
                    </p>

                    <span class="product-count">
                        {{ $category->products_count }} Produk
                    </span>

                </a>

            @endforeach

        </div>

    @else

        <div class="empty">
            Belum ada kategori produk.
        </div>

    @endif

</div>
@endsection