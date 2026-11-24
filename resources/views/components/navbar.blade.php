@props(['mode' => 'user'])

<div class="navbar-floating-wrapper">
    <nav class="navbar" id="mainNavbar">
        <div class="navbar-container">

            {{-- LOGO --}}
            <a href="{{ $mode === 'landing' ? route('home') : route('dashboard') }}" class="logo">
                <x-logo background="light" :tight="true" />
            </a>

            {{-- TOGGLE MOBILE --}}
            <button class="nav-toggle"
                    type="button"
                    aria-label="Buka menu"
                    onclick="this.closest('.navbar').classList.toggle('open')">
                ☰
            </button>

            <div class="nav-links">

                @if($mode === 'landing')

                    <a href="#home">Home</a>
                    <a href="#info">Informasi</a>

                    @auth
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}"
                           class="nav-register">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-login">Login</a>
                        <a href="{{ route('register') }}" class="nav-register">Registrasi</a>
                    @endauth

                @else

                    {{-- HOME --}}
                    <a href="{{ route('dashboard') }}"
                       class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        Home
                    </a>

                    {{-- PRODUCTS --}}
                    <a href="{{ route('products.index') }}"
                       class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                        Products
                    </a>

                    {{-- CART --}}
                    <a href="{{ route('cart.index') }}"
                       class="nav-cart {{ request()->routeIs('cart.*') ? 'active' : '' }}"
                       id="navCartButton"
                       aria-label="Keranjang"
                       title="Keranjang">

                        <i class="fa-solid fa-cart-shopping"></i>

                        @if(($cartCount ?? 0) > 0)
                            <span class="cart-badge" id="navCartBadge">
                                {{ $cartCount > 99 ? '99+' : $cartCount }}
                            </span>
                        @endif
                    </a>

                    {{-- PROFILE --}}
                    <a href="{{ route('profile') }}"
                       class="nav-profile {{ request()->routeIs('profile', 'profile.*', 'orders.*') ? 'active' : '' }}"
                       aria-label="Profil"
                       title="Profil">
                        <i class="fa-solid fa-user"></i>
                    </a>

                @endif

            </div>
        </div>
    </nav>
</div>

<script>
(function() {
    var navbar = document.getElementById('mainNavbar');

    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }, { passive: true });
    }
})();
</script>