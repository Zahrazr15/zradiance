@extends('layouts.user')

@section('title', $product->name . ' - Beauty Store')

@push('styles')
<style>
    .detail-container {
        max-width: 1200px;
        margin: 36px auto;
        padding: 0 32px 80px;
    }

    .detail-card {
        display: grid;
        grid-template-columns: 1fr 1.25fr;
        gap: 48px;
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 36px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 40px;
    }

    .detail-image-wrap {
        position: sticky;
        top: 100px;
        border-radius: var(--radius-md);
        overflow: hidden;
        background: linear-gradient(135deg, #FFF6EC, var(--c-cream));
        border: 1px solid var(--c-border-subtle);
        box-shadow: var(--shadow-xs);
    }

    .detail-image {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: cover;
        display: block;
        transition: transform 0.4s ease;
    }
    .detail-image:hover { transform: scale(1.03); }

    .detail-placeholder {
        aspect-ratio: 1 / 1;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, #FFF9F2, var(--c-cream));
        font-size: 80px;
    }

    .detail-brand {
        color: var(--c-ink-light);
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 6px;
    }

    .detail-name {
        font-size: 32px;
        font-weight: 800;
        color: var(--c-ink-heading);
        line-height: 1.3;
        margin-bottom: 12px;
    }

    .detail-meta-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .detail-price {
        font-size: 30px;
        font-weight: 800;
        color: var(--c-ink-heading);
        margin: 16px 0;
    }

    .detail-stock-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: var(--radius-pill);
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 24px;
    }
    .stock-in { background: #E3F6EC; color: #14633F; }
    .stock-out { background: #FDE7E4; color: #8E2B20; }

    .detail-actions {
        display: flex;
        gap: 14px;
        margin-bottom: 32px;
        flex-wrap: wrap;
    }

    .detail-cart {
        flex: 1;
        min-width: 200px;
        padding: 15px 28px;
        border: 0;
        border-radius: var(--radius-pill);
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        color: #fff;
        font: 800 15px var(--font);
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 14px rgba(233, 129, 158, 0.4);
    }
    .detail-cart:hover {
        background: linear-gradient(135deg, var(--c-pink-strong), #D46684);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(233, 129, 158, 0.5);
    }

    .detail-ask-bot {
        padding: 14px 22px;
        border-radius: var(--radius-pill);
        border: 1.5px solid var(--c-pink-strong);
        background: rgba(250, 172, 191, 0.15);
        color: var(--c-pink-strong);
        font: 800 14px var(--font);
        cursor: pointer;
        transition: var(--transition);
    }
    .detail-ask-bot:hover {
        background: var(--c-pink);
        color: var(--c-ink);
    }

    /* Product Specs / Accordions */
    .specs-grid {
        display: flex;
        flex-direction: column;
        gap: 16px;
        border-top: 1px solid var(--c-border-subtle);
        padding-top: 24px;
    }

    .spec-item {
        background: #FCF8F6;
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        padding: 18px 20px;
    }

    .spec-item h3 {
        font-size: 15px;
        font-weight: 800;
        color: var(--c-ink-heading);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .spec-item p {
        color: var(--c-ink-soft);
        line-height: 1.7;
        font-size: 14px;
        white-space: pre-line;
    }

    /* Review Card */
    .review-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 36px;
        box-shadow: var(--shadow-sm);
    }

    .review-card h2 {
        font-size: 24px;
        margin-bottom: 20px;
        color: var(--c-ink-heading);
    }

    .review-summary {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 24px;
        background: #FCF8F6;
        border-radius: var(--radius-md);
        margin-bottom: 28px;
    }

    .review-average {
        font-size: 38px;
        font-weight: 800;
        color: var(--c-ink-heading);
    }

    .review-stars {
        color: #E6A213;
        font-size: 18px;
        letter-spacing: 2px;
    }

    .review-item {
        padding: 20px 0;
        border-bottom: 1px solid var(--c-border-subtle);
    }
    .review-item:last-child { border-bottom: 0; }

    .review-user-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 6px;
    }

    .review-form {
        margin-top: 28px;
        padding: 28px;
        background: #FCF8F6;
        border-radius: var(--radius-md);
        border: 1px solid var(--c-border-subtle);
    }

    @media(max-width: 900px) {
        .detail-card {
            grid-template-columns: 1fr;
            gap: 32px;
            padding: 24px;
        }
        .detail-image-wrap { position: static; }
        .detail-name { font-size: 26px; }
        .detail-price { font-size: 24px; }
        .review-card { padding: 24px; }
    }
</style>
@endpush

@section('content')
<div class="detail-container">

    <div class="back-bar">
        <x-back-button
            :href="route('products.index')"
            label="← Kembali ke Katalog Produk"
        />
    </div>

    {{-- DETAIL PRODUK --}}
    <div class="detail-card">

        <div class="detail-image-wrap">
            @if($product->image)
                <img
                    src="{{ $product->image }}"
                    alt="{{ $product->name }}"
                    class="detail-image"
                    id="detailProductImage"
                >
            @else
                <div class="detail-placeholder">✨</div>
            @endif
        </div>

        <div>
            <div class="detail-brand">{{ $product->brand }}</div>

            <h1 class="detail-name">{{ $product->name }}</h1>

            <div class="detail-meta-row">
                <span class="badge">{{ $product->category->cat_title ?? 'Kategori Umum' }}</span>
                @if($product->reviews->count() > 0)
                    <span style="color:#E6A213;font-size:14px;font-weight:700;">
                        ★ {{ number_format($product->reviews->avg('rating'), 1) }} ({{ $product->reviews->count() }} ulasan)
                    </span>
                @endif
            </div>

            <div class="detail-price">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </div>

            <div class="detail-stock-badge {{ $product->stock > 0 ? 'stock-in' : 'stock-out' }}">
                @if($product->stock > 0)
                    ● Stok tersedia: {{ $product->stock }} unit
                @else
                    ✕ Stok sedang habis
                @endif
            </div>

            <div class="detail-actions">
                @if($product->stock > 0)
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" id="detailAddToCartForm" style="flex:1;">
                        @csrf
                        <button type="submit" class="detail-cart">
                            + Tambahkan ke Keranjang
                        </button>
                    </form>
                @endif

                <button
                    type="button"
                    class="detail-ask-bot"
                    data-chat-ask="{{ $product->brand }} {{ $product->name }}"
                    data-chat-product="{{ $product->id }}"
                >
                    💬 Tanya Chatbot
                </button>
            </div>

            {{-- SPESIFIKASI & KANDUNGAN --}}
            <div class="specs-grid">
                <div class="spec-item">
                    <h3>📖 Deskripsi Produk</h3>
                    <p>{{ $product->description ?: 'Belum ada deskripsi detail untuk produk ini.' }}</p>
                </div>

                @if($product->skin_type)
                <div class="spec-item">
                    <h3>🧴 Cocok untuk Jenis Kulit</h3>
                    <p>{{ $product->skin_type }}</p>
                </div>
                @endif

                @if($product->ingredients)
                <div class="spec-item">
                    <h3>🧪 Kandungan Utama (Ingredients)</h3>
                    <p>{{ $product->ingredients }}</p>
                </div>
                @endif

                @if($product->benefits)
                <div class="spec-item">
                    <h3>✨ Manfaat Produk</h3>
                    <p>{{ $product->benefits }}</p>
                </div>
                @endif

                @if($product->how_to_use)
                <div class="spec-item">
                    <h3>💡 Cara Penggunaan</h3>
                    <p>{{ $product->how_to_use }}</p>
                </div>
                @endif

                @if($product->warnings)
                <div class="spec-item" style="border-left:3px solid #E67E22;">
                    <h3>⚠️ Perhatian & Peringatan</h3>
                    <p>{{ $product->warnings }}</p>
                </div>
                @endif
            </div>

        </div>
    </div>

    {{-- ULASAN PELANGGAN --}}
    <div class="review-card">
        <h2>Ulasan Pelanggan ({{ $product->reviews->count() }})</h2>

        @if($product->reviews->count() > 0)
            <div class="review-summary">
                <div class="review-average">
                    {{ number_format($product->reviews->avg('rating'), 1) }}
                    <span style="font-size:18px;color:var(--c-ink-soft);">/ 5.0</span>
                </div>

                <div>
                    <div class="review-stars">
                        @php $avg = (int) round($product->reviews->avg('rating')); @endphp
                        {{ str_repeat('★', $avg) }}{{ str_repeat('☆', 5 - $avg) }}
                    </div>
                    <small style="color:var(--c-ink-soft);font-size:13px;">
                        Berdasarkan {{ $product->reviews->count() }} ulasan terverifikasi
                    </small>
                </div>
            </div>

            @foreach($product->reviews as $review)
                <div class="review-item">
                    <div class="review-user-row">
                        <strong>{{ $review->user->name ?? 'Pelanggan Terverifikasi' }}</strong>
                        <span style="font-size:12.5px;color:var(--c-ink-soft);">
                            {{ $review->created_at->format('d M Y') }}
                        </span>
                    </div>

                    <div class="review-stars" style="font-size:14px;margin-bottom:6px;">
                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                    </div>

                    <p style="color:var(--c-ink);font-size:14px;line-height:1.6;">
                        {{ $review->comment ?: 'Tidak ada komentar tertulis.' }}
                    </p>
                </div>
            @endforeach
        @else
            <p style="color:var(--c-ink-soft);padding:10px 0 20px;">
                Belum ada ulasan untuk produk ini. Jadilah yang pertama memberikan ulasan!
            </p>
        @endif

        {{-- FORM TULIS ULASAN --}}
        @if($canReview)
            <div class="review-form">
                <h3 style="font-size:18px;color:var(--c-ink-heading);margin-bottom:12px;">
                    {{ $myReview ? 'Perbarui Ulasan Anda' : 'Tulis Ulasan Produk' }}
                </h3>

                @if(session('success'))
                    <div class="alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('products.reviews.store', $product->id) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="rating">Rating Bintang</label>
                        <select name="rating" id="rating" required>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" @selected(old('rating', $myReview?->rating) == $i)>
                                    {{ $i }} Bintang {{ str_repeat('★', $i) }}
                                </option>
                            @endfor
                        </select>
                        @error('rating') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="comment">Pengalaman Anda</label>
                        <textarea
                            name="comment"
                            id="comment"
                            rows="4"
                            maxlength="2000"
                            placeholder="Ceritakan hasil dan tekstur produk ini setelah pemakaian..."
                        >{{ old('comment', $myReview?->comment) }}</textarea>
                        @error('comment') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ $myReview ? 'Simpan Perubahan Ulasan' : 'Kirim Ulasan' }}
                    </button>
                </form>
            </div>
        @else
            <div style="padding:16px 20px;background:#FAF5F6;border-radius:var(--radius-sm);margin-top:20px;font-size:13.5px;color:var(--c-ink-soft);">
                💡 Anda dapat menulis ulasan setelah membeli produk ini dan pesanan telah berhasil diselesaikan.
            </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('detailAddToCartForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;

        var img = document.getElementById('detailProductImage');
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
                    ghost.style.width = Math.min(startRect.width, 100) + 'px';
                    ghost.style.height = Math.min(startRect.height, 100) + 'px';
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
                        } else {
                            var newBadge = document.createElement('span');
                            newBadge.className = 'cart-badge bounce';
                            newBadge.id = 'navCartBadge';
                            newBadge.textContent = '1';
                            cartTarget.appendChild(newBadge);
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
</script>
@endpush
