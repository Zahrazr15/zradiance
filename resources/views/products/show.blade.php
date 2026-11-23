
@extends('layouts.admin')

@section('title', 'Detail Produk - Admin')

@section('page-title', 'Detail Produk')

@section('content')

<div class="page-header">

    <h2>Detail Produk</h2>

    <p>
        Informasi lengkap mengenai produk.
    </p>

</div>

<div class="admin-card">

    <div style="
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 35px;
        align-items: start;
    ">

        {{-- GAMBAR PRODUK --}}
        <div>

            @if($product->image)

                <img
                    src="{{ $product->image }}"
                    alt="{{ $product->name }}"
                    style="
                        width: 100%;
                        height: 320px;
                        object-fit: cover;
                        border-radius: 10px;
                        background: var(--c-cream);
                    "
                >

            @else

                <div style="
                    width: 100%;
                    height: 320px;
                    border-radius: 10px;
                    background: var(--c-cream);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--c-ink-soft);
                ">
                    Tidak ada gambar
                </div>

            @endif

        </div>


        {{-- INFORMASI PRODUK --}}
        <div>

            <h2 style="
                font-size: 28px;
                margin-bottom: 25px;
                color: var(--c-ink);
            ">
                {{ $product->name }}
            </h2>


            {{-- BRAND --}}
            <div style="margin-bottom: 18px;">

                <span style="
                    display: block;
                    color: var(--c-ink-soft);
                    font-size: 13px;
                    margin-bottom: 8px;
                ">
                    Brand
                </span>

                <div style="
                    display: flex;
                    align-items: center;
                    gap: 10px;
                ">

                    @if($product->brandRelation && $product->brandRelation->brand_logo)

                        <img
                            src="{{ asset('storage/' . $product->brandRelation->brand_logo) }}"
                            alt="{{ $product->brandRelation->brand_name }}"
                            style="
                                width: 45px;
                                height: 45px;
                                object-fit: contain;
                                background: white;
                                border: 1px solid var(--c-border);
                                border-radius: 8px;
                                padding: 5px;
                            "
                        >

                    @endif

                    <strong>
                        {{ $product->brandRelation->brand_name ?? $product->brand ?? '-' }}
                    </strong>

                </div>

            </div>


            {{-- KATEGORI --}}
            <div style="margin-bottom: 18px;">

                <span style="
                    display: block;
                    color: var(--c-ink-soft);
                    font-size: 13px;
                    margin-bottom: 5px;
                ">
                    Kategori
                </span>

                <strong>
                    {{ $product->category->cat_title ?? 'Tidak ada kategori' }}
                </strong>

            </div>


            {{-- HARGA --}}
            <div style="margin-bottom: 18px;">

                <span style="
                    display: block;
                    color: var(--c-ink-soft);
                    font-size: 13px;
                    margin-bottom: 5px;
                ">
                    Harga
                </span>

                <strong style="
                    font-size: 22px;
                    color: #d97706;
                ">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </strong>

            </div>


            {{-- STOK --}}
            <div style="margin-bottom: 18px;">

                <span style="
                    display: block;
                    color: var(--c-ink-soft);
                    font-size: 13px;
                    margin-bottom: 5px;
                ">
                    Stok
                </span>

                <strong>
                    {{ $product->stock }} produk
                </strong>

            </div>


            {{-- DESKRIPSI --}}
            <div style="margin-bottom: 25px;">

                <span style="
                    display: block;
                    color: var(--c-ink-soft);
                    font-size: 13px;
                    margin-bottom: 7px;
                ">
                    Deskripsi
                </span>

                <p style="
                    color: var(--c-ink-soft);
                    line-height: 1.7;
                ">
                    {{ $product->description ?: 'Tidak ada deskripsi.' }}
                </p>

            </div>


            {{-- BUTTON --}}
            <div style="
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            ">

                <a
                    href="{{ route('admin.products.edit', $product) }}"
                    class="btn btn-primary"
                >
                    Edit Produk
                </a>


                <a
                    href="{{ route('admin.products.index') }}"
                    class="btn btn-secondary"
                >
                    Kembali
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
                        class="btn btn-danger"
                    >
                        Hapus
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


<style>

@media (max-width: 800px) {

    .admin-card > div {
        grid-template-columns: 1fr !important;
    }

}

</style>

@endsection
