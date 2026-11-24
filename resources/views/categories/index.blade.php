@extends('layouts.admin')

@section('title', 'Kategori - Admin')

@section('page-title', 'Kategori')

@section('content')

<div class="page-header-row">
    <div class="page-header">
        <h2>Management Kategori</h2>
        <p>Kelola kategori produk Beauty Store.</p>
    </div>

    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        + Tambah Kategori
    </a>
</div>

<div class="admin-card">

    @if($categories->count() > 0)

        <div class="table-wrap">

            <table class="table">

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Nama Kategori
                        </th>

                        <th>
                            Jumlah Produk
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($categories as $category)

                        <tr>

                            <td>
                                {{ $category->cat_id }}
                            </td>


                            <td>

                                <div style="display: flex; align-items: center; gap: 12px;">

                                    <div style="width: 42px; height: 42px; border-radius: 9px; background: var(--c-cream); display: flex; align-items: center; justify-content: center; font-size: 19px;">
                                        🏷️
                                    </div>

                                    <strong>
                                        {{ $category->cat_title }}
                                    </strong>

                                </div>

                            </td>


                            <td>

                                <span class="badge">
                                    {{ $category->products->count() }}
                                    Produk
                                </span>

                            </td>


                            <td>

                                <div class="row-actions">

                                    <a
                                        href="{{ route('admin.categories.show', $category->cat_id) }}"
                                        class="btn btn-outline btn-sm"
                                    >
                                        Detail
                                    </a>


                                    <a
                                        href="{{ route('admin.categories.edit', $category->cat_id) }}"
                                        class="btn btn-secondary btn-sm"
                                    >
                                        Edit
                                    </a>


                                    <form
                                        action="{{ route('admin.categories.destroy', $category->cat_id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?')"
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

            <div style="font-size: 40px; margin-bottom: 15px;">
                🏷️
            </div>

            <p style="margin-bottom: 15px;">
                Belum ada kategori.
            </p>

            <a
                href="{{ route('admin.categories.create') }}"
                class="btn btn-primary"
            >
                + Tambah Kategori
            </a>

        </div>

    @endif

</div>

@endsection