<!DOCTYPE html>
<html lang="id">

<head>
    @include('layouts.partials.head')
    <title>Lupa Password - Beauty Store</title>
</head>

<body>

    <div class="auth-split-wrapper">

        {{-- VISUAL SIDE (DESKTOP) --}}
        <div class="auth-visual-side">
            <div class="auth-visual-brand">
                <a href="{{ route('home') }}" style="text-decoration:none;">
                    <x-logo :size="42" />
                </a>
            </div>

            <div class="auth-visual-quote">
                <h2>Pemulihan Akses Akun Anda</h2>
                <p>
                    Kami menjaga privasi dan keamanan akun Anda. Ajukan permintaan reset password baru yang akan diverifikasi dengan aman oleh tim kami.
                </p>

                <div class="auth-visual-badges">
                    <div class="auth-glass-pill">🔒 Verifikasi Aman</div>
                    <div class="auth-glass-pill">🛡️ Perlindungan Data</div>
                    <div class="auth-glass-pill">⏱️ Proses Cepat</div>
                </div>
            </div>

            <div style="font-size:13px;color:rgba(255,255,255,0.7);position:relative;z-index:2;">
                © {{ date('Y') }} Beauty Store. All rights reserved.
            </div>
        </div>

        {{-- FORM SIDE --}}
        <div class="auth-form-side">
            <div class="auth-form-box">

                <div class="auth-logo" style="justify-content:flex-start;margin-bottom:20px;">
                    <x-logo :size="36" />
                </div>

                <h1>Lupa Password</h1>
                <p class="subtitle">
                    Masukkan email dan password baru. Password baru akan aktif setelah disetujui oleh admin.
                </p>

                @if (session('success'))
                    <div class="alert-success">
                        <span>✓</span>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert-error">
                        <ul>
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.request.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Akun Terdaftar</label>
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
                        <label for="password">Password Baru</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            minlength="6"
                            placeholder="Minimal 6 karakter"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password Baru</label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            minlength="6"
                            placeholder="Ulangi password baru"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="note">Catatan untuk Admin (Opsional)</label>
                        <input
                            type="text"
                            id="note"
                            name="note"
                            maxlength="255"
                            value="{{ old('note') }}"
                            placeholder="Contoh: No HP / Info pemesanan terakhir"
                        >
                    </div>

                    <button type="submit" class="auth-button">
                        Kirim Permintaan Reset
                    </button>
                </form>

                <div class="auth-link">
                    <div>
                        Sudah ingat password Anda?
                        <a href="{{ route('login') }}">Kembali ke Login</a>
                    </div>
                    <div style="margin-top:8px;">
                        <a href="{{ route('home') }}" style="color:var(--c-ink-soft);font-size:13px;font-weight:600;">← Kembali ke Beranda</a>
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>

</html>
