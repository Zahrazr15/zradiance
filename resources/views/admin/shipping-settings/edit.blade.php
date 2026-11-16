
@extends('layouts.admin')

@section('title', 'Pengaturan Lokasi Pengiriman')
@section('page-title', 'Pengaturan Lokasi Pengiriman')

@section('content')

<div class="page-header">
    <h2>Pengaturan Lokasi Pengiriman</h2>
    <p>Atur lokasi asal pengiriman produk ZRadiance.</p>
</div>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Periksa kembali data berikut:</strong>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="admin-card">
    <h3>Lokasi Asal Toko</h3>
    <p class="muted">
        Cari lokasi toko melalui database wilayah RajaOngkir.
    </p>

    <div class="mb-3">
        <label for="location_keyword">Cari Kecamatan atau Kelurahan</label>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <input
                type="text"
                id="location_keyword"
                class="form-control"
                placeholder="Contoh: Gondokusuman"
                style="flex:1;min-width:200px;"
            >

            <button
                type="button"
                id="search_location_button"
                class="btn btn-primary"
            >
                Cari Lokasi
            </button>
        </div>

        <small class="muted">
            Masukkan minimal 3 karakter, lalu pilih hasil yang sesuai.
        </small>
    </div>

    <div id="location_message" class="mb-3"></div>
    <div id="location_results" class="mb-4"></div>
</div>

<div class="admin-card" style="margin-top:20px;">
    <form
        action="{{ route('admin.shipping-settings.update') }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        <h3>Informasi Toko</h3>

        <div class="mb-3">
            <label for="store_name">Nama Toko</label>
            <input
                type="text"
                name="store_name"
                id="store_name"
                class="form-control"
                value="{{ old('store_name', $shippingSetting?->store_name ?? 'ZRadiance') }}"
                required
            >
        </div>

        <hr>

        <h3>Wilayah Toko</h3>

        <div class="mb-3">
            <label for="province_name">Provinsi</label>
            <input
                type="text"
                name="province_name"
                id="province_name"
                class="form-control"
                value="{{ old('province_name', $shippingSetting?->province_name ?? 'DI Yogyakarta') }}"
                required
            >
        </div>

        <div class="mb-3">
            <label for="city_name">Kota/Kabupaten</label>
            <input
                type="text"
                name="city_name"
                id="city_name"
                class="form-control"
                value="{{ old('city_name', $shippingSetting?->city_name ?? 'Kota Yogyakarta') }}"
                required
            >
        </div>

        <div class="mb-3">
            <label for="district_name">Kecamatan</label>
            <input
                type="text"
                name="district_name"
                id="district_name"
                class="form-control"
                value="{{ old('district_name', $shippingSetting?->district_name ?? 'Gondokusuman') }}"
                required
            >
        </div>

        <input
            type="hidden"
            name="province_code"
            id="province_code"
            value="{{ old('province_code', $shippingSetting?->province_code) }}"
        >

        <input
            type="hidden"
            name="city_code"
            id="city_code"
            value="{{ old('city_code', $shippingSetting?->city_code) }}"
        >

        <input
            type="hidden"
            name="district_code"
            id="district_code"
            value="{{ old('district_code', $shippingSetting?->district_code) }}"
        >

        <hr>

        <h3>Alamat Pengiriman</h3>

        <div class="mb-3">
            <label for="address">Alamat Lengkap Toko/Gudang</label>
            <textarea
                name="address"
                id="address"
                class="form-control"
                rows="3"
                required
            >{{ old('address', $shippingSetting?->address) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="postal_code">Kode Pos</label>
            <input
                type="text"
                name="postal_code"
                id="postal_code"
                class="form-control"
                value="{{ old('postal_code', $shippingSetting?->postal_code) }}"
            >
        </div>

        <div class="mb-3">
            <label for="rajaongkir_origin_id">ID Asal RajaOngkir</label>
            <input
                type="text"
                name="rajaongkir_origin_id"
                id="rajaongkir_origin_id"
                class="form-control"
                value="{{ old('rajaongkir_origin_id', $shippingSetting?->rajaongkir_origin_id) }}"
                readonly
                required
            >
            <small class="muted">
                ID akan terisi setelah kamu memilih hasil pencarian lokasi.
            </small>
        </div>

        <button type="submit" class="btn btn-primary">
            Simpan Pengaturan
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const keywordInput = document.getElementById('location_keyword');
    const searchButton = document.getElementById('search_location_button');
    const resultsContainer = document.getElementById('location_results');
    const messageContainer = document.getElementById('location_message');

    const searchUrl = @json(route('admin.shipping-settings.search-location'));

    function showMessage(message, type = 'info') {
        const colors = {
            info: 'var(--c-ink-soft)',
            success: 'var(--c-success)',
            error: 'var(--c-danger)'
        };

        messageContainer.textContent = message;
        messageContainer.style.color = colors[type] || colors.info;
    }

    searchButton.addEventListener('click', async function () {
        const keyword = keywordInput.value.trim();

        resultsContainer.innerHTML = '';

        if (keyword.length < 3) {
            showMessage('Masukkan minimal 3 karakter.', 'error');
            return;
        }

        searchButton.disabled = true;
        searchButton.textContent = 'Mencari...';
        showMessage('Sedang mencari lokasi...');

        try {
            const url = new URL(searchUrl, window.location.origin);
            url.searchParams.set('keyword', keyword);

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || 'Pencarian lokasi gagal.'
                );
            }

            const locations = result.data || [];

            if (locations.length === 0) {
                showMessage('Lokasi tidak ditemukan.', 'error');
                return;
            }

            showMessage(
                'Ditemukan ' + locations.length + ' hasil. Pilih lokasi toko yang benar.',
                'success'
            );

            locations.forEach(function (location) {
                const button = document.createElement('button');

                button.type = 'button';
                button.className = 'btn btn-outline shipping-location-option';

                button.textContent =
                    location.label ||
                    [
                        location.subdistrict_name,
                        location.district_name,
                        location.city_name,
                        location.province_name,
                        location.zip_code
                    ].filter(Boolean).join(', ');

                button.addEventListener('click', function () {
                    document.getElementById('rajaongkir_origin_id').value =
                        location.id || '';

                    document.getElementById('province_name').value =
                        location.province_name || '';

                    document.getElementById('city_name').value =
                        location.city_name || '';

                    document.getElementById('district_name').value =
                        location.district_name || '';

                    document.getElementById('postal_code').value =
                        location.zip_code || '';

                    // API Direct Search tidak mengembalikan kode wilayah
                    // administratif dari sistem wilayah.id.
                    document.getElementById('province_code').value = '';
                    document.getElementById('city_code').value = '';
                    document.getElementById('district_code').value = '';

                    showMessage(
                        'Lokasi berhasil dipilih. Jangan lupa simpan pengaturan.',
                        'success'
                    );

                    resultsContainer.innerHTML = '';
                });

                resultsContainer.appendChild(button);
            });

        } catch (error) {
            showMessage(error.message, 'error');
        } finally {
            searchButton.disabled = false;
            searchButton.textContent = 'Cari Lokasi';
        }
    });
});
</script>

@endsection
