@extends('layouts.admin')

@section('page-title', 'Detail Brand')

@section('content')

<div class="admin-card-glass">
    <div class="admin-card-heading">
        <div>
            <h3>{{ $brand->brand_name }}</h3>
            <p>Detail informasi brand.</p>
        </div>

        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.brands.edit', $brand) }}"
               class="btn btn-primary">
                Edit Brand
            </a>

            <a href="{{ route('admin.brands.index') }}"
               class="btn btn-outline">
                Kembali
            </a>
        </div>
    </div>

    {{-- Logo --}}
    <div style="margin-bottom:25px;">
        <h4>Logo Brand</h4>

        @if($brand->brand_logo)
            <img src="{{ asset('storage/' . $brand->brand_logo) }}"
                 alt="{{ $brand->brand_name }}"
                 style="max-width:200px;max-height:200px;object-fit:contain;border:1px solid #ddd;border-radius:12px;padding:15px;">
        @else
            <p class="muted">Brand belum memiliki logo.</p>
        @endif
    </div>

    {{-- Informasi --}}
    <div class="table-wrap">
        <table class="table">
            <tbody>
                <tr>
                    <th>Nama Brand</th>
                    <td>{{ $brand->brand_name }}</td>
                </tr>

                <tr>
                    <th>Slug</th>
                    <td>{{ $brand->brand_slug }}</td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge {{ $brand->is_active ? 'badge-success' : 'badge-muted' }}">
                            {{ $brand->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <th>Deskripsi</th>
                    <td>
                        {{ $brand->description ?: 'Belum ada deskripsi.' }}
                    </td>
                </tr>

                <tr>
                    <th>Jumlah Produk</th>
                    <td>{{ $brand->products->count() }} produk</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Produk --}}
    <div style="margin-top:25px;">
        <h3>Produk dari Brand Ini</h3>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($brand->products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>

                            <td>
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>

                            <td>{{ $product->stock }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="admin-table-empty">
                                Belum ada produk yang menggunakan brand ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection