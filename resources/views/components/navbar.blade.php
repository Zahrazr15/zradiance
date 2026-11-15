@props(['mode' => 'user'])

<div class="navbar-floating-wrapper">
    <nav class="navbar" id="mainNavbar">
        <div class="navbar-container">

            <a href="{{ $mode === 'landing' ? route('home') : route('dashboard') }}" class="logo">
                <x-logo />
            </a>

            <button class="nav-toggle" type="button" aria-label="Buka menu"
                    onclick="this.closest('.navbar').classList.toggle('open')">☰</button>

            <div class="nav-links">
                @if($mode === 'landing')
                    {{-- LANDING --}}
                    <a href="#home">Home</a>
                    <a href="#info">Informasi</a>

                    @auth
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="nav-register">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-login">Login</a>
                        <a href="{{ route('register') }}" class="nav-register">Registrasi</a>
                    @endauth
                @else
                    {{-- USER --}}
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Products</a>
                    <a href="{{ route('cart.index') }}" class="nav-cart {{ request()->routeIs('cart.*') ? 'active' : '' }}" id="navCartButton" aria-label="Keranjang">
                        🛒
                        @if(($cartCount ?? 0) > 0)
                        <span class="cart-badge" id="navCartBadge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile', 'profile.*', 'orders.*') ? 'active' : '' }}">Profil</a>
                @endif
            </div>

        </div>
    </nav>
</div>

<script>
(function() {
    var nb = document.getElementById('mainNavbar');
    if (nb) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 20) {
                nb.classList.add('scrolled');
            } else {
                nb.classList.remove('scrolled');
            }
        }, { passive: true });
    }
})();
</script>
