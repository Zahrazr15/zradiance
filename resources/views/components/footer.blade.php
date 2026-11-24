
@props(['mode' => 'user'])

@php
    $cats = \App\Models\Category::orderBy('cat_title')
        ->limit(4)
        ->get();
@endphp

<footer class="footer">
    <div class="footer-container">

        {{-- BRAND --}}
        <div class="footer-brand">
            <h2>
                <x-logo :size="54" background="light" />
            </h2>

            <p>
                Temukan produk skincare dan makeup pilihan
                untuk menemani rutinitas kecantikanmu.
            </p>
        </div>

        {{-- CATEGORY --}}
        <div class="footer-section">
            <h3>Kategori</h3>

            @foreach($cats as $cat)
                <a href="{{ $mode === 'landing' ? route('login') : route('categories.show', $cat->cat_id) }}">
                    {{ $cat->cat_title }}
                </a>
            @endforeach
        </div>

        {{-- QUICK LINKS --}}
        <div class="footer-section">
            <h3>Menu</h3>

            @if($mode === 'landing')
                <a href="#home">Home</a>
                <a href="#products">Produk Pilihan</a>
                <a href="#info">Informasi</a>
                <a href="{{ route('login') }}">Login</a>
            @else
                <a href="{{ route('dashboard') }}">Home</a>
                <a href="{{ route('products.index') }}">Produk</a>
                <a href="{{ route('cart.index') }}">Keranjang</a>
                <a href="{{ route('orders.index') }}">Pesanan</a>
            @endif
        </div>

    </div>

    <div class="footer-bottom">
        © {{ date('Y') }} ZRadiance. All rights reserved.
    </div>
</footer>
