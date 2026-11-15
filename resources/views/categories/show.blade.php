@extends('layouts.admin')

@section('title', 'Detail Kategori - Admin')

@section('page-title', 'Detail Kategori')

@section('content')

<div class="page-header">

    <div>

        <h2>Detail Kategori</h2>

        <p>
            Informasi kategori dan produk yang berada di dalamnya.
        </p>

    </div>

</div>


<div class="admin-card">

    <div style="
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
    ">

        <div style="
            width: 65px;
            height: 65px;
            border-radius: 12px;
            background: var(--c-cream);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        ">
            🏷️
        </div>


        <div>

            <div style="
                color: var(--c-ink-soft);
                font-size: 13px;
                margin-bottom: 5px;
            ">
                ID Kategori
            </div>

            <strong style="
                font-size: 14px;
            ">
                #{{ $category->cat_id }}
            </strong>

        </div>


        <div>

            <div style="
                color: var(--c-ink-soft);
                font-size: 13px;
                margin-bottom: 5px;
            ">
                Nama Kategori
            </div>

            <strong style="
                font-size: 22px;
                color: var(--c-ink);
            ">
                {{ $category->cat_title }}
            </strong>

        </div>

    </div>


    <div style="
        border-top: 1px solid var(--c-border);
        padding-top: 25px;
    ">

        <h3 style="
            font-size: 18px;
            margin-bottom: 18px;
        ">
            Produk dalam Kategori
        </h3>


        @if($category->products->count() > 0)

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

                            <th style="padding: 13px;">
                                Produk
                            </th>

                            <th style="padding: 13px;">
                                Brand
                            </th>

                            <th style="padding: 13px;">
                                Harga
                            </th>

                            <th style="padding: 13px;">
                                Stok
                            </th>

                            <th style="padding: 13px;">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($category->products as $product)

                            <tr style="
                                border-top: 1px solid var(--c-border);
                            ">

                                <td style="padding: 13px;">

                                    <strong>
                                        {{ $product->name }}
                                    </strong>

                                </td>


                                <td style="padding: 13px;">
                                    {{ $product->brand }}
                                </td>


                                <td style="
                                    padding: 13px;
                                    white-space: nowrap;
                                ">

                                    Rp
                                    {{ number_format($product->price, 0, ',', '.') }}

                                </td>


                                <td style="padding: 13px;">
                                    {{ $product->stock }}
                                </td>


                                <td style="padding: 13px;">

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

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div style="
                padding: 30px;
                text-align: center;
                color: var(--c-ink-soft);
                background: var(--c-bg);
                border-radius: 8px;
            ">

                Belum ada produk dalam kategori ini.

            </div>

        @endif

    </div>


    <div style="
        display: flex;
        gap: 10px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid var(--c-border);
    ">

        <a
            href="{{ route('admin.categories.edit', $category->cat_id) }}"
            class="btn btn-primary"
        >
            Edit Kategori
        </a>


        <a
            href="{{ route('admin.categories.index') }}"
            class="btn"
            style="
                background: var(--c-border);
                color: var(--c-ink);
            "
        >
            Kembali
        </a>

    </div>

</div>

@endsection