
@extends('layouts.user')

@section('title', $product->name . ' - Beauty Store')

@push('styles')
<style>
    .detail-container {
        max-width: 1100px;
        margin: 40px auto;
        padding: 0 24px 60px;
    }

    .detail-card {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 35px;
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 16px;
        padding: 28px;
    }

    .detail-image {
        width: 100%;
        aspect-ratio: 1 / 1;
        object-fit: contain;
        background: var(--c-cream);
        border-radius: 12px;
    }

    .detail-placeholder {
        aspect-ratio: 1 / 1;
        display: grid;
        place-items: center;
        background: var(--c-cream);
        border-radius: 12px;
        font-size: 70px;
    }

    .detail-brand {
        color: var(--c-ink-soft);
        margin-bottom: 8px;
    }

    .detail-name {
        font-size: 30px;
        margin-bottom: 14px;
    }

    .detail-price {
        font-size: 27px;
        font-weight: 700;
        margin: 20px 0;
    }

    .detail-stock {
        color: var(--c-ink-soft);
        margin-bottom: 20px;
    }

    .detail-section {
        border-top: 1px solid var(--c-border);
        padding: 20px 0;
    }

    .detail-section h3 {
        margin-bottom: 9px;
        font-size: 17px;
    }

    .detail-section p {
        color: var(--c-ink-soft);
        line-height: 1.7;
        white-space: pre-line;
    }

    .detail-cart {
        display: block;
        width: 100%;
        padding: 13px;
        border: 0;
        border-radius: 8px;
        background: var(--c-pink);
        color: var(--c-ink);
        font-weight: 600;
        cursor: pointer;
    }

    .review-card {
        margin-top: 25px;
        padding: 25px;
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 16px;
    }

    .review-summary {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 18px 0;
    }

    .review-average {
        font-size: 30px;
        font-weight: 700;
    }

    .review-stars {
        color: #e5a000;
        letter-spacing: 2px;
    }

    .review-item {
        padding: 16px 0;
        border-bottom: 1px solid var(--c-border);
    }

    .review-item:last-child {
        border-bottom: 0;
    }

    .review-date {
        display: block;
        margin-top: 7px;
        color: var(--c-ink-soft);
        font-size: 12px;
    }

    .review-form label {
        display: block;
        margin-bottom: 7px;
        font-weight: 600;
    }

    .review-form select,
    .review-form textarea {
        display: block;
        width: 100%;
        padding: 12px;
        margin-bottom: 16px;
        border: 1px solid var(--c-border);
        border-radius: 8px;
        background: var(--c-bg);
        color: var(--c-ink);
        font: inherit;
    }

    .review-form textarea {
        resize: vertical;
    }

    .review-alert {
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 8px;
        background: var(--c-cream);
    }

    .review-error {
        color: #d93025;
        font-size: 13px;
        margin: -10px 0 12px;
    }

    @media(max-width: 750px) {
        .detail-card {
            grid-template-columns: 1fr;
            padding: 18px;
        }

        .detail-name {
            font-size: 24px;
        }

        .review-card {
            padding: 18px;
        }
    }
</style>
@endpush

@section('content')
<div class="detail-container">

    <x-back-button
        :href="route('products.index')"
        label="Kembali ke Produk"
    />

    {{-- DETAIL PRODUK --}}
    <div class="detail-card">

        <div>
            @if($product->image)
                <img
                    src="{{ $product->image }}"
                    alt="{{ $product->name }}"
                    class="detail-image"
                >
            @else
                <div class="detail-placeholder">✨</div>
            @endif
        </div>

        <div>
            <div class="detail-brand">{{ $product->brand }}</div>

            <h1 class="detail-name">{{ $product->name }}</h1>

            <div class="product-meta">
                {{ $product->category->cat_title ?? 'Tanpa kategori' }}
            </div>

            <div class="detail-price">
                Rp{{ number_format($product->price, 0, ',', '.') }}
            </div>

            <div class="detail-stock">
                @if($product->stock > 0)
                    Stok tersedia: {{ $product->stock }}
                @else
                    Stok sedang habis
                @endif
            </div>

            @if($product->stock > 0)
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="detail-cart">
                        + Tambahkan ke Keranjang
                    </button>
                </form>
            @endif

            <div class="detail-section">
                <h3>Deskripsi Produk</h3>
                <p>{{ $product->description ?: 'Belum ada deskripsi produk.' }}</p>
            </div>

            <div class="detail-section">
                <h3>Jenis Kulit</h3>
                <p>{{ $product->skin_type ?: 'Belum ada informasi.' }}</p>
            </div>

            <div class="detail-section">
                <h3>Kandungan</h3>
                <p>{{ $product->ingredients ?: 'Belum ada informasi.' }}</p>
            </div>

            <div class="detail-section">
                <h3>Manfaat</h3>
                <p>{{ $product->benefits ?: 'Belum ada informasi.' }}</p>
            </div>

            <div class="detail-section">
                <h3>Cara Penggunaan</h3>
                <p>{{ $product->how_to_use ?: 'Belum ada informasi.' }}</p>
            </div>

            <div class="detail-section">
                <h3>Perhatian</h3>
                <p>{{ $product->warnings ?: 'Tidak ada informasi tambahan.' }}</p>
            </div>
        </div>
    </div>

    {{-- ULASAN PELANGGAN --}}
    <div class="review-card">

        <h2>Ulasan Pelanggan ({{ $product->reviews->count() }})</h2>

        @if($product->reviews->count() > 0)

            <div class="review-summary">
                <span class="review-average">
                    {{ number_format($product->reviews->avg('rating'), 1) }}/5
                </span>

                <div>
                    <div class="review-stars">
                        @php
                            $averageRating = (int) round($product->reviews->avg('rating'));
                        @endphp

                        {{ str_repeat('★', $averageRating) }}{{ str_repeat('☆', 5 - $averageRating) }}
                    </div>

                    <small style="color:var(--c-ink-soft);">
                        Berdasarkan {{ $product->reviews->count() }} ulasan
                    </small>
                </div>
            </div>

            @foreach($product->reviews as $review)
                <div class="review-item">

                    <strong>
                        {{ $review->user->name ?? 'Pelanggan' }}
                    </strong>

                    <div class="review-stars" style="margin:5px 0;">
                        {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                    </div>

                    <p>
                        {{ $review->comment ?: 'Tidak ada komentar.' }}
                    </p>

                    <span class="review-date">
                        {{ $review->created_at->format('d M Y') }}
                    </span>

                </div>
            @endforeach

        @else
            <p style="color:var(--c-ink-soft);margin-top:15px;">
                Belum ada ulasan untuk produk ini.
            </p>
        @endif

        {{-- FORM REVIEW --}}
        <div class="detail-section">

            @if(session('success'))
                <div class="review-alert">
                    {{ session('success') }}
                </div>
            @endif

            @if($canReview)

                <h3>
                    {{ $myReview ? 'Perbarui Ulasanmu' : 'Tulis Ulasan' }}
                </h3>

                <form
                    action="{{ route('products.reviews.store', $product->id) }}"
                    method="POST"
                    class="review-form"
                >
                    @csrf

                    <label for="rating">Rating</label>

                    <select name="rating" id="rating" required>
                        @for($i = 5; $i >= 1; $i--)
                            <option
                                value="{{ $i }}"
                                @selected(old('rating', $myReview?->rating) == $i)
                            >
                                {{ $i }} Bintang
                            </option>
                        @endfor
                    </select>

                    @error('rating')
                        <div class="review-error">{{ $message }}</div>
                    @enderror

                    <label for="comment">Komentar</label>

                    <textarea
                        name="comment"
                        id="comment"
                        rows="4"
                        maxlength="2000"
                        placeholder="Ceritakan pengalamanmu menggunakan produk ini..."
                    >{{ old('comment', $myReview?->comment) }}</textarea>

                    @error('comment')
                        <div class="review-error">{{ $message }}</div>
                    @enderror

                    @error('review')
                        <div class="review-error">{{ $message }}</div>
                    @enderror

                    <button type="submit" class="detail-cart">
                        {{ $myReview ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                    </button>

                </form>

            @else

                <p style="font-size:13px;">
                    Kamu dapat memberikan ulasan setelah pesanan produk ini
                    selesai dan pembayarannya berhasil.
                </p>

            @endif

        </div>
    </div>

</div>
@endsection
