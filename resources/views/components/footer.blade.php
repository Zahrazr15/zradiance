@props(['mode' => 'user'])
@php($cats = \App\Models\Category::orderBy('cat_title')->limit(4)->get())

<footer class="footer">
    <div class="footer-container">

        <div class="footer-brand">
            <h2><x-logo :size="30" /></h2>
            <p>Temukan berbagai produk kecantikan pilihan untuk melengkapi kebutuhanmu.</p>
        </div>

        <div class="footer-section">
            <h3>Kategori</h3>
            @foreach($cats as $cat)
                <a href="{{ $mode === 'landing' ? route('login') : route('categories.show', $cat->cat_id) }}">{{ $cat->cat_title }}</a>
            @endforeach
        </div>

        <div class="footer-section">
            <h3>Menu</h3>
            @if($mode === 'landing')
                <a href="#home">Home</a>
                <a href="#info">Informasi</a>
                <a href="{{ route('login') }}">Login</a>
            @else
                <a href="{{ route('dashboard') }}">Home</a>
                <a href="{{ route('categories.index') }}">Kategori</a>
                <a href="{{ route('cart.index') }}">Keranjang</a>
            @endif
        </div>

    </div>

    <div class="footer-bottom">© {{ date('Y') }} Beauty Store. Sistem e-commerce produk kecantikan.</div>
</footer>