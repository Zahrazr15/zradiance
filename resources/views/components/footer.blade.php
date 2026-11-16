@props(['mode' => 'user'])
@php($cats = \App\Models\Category::orderBy('cat_title')->limit(4)->get())

<footer class="footer">
    <div class="footer-container">

        <div class="footer-brand">
            <h2><x-logo :size="28" background="light" /></h2>
            <p>Platform e-commerce kecantikan terpercaya. Produk original, BPOM teruji, dan konsultasi cerdas bersama AI.</p>
        </div>

        <div class="footer-section">
            <h3>Kategori</h3>
            @foreach($cats as $cat)
                <a href="{{ $mode === 'landing' ? route('login') : route('categories.show', $cat->cat_id) }}">{{ $cat->cat_title }}</a>
            @endforeach
        </div>

        <div class="footer-section">
            <h3>Menu Cepat</h3>
            @if($mode === 'landing')
                <a href="#home">Home</a>
                <a href="#info">Informasi Sistem</a>
                <a href="{{ route('login') }}">Masuk Akun</a>
                <a href="{{ route('register') }}">Daftar Gratis</a>
            @else
                <a href="{{ route('dashboard') }}">Home</a>
                <a href="{{ route('products.index') }}">Products</a>
                <a href="{{ route('cart.index') }}">Keranjang Belanja</a>
                <a href="{{ route('orders.index') }}">Pesanan Saya</a>
            @endif
        </div>

    </div>

    <div class="footer-bottom">© {{ date('Y') }} ZRadiance. Sistem e-commerce produk kecantikan modern.</div>
</footer>
