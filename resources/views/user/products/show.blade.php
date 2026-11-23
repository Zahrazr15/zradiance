@extends('layouts.user')

@section('title', $product->name . ' - Beauty Store')

@push('styles')
<style>
    .detail-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 32px 24px 80px;
    }

    .detail-back {
        margin-bottom: 22px;
    }

    .detail-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.15fr);
        gap: 32px;
        align-items: start;
    }

    .detail-card,
    .review-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: 24px;
        box-shadow: var(--shadow-sm);
    }

    .detail-card {
        padding: 26px;
        margin-bottom: 32px;
    }

    .detail-image-column {
        position: sticky;
        top: 100px;
    }

    .detail-image-wrap {
        border-radius: 20px;
        overflow: hidden;
        background: linear-gradient(135deg, #FFF6EC, #FFF0F4);
        border: 1px solid var(--c-border-subtle);
        position: relative;
    }

    .detail-image {
        display: block;
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: contain;
        padding: 20px;
        transition: transform .4s ease;
    }

    .detail-image-wrap:hover .detail-image {
        transform: scale(1.035);
    }

    .detail-placeholder {
        aspect-ratio: 1 / 1;
        display: grid;
        place-items: center;
        font-size: 80px;
        background: linear-gradient(135deg, #FFF9F2, #FFEABB);
    }

    .detail-image-caption {
        padding: 12px;
        text-align: center;
        font-size: 12px;
        color: var(--c-ink-soft);
    }

    .detail-info {
        min-width: 0;
    }

    .detail-breadcrumb {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
        font-size: 12px;
        color: var(--c-ink-soft);
        margin-bottom: 16px;
    }

    .detail-breadcrumb a {
        color: var(--c-ink-soft);
        text-decoration: none;
    }

    .detail-breadcrumb a:hover {
        color: var(--c-pink-strong);
    }

    .detail-labels {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 16px;
    }

    .detail-brand-badge,
    .detail-category-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none;
        transition: .2s ease;
    }

    .detail-brand-badge {
        background: #F8E8EE;
        color: #9B4C68;
        border: 1px solid #F0CFDA;
    }

    .detail-brand-badge:hover {
        background: #F3D7E1;
        transform: translateY(-1px);
    }

    .detail-category-badge {
        background: #F3EEE8;
        color: #766455;
        border: 1px solid #E6DCD0;
    }

    .detail-category-badge:hover {
        background: #EAE0D5;
        transform: translateY(-1px);
    }

    .detail-brand-logo {
        width: 24px;
        height: 24px;
        object-fit: contain;
        border-radius: 50%;
        background: #fff;
        padding: 2px;
    }

    .detail-name {
        font-size: clamp(25px, 3vw, 35px);
        font-weight: 900;
        color: var(--c-ink-heading);
        line-height: 1.3;
        margin: 0 0 14px;
    }

    .detail-rating {
        display: flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
        font-size: 13px;
        margin-bottom: 14px;
    }

    .detail-rating-stars {
        color: #E6A213;
        letter-spacing: 1px;
        font-size: 16px;
    }

    .detail-rating a {
        color: var(--c-ink-soft);
        text-decoration: none;
    }

    .detail-rating a:hover {
        color: var(--c-pink-strong);
        text-decoration: underline;
    }

    .detail-price {
        font-size: clamp(26px, 3vw, 34px);
        font-weight: 900;
        color: var(--c-ink-heading);
        margin: 18px 0 12px;
    }

    .detail-stock {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 20px;
    }

    .stock-in {
        background: #E3F6EC;
        color: #14633F;
    }

    .stock-out {
        background: #FDE7E4;
        color: #8E2B20;
    }

    .detail-short-description {
        color: var(--c-ink-soft);
        line-height: 1.75;
        font-size: 14px;
        margin-bottom: 22px;
    }

    .detail-actions {
        display: flex;
        gap: 12px;
        margin-bottom: 26px;
        flex-wrap: wrap;
    }

    .detail-cart-form {
        flex: 1;
        min-width: 200px;
    }

    .detail-cart {
        width: 100%;
        padding: 15px 24px;
        border: 0;
        border-radius: 999px;
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        color: #fff;
        font: 800 14px var(--font);
        cursor: pointer;
        transition: var(--transition);
        box-shadow: 0 4px 14px rgba(233,129,158,.35);
    }

    .detail-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(233,129,158,.45);
    }

    .detail-cart:disabled {
        opacity: .65;
        cursor: wait;
        transform: none;
    }

    .detail-ask-bot {
        padding: 14px 20px;
        border-radius: 999px;
        border: 1.5px solid var(--c-pink-strong);
        background: rgba(250,172,191,.12);
        color: var(--c-pink-strong);
        font: 800 13px var(--font);
        cursor: pointer;
        transition: var(--transition);
    }

    .detail-ask-bot:hover {
        background: var(--c-pink);
        color: var(--c-ink);
    }

    .detail-benefit-highlights {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 24px;
    }

    .detail-highlight {
        border: 1px solid var(--c-border-subtle);
        background: #FFFAF8;
        border-radius: 14px;
        padding: 13px 10px;
        text-align: center;
    }

    .detail-highlight-icon {
        font-size: 20px;
        display: block;
        margin-bottom: 5px;
    }

    .detail-highlight-text {
        color: var(--c-ink-soft);
        font-size: 11px;
        font-weight: 700;
    }

    /* Spesifikasi produk */
    .specs-section {
        margin-top: 28px;
    }

    .specs-heading {
        font-size: 19px;
        font-weight: 900;
        color: var(--c-ink-heading);
        margin-bottom: 14px;
    }

    .specs-grid {
        display: grid;
        gap: 12px;
    }

    .spec-item {
        background: #FCF8F6;
        border: 1px solid var(--c-border-subtle);
        border-radius: 16px;
        padding: 17px 18px;
    }

    .spec-item h3 {
        font-size: 14px;
        font-weight: 900;
        color: var(--c-ink-heading);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .spec-item p {
        color: var(--c-ink-soft);
        line-height: 1.75;
        font-size: 13.5px;
        white-space: pre-line;
        margin: 0;
    }

    .spec-warning {
        border-left: 4px solid #E67E22;
        background: #FFF9F0;
    }

    /* Reviews */
    .review-card {
        padding: 30px;
    }

    .review-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 22px;
    }

    .review-heading h2 {
        font-size: 23px;
        color: var(--c-ink-heading);
        font-weight: 900;
        margin: 0;
    }

    .review-summary {
        display: flex;
        align-items: center;
        gap: 22px;
        padding: 22px;
        background: #FCF8F6;
        border: 1px solid var(--c-border-subtle);
        border-radius: 18px;
        margin-bottom: 26px;
    }

    .review-average {
        font-size: 40px;
        font-weight: 900;
        color: var(--c-ink-heading);
        line-height: 1;
    }

    .review-stars {
        color: #E6A213;
        font-size: 18px;
        letter-spacing: 2px;
    }

    .review-summary-caption {
        color: var(--c-ink-soft);
        font-size: 12px;
        margin-top: 5px;
    }

    .review-item {
        padding: 20px 0;
        border-bottom: 1px solid var(--c-border-subtle);
    }

    .review-item:last-child {
        border-bottom: 0;
    }

    .review-user-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 7px;
    }

    .review-user-name {
        font-size: 14px;
        font-weight: 800;
        color: var(--c-ink-heading);
    }

    .review-date {
        font-size: 12px;
        color: var(--c-ink-soft);
    }

    .review-comment {
        color: var(--c-ink);
        font-size: 14px;
        line-height: 1.7;
        margin-top: 8px;
        white-space: pre-line;
    }

    .review-empty {
        padding: 26px 18px;
        text-align: center;
        border-radius: 16px;
        background: #FCF8F6;
        color: var(--c-ink-soft);
        margin-bottom: 20px;
    }

    .review-form {
        margin-top: 28px;
        padding: 26px;
        background: #FCF8F6;
        border-radius: 18px;
        border: 1px solid var(--c-border-subtle);
    }

    .review-form h3 {
        font-size: 18px;
        font-weight: 900;
        color: var(--c-ink-heading);
        margin-bottom: 16px;
    }

    .review-form .form-group {
        margin-bottom: 18px;
    }

    .review-form label {
        display: block;
        font-weight: 800;
        color: var(--c-ink-heading);
        font-size: 13px;
        margin-bottom: 8px;
    }

    .review-form select,
    .review-form textarea {
        width: 100%;
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 12px 14px;
        background: #fff;
        color: var(--c-ink);
        font: 14px var(--font);
    }

    .review-form select:focus,
    .review-form textarea:focus {
        outline: none;
        border-color: var(--c-pink-strong);
        box-shadow: 0 0 0 3px rgba(233,129,158,.12);
    }

    .review-form textarea {
        resize: vertical;
        line-height: 1.6;
    }

    .review-notice {
        padding: 15px 18px;
        background: #FAF5F6;
        border: 1px solid var(--c-border-subtle);
        border-radius: 14px;
        margin-top: 20px;
        font-size: 13px;
        line-height: 1.6;
        color: var(--c-ink-soft);
    }

    .review-notice a {
        color: var(--c-pink-strong);
        font-weight: 800;
    }

    .detail-divider {
        border: 0;
        border-top: 1px solid var(--c-border-subtle);
        margin: 24px 0;
    }

    @media(max-width: 900px) {
        .detail-layout {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .detail-image-column {
            position: static;
        }

        .detail-image-wrap {
            max-width: 620px;
            margin: 0 auto;
        }

        .detail-card {
            padding: 22px;
        }
    }

    @media(max-width: 600px) {
        .detail-container {
            padding: 20px 15px 55px;
        }

        .detail-card,
        .review-card {
            padding: 18px;
            border-radius: 18px;
        }

        .detail-name {
            font-size: 25px;
        }

        .detail-price {
            font-size: 27px;
        }

        .detail-actions {
            display: grid;
            grid-template-columns: 1fr;
        }

        .detail-cart-form {
            min-width: 0;
        }

        .detail-ask-bot {
            width: 100%;
        }

        .detail-benefit-highlights {
            gap: 7px;
        }

        .detail-highlight {
            padding: 10px 5px;
        }

        .detail-highlight-text {
            font-size: 10px;
        }

        .review-summary {
            padding: 17px;
            gap: 15px;
        }

        .review-average {
            font-size: 32px;
        }

        .review-card {
            padding: 18px;
        }

        .review-form {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="detail-container">

    {{-- KEMBALI KE KATALOG --}}
    <div class="detail-back">
        <x-back-button
            :href="route('products.index')"
            label="← Kembali ke Katalog Produk"
        />
    </div>

    {{-- DETAIL PRODUK --}}
    <div class="detail-card">
        <div class="detail-layout">

            {{-- FOTO --}}
            <div class="detail-image-column">
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
                <div class="detail-image-caption">
                    Foto produk {{ $product->name }}
                </div>
            </div>

            {{-- INFORMASI --}}
            <div class="detail-info">

                <div class="detail-breadcrumb">
                    <a href="{{ route('products.index') }}">Katalog</a>
                    <span>›</span>
                    @if($product->category)
                        <a href="{{ route('products.index', ['category' => $product->product_cat]) }}">
                            {{ $product->category->cat_title }}
                        </a>
                    @else
                        <span>Kategori</span>
                    @endif
                </div>

                {{-- BADGE BRAND DAN KATEGORI --}}
                <div class="detail-labels">

                    @if($product->brandRelation)
                        <a
                            href="{{ route('products.index', ['brand' => $product->brand_id]) }}"
                            class="detail-brand-badge"
                            title="Lihat produk {{ $product->brandRelation->brand_name }}"
                        >
                            @if($product->brandRelation->brand_logo)
                                <img
                                    src="{{ asset('storage/' . $product->brandRelation->brand_logo) }}"
                                    alt=""
                                    class="detail-brand-logo"
                                >
                            @endif

                            {{ $product->brandRelation->brand_name }}
                            <span>↗</span>
                        </a>
                    @elseif($product->brand)
                        <span class="detail-brand-badge">
                            {{ $product->brand }}
                        </span>
                    @endif

                    @if($product->category)
                        <a
                            href="{{ route('products.index', ['category' => $product->product_cat]) }}"
                            class="detail-category-badge"
                            title="Lihat kategori {{ $product->category->cat_title }}"
                        >
                            {{ $product->category->cat_title }}
                            <span>↗</span>
                        </a>
                    @else
                        <span class="detail-category-badge">
                            Kategori Umum
                        </span>
                    @endif
                </div>

                <h1 class="detail-name">{{ $product->name }}</h1>

                {{-- RATING --}}
                <div class="detail-rating">
                    @if($product->reviews->count() > 0)
                        <span class="detail-rating-stars">
                            ★ {{ number_format($product->reviews->avg('rating'), 1) }}
                        </span>

                        <a href="#customer-reviews">
                            ({{ $product->reviews->count() }} ulasan)
                        </a>
                    @else
                        <span style="color:var(--c-ink-soft);">
                            Belum ada ulasan
                        </span>
                    @endif
                </div>

                {{-- HARGA --}}
                <div class="detail-price">
                    Rp{{ number_format($product->price, 0, ',', '.') }}
                </div>

                {{-- STOK --}}
                <div class="detail-stock {{ $product->stock > 0 ? 'stock-in' : 'stock-out' }}">
                    @if($product->stock > 0)
                        ● Stok tersedia: {{ $product->stock }} unit
                    @else
                        ✕ Stok sedang habis
                    @endif
                </div>

                {{-- DESKRIPSI --}}
                <p class="detail-short-description">
                    {{ $product->description ?: 'Belum ada deskripsi detail untuk produk ini.' }}
                </p>

                {{-- HIGHLIGHT --}}
                <div class="detail-benefit-highlights">
                    <div class="detail-highlight">
                        <span class="detail-highlight-icon">🧴</span>
                        <span class="detail-highlight-text">Perawatan Kulit</span>
                    </div>
                    <div class="detail-highlight">
                        <span class="detail-highlight-icon">✨</span>
                        <span class="detail-highlight-text">Produk Pilihan</span>
                    </div>
                    <div class="detail-highlight">
                        <span class="detail-highlight-icon">💗</span>
                        <span class="detail-highlight-text">Beauty Essentials</span>
                    </div>
                </div>

                {{-- ACTION --}}
                <div class="detail-actions">

                    @if($product->stock > 0)
                        <form
                            action="{{ route('cart.add', $product->id) }}"
                            method="POST"
                            id="detailAddToCartForm"
                            class="detail-cart-form"
                        >
                            @csrf

                            <button type="submit" class="detail-cart">
                                + Tambahkan ke Keranjang
                            </button>
                        </form>
                    @else
                        <button type="button" class="detail-cart" disabled style="opacity:.5;cursor:not-allowed;">
                            Stok Habis
                        </button>
                    @endif

                    <button
                        type="button"
                        class="detail-ask-bot"
                        data-chat-ask="{{ $product->brandRelation->brand_name ?? $product->brand }} {{ $product->name }}"
                        data-chat-product="{{ $product->id }}"
                    >
                        💬 Tanya Chatbot
                    </button>
                </div>

                {{-- SPESIFIKASI --}}
                <div class="specs-section">
                    <h2 class="specs-heading">Informasi Produk</h2>

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
                            <div class="spec-item spec-warning">
                                <h3>⚠️ Perhatian & Peringatan</h3>
                                <p>{{ $product->warnings }}</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ULASAN --}}
    <div class="review-card" id="customer-reviews">

        <div class="review-heading">
            <h2>Ulasan Pelanggan</h2>
            <span style="color:var(--c-ink-soft);font-size:13px;font-weight:700;">
                {{ $product->reviews->count() }} ulasan
            </span>
        </div>

        @if($product->reviews->count() > 0)

            {{-- RINGKASAN RATING --}}
            <div class="review-summary">
                <div class="review-average">
                    {{ number_format($product->reviews->avg('rating'), 1) }}
                    <span style="font-size:17px;color:var(--c-ink-soft);">/ 5</span>
                </div>

                <div>
                    <div class="review-stars">
                        @php
                            $avg = (int) round($product->reviews->avg('rating'));
                        @endphp

                        {{ str_repeat('★', $avg) }}{{ str_repeat('☆', 5 - $avg) }}
                    </div>

                    <div class="review-summary-caption">
                        Berdasarkan {{ $product->reviews->count() }} ulasan pelanggan
                    </div>
                </div>
            </div>

            {{-- LIST ULASAN --}}
            @foreach($product->reviews as $review)
                <div class="review-item">
                    <div class="review-user-row">
                        <span class="review-user-name">
                            {{ $review->user->name ?? 'Pelanggan Terverifikasi' }}
                        </span>

                        <span class="review-date">
                            {{ $review->created_at->format('d M Y') }}
                        </span>
                    </div>

                    <div class="review-stars" style="font-size:14px;">
                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                    </div>

                    <p class="review-comment">
                        {{ $review->comment ?: 'Tidak ada komentar tertulis.' }}
                    </p>
                </div>
            @endforeach

        @else
            <div class="review-empty">
                <div style="font-size:32px;margin-bottom:8px;">💬</div>
                <strong>Belum ada ulasan</strong>
                <p style="margin:6px 0 0;">
                    Jadilah pelanggan pertama yang memberikan ulasan untuk produk ini.
                </p>
            </div>
        @endif

        {{-- FORM REVIEW --}}
        @if($canReview)

            <div class="review-form">
                <h3>
                    {{ $myReview ? 'Perbarui Ulasan Anda' : 'Tulis Ulasan Produk' }}
                </h3>

                @if(session('success'))
                    <div class="alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form
                    action="{{ route('products.reviews.store', $product->id) }}"
                    method="POST"
                >
                    @csrf

                    <div class="form-group">
                        <label for="rating">Rating Bintang</label>

                        <select name="rating" id="rating" required>
                            @for($i = 5; $i >= 1; $i--)
                                <option
                                    value="{{ $i }}"
                                    @selected(old('rating', $myReview?->rating) == $i)
                                >
                                    {{ $i }} Bintang {{ str_repeat('★', $i) }}
                                </option>
                            @endfor
                        </select>

                        @error('rating')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
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

                        @error('comment')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        {{ $myReview ? 'Simpan Perubahan Ulasan' : 'Kirim Ulasan' }}
                    </button>
                </form>
            </div>

        @else

            <div class="review-notice">
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

        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Menambahkan...';
        }

        var img = document.getElementById('detailProductImage');
        var cartTarget = document.getElementById('navCartButton')
            || document.querySelector('.nav-cart');

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

                            ghost.style.transform =
                                'translate(' + (targetX - startRect.left) + 'px, ' +
                                (targetY - startRect.top) + 'px) scale(0.2)';

                            ghost.style.opacity = '0.2';
                        });
                    });

                    setTimeout(function () {
                        ghost.remove();

                        var badge = document.getElementById('navCartBadge')
                            || document.querySelector('.cart-badge');

                        if (badge) {
                            badge.classList.remove('bounce');
                            void badge.offsetWidth;
                            badge.classList.add('bounce');

                            var count = parseInt(badge.textContent.trim()) || 0;

                            badge.textContent = (count + 1) > 99
                                ? '99+'
                                : (count + 1);
                        } else {
                            var newBadge = document.createElement('span');

                            newBadge.className = 'cart-badge bounce';
                            newBadge.id = 'navCartBadge';
                            newBadge.textContent = '1';

                            cartTarget.appendChild(newBadge);
                        }

                        if (btn) {
                            btn.disabled = false;
                            btn.textContent = '+ Tambahkan ke Keranjang';
                        }
                    }, 750);

                } else {
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = '+ Tambahkan ke Keranjang';
                    }
                }
            } else {
                throw new Error('Gagal menambahkan produk ke keranjang.');
            }
        })
        .catch(function () {
            if (btn) {
                btn.disabled = false;
                btn.textContent = '+ Tambahkan ke Keranjang';
            }

            form.submit();
        });
    });
});
</script>
@endpush