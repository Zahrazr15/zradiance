
<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.partials.head')
    <title>@yield('title', 'Admin - ZRadiance')</title>
    <script>
        try {
            if (localStorage.getItem('sb-collapsed') === '1' && innerWidth > 900) {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        } catch (e) {}
    </script>
</head>
<body>
<div class="admin-wrapper">

    <aside class="admin-sidebar">
        <div class="admin-logo"><x-logo :size="30" background="light" /></div>

        <ul class="admin-menu">
            <li><div class="menu-section">Menu utama</div></li>

            <li>
                <a href="{{ route('admin.dashboard') }}"
                   title="Dashboard"
                   class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="menu-icon">🏠</span>
                    <span class="menu-label">Dashboard</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.products.index') }}"
                   title="Produk"
                   class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <span class="menu-icon">📦</span>
                    <span class="menu-label">Produk</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.categories.index') }}"
                   title="Kategori"
                   class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <span class="menu-icon">🏷️</span>
                    <span class="menu-label">Kategori</span>
                </a>
            </li>

           <li>
                <a href="{{ route('admin.brands.index') }}"
                title="Brand"
                class="{{ request()->routeIs('admin.brands.*') ? 'active' : '' }}">

                    <span class="menu-icon">🏷️</span>
                    <span class="menu-label">Brand</span>

                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.index') }}"
                   title="Pesanan"
                   class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <span class="menu-icon">🛒</span>
                    <span class="menu-label">Pesanan</span>
                </a>
            </li>

            {{-- Notifikasi Admin --}}
            <li>
                <a href="{{ route('admin.notifications.index') }}"
                   title="Notifikasi"
                   class="{{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}">
                    <span class="menu-icon">🔔</span>
                    <span class="menu-label">
                        Notifikasi

                        @php
                            $unreadNotificationCount = Auth::user()
                                ->unreadNotifications()
                                ->count();
                        @endphp

                        @if($unreadNotificationCount > 0)
                            <span class="cart-badge"
                                  style="position:static;display:inline-block;margin-left:6px;">
                                {{ $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount }}
                            </span>
                        @endif
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.users.index') }}"
                   title="Pengguna"
                   class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="menu-icon">👥</span>
                    <span class="menu-label">Pengguna</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.password-resets.index') }}"
                   title="Reset Password"
                   class="{{ request()->routeIs('admin.password-resets.*') ? 'active' : '' }}">
                    <span class="menu-icon">🔑</span>
                    <span class="menu-label">
                        Reset Password

                        @if(($pendingResetCount ?? 0) > 0)
                            <span class="cart-badge"
                                  style="position:static;display:inline-block;margin-left:6px;">
                                {{ $pendingResetCount }}
                            </span>
                        @endif
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.shipping-settings.edit') }}"
                   title="Lokasi Toko"
                   class="{{ request()->routeIs('admin.shipping-settings.*') ? 'active' : '' }}">
                    <span class="menu-icon">📍</span>
                    <span class="menu-label">Lokasi Toko (Ongkir)</span>
                </a>
            </li>

            <li><div class="menu-section">Akun</div></li>

            <li>
                <form action="{{ route('logout') }}"
                      method="POST"
                      data-confirm="Yakin ingin logout?">
                    @csrf
                    <button type="submit" title="Logout">
                        <span class="menu-icon">🚪</span>
                        <span class="menu-label">Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    <div class="sb-backdrop"></div>

    <main class="admin-main">
        <header class="admin-topbar">
            <button class="sb-toggle"
                    type="button"
                    data-sb-toggle
                    aria-label="Sembunyikan atau tampilkan sidebar">
                ☰
            </button>

            <h1>@yield('page-title', 'Admin Dashboard')</h1>

            <div class="admin-user">
                Login sebagai: <strong>{{ Auth::user()->name }}</strong>
            </div>
        </header>

        <section class="admin-content">
            @if($errors->any())
                <div class="alert-error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </section>
    </main>
</div>

<script>
(function () {
    var root = document.documentElement;
    var mobile = function () {
        return matchMedia('(max-width:900px)').matches;
    };

    document.querySelector('[data-sb-toggle]').addEventListener('click', function () {
        if (mobile()) {
            root.classList.toggle('sidebar-open');
        } else {
            root.classList.toggle('sidebar-collapsed');

            try {
                localStorage.setItem(
                    'sb-collapsed',
                    root.classList.contains('sidebar-collapsed') ? '1' : '0'
                );
            } catch (e) {}
        }
    });

    document.querySelector('.sb-backdrop').addEventListener('click', function () {
        root.classList.remove('sidebar-open');
    });
})();
</script>

<x-flash-popup />
</body>
</html>
