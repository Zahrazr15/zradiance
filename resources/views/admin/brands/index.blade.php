@extends('layouts.admin')

@section('title', 'Brand - Admin')

@section('page-title', 'Brand')

@section('content')

<div class="page-header-row">
    <div class="page-header">
        <h2>Management Brand</h2>
        <p>Kelola brand produk ZRadiance.</p>
    </div>

    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
        + Tambah Brand
    </a>
</div>

<div class="admin-card">

    @if($brands->count() > 0)

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Brand</th>
                        <th>Slug</th>
                        <th>Produk</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($brands as $brand)
                        <tr>
                            {{-- BRAND --}}
                            <td>
                                <div class="user-cell">
                                    <div class="brand-thumb">
                                        @if($brand->brand_logo)
                                            <img src="{{ asset('storage/' . $brand->brand_logo) }}" alt="{{ $brand->brand_name }}">
                                        @else
                                            <span class="muted">No Logo</span>
                                        @endif
                                    </div>

                                    <strong>{{ $brand->brand_name }}</strong>
                                </div>
                            </td>

                            <td class="muted">{{ $brand->brand_slug }}</td>

                            <td>
                                <span class="badge">{{ $brand->products_count }} Produk</span>
                            </td>

                            <td>
                                <span class="badge {{ $brand->is_active ? 'badge-success' : 'badge-muted' }}">
                                    {{ $brand->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td>
                                <div class="row-actions">
                                    <a href="{{ route('admin.brands.show', $brand) }}" class="btn btn-outline btn-sm">
                                        Detail
                                    </a>

                                    <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-secondary btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.brands.destroy', $brand) }}"
                                          method="POST"
                                          data-confirm="Yakin ingin menghapus brand {{ $brand->brand_name }}?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
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

        @if($brands->hasPages())
            <div class="pager">
                <span>Halaman {{ $brands->currentPage() }} dari {{ $brands->lastPage() }}</span>

                <div class="row-actions">
                    @if($brands->previousPageUrl())
                        <a href="{{ $brands->previousPageUrl() }}" class="btn btn-secondary btn-sm">&larr; Sebelumnya</a>
                    @endif

                    @if($brands->nextPageUrl())
                        <a href="{{ $brands->nextPageUrl() }}" class="btn btn-secondary btn-sm">Berikutnya &rarr;</a>
                    @endif
                </div>
            </div>
        @endif

    @else

        <div style="text-align: center; padding: 50px; color: var(--c-ink-soft);">
            <p style="margin-bottom: 15px;">Belum ada brand.</p>

            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
                + Tambah Brand
            </a>
        </div>

    @endif

</div>
@endsection
