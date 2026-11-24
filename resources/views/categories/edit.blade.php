@extends('layouts.admin')

@section('title', 'Edit Kategori - Admin')

@section('page-title', 'Edit Kategori')

@section('content')

<div class="page-header">

    <div>

        <h2>Edit Kategori</h2>

        <p>
            Perbarui informasi kategori produk.
        </p>

    </div>

</div>


<div class="admin-card">

    <form
        action="{{ route('admin.categories.update', $category->cat_id) }}"
        method="POST"
    >

        @csrf

        @method('PUT')


        <div style="margin-bottom: 20px;">

            <label style="display: block; margin-bottom: 7px; font-weight: 600;">
                Nama Kategori
            </label>


            <input
                type="text"
                name="cat_title"
                value="{{ old('cat_title', $category->cat_title) }}"
                required
                style="width: 100%; padding: 11px; border: 1px solid var(--c-border); border-radius: 7px; font-size: 14px;"
            >


            @error('cat_title')

                <small style="display: block; color: #dc2626; margin-top: 5px;">
                    {{ $message }}
                </small>

            @enderror

        </div>

        <div class="form-group">
            <label for="description">Deskripsi singkat</label>
            <input type="text" id="description" name="description" maxlength="255"
                value="{{ old('description', $category->description ?? '') }}">
        </div>

        <div style="display: flex; gap: 10px;">

            <button
                type="submit"
                class="btn btn-primary"
            >
                Simpan Perubahan
            </button>


            <a
                href="{{ route('admin.categories.index') }}"
                class="btn btn-outline"
            >
                Batal
            </a>

        </div>

    </form>

</div>

@endsection