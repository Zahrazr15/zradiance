@extends('layouts.user')

@section('title', 'Produk Kategori - Beauty Store')

@push('styles')
    <style>
        .content {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 30px 60px;
        }

        .back:hover {
            color: var(--c-ink);
        }

        .page-header {
            background: var(--c-surface);
            padding: 35px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .page-header p {
            color: var(--c-ink-soft);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .product-card {
            background: var(--c-surface);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid var(--c-border);
            transition: 0.2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: var(--c-cream);
        }

        .product-info {
            padding: 20px;
        }

        .brand {
            font-size: 13px;
            color: var(--c-ink-soft);
            margin-bottom: 6px;
        }

        .product-name {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .description {
            color: var(--c-ink-soft);
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .price {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .stock {
            font-size: 13px;
            color: var(--c-ink-soft);
            margin-bottom: 15px;
        }

        .cart-button {
            display: block;
            width: 100%;
            border: none;
            background: var(--c-pink);
            color: var(--c-ink);
            padding: 11px;
            border-radius: 7px;
            cursor: pointer;
            font-size: 14px;
        }

        .cart-button:hover {
            background: var(--c-pink-strong);
        }

        .empty {
            background: var(--c-surface);
            padding: 50px;
            border-radius: 15px;
            text-align: center;
            color: var(--c-ink-soft);
        }

        @media (max-width: 1000px) {
            .product-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 750px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 500px) {
            .content {
                padding: 0 20px 40px;
            }

            .product-grid {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 26px;
            }
        }
    </style>
@endpush

@section('content')
<div class="content">

    <x-back-button :href="route('categories.index')" label="Kembali" />

    <div class="page-header">

        <h1>
            {{ $category->cat_title }}
        </h1>

        <p>
            Produk {{ $category->cat_title }} yang tersedia di Beauty Store.
        </p>

    </div>


    @if ($category->products->count() > 0)

        <div class="product-grid">

            @foreach ($category->products as $product)

                <div class="product-card">

                    @if ($product->image)

                        <img
                            src="{{ $product->image }}"
                            alt="{{ $product->name }}"
                            class="product-image"
                        >

                    @else

                        <div
                            class="product-image"
                            style="
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-size:50px;
                            "
                        >
                            ✨
                        </div>

                    @endif


                    <div class="product-info">

                        <div class="brand">
                            {{ $product->brand }}
                        </div>

                        <h2 class="product-name">
                            {{ $product->name }}
                        </h2>

                        @if ($product->description)

                            <p class="description">
                                {{ $product->description }}
                            </p>

                        @endif

                        <div class="price">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>

                        <div class="stock">
                            Stok: {{ $product->stock }}
                        </div>


                        @if ($product->stock > 0)

                            <form
                                action="{{ route('cart.add', $product->id) }}"
                                method="POST"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="cart-button"
                                >
                                    + Tambah ke Keranjang
                                </button>

                            </form>
                        <button type="button"
                                class="cart-button"
                                style="margin-top:8px;background:var(--c-cream);"
                                data-chat-ask="{{ $product->brand }} {{ $product->name }}"
                                data-chat-product="{{ $product->id }}">
                            💬 Tanya Chatbot
                        </button>
                        
                        @else

                            <button
                                type="button"
                                class="cart-button"
                                disabled
                                style="
                                    background:#aaa;
                                    cursor:not-allowed;
                                "
                            >
                                Stok Habis
                            </button>

                        @endif

                    </div>

                </div>

            @endforeach

        </div>

    @else

        <div class="empty">

            <h3 style="margin-bottom:10px;">
                Belum Ada Produk
            </h3>

            <p>
                Belum ada produk dalam kategori
                {{ $category->cat_title }}.
            </p>

        </div>

    @endif

</div>
@endsection