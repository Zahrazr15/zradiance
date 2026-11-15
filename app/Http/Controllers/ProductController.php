<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk untuk admin.
     */
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->get();

        return view('products.index', compact('products'));
    }

    /**
     * Menampilkan form tambah produk.
     */
    public function create()
    {
        $categories = Category::orderBy('cat_title')->get();

        return view('products.create', compact('categories'));
    }

    /**
     * Menyimpan produk baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_cat' => 'required|exists:categories,cat_id',
            'brand' => 'required|string|max:255',
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

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail produk untuk admin.
     */
    public function show(Product $product)
    {
        $product->load('category');

        return view('products.show', compact('product'));
    }

    /**
     * Menampilkan form edit produk.
     */
    public function edit(Product $product)
    {
        $categories = Category::orderBy('cat_title')->get();

        return view('products.edit', compact(
            'product',
            'categories'
        ));
    }

    /**
     * Mengupdate produk.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_cat' => 'required|exists:categories,cat_id',
            'brand' => 'required|string|max:255',
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

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Menghapus produk.
     */
    public function destroy(Product $product)
    {
        if ($product->orderItems()->exists()) {
            return back()->withErrors([
                'product' => "Produk \"{$product->name}\" sudah pernah dipesan, jadi tidak bisa dihapus. Set stoknya ke 0 kalau ingin menyembunyikannya dari penjualan.",
            ]);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Katalog semua produk untuk pelanggan.
     */
    public function userIndex(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'category' => 'nullable|exists:categories,cat_id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0|gte:min_price',
            'sort' => 'nullable|in:latest,price_low,price_high',
        ]);

        $products = Product::with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')

            // Pencarian nama produk dan brand.
            ->when($request->filled('q'), function ($query) use ($request) {
                $keyword = trim($request->q);

                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%")
                        ->orWhere('brand', 'like', "%{$keyword}%");
                });
            })

            // Filter kategori.
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->where('product_cat', $request->category);
            })

            // Filter harga minimum.
            ->when($request->filled('min_price'), function ($query) use ($request) {
                $query->where('price', '>=', $request->min_price);
            })

            // Filter harga maksimum.
            ->when($request->filled('max_price'), function ($query) use ($request) {
                $query->where('price', '<=', $request->max_price);
            })

            // Pengurutan produk.
            ->when($request->sort === 'price_low', function ($query) {
                $query->orderBy('price', 'asc');
            })
            ->when($request->sort === 'price_high', function ($query) {
                $query->orderBy('price', 'desc');
            })
            ->when(
                !$request->filled('sort') || $request->sort === 'latest',
                function ($query) {
                    $query->latest();
                }
            )

            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('cat_title')->get();

        return view('user.products.index', compact(
            'products',
            'categories'
        ));
    }

    /**
     * Detail produk untuk pelanggan.
     */
    public function userShow(Product $product)
    {
        $product->load([
            'category',
            'reviews.user',
        ]);

        // Memeriksa apakah pelanggan pernah membeli produk,
        // sudah membayar, dan pesanan sudah selesai.
        $canReview = Order::query()
            ->where('user_id', auth()->id())
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->exists();

        // Mengambil ulasan milik pelanggan yang sedang login.
        $myReview = $product->reviews
            ->firstWhere('user_id', auth()->id());

        return view('user.products.show', compact(
            'product',
            'canReview',
            'myReview'
        ));
    }
}
