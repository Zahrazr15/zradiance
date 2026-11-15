@extends('layouts.admin')

@section('title', 'Tambah Kategori - Admin')

@section('page-title', 'Tambah Kategori')

@section('content')

<div class="page-header">

    <div>

        <h2>Tambah Kategori</h2>

        <p>
            Tambahkan kategori baru untuk produk Beauty Store.
        </p>

    </div>

</div>


<div class="admin-card">

    <form
        action="{{ route('admin.categories.store') }}"
        method="POST"
    >

        @csrf


        <div style="margin-bottom: 20px;">

            <label style="
                display: block;
                margin-bottom: 7px;
                font-weight: 600;
            ">
                Nama Kategori
            </label>


            <input
                type="text"
                name="cat_title"
                value="{{ old('cat_title') }}"
                placeholder="Contoh: Skincare"
                required
                style="
                    width: 100%;
                    padding: 11px;
                    border: 1px solid var(--c-border);
                    border-radius: 7px;
                    font-size: 14px;
                "
            >


            @error('cat_title')

                <small style="
                    display: block;
                    color: #dc2626;
                    margin-top: 5px;
                ">
                    {{ $message }}
                </small>

            @enderror

        </div>


        <div style="
            display: flex;
            gap: 10px;
        ">

            <button
                type="submit"
                class="btn btn-primary"
            >
                Simpan Kategori
            </button>


            <a
                href="{{ route('admin.categories.index') }}"
                class="btn"
                style="
                    background: var(--c-border);
                    color: var(--c-ink);
                "
            >
                Batal
            </a>

        </div>

    </form>

</div>

@endsection