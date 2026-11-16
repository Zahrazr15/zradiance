<!DOCTYPE html>
<html lang="id">

<head>
    @include('layouts.partials.head')
    <title>Daftar Akun - ZRadiance</title>
</head>

<body>

    <div class="auth-split-wrapper">

        {{-- VISUAL SIDE (DESKTOP) --}}
        <div class="auth-visual-side">
            <div class="auth-visual-brand">
                <a href="{{ route('home') }}" style="text-decoration:none;">
                    <x-logo :size="42" background="dark" />
                </a>
            </div>

            <div class="auth-visual-quote">
                <h2>Awali Perjalanan Cantikmu Bersama Kami</h2>
                <p>
                    Daftar akun gratis sekarang untuk menikmati kemudahan berbelanja kosmetik & perawatan kulit original, cek ongkos kirim otomatis, serta asisten AI pribadi.
                </p>

                <div class="auth-visual-badges">
                    <div class="auth-glass-pill">🌸 Koleksi Lengkap</div>
                    <div class="auth-glass-pill">💎 Jaminan Kualitas</div>
                    <div class="auth-glass-pill">⚡ Transaksi Instan</div>
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

                <h1>Buat Akun Baru</h1>
                <p class="subtitle">Lengkapi data diri Anda untuk memulai belanja</p>

                @if ($errors->any())
                    <div class="error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Sarah Anindita"
                            required
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@email.com"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 6 karakter"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            required
                        >
                    </div>

                    <button type="submit" class="auth-button">
                        Daftarkan Akun
                    </button>
                </form>

                <div class="auth-link">
                    <div>
                        Sudah memiliki akun?
                        <a href="{{ route('login') }}">Masuk di Sini</a>
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
