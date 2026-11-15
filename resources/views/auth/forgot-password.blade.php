<!DOCTYPE html>
<html lang="id">
<head>
    @include('layouts.partials.head')
    <title>Lupa Password - Beauty Store</title>
</head>
<body>
<div style="max-width:440px;margin:60px auto;padding:0 20px;">
    <h1 style="margin-bottom:8px;">Lupa Password</h1>
    <p style="color:var(--c-ink-soft);margin-bottom:24px;font-size:14px;">
        Masukkan email dan password baru. Password baru baru berlaku setelah disetujui admin.
    </p>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('password.request.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="email">Email akun</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="form-group">
            <label for="password">Password baru</label>
            <input type="password" id="password" name="password" minlength="6" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Konfirmasi password baru</label>
            <input type="password" id="password_confirmation" name="password_confirmation" minlength="6" required>
        </div>
        <div class="form-group">
            <label for="note">Catatan untuk admin (opsional)</label>
            <input type="text" id="note" name="note" maxlength="255" value="{{ old('note') }}"
                   placeholder="Contoh: nomor HP / info supaya admin yakin ini kamu">
        </div>
        <button type="submit" class="btn btn-primary">Kirim Permintaan</button>
        <a href="{{ route('login') }}" class="btn btn-secondary">Kembali ke Login</a>
    </form>
</div>
</body>
</html>