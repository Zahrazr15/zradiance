@props(['mode' => 'user'])

<nav class="navbar">
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
                <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Kategori</a>
                <a href="{{ route('cart.index') }}" class="nav-cart {{ request()->routeIs('cart.*') ? 'active' : '' }}" aria-label="Keranjang">
                    🛒
                    @if(($cartCount ?? 0) > 0)
                    <span class="cart-badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                    @endif
                </a>
                <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">Pesanan</a>
                <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">Profil</a>

                <form action="{{ route('logout') }}" method="POST" data-confirm="Yakin ingin logout?">
                    @csrf
                    <button type="submit">Logout</button>
                    
                </form>
            @endif
        </div>

    </div>
</nav>