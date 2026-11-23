<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Order;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /** Daftar produk untuk admin. */
    public function index()
    {
        $products = Product::with(['category', 'brandRelation'])
            ->latest()
            ->get();

        return view('products.index', compact('products'));
    }

    /** Form tambah produk. */
    public function create()
    {
        $categories = Category::orderBy('cat_title')->get();
        $brands = Brand::where('is_active', true)->orderBy('brand_name')->get();

        return view('products.create', compact('categories', 'brands'));
    }

    /** Simpan produk baru. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_cat' => 'required|exists:categories,cat_id',
            'brand_id' => 'required|exists:brands,brand_id,is_active,1',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:999999999',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:1|max:100000',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'skin_type' => 'nullable|string|max:255',
            'ingredients' => 'nullable|string',
            'benefits' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'warnings' => 'nullable|string',
        ]);

        $brand = Brand::findOrFail($validated['brand_id']);
        $validated['brand'] = $brand->brand_name; // Sinkronisasi kolom brand lama.

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    /** Detail produk admin. */
    public function show(Product $product)
    {
        $product->load(['category', 'brandRelation']);

        return view('products.show', compact('product'));
    }

    /** Form edit produk. */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('cat_title')->get();
        $brands = Brand::where('is_active', true)
            ->orWhere('brand_id', $product->brand_id)
            ->orderBy('brand_name')
            ->get();

        return view('products.edit', compact('product', 'categories', 'brands'));
    }

    /** Update produk. */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_cat' => 'required|exists:categories,cat_id',
            'brand_id' => 'required|exists:brands,brand_id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0|max:999999999',
            'stock' => 'required|integer|min:0',
            'weight' => 'required|integer|min:1|max:100000',
            'image' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'skin_type' => 'nullable|string|max:255',
            'ingredients' => 'nullable|string',
            'benefits' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'warnings' => 'nullable|string',
        ]);

        $brand = Brand::findOrFail($validated['brand_id']);
        $validated['brand'] = $brand->brand_name; // Sinkronisasi nama brand legacy.
        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    /** Hapus produk, kecuali pernah tercatat dalam pesanan. */
    public function destroy(Product $product)
    {
        if ($product->orderItems()->exists()) {
            return back()->withErrors([
                'product' => "Produk \"{$product->name}\" sudah pernah dipesan, jadi tidak bisa dihapus. Set stoknya ke 0 kalau ingin menyembunyikannya dari penjualan.",
            ]);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    /** Katalog produk untuk pelanggan dengan filter brand, kategori, harga, pencarian, dan sorting. */
    public function userIndex(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'category' => 'nullable|exists:categories,cat_id',
            'brand' => 'nullable|exists:brands,brand_id,is_active,1',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'sort' => 'nullable|in:latest,price_low,price_high',
        ]);

        $products = Product::with(['category', 'brandRelation'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = trim($request->q);
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('brand', 'like', "%{$keyword}%")
                        ->orWhereHas('brandRelation', function ($brandQuery) use ($keyword) {
                            $brandQuery->where('brand_name', 'like', "%{$keyword}%");
                        });
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('product_cat', $request->category))
            ->when($request->filled('brand'), fn ($query) => $query->where('brand_id', $request->brand))
            ->when($request->filled('min_price'), fn ($query) => $query->where('price', '>=', $request->min_price))
            ->when($request->filled('max_price'), fn ($query) => $query->where('price', '<=', $request->max_price))
            ->when($request->sort === 'price_low', fn ($query) => $query->orderBy('price', 'asc'))
            ->when($request->sort === 'price_high', fn ($query) => $query->orderBy('price', 'desc'))
            ->when(!$request->filled('sort') || $request->sort === 'latest', fn ($query) => $query->latest())
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('cat_title')->get();
        $brands = Brand::where('is_active', true)->orderBy('brand_name')->get();

        return view('user.products.index', compact('products', 'categories', 'brands'));
    }

    /** Detail produk pelanggan. */
    public function userShow(Product $product)
    {
        $product->load(['category', 'brandRelation', 'reviews.user']);

        $canReview = Order::query()
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereHas('items', fn ($query) => $query->where('product_id', $product->id))
            ->exists();

        $myReview = $product->reviews->firstWhere('user_id', auth()->id());

        return view('user.products.show', compact('product', 'canReview', 'myReview'));
    }
}
