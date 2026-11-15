
@extends('layouts.admin')

@section('title', 'Edit Produk - Admin')

@section('page-title', 'Edit Produk')

@section('content')

<div class="page-header">
    <h2>Edit Produk</h2>
    <p>
        Perbarui informasi produk yang dipilih.
    </p>
</div>

<div class="admin-card">

    <form
        action="{{ route('admin.products.update', $product) }}"
        method="POST"
    >
        @csrf
        @method('PUT')

        {{-- KATEGORI --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 7px; font-weight: 600;">
                Kategori
            </label>

            @include('products._category_picker', ['selectedId' => old('product_cat', $product->product_cat)])

            @error('product_cat')
                <small style="color: #dc2626;">{{ $message }}</small>
            @enderror
        </div>

        {{-- BRAND --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 7px; font-weight: 600;">
                Brand
            </label>

            <input
                type="text"
                name="brand"
                value="{{ old('brand', $product->brand) }}"
                required
                style="width: 100%; padding: 11px; border: 1px solid var(--c-border); border-radius: 7px;"
            >

            @error('brand')
                <small style="color: #dc2626;">{{ $message }}</small>
            @enderror
        </div>

        {{-- NAMA --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 7px; font-weight: 600;">
                Nama Produk
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name', $product->name) }}"
                required
                style="width: 100%; padding: 11px; border: 1px solid var(--c-border); border-radius: 7px;"
            >

            @error('name')
                <small style="color: #dc2626;">{{ $message }}</small>
            @enderror
        </div>

        {{-- HARGA --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 7px; font-weight: 600;">
                Harga
            </label>

            <input
                type="number"
                name="price"
                value="{{ old('price', $product->price) }}"
                min="0"
                required
                style="width: 100%; padding: 11px; border: 1px solid var(--c-border); border-radius: 7px;"
            >

            @error('price')
                <small style="color: #dc2626;">{{ $message }}</small>
            @enderror
        </div>

        {{-- STOK --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 7px; font-weight: 600;">
                Stok
            </label>

            <input
                type="number"
                name="stock"
                value="{{ old('stock', $product->stock) }}"
                min="0"
                required
                style="width: 100%; padding: 11px; border: 1px solid var(--c-border); border-radius: 7px;"
            >

            @error('stock')
                <small style="color: #dc2626;">{{ $message }}</small>
            @enderror
        </div>

        {{-- BERAT PRODUK --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 7px; font-weight: 600;">
                Berat Produk (gram)
            </label>

            <input
                type="number"
                name="weight"
                value="{{ old('weight', $product->weight) }}"
                min="1"
                max="100000"
                step="1"
                required
                placeholder="Contoh: 250"
                style="width: 100%; padding: 11px; border: 1px solid var(--c-border); border-radius: 7px;"
            >

            <small style="color: #64748b;">
                Masukkan berat satu produk dalam gram.
            </small>

            @error('weight')
                <small style="display: block; color: #dc2626;">{{ $message }}</small>
            @enderror
        </div>

        {{-- GAMBAR --}}
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 7px; font-weight: 600;">
                URL Gambar
            </label>

            <input
                type="url"
                name="image"
                value="{{ old('image', $product->image) }}"
                placeholder="https://..."
                style="width: 100%; padding: 11px; border: 1px solid var(--c-border); border-radius: 7px;"
            >

            @error('image')
                <small style="color: #dc2626;">{{ $message }}</small>
            @enderror
        </div>

        {{-- DESKRIPSI --}}
        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 7px; font-weight: 600;">
                Deskripsi
            </label>

            <textarea
                name="description"
                rows="5"
                style="width: 100%; padding: 11px; border: 1px solid var(--c-border); border-radius: 7px; resize: vertical;"
            >{{ old('description', $product->description) }}</textarea>

            @error('description')
                <small style="color: #dc2626;">{{ $message }}</small>
            @enderror
        </div>

        @include('products._knowledge_fields', ['product' => $product ?? null])

        {{-- BUTTON --}}
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                Simpan Perubahan
            </button>

            <a
                href="{{ route('admin.products.index') }}"
                class="btn btn-secondary"
            >
                Batal
            </a>
        </div>

    </form>

</div>

@endsection
