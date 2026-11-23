@extends('layouts.admin')

@section('page-title', 'Tambah Brand')

@section('content')

<div class="admin-card-glass">
    <div class="admin-card-heading">
        <div>
            <h3>Tambah Brand Baru</h3>
            <p>Tambahkan identitas brand ke ZRadiance.</p>
        </div>

        <a href="{{ route('admin.brands.index') }}"
           class="btn btn-outline">
            Kembali
        </a>
    </div>

    <form action="{{ route('admin.brands.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        {{-- Nama Brand --}}
        <div class="form-group">
            <label for="brand_name">Nama Brand</label>

            <input type="text"
                   id="brand_name"
                   name="brand_name"
                   value="{{ old('brand_name') }}"
                   required
                   maxlength="255"
                   placeholder="Contoh: Azarine">
        </div>

        {{-- Slug --}}
        <div class="form-group">
            <label for="brand_slug">Slug</label>

            <input type="text"
                   id="brand_slug"
                   name="brand_slug"
                   value="{{ old('brand_slug') }}"
                   maxlength="255"
                   placeholder="Otomatis dari nama brand">

            <small>Slug digunakan untuk URL brand.</small>
        </div>

        {{-- Logo --}}
        <div class="form-group">
            <label for="brand_logo">Logo Brand</label>

            <input type="file"
                   id="brand_logo"
                   name="brand_logo"
                   accept=".jpg,.jpeg,.png,.webp">

            <small>Format JPG, PNG, atau WebP. Maksimal 2 MB.</small>

            <div id="logo-preview-container"
                 style="display:none;margin-top:15px;">
                <p>Preview logo:</p>

                <img id="logo-preview"
                     src=""
                     alt="Preview logo"
                     style="max-width:180px;max-height:180px;object-fit:contain;border:1px solid #ddd;border-radius:10px;padding:10px;">
            </div>
        </div>

        {{-- Deskripsi --}}
        <div class="form-group">
            <label for="description">Deskripsi Brand</label>

            <textarea id="description"
                      name="description"
                      rows="4"
                      placeholder="Deskripsi singkat brand">{{ old('description') }}</textarea>
        </div>

        {{-- Status --}}
        <div class="form-group">
            <label for="is_active">Status Brand</label>

            <select id="is_active" name="is_active" required>
                <option value="1"
                    {{ old('is_active', '1') == '1' ? 'selected' : '' }}>
                    Aktif
                </option>

                <option value="0"
                    {{ old('is_active') == '0' ? 'selected' : '' }}>
                    Nonaktif
                </option>
            </select>
        </div>

        {{-- Tombol --}}
        <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;">
            <button type="submit" class="btn btn-primary">
                Simpan Brand
            </button>

            <a href="{{ route('admin.brands.index') }}"
               class="btn btn-outline">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const nameInput = document.getElementById('brand_name');
    const slugInput = document.getElementById('brand_slug');
    const logoInput = document.getElementById('brand_logo');
    const preview = document.getElementById('logo-preview');
    const previewContainer = document.getElementById('logo-preview-container');

    let slugManuallyEdited = slugInput.value.trim() !== '';

    slugInput.addEventListener('input', function () {
        slugManuallyEdited = true;
    });

    nameInput.addEventListener('input', function () {
        if (slugManuallyEdited) {
            return;
        }

        slugInput.value = this.value
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    });

    logoInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            previewContainer.style.display = 'none';
            preview.src = '';
            return;
        }

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('Format logo harus JPG, PNG, atau WebP.');
            this.value = '';
            previewContainer.style.display = 'none';
            preview.src = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran logo maksimal 2 MB.');
            this.value = '';
            previewContainer.style.display = 'none';
            preview.src = '';
            return;
        }

        preview.src = URL.createObjectURL(file);
        previewContainer.style.display = 'block';
    });
});
</script>

@endsection