@extends('layouts.user')

@section('title', 'Edit Profil Pengguna - Beauty Store')

@push('styles')
<style>
    .profile-edit-wrap {
        max-width: 760px;
        margin: 36px auto;
        padding: 0 24px 80px;
    }

    .edit-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 32px;
        box-shadow: var(--shadow-sm);
        margin-top: 24px;
    }

    .edit-card h2 {
        font-size: 20px;
        color: var(--c-ink-heading);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--c-border-subtle);
    }

    .avatar-preview-box {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .avatar-lg {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--c-blush);
        box-shadow: var(--shadow-sm);
    }
</style>
@endpush

@section('content')
<div class="profile-edit-wrap">

    <div class="back-bar">
        <x-back-button :href="route('profile')" label="← Kembali ke Profil" />
    </div>

    @if($errors->any())
        <div class="alert-error" style="margin-top:20px;">
            <ul style="margin:0;padding-left:20px;">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- KARTU 1: FOTO PROFIL --}}
    <div class="edit-card">
        <h2>📷 Foto Profil</h2>

        @if(Auth::user()->avatar_url)
            <div class="avatar-preview-box">
                <img src="{{ Auth::user()->avatar_url }}" alt="Foto profil" class="avatar-lg">
                <div>
                    <strong style="color:var(--c-ink-heading);font-size:15px;">Foto Saat Ini</strong>
                    <p style="color:var(--c-ink-soft);font-size:13px;margin:2px 0 0;">Format didukung: JPG, PNG, WEBP (maks. 2 MB)</p>
                </div>
            </div>
        @endif

        <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="photo">Pilih Foto Baru</label>
                <input type="file" id="photo" name="photo" accept="image/png,image/jpeg,image/webp" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                Unggah Foto Profil
            </button>
        </form>

        @if(Auth::user()->avatar)
            <form
                action="{{ route('profile.photo.destroy') }}"
                method="POST"
                style="margin-top:16px;"
                data-confirm="Yakin ingin menghapus foto profil ini?"
            >
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm" style="color:var(--c-danger);background:rgba(192,57,43,0.08);border:0;">
                    Hapus Foto Profil
                </button>
            </form>
        @endif
    </div>

    {{-- KARTU 2: DATA DIRI --}}
    <div class="edit-card">
        <h2>👤 Data Diri</h2>
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', Auth::user()->name) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">Alamat Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', Auth::user()->email) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="phone">Nomor HP</label>
                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="{{ old('phone', Auth::user()->phone) }}"
                    placeholder="Contoh: 081234567890"
                >
            </div>

            <div style="display:flex;gap:12px;margin-top:24px;">
                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>
                <a href="{{ route('profile') }}" class="btn btn-outline">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
