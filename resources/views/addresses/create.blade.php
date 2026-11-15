@extends('layouts.user')

@section('title', 'Tambah Alamat Baru - Beauty Store')

@push('styles')
<style>
    .address-page-wrap {
        max-width: 820px;
        margin: 36px auto;
        padding: 0 24px 80px;
    }

    .address-main-card {
        padding: 36px;
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
    }

    .address-card-header {
        margin-bottom: 28px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--c-border-subtle);
    }

    .address-card-header h1 {
        margin: 0 0 6px;
        color: var(--c-ink-heading);
        font-size: 26px;
    }

    .address-card-header p {
        margin: 0;
        color: var(--c-ink-soft);
        font-size: 14.5px;
    }

    @media (max-width: 600px) {
        .address-page-wrap { margin: 20px auto; padding: 0 16px; }
        .address-main-card { padding: 22px; }
    }
</style>
@endpush

@section('content')
<div class="address-page-wrap">

    <div class="back-bar">
        <x-back-button :href="route('profile')" label="← Kembali ke Profil" />
    </div>

    <div class="address-main-card">
        <div class="address-card-header">
            <h1>Tambah Alamat Pengiriman</h1>
            <p>Lengkapi nama penerima, kontak aktif, dan detail wilayah pengiriman Anda.</p>
        </div>

        @if($errors->any())
            <div class="alert-error" style="margin-bottom:24px;">
                <strong>Periksa kembali data berikut:</strong>
                <ul style="margin:6px 0 0; padding-left:20px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('addresses._form')
    </div>

</div>
@endsection
