
@extends('layouts.user')

@section('title', 'Profil - Beauty Store')

@push('styles')
<style>
    .profile-container {
        max-width: 900px;
        margin: 50px auto;
        padding: 0 20px;
    }

    .profile-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 15px;
        padding: 40px;
    }

    .profile-header {
        text-align: center;
        margin-bottom: 35px;
    }

    .profile-icon {
        width: 85px;
        height: 85px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: var(--c-cream);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        font-size: 38px;
    }

    .profile-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-header h1 {
        margin: 0 0 8px;
        color: var(--c-ink);
        font-size: 28px;
    }

    .profile-header p {
        margin: 0;
        color: var(--c-ink-soft);
        font-size: 14px;
    }

    .profile-info {
        display: grid;
        gap: 15px;
    }

    .info-item {
        padding: 18px 20px;
        background: var(--c-bg);
        border: 1px solid var(--c-border);
        border-radius: 10px;
    }

    .info-item span {
        display: block;
        margin-bottom: 7px;
        color: var(--c-ink-soft);
        font-size: 13px;
    }

    .info-item strong {
        color: var(--c-ink);
        font-size: 15px;
        overflow-wrap: anywhere;
    }

    /* BUKU ALAMAT */
    .address-section {
        margin-top: 35px;
        padding-top: 25px;
        border-top: 1px solid var(--c-border);
    }

    .address-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .address-heading h2 {
        margin: 0;
        color: var(--c-ink);
        font-size: 21px;
    }

    .address-add {
        display: inline-block;
        padding: 10px 16px;
        border-radius: 8px;
        background: var(--c-pink);
        color: var(--c-ink);
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
    }

    .address-item {
        padding: 18px 20px;
        margin-bottom: 15px;
        background: var(--c-bg);
        border: 1px solid var(--c-border);
        border-radius: 10px;
    }

    .address-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
    }

    .address-label {
        font-weight: 700;
        color: var(--c-ink);
    }

    .default-label {
        padding: 5px 9px;
        border-radius: 5px;
        background: var(--c-cream);
        color: var(--c-ink);
        font-size: 11px;
        font-weight: 700;
    }

    .address-item p {
        margin: 5px 0;
        color: var(--c-ink-soft);
        font-size: 14px;
        line-height: 1.6;
    }

    .address-recipient {
        color: var(--c-ink) !important;
        font-weight: 600;
    }

    .address-empty {
        padding: 25px;
        text-align: center;
        background: var(--c-bg);
        border: 1px dashed var(--c-border);
        border-radius: 10px;
        color: var(--c-ink-soft);
    }

    .address-empty p {
        margin: 0 0 12px;
    }

    /* AKSI ALAMAT */
    .address-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-top: 15px;
    }

    .address-actions form {
        margin: 0;
    }

    .address-actions a,
    .address-actions button {
        display: inline-block;
        padding: 8px 14px;
        border: 1px solid var(--c-border);
        border-radius: 6px;
        background: transparent;
        color: var(--c-ink);
        font: inherit;
        font-size: 13px;
        text-decoration: none;
        cursor: pointer;
    }

    .address-actions a:hover,
    .address-actions button:hover {
        opacity: .7;
    }

    .address-actions .delete-button {
        color: #c0392b;
        border-color: #c0392b;
    }

    .profile-alert {
        padding: 14px 16px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-size: 14px;
    }

    .profile-alert.success {
        background: #eaf7ee;
        color: #216e39;
    }

    .profile-alert.error {
        background: #fff0f0;
        color: #b42318;
    }

    .profile-alert ul {
        margin: 8px 0 0;
        padding-left: 20px;
    }

    .profile-edit-button {
        display: inline-block;
        margin-top: 25px;
        text-decoration: none;
    }

    @media (max-width: 600px) {
        .profile-container {
            margin: 30px auto;
        }

        .profile-card {
            padding: 25px;
        }

        .address-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .address-top {
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="profile-container">

    <x-back-button
        :href="route('dashboard')"
        label="Kembali ke Dashboard"
    />

    <div class="profile-card">

        {{-- NOTIFIKASI --}}
        @if(session('success'))
            <div class="profile-alert success" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="profile-alert error" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- HEADER PROFIL --}}
        <div class="profile-header">

            <div class="profile-icon">
                @if(Auth::user()->avatar_url)
                    <img
                        src="{{ Auth::user()->avatar_url }}"
                        alt="Foto profil {{ Auth::user()->name }}"
                    >
                @else
                    👤
                @endif
            </div>

            <h1>Profil Saya</h1>
            <p>Informasi akun pengguna</p>

        </div>

        {{-- INFORMASI PENGGUNA --}}
        <div class="profile-info">

            <div class="info-item">
                <span>Nama</span>
                <strong>{{ Auth::user()->name }}</strong>
            </div>

            <div class="info-item">
                <span>Email</span>
                <strong>{{ Auth::user()->email }}</strong>
            </div>

            <div class="info-item">
                <span>Role</span>
                <strong>{{ ucfirst(Auth::user()->role) }}</strong>
            </div>

        </div>

        {{-- BUKU ALAMAT --}}
        <div class="address-section">

            <div class="address-heading">
                <h2>Buku Alamat</h2>

                <a
                    href="{{ route('addresses.create') }}"
                    class="address-add"
                >
                    + Tambah Alamat
                </a>
            </div>

            @forelse($addresses as $address)

                <div class="address-item">

                    <div class="address-top">

                        <span class="address-label">
                            {{ $address->label }}
                        </span>

                        @if($address->is_default)
                            <span class="default-label">
                                ALAMAT UTAMA
                            </span>
                        @endif

                    </div>

                    <p class="address-recipient">
                        {{ $address->recipient_name }}
                        · {{ $address->phone }}
                    </p>

                    <p>
                        {{ $address->detail }},
                        {{ $address->village_name }},
                        {{ $address->district_name }},
                        {{ $address->city_name }},
                        {{ $address->province_name }}
                        @if($address->postal_code)
                            · {{ $address->postal_code }}
                        @endif
                    </p>

                    {{-- AKSI ALAMAT --}}
                    <div class="address-actions">

                        <a href="{{ route('addresses.edit', $address->id) }}">
                            Edit
                        </a>

                        @if(!$address->is_default)
                            <form
                                action="{{ route('addresses.default', $address->id) }}"
                                method="POST"
                            >
                                @csrf
                                @method('PUT')

                                <button type="submit">
                                    Jadikan Alamat Utama
                                </button>
                            </form>
                        @endif

                        <form
                            action="{{ route('addresses.destroy', $address->id) }}"
                            method="POST"
                            onsubmit="return confirm('Yakin ingin menghapus alamat {{ $address->label }}?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="delete-button">
                                Hapus
                            </button>
                        </form>

                    </div>

                </div>

            @empty

                <div class="address-empty">
                    <p><strong>Belum ada alamat tersimpan.</strong></p>
                    <p>
                        Tambahkan alamat agar proses checkout lebih mudah.
                    </p>
                </div>

            @endforelse

        </div>

        {{-- EDIT PROFIL --}}
        <a
            href="{{ route('profile.edit') }}"
            class="btn btn-primary profile-edit-button"
        >
            Edit Profil
        </a>

    </div>
</div>
@endsection
