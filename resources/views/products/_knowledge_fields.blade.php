<div class="form-group">
    <label>Jenis kulit yang cocok</label>
    <input type="text" name="skin_type"
           value="{{ old('skin_type', $product->skin_type ?? '') }}"
           placeholder="Contoh: berminyak, kombinasi  (atau: semua jenis kulit)">
</div>

<div class="form-group">
    <label>Kandungan</label>
    <textarea name="ingredients" rows="3"
              placeholder="Contoh: Niacinamide, hyaluronic acid, ceramide">{{ old('ingredients', $product->ingredients ?? '') }}</textarea>
</div>

<div class="form-group">
    <label>Fungsi / manfaat</label>
    <textarea name="benefits" rows="3">{{ old('benefits', $product->benefits ?? '') }}</textarea>
</div>

<div class="form-group">
    <label>Cara penggunaan</label>
    <textarea name="how_to_use" rows="3">{{ old('how_to_use', $product->how_to_use ?? '') }}</textarea>
</div>

<div class="form-group">
    <label>Hal yang perlu diperhatikan</label>
    <textarea name="warnings" rows="3">{{ old('warnings', $product->warnings ?? '') }}</textarea>
</div>