<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Nama session untuk menyimpan pilihan produk per pengguna.
     */
    private function selectionKey(User $user): string
    {
        return 'cart.selected.' . $user->id;
    }

    /**
     * Mengambil isi keranjang.
     */
    public function items(User $user): array
    {
        return CartItem::with('product')
            ->where('user_id', $user->id)
            ->orderBy('id')
            ->get()
            ->filter(fn (CartItem $item) => $item->product !== null)
            ->mapWithKeys(fn (CartItem $item) => [
                $item->product_id => [
                    'id'       => $item->product_id,
                    'brand'    => $item->product->brand,
                    'name'     => $item->product->name,
                    'price'    => (float) $item->product->price,
                    'image'    => $item->product->image,
                    'stock'    => (int) $item->product->stock,
                    'quantity' => (int) $item->quantity,
                ],
            ])
            ->all();
    }

    /**
     * Menghitung total seluruh barang.
     */
    public function total(array $items): float
    {
        return array_reduce(
            $items,
            fn ($carry, $item) => $carry + (
                $item['price'] * $item['quantity']
            ),
            0.0
        );
    }

    /**
     * Menghitung jumlah seluruh barang untuk badge keranjang.
     */
    public function count(User $user): int
    {
        return (int) CartItem::where('user_id', $user->id)
            ->sum('quantity');
    }

    /**
     * Mengambil ID produk yang sedang dipilih.
     * ID yang sudah tidak ada di keranjang akan diabaikan.
     */
    public function selectedItems(User $user): array
    {
        $storedIds = session()->get($this->selectionKey($user), []);

        if (!is_array($storedIds)) {
            $storedIds = [];
        }

        $cartIds = CartItem::where('user_id', $user->id)
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $selectedIds = array_values(array_intersect(
            array_unique(array_map('intval', $storedIds)),
            $cartIds
        ));

        session()->put($this->selectionKey($user), $selectedIds);

        return $selectedIds;
    }

    /**
     * Memperbarui pilihan produk.
     * Hanya produk yang benar-benar ada di keranjang yang dapat dipilih.
     */
    public function updateSelection(User $user, array $productIds): array
    {
        $cartIds = CartItem::where('user_id', $user->id)
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $selectedIds = array_values(array_unique(array_intersect(
            array_map('intval', $productIds),
            $cartIds
        )));

        session()->put($this->selectionKey($user), $selectedIds);

        return $selectedIds;
    }

    /**
     * Memilih seluruh produk yang ada di keranjang.
     */
    public function selectAll(User $user): array
    {
        $productIds = CartItem::where('user_id', $user->id)
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        session()->put($this->selectionKey($user), $productIds);

        return $productIds;
    }

    /**
     * Menghapus ID produk tertentu dari daftar pilihan.
     */
    public function forgetSelected(User $user, array $productIds): void
    {
        $selectedIds = $this->selectedItems($user);

        $remainingIds = array_values(array_diff(
            $selectedIds,
            array_map('intval', $productIds)
        ));

        session()->put($this->selectionKey($user), $remainingIds);
    }

    /**
     * Menambahkan produk ke keranjang.
     * Produk yang baru ditambahkan otomatis dipilih.
     */
    public function add(User $user, Product $product): ?string
    {
        return DB::transaction(function () use ($user, $product) {
            $lockedProduct = Product::whereKey($product->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedProduct || $lockedProduct->stock < 1) {
                return "Stok {$product->name} habis.";
            }

            $item = CartItem::where('user_id', $user->id)
                ->where('product_id', $lockedProduct->id)
                ->lockForUpdate()
                ->first();

            $newQty = ($item ? $item->quantity : 0) + 1;

            if ($newQty > $lockedProduct->stock) {
                return "Stok {$lockedProduct->name} hanya tersisa {$lockedProduct->stock}.";
            }

            if (!$item) {
                $item = new CartItem([
                    'user_id' => $user->id,
                    'product_id' => $lockedProduct->id,
                ]);
            }

            $item->quantity = $newQty;
            $item->save();

            $selectedIds = $this->selectedItems($user);

            if (!in_array((int) $lockedProduct->id, $selectedIds, true)) {
                $selectedIds[] = (int) $lockedProduct->id;
            }

            session()->put($this->selectionKey($user), $selectedIds);

            return null;
        });
    }

    /**
     * Menambah kuantitas produk.
     */
    public function increase(User $user, int $productId): ?string
    {
        return DB::transaction(function () use ($user, $productId) {
            $item = CartItem::with('product')
                ->where('user_id', $user->id)
                ->where('product_id', $productId)
                ->lockForUpdate()
                ->first();

            if (!$item || !$item->product) {
                return 'Produk tidak ditemukan di keranjang.';
            }

            $product = Product::whereKey($productId)
                ->lockForUpdate()
                ->first();

            if (!$product || $item->quantity + 1 > $product->stock) {
                return "Stok {$item->product->name} tidak mencukupi.";
            }

            $item->increment('quantity');

            return null;
        });
    }

    /**
     * Mengurangi kuantitas produk.
     */
    public function decrease(User $user, int $productId): void
    {
        $item = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if (!$item) {
            return;
        }

        if ($item->quantity > 1) {
            $item->decrement('quantity');
            return;
        }

        $item->delete();

        $this->forgetSelected($user, [$productId]);
    }

    /**
     * Menghapus produk dari keranjang dan pilihan.
     */
    public function remove(User $user, int $productId): void
    {
        CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        $this->forgetSelected($user, [$productId]);
    }

    /**
     * Menyesuaikan keranjang dengan stok terbaru.
     *
     * @return string[]
     */
    public function syncWithStock(User $user): array
    {
        $warnings = [];
        $removedIds = [];

        CartItem::with('product')
            ->where('user_id', $user->id)
            ->get()
            ->each(function (CartItem $item) use (&$warnings, &$removedIds) {
                $product = $item->product;

                if (!$product || $product->stock < 1) {
                    $warnings[] = (
                        $product?->name ?? 'Sebuah produk'
                    ) . ' sudah habis dan dihapus dari keranjang.';

                    $removedIds[] = (int) $item->product_id;

                    $item->delete();

                    return;
                }

                if ($item->quantity > $product->stock) {
                    $item->update([
                        'quantity' => $product->stock,
                    ]);

                    $warnings[] = "Jumlah {$product->name} disesuaikan menjadi {$product->stock} sesuai stok.";
                }
            });

        if (!empty($removedIds)) {
            $this->forgetSelected($user, $removedIds);
        }

        // Bersihkan juga ID pilihan yang sudah tidak ada di keranjang.
        $this->selectedItems($user);

        return $warnings;
    }
}
