@extends('layouts.admin')

@section('page-title', 'Manajemen Brand')

@section('content')

<div class="admin-card-glass">
    <div class="admin-card-heading">
        <div>
            <h3>Daftar Brand</h3>
            <p>Kelola brand produk ZRadiance.</p>
        </div>

        <a href="{{ route('admin.brands.create') }}"
           class="btn btn-primary">
            + Tambah Brand
        </a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-data-table">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Nama Brand</th>
                    <th>Slug</th>
                    <th>Jumlah Produk</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($brands as $brand)
                    <tr>
                        <td>
                            @if($brand->brand_logo)
                                <img src="{{ asset('storage/' . $brand->brand_logo) }}"
                                     alt="{{ $brand->brand_name }}"
                                     style="width:60px;height:40px;object-fit:contain;">
                            @else
                                <span class="muted">Belum ada logo</span>
                            @endif
                        </td>

                        <td>
                            <strong>{{ $brand->brand_name }}</strong>
                        </td>

                        <td>{{ $brand->brand_slug }}</td>

                        <td>{{ $brand->products_count }}</td>

                        <td>
                            @if($brand->is_active)
                                <span class="badge">Aktif</span>
                            @else
                                <span class="badge">Nonaktif</span>
                            @endif
                        </td>

                        <td>
                            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                <a href="{{ route('admin.brands.edit', $brand) }}"
                                   class="btn btn-outline btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('admin.brands.destroy', $brand) }}"
                                      method="POST"
                                      data-confirm="Yakin ingin menghapus brand {{ $brand->brand_name }}?">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-outline btn-sm">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="admin-table-empty">
                            Belum ada brand.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        {{ $brands->links() }}
    </div>
</div>

@endsection