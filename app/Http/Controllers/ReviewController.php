<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Menyimpan atau memperbarui review pelanggan.
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $user = Auth::user();

        // Periksa apakah pelanggan memiliki pesanan
        // yang sudah dibayar dan selesai untuk produk ini.
        $hasEligiblePurchase = Order::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->exists();

        if (!$hasEligiblePurchase) {
            return back()->withErrors([
                'review' => 'Review hanya dapat diberikan setelah produk dibeli, dibayar, dan pesanan selesai.',
            ]);
        }

        // Simpan review baru atau perbarui review sebelumnya.
        Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $product->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]
        );

        return back()->with(
            'success',
            'Review produk berhasil disimpan.'
        );
    }
}
