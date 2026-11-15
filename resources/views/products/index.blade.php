@extends('layouts.admin')

@section('title', 'Produk - Admin')

@section('page-title', 'Produk')

@section('content')

<div class="page-header">

    <div style="
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    ">

        <div>
            <h2>Management Produk</h2>

            <p>
                Kelola produk, kategori, harga, dan stok.
            </p>
        </div>

        <a
            href="{{ route('admin.products.create') }}"
            class="btn btn-primary"
        >
            + Tambah Produk
        </a>

    </div>

</div>

<div class="admin-card">

    @if($products->count() > 0)

        <div style="overflow-x: auto;">

            <table style="
                width: 100%;
                border-collapse: collapse;
            ">

                <thead>

                    <tr style="
                        background: var(--c-bg);
                        text-align: left;
                    ">

                        <th style="padding: 14px;">
                            Produk
                        </th>

                        <th style="padding: 14px;">
                            Kategori
                        </th>

                        <th style="padding: 14px;">
                            Harga
                        </th>

                        <th style="padding: 14px;">
                            Stok
                        </th>

                        <th style="padding: 14px;">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($products as $product)

                        <tr style="
                            border-top: 1px solid var(--c-border);
                        ">

                            {{-- PRODUK --}}
                            <td style="padding: 14px;">

                                <div style="
                                    display: flex;
                                    align-items: center;
                                    gap: 12px;
                                ">

                                    <div style="
                                        width: 60px;
                                        height: 60px;
                                        border-radius: 8px;
                                        overflow: hidden;
                                        background: var(--c-cream);
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        flex-shrink: 0;
                                    ">

                                        @if($product->image)

                                            <img
                                                src="{{ $product->image }}"
                                                alt="{{ $product->name }}"
                                                style="
                                                    width: 100%;
                                                    height: 100%;
                                                    object-fit: cover;
                                                "
                                            >

                                        @else

                                            <span style="
                                                font-size: 11px;
                                                color: var(--c-ink-soft);
                                            ">
                                                No Image
                                            </span>

                                        @endif

                                    </div>


                                    <div>

                                        <div style="
                                            font-size: 12px;
                                            color: var(--c-ink-soft);
                                            margin-bottom: 4px;
                                        ">
                                            {{ $product->brand }}
                                        </div>

                                        <strong>
                                            {{ $product->name }}
                                        </strong>

                                    </div>

                                </div>

                            </td>


                            {{-- KATEGORI --}}
                            <td style="padding: 14px;">

                                <span style="
                                    display: inline-block;
                                    background: var(--c-cream);
                                    padding: 6px 10px;
                                    border-radius: 20px;
                                    font-size: 12px;
                                ">
                                    {{ $product->category->cat_title ?? '-' }}
                                </span>

                            </td>


                            {{-- HARGA --}}
                            <td style="
                                padding: 14px;
                                white-space: nowrap;
                            ">

                                <strong>
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </strong>

                            </td>


                            {{-- STOK --}}
                            <td style="padding: 14px;">

                                {{ $product->stock }}

                            </td>


                            {{-- AKSI --}}
                            <td style="padding: 14px;">

                                <div style="
                                    display: flex;
                                    gap: 6px;
                                    flex-wrap: wrap;
                                ">

                                    <a
                                        href="{{ route('admin.products.show', $product) }}"
                                        class="btn"
                                        style="
                                            background: var(--c-border);
                                            color: var(--c-ink);
                                        "
                                    >
                                        Detail
                                    </a>


                                    <a
                                        href="{{ route('admin.products.edit', $product) }}"
                                        class="btn"
                                        style="
                                            background: #f59e0b;
                                            color: var(--c-ink);
                                        "
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
                                            class="btn"
                                            style="
                                                background: #dc2626;
                                                color: white;
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