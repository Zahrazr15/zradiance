
@extends('layouts.user')

@section('title', 'Semua Produk - Beauty Store')

@push('styles')
<style>
    .catalog {
        max-width: 1250px;
        margin: 35px auto;
        padding: 0 24px 60px;
    }

    .catalog-header {
        margin-bottom: 25px;
    }

    .catalog-header h1 {
        font-size: 30px;
        margin-bottom: 8px;
    }

    .catalog-header p,
    .product-meta {
        color: var(--c-ink-soft);
    }

    .filter-panel {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 25px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: 2fr 1.2fr 1fr 1fr 1fr auto;
        gap: 10px;
    }

    .filter-form input,
    .filter-form select {
        width: 100%;
        min-width: 0;
        padding: 11px;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        background: var(--c-bg);
        color: var(--c-ink);
    }

    .filter-button {
        border: 0;
        border-radius: 8px;
        padding: 11px 18px;
        background: var(--c-pink);
        color: var(--c-ink);
        cursor: pointer;
    }

    .catalog-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
        gap: 12px;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
    }

    .product-card {
        overflow: hidden;
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 13px;
        transition: .2s;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 22px rgba(0,0,0,.08);
    }

    .product-image-link {
        display: block;
        background: var(--c-cream);
    }

    .product-image {
        width: 100%;
        height: 210px;
        object-fit: cover;
        display: block;
    }

    .image-placeholder {
        height: 210px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 45px;
    }

    .product-info {
        padding: 15px;
    }

    .product-brand {
        color: var(--c-ink-soft);
        font-size: 12px;
        margin-bottom: 5px;
    }

    .product-name {
        min-height: 42px;
        font-size: 16px;
        margin-bottom: 8px;
    }

    .product-name a {
        color: var(--c-ink);
        text-decoration: none;
    }

    .product-price {
        font-size: 17px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .product-rating {
        font-size: 13px;
        color: #d99b00;
        margin-bottom: 6px;
    }

    .product-rating span {
        color: var(--c-ink-soft);
    }

    .product-meta {
        font-size: 12px;
        margin-bottom: 12px;
    }

    .product-actions {
        display: grid;
        gap: 8px;
    }

    .product-actions button,
    .product-actions a {
        width: 100%;
        text-align: center;
        padding: 10px;
        border: 0;
        border-radius: 7px;
        text-decoration: none;
        cursor: pointer;
        font-size: 13px;
    }

    .detail-button {
        background: var(--c-cream);
        color: var(--c-ink);
    }

    .cart-button {
        background: var(--c-pink);
        color: var(--c-ink);
    }

    .empty-products {
        padding: 50px 20px;
        text-align: center;
        background: var(--c-surface);
        border-radius: 12px;
        color: var(--c-ink-soft);
    }

    .pagination {
        margin-top: 30px;
    }

    @media(max-width: 1000px) {
        .filter-form {
            grid-template-columns: repeat(3, 1fr);
        }

        .product-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media(max-width: 700px) {
        .filter-form {
            grid-template-columns: repeat(2, 1fr);
        }

        .product-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .product-image,
        .image-placeholder {
            height: 160px;
        }

        .catalog {
            padding-left: 14px;
            padding-right: 14px;
        }

        .catalog-header h1 {
            font-size: 24px;
        }
    }

    @media(max-width: 380px) {
        .filter-form {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="catalog">

    <div class="catalog-header">
        <h1>Temukan Produk Favoritmu</h1>
        <p>Jelajahi berbagai produk kecantikan sesuai kebutuhanmu.</p>
    </div>

    <div class="filter-panel">
        <form action="{{ route('products.index') }}" method="GET" class="filter-form">

            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Cari nama produk atau brand..."
            >

            <select name="category">
                <option value="">Semua Kategori</option>
                @foreach($categories as $category)
                    <option
                        value="{{ $category->cat_id }}"
                        @selected((string) request('category') === (string) $category->cat_id)
                    >
                        {{ $category->cat_title }}
                    </option>
                @endforeach
            </select>

            <input
                type="number"
                name="min_price"
                min="0"
                value="{{ request('min_price') }}"
                placeholder="Harga minimum"
            >

            <input
                type="number"
                name="max_price"
                min="0"
                value="{{ request('max_price') }}"
                placeholder="Harga maksimum"
            >

            <select name="sort">
                <option value="latest" @selected(request('sort', 'latest') === 'latest')>
                    Terbaru
                </option>
                <option value="price_low" @selected(request('sort') === 'price_low')>
                    Harga terendah
                </option>
                <option value="price_high" @selected(request('sort') === 'price_high')>
                    Harga tertinggi
                </option>
            </select>

            <button type="submit" class="filter-button">
                Terapkan
            </button>
        </form>

        @if(request()->hasAny(['q', 'category', 'min_price', 'max_price', 'sort']))
            <a href="{{ route('products.index') }}"
               style="display:inline-block;margin-top:12px;color:var(--c-ink-soft);font-size:13px;">
                Reset filter
            </a>
        @endif
    </div>

    <div class="catalog-toolbar">
        <strong>Semua Produk</strong>
        <span class="product-meta">{{ $products->total() }} produk ditemukan</span>
    </div>

    @if($products->count())
        <div class="product-grid">
            @foreach($products as $product)
                <article class="product-card">

                    <a href="{{ route('products.show', $product->id) }}"
                       class="product-image-link">

                        @if($product->image)
                            <img
                                src="{{ $product->image }}"
                                alt="{{ $product->name }}"
                                class="product-image"
                                loading="lazy"
                            >
                        @else
                            <div class="image-placeholder">✨</div>
                        @endif
                    </a>

                    <div class="product-info">
                        <div class="product-brand">{{ $product->brand }}</div>

                        <h2 class="product-name">
                            <a href="{{ route('products.show', $product->id) }}">
                                {{ $product->name }}
                            </a>
                        </h2>

                        <div class="product-price">
                            Rp{{ number_format($product->price, 0, ',', '.') }}
                        </div>

                        {{-- Rating dan jumlah ulasan --}}
                        <div class="product-rating">
                            @if($product->reviews_count > 0)
                                ★ {{ number_format($product->reviews_avg_rating, 1) }}
                                <span>
                                    ({{ $product->reviews_count }} ulasan)
                                </span>
                            @else
                                <span>Belum ada ulasan</span>
                            @endif
                        </div>

                        <div class="product-meta">
                            {{ $product->category->cat_title ?? 'Tanpa kategori' }}
                            · Stok: {{ $product->stock }}
                        </div>

                        <div class="product-actions">
                            <a href="{{ route('products.show', $product->id) }}"
                               class="detail-button">
                                Lihat Detail
                            </a>

                            @if($product->stock > 0)
                                <form action="{{ route('cart.add', $product->id) }}"
                                      method="POST">
                                    @csrf
                                    <button type="submit" class="cart-button">
                                        + Keranjang
                                    </button>
                                </form>
                            @else
                                <button type="button" disabled
                                        style="background:#aaa;color:white;">
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="pagination">
            {{ $products->links() }}
        </div>
    @else
        <div class="empty-products">
            <h3>Produk tidak ditemukan</h3>
            <p>Coba ubah kata pencarian atau filter yang digunakan.</p>
        </div>
    @endif

</div>
@endsection
