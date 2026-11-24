
@extends('layouts.admin')

@section('title', 'Produk - Admin')

@section('page-title', 'Produk')

@section('content')

<div class="page-header-row">
    <div class="page-header">
        <h2>Management Produk</h2>
        <p>Kelola produk, kategori, harga, dan stok.</p>
    </div>

    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        + Tambah Produk
    </a>
</div>

<div class="admin-card">

    @if($products->count() > 0)

        <div class="table-wrap">

            <table class="table">

                <thead>

                    <tr>

                        <th>
                            Produk
                        </th>

                        <th>
                            Brand
                        </th>

                        <th>
                            Kategori
                        </th>

                        <th>
                            Harga
                        </th>

                        <th>
                            Stok
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($products as $product)

                        <tr>

                            {{-- PRODUK --}}
                            <td>

                                <div style="display: flex; align-items: center; gap: 12px;">

                                    <div style="width: 60px; height: 60px; border-radius: 8px; overflow: hidden; background: var(--c-cream); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">

                                        @if($product->image)

                                            <img
                                                src="{{ $product->image }}"
                                                alt="{{ $product->name }}"
                                                style="width: 100%; height: 100%; object-fit: cover;"
                                            >

                                        @else

                                            <span style="font-size: 11px; color: var(--c-ink-soft);">
                                                No Image
                                            </span>

                                        @endif

                                    </div>

                                    <div>

                                        <strong>
                                            {{ $product->name }}
                                        </strong>

                                    </div>

                                </div>

                            </td>

                            {{-- BRAND --}}
                            <td>

                                <div style="display: flex; align-items: center; gap: 8px;">

                                    @if($product->brandRelation && $product->brandRelation->brand_logo)

                                        <img
                                            src="{{ asset('storage/' . $product->brandRelation->brand_logo) }}"
                                            alt="{{ $product->brandRelation->brand_name }}"
                                            style="width: 32px; height: 32px; object-fit: contain; border-radius: 6px; background: white; padding: 3px; flex-shrink: 0;"
                                        >

                                    @endif

                                    <span>
                                        {{ $product->brandRelation->brand_name ?? $product->brand ?? '-' }}
                                    </span>

                                </div>

                            </td>

                            {{-- KATEGORI --}}
                            <td>

                                <span style="display: inline-block; background: var(--c-cream); padding: 6px 10px; border-radius: 20px; font-size: 12px;">
                                    {{ $product->category->cat_title ?? '-' }}
                                </span>

                            </td>

                            {{-- HARGA --}}
                            <td style="white-space: nowrap;">

                                <strong>
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </strong>

                            </td>

                            {{-- STOK --}}
                            <td>

                                {{ $product->stock }}

                            </td>

                            {{-- AKSI --}}
                            <td>

                                <div class="row-actions">

                                    <a
                                        href="{{ route('admin.products.show', $product) }}"
                                        class="btn btn-outline btn-sm"
                                    >
                                        Detail
                                    </a>

                                    <a
                                        href="{{ route('admin.products.edit', $product) }}"
                                        class="btn btn-secondary btn-sm"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('admin.products.destroy', $product) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus produk ini?')"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm"
                                        >
                                            Hapus
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div style="text-align: center; padding: 50px; color: var(--c-ink-soft);">

            <p style="margin-bottom: 15px;">
                Belum ada produk.
            </p>

            <a
                href="{{ route('admin.products.create') }}"
                class="btn btn-primary"
            >
                + Tambah Produk
            </a>

        </div>

    @endif

</div>

@endsection