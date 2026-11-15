@extends('layouts.user')

@section('title', 'Edit Profil - Beauty Store')

@section('content')
<div class="profile-container" style="max-width:700px;margin:40px auto;padding:0 20px;">

    <a href="{{ route('profile') }}" class="btn btn-secondary">Kembali</a>

    @if($errors->any())
        <div class="alert-error" style="margin-top:20px;">
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- FOTO --}}
    <div class="profile-card" style="margin-top:20px;">
        <h2>Foto Profil</h2>

        @if(Auth::user()->avatar_url)
            <img src="{{ Auth::user()->avatar_url }}" alt="Foto profil"
                 style="width:100px;height:100px;border-radius:50%;object-fit:cover;margin:12px 0;">
        @endif

        <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <input type="file" name="photo" accept="image/png,image/jpeg,image/webp" required>
                <small>JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
            </div>
            <button type="submit" class="btn btn-primary">Upload Foto</button>
        </form>

        @if(Auth::user()->avatar)
            <form action="{{ route('profile.photo.destroy') }}" method="POST" style="margin-top:10px;"
                  data-confirm="Hapus foto profil?">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Hapus Foto</button>
            </form>
        @endif
    </div>

    {{-- DATA DIRI --}}
    <div class="profile-card" style="margin-top:20px;">
        <h2>Data Diri</h2>
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf @method('PUT')

            <div class="form-group">
                <label for="name">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
            </div>
            <div class="form-group">
                <label for="phone">Nomor HP</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}" placeholder="081234567890">
            </div>

            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection