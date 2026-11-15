<?php

namespace App\Services;

use App\Exceptions\StockException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderStockService
{
    /**
     * Mengurangi stok satu kali untuk setiap pesanan.
     */
    public function deduct(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::with('items')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->status === 'cancelled') {
                throw new StockException(
                    'Stok tidak dapat dipotong untuk pesanan yang sudah dibatalkan.'
                );
            }

            // Jangan kurangi stok dua kali.
            if ($lockedOrder->stock_deducted_at !== null) {
                return;
            }

            $items = $lockedOrder->items->sortBy('product_id');
            $products = [];

            // Periksa seluruh produk terlebih dahulu.
            foreach ($items as $item) {
                $product = Product::lockForUpdate()
                    ->find($item->product_id);

                if (!$product) {
                    throw new StockException(
                        "Produk {$item->product_name} sudah tidak tersedia."
                    );
                }

                if ($item->quantity < 1 || $product->stock < $item->quantity) {
                    throw new StockException(
                        "Stok {$item->product_name} tidak mencukupi."
                    );
                }

                $products[] = [
                    'product' => $product,
                    'quantity' => (int) $item->quantity,
                ];
            }

            if ($items->isEmpty()) {
                throw new StockException(
                    'Pesanan tidak memiliki produk untuk diproses.'
                );
            }

            // Potong stok setelah semua produk lolos pemeriksaan.
            foreach ($products as $entry) {
                $entry['product']->decrement(
                    'stock',
                    $entry['quantity']
                );
            }

            $lockedOrder->stock_deducted_at = now();
            $lockedOrder->save();
        });
    }

    /**
     * Mengembalikan stok satu kali jika sebelumnya sudah dikurangi.
     */
    public function restore(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $lockedOrder = Order::with('items')
                ->lockForUpdate()
                ->findOrFail($order->id);

            // Stok belum pernah dikurangi.
            if ($lockedOrder->stock_deducted_at === null) {
                return;
            }

            $items = $lockedOrder->items->sortBy('product_id');
            $products = [];

            // Pastikan semua produk ditemukan sebelum mengembalikan stok.
            foreach ($items as $item) {
                $product = Product::lockForUpdate()
                    ->find($item->product_id);

                if (!$product) {
                    throw new StockException(
                        "Produk {$item->product_name} tidak ditemukan. Pengembalian stok dibatalkan."
                    );
                }

                $products[] = [
                    'product' => $product,
                    'quantity' => (int) $item->quantity,
                ];
            }

            foreach ($products as $entry) {
                $entry['product']->increment(
                    'stock',
                    $entry['quantity']
                );
            }

            // Tandai bahwa stok sudah dikembalikan.
            $lockedOrder->stock_deducted_at = null;
            $lockedOrder->save();
        });
    }
}
