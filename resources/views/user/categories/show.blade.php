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

    .empty-category-box {
        background: var(--c-surface);
        padding: 60px 24px;
        border-radius: var(--radius-lg);
        text-align: center;
        color: var(--c-ink-soft);
        border: 1px dashed var(--c-border);
    }

    @media (max-width: 1040px) {
    }

    @media (max-width: 768px) {
        .category-show-page { padding: 0 18px 60px; margin-top: 24px; }
        .category-banner { flex-direction: column; text-align: center; padding: 24px; }
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
                    <a href="{{ route('products.show', $product->id) }}" class="product-image-link">
                        @if ($product->image)
                            <img
                                src="{{ $product->image }}"
                                alt="{{ $product->name }}"
                                class="product-image"
                                loading="lazy"
                            >
                        @else
                            <div class="image-placeholder">✨</div>
                        @endif
                        <span class="product-view-hint">Lihat Produk</span>
                    </a>

                    <div class="product-info">
                        <span class="product-brand">{{ $product->brandRelation->brand_name ?? $product->brand }}</span>
                        <h2 class="product-name">
                            <a href="{{ route('products.show', $product->id) }}">{{ $product->name }}</a>
                        </h2>
                        <p class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <div class="product-actions">
                            @if ($product->stock > 0)
                                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="cat-add-cart-form" style="grid-column: span 2;">
                                    @csrf
                                    <button type="submit" class="cart-button">+ Keranjang</button>
                                </form>
                            @else
                                <button type="button" class="cart-button" disabled style="grid-column: span 2;">Stok Habis</button>
                            @endif
                            <button type="button" class="btn-ask-bot"
                                data-chat-ask="{{ $product->brandRelation->brand_name ?? $product->brand }} {{ $product->name }}"
                                data-chat-product="{{ $product->id }}">💬 Tanya Chatbot</button>
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
