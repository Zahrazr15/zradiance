<!DOCTYPE html>
<html lang="id">

<head>
    @include('layouts.partials.head')
    <title>Login - ZRadiance</title>
</head>

<body class="login-page">

    <div class="auth-split-wrapper">

        {{-- VISUAL SIDE (DESKTOP) --}}
        <div class="auth-visual-side">
            <div class="auth-visual-brand">
                <a href="{{ route('home') }}" style="text-decoration:none;">
                    <x-logo :size="42" background="dark" />
                </a>
            </div>

            <div class="auth-visual-quote">
                <h2>Pancarkan Pesonamu Setiap Hari</h2>
                <p>
                    Masuk ke akun ZRadiance Anda untuk melanjutkan belanja produk perawatan terbaik dengan promo eksklusif dan kemudahan pengiriman.
                </p>

                <div class="auth-visual-badges">
                    <div class="auth-glass-pill">✨ 100% Original</div>
                    <div class="auth-glass-pill">🌿 BPOM Certified</div>
                    <div class="auth-glass-pill">🚚 Pengiriman Aman</div>
                </div>
            </div>

            <div style="font-size:13px;color:rgba(255,255,255,0.7);position:relative;z-index:2;">
                © {{ date('Y') }} ZRadiance. All rights reserved.
            </div>
        </div>

        {{-- FORM SIDE --}}
        <div class="auth-form-side">
            <div class="auth-form-box">

                <div class="auth-logo" style="justify-content:flex-start;margin-bottom:20px;">
                    <x-logo :size="36" background="light" />
                </div>

                <h1>Selamat Datang</h1>
                <p class="subtitle">Silakan masukkan email dan password akun Anda</p>

                @if (session('success'))
                    <div class="alert-success">
                        <span>✓</span>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            required
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                            <label for="password" style="margin-bottom:0;">Password</label>
                            <a href="{{ route('password.request') }}" style="font-size:13px;color:var(--c-pink-strong);text-decoration:none;font-weight:700;">Lupa password?</a>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                        >
                    </div>

                    <button type="submit" class="auth-button">
                        Masuk ke Akun
                    </button>
                </form>

                <div class="auth-link">
                    <div>
                        Belum punya akun?
                        <a href="{{ route('register') }}">Daftar Sekarang</a>
                    </div>
                    <div style="margin-top:8px;">
                        <a href="{{ route('home') }}" style="color:var(--c-ink-soft);font-size:13px;font-weight:600;">Kembali ke Beranda</a>
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>

</html>
