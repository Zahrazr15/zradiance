<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Menampilkan daftar brand untuk admin.
     */
    public function index()
    {
        $brands = Brand::withCount('products')
            ->orderBy('brand_name')
            ->paginate(15);

        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Menampilkan form tambah brand.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Menyimpan brand baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255|unique:brands,brand_name',
            'brand_slug' => 'nullable|string|max:255|unique:brands,brand_slug',
            'brand_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $slugInput = $validated['brand_slug'] ?: $validated['brand_name'];

        $validated['brand_slug'] = $this->makeUniqueSlug($slugInput);

        if ($request->hasFile('brand_logo')) {
            $validated['brand_logo'] = $request->file('brand_logo')
                ->store('brands', 'public');
        } else {
            $validated['brand_logo'] = null;
        }

        Brand::create($validated);

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail brand.
     */
    public function show(Brand $brand)
    {
        $brand->load('products');

        return view('admin.brands.show', compact('brand'));
    }

    /**
     * Menampilkan form edit brand.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Memperbarui brand.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'brand_name' => 'required|string|max:255|unique:brands,brand_name,' . $brand->brand_id . ',brand_id',
            'brand_slug' => 'nullable|string|max:255|unique:brands,brand_slug,' . $brand->brand_id . ',brand_id',
            'brand_logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $slugInput = $validated['brand_slug'] ?: $validated['brand_name'];

        $validated['brand_slug'] = $this->makeUniqueSlug(
            $slugInput,
            $brand->brand_id
        );

        $oldLogo = $brand->brand_logo;

        if ($request->hasFile('brand_logo')) {
            $validated['brand_logo'] = $request->file('brand_logo')
                ->store('brands', 'public');
        }

        $brand->update($validated);

        // Sinkronkan nama brand lama di tabel products.
        // brand_id tetap menjadi relasi utama.
        if ($brand->wasChanged('brand_name')) {
            $brand->products()->update([
                'brand' => $brand->brand_name,
            ]);
        }

        // Hapus logo lama setelah logo baru berhasil disimpan.
        if (
            $request->hasFile('brand_logo')
            && $oldLogo
        ) {
            Storage::disk('public')->delete($oldLogo);
        }

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand berhasil diperbarui.');
    }

    /**
     * Menghapus brand.
     */
    public function destroy(Brand $brand)
    {
        if ($brand->products()->exists()) {
            return back()->withErrors([
                'brand' => "Brand {$brand->brand_name} masih digunakan oleh {$brand->products()->count()} produk. Nonaktifkan brand jika tidak ingin digunakan lagi.",
            ]);
        }

        if ($brand->brand_logo) {
            Storage::disk('public')->delete($brand->brand_logo);
        }

        $brand->delete();

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'Brand berhasil dihapus.');
    }

    /**
     * Membuat slug unik.
     */
    private function makeUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);

        if ($baseSlug === '') {
            $baseSlug = 'brand';
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            Brand::where('brand_slug', $slug)
                ->when($ignoreId, function ($query) use ($ignoreId) {
                    $query->where('brand_id', '!=', $ignoreId);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}