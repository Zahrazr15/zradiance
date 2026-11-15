@extends('layouts.admin')

@section('title', 'Kategori - Admin')

@section('page-title', 'Kategori')

@section('content')

<div class="page-header">

    <div>
        <h2>Management Kategori</h2>

        <p>
            Kelola kategori produk Beauty Store.
        </p>
    </div>

    <a
        href="{{ route('admin.categories.create') }}"
        class="btn btn-primary"
    >
        + Tambah Kategori
    </a>

</div>


<div class="admin-card">

    @if($categories->count() > 0)

        <div style="overflow-x: auto;">

            <table style="width: 100%; border-collapse: collapse;">

                <thead>

                    <tr style="
                        background: var(--c-bg);
                        text-align: left;
                    ">

                        <th style="padding: 14px;">
                            ID
                        </th>

                        <th style="padding: 14px;">
                            Nama Kategori
                        </th>

                        <th style="padding: 14px;">
                            Jumlah Produk
                        </th>

                        <th style="padding: 14px;">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($categories as $category)

                        <tr style="
                            border-top: 1px solid var(--c-border);
                        ">

                            <td style="padding: 14px;">
                                {{ $category->cat_id }}
                            </td>


                            <td style="padding: 14px;">

                                <div style="
                                    display: flex;
                                    align-items: center;
                                    gap: 12px;
                                ">

                                    <div style="
                                        width: 42px;
                                        height: 42px;
                                        border-radius: 9px;
                                        background: var(--c-cream);
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        font-size: 19px;
                                    ">
                                        🏷️
                                    </div>

                                    <strong>
                                        {{ $category->cat_title }}
                                    </strong>

                                </div>

                            </td>


                            <td style="padding: 14px;">

                                <span style="
                                    display: inline-block;
                                    background: var(--c-cream);
                                    padding: 6px 10px;
                                    border-radius: 20px;
                                    font-size: 12px;
                                ">
                                    {{ $category->products->count() }}
                                    Produk
                                </span>

                            </td>


                            <td style="padding: 14px;">

                                <div style="
                                    display: flex;
                                    gap: 6px;
                                    flex-wrap: wrap;
                                ">

                                    <a
                                        href="{{ route('admin.categories.show', $category->cat_id) }}"
                                        class="btn"
                                        style="
                                            background: var(--c-border);
                                            color: var(--c-ink);
                                        "
                                    >
                                        Detail
                                    </a>


                                    <a
                                        href="{{ route('admin.categories.edit', $category->cat_id) }}"
                                        class="btn"
                                        style="
                                            background: #f59e0b;
                                            color: var(--c-ink);
                                        "
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
                                            class="btn"
                                            style="
                                                background: #dc2626;
                                                color: var(--c-surface);
                                            "
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

        <div style="
            text-align: center;
            padding: 50px;
            color: var(--c-ink-soft);
        ">

            <div style="
                font-size: 40px;
                margin-bottom: 15px;
            ">
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