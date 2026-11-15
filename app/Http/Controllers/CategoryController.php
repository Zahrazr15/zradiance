<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * =========================================================
     * ADMIN
     * =========================================================
     */

    /**
     * Menampilkan semua kategori untuk admin.
     */
    public function index()
    {
        $categories = Category::with('products')
            ->orderBy('cat_title')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
    * Menampilkan form tambah kategori.
    */
    public function create()
    {
        return view('categories.create');
    }
    
    /**
     * Menampilkan form tambah kategori.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cat_title'   => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        Category::create($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }


    /**
     * Menampilkan detail kategori untuk admin.
     */
    public function show(Category $category)
    {
        $category->load('products');

        return view('categories.show', compact('category'));
    }

    /**
     * Menampilkan form edit kategori.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Memperbarui kategori.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'cat_title'   => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $category->update($validated);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }


    /**
     * Menghapus kategori.
     */
    public function destroy(Category $category)
    {
        // FK products.product_cat memakai cascade: tanpa blok ini, menghapus
        // kategori ikut menghapus semua produknya (dan riwayat order_items).
        if ($category->products()->exists()) {
            return back()->withErrors([
                'category' => "Kategori \"{$category->cat_title}\" masih punya {$category->products()->count()} produk. Pindahkan atau hapus produknya dulu.",
            ]);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $categories = Category::query()
            ->when($request->q, fn ($q, $s) => $q->where('cat_title', 'like', '%' . trim($s) . '%'))
            ->orderBy('cat_title')
            ->limit(20)
            ->get(['cat_id', 'cat_title']);

        return response()->json($categories);
    }

    /**
     * =========================================================
     * USER
     * =========================================================
     */

    /**
     * Menampilkan daftar kategori untuk user.
     */
    public function userIndex()
    {
        $categories = Category::withCount('products')
            ->orderBy('cat_title')
            ->get();

        return view('user.categories.index', compact('categories'));
    }

    /**
     * Menampilkan produk berdasarkan kategori untuk user.
     */
    public function userShow($cat_id)
    {
        $category = Category::with('products')
            ->findOrFail($cat_id);

        return view('user.categories.show', compact('category'));
    }
}