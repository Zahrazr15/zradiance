@extends('layouts.user')

@section('title', $category->cat_title . ' - Beauty Store')

@push('styles')
<style>
    .category-show-page {
        max-width: 1240px;
        margin: 36px auto;
        padding: 0 32px 80px;
    }

    .category-banner {
        background: linear-gradient(135deg, rgba(250, 172, 191, 0.25) 0%, rgba(255, 234, 187, 0.35) 100%);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 38px 36px;
        margin-bottom: 36px;
        display: flex;
        align-items: center;
        gap: 24px;
        box-shadow: var(--shadow-sm);
    }

    .category-banner h1 {
        font-size: 32px;
        margin-bottom: 6px;
        color: var(--c-ink-heading);
    }

    .category-banner p {
        color: var(--c-ink-soft);
        font-size: 15px;
        line-height: 1.6;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 24px;
    }

    .product-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        overflow: hidden;
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-xs);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
        border-color: var(--c-blush);
    }

    .product-image-wrap {
        position: relative;
        padding-top: 88%;
        background: linear-gradient(135deg, #FFF6EC, var(--c-cream));
        overflow: hidden;
    }

    .product-image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    .product-placeholder {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 46px;
    }

    .product-info {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .brand {
        font-size: 11.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--c-ink-light);
        margin-bottom: 4px;
    }

    .product-name {
        font-size: 15.5px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--c-ink-heading);
        line-height: 1.4;
    }

    .product-name a {
        color: inherit;
        text-decoration: none;
    }
    .product-name a:hover { color: var(--c-pink-strong); }

    .price {
        font-size: 17.5px;
        font-weight: 800;
        color: var(--c-ink-heading);
        margin-bottom: 6px;
    }

    .stock {
        font-size: 12px;
        color: var(--c-ink-soft);
        margin-bottom: 14px;
    }

    .actions-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        margin-top: auto;
    }

    .cart-btn {
        width: 100%;
        border: 0;
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        color: #fff;
        padding: 10px 14px;
        border-radius: var(--radius-pill);
        cursor: pointer;
        font: 800 13px var(--font);
        transition: var(--transition);
        box-shadow: 0 2px 8px rgba(233, 129, 158, 0.3);
    }
    .cart-btn:hover {
        background: linear-gradient(135deg, var(--c-pink-strong), #D46684);
        transform: translateY(-1px);
    }

    .bot-btn {
        width: 100%;
        border: 1px dashed var(--c-pink-strong);
        background: rgba(250, 172, 191, 0.12);
        color: var(--c-pink-strong);
        padding: 7px 12px;
        border-radius: var(--radius-pill);
        cursor: pointer;
        font: 700 12px var(--font);
        transition: var(--transition);
    }
    .bot-btn:hover {
        background: var(--c-pink);
        color: var(--c-ink);
    }

    .empty-category-box {
        background: var(--c-surface);
        padding: 60px 24px;
        border-radius: var(--radius-lg);
        text-align: center;
        color: var(--c-ink-soft);
        border: 1px dashed var(--c-border);
    }

    @media (max-width: 1040px) {
        .product-grid { grid-template-columns: repeat(3, 1fr); }
    }

    @media (max-width: 768px) {
        .category-show-page { padding: 0 18px 60px; margin-top: 24px; }
        .category-banner { flex-direction: column; text-align: center; padding: 24px; }
        .product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .product-info { padding: 14px; }
        .product-name { font-size: 14px; }
        .price { font-size: 15px; }
    }

    @media (max-width: 420px) {
        .product-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="category-show-page">

    <div class="back-bar">
        <x-back-button :href="route('categories.index')" label="← Kembali ke Daftar Kategori" />
    </div>

    <div class="category-banner">
        <x-category-icon :title="$category->cat_title" />
        <div>
            <h1>{{ $category->cat_title }}</h1>
            <p>{{ $category->description ?: 'Temukan produk ' . $category->cat_title . ' pilihan dengan mutu terjamin.' }}</p>
        </div>
    </div>

    @if ($category->products->count() > 0)
        <div class="product-grid">
            @foreach ($category->products as $product)
                <div class="product-card">
                    <a href="{{ route('products.show', $product->id) }}" class="product-image-wrap">
                        @if ($product->image)
                            <img
                                src="{{ $product->image }}"
                                alt="{{ $product->name }}"
                                class="product-image"
                                loading="lazy"
                            >
                        @else
                            <div class="product-placeholder">✨</div>
                        @endif
                    </a>

                    <div class="product-info">
                        <div class="brand">{{ $product->brand }}</div>

                        <h2 class="product-name">
                            <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                        </h2>

                        <div class="price">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>

                        <div class="stock">
                            ● Stok tersedia: {{ $product->stock }}
                        </div>

                        <div class="actions-row">
                            @if ($product->stock > 0)
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="cat-add-cart-form">
                                    @csrf
                                    <button type="submit" class="cart-btn">
                                        + Keranjang
                                    </button>
                                </form>
                                <button
                                    type="button"
                                    class="bot-btn"
                                    data-chat-ask="{{ $product->brand }} {{ $product->name }}"
                                    data-chat-product="{{ $product->id }}"
                                >
                                    💬 Tanya Chatbot
                                </button>
                            @else
                                <button type="button" class="cart-btn" disabled style="background:#ddd;color:#888;cursor:not-allowed;box-shadow:none;">
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-category-box">
            <h3 style="font-size:20px;color:var(--c-ink-heading);margin-bottom:8px;">Belum Ada Produk</h3>
            <p>Belum ada produk aktif yang terdaftar dalam kategori {{ $category->cat_title }}.</p>
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.cat-add-cart-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            var card = form.closest('.product-card');
            var img = card ? card.querySelector('.product-image') : null;
            var cartTarget = document.getElementById('navCartButton') || document.querySelector('.nav-cart');

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                }
            })
            .then(function (res) {
                if (res.ok || res.status === 302 || res.type === 'opaqueredirect') {
                    if (img && cartTarget) {
                        var startRect = img.getBoundingClientRect();
                        var endRect = cartTarget.getBoundingClientRect();

                        var ghost = document.createElement('img');
                        ghost.src = img.src;
                        ghost.className = 'flying-product-ghost';
                        ghost.style.left = startRect.left + 'px';
                        ghost.style.top = startRect.top + 'px';
                        ghost.style.width = Math.min(startRect.width, 90) + 'px';
                        ghost.style.height = Math.min(startRect.height, 90) + 'px';
                        document.body.appendChild(ghost);

                        requestAnimationFrame(function () {
                            requestAnimationFrame(function () {
                                var targetX = endRect.left + (endRect.width / 2) - 15;
                                var targetY = endRect.top + (endRect.height / 2) - 15;
                                ghost.style.transform = 'translate(' + (targetX - startRect.left) + 'px, ' + (targetY - startRect.top) + 'px) scale(0.2)';
                                ghost.style.opacity = '0.2';
                            });
                        });

                        setTimeout(function () {
                            ghost.remove();
                            var badge = document.getElementById('navCartBadge') || document.querySelector('.cart-badge');
                            if (badge) {
                                badge.classList.remove('bounce');
                                void badge.offsetWidth;
                                badge.classList.add('bounce');
                                var count = parseInt(badge.textContent.trim()) || 0;
                                badge.textContent = (count + 1) > 99 ? '99+' : (count + 1);
                            }
                            if (btn) btn.disabled = false;
                        }, 750);
                    } else {
                        if (btn) btn.disabled = false;
                    }
                } else {
                    throw new Error('Gagal');
                }
            })
            .catch(function () {
                if (btn) btn.disabled = false;
                form.submit();
            });
        });
    });
});
</script>
@endpush
