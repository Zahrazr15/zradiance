<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }

    /**
     * Menampilkan keranjang.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        if ($warnings = $this->cart->syncWithStock($user)) {
            $request->session()->now(
                'warning',
                implode(' ', $warnings)
            );
        }

        $items = $this->cart->items($user);
        $selectedIds = $this->cart->selectedItems($user);

        return view('cart.index', [
            'cart' => $items,
            'total' => $this->cart->total($items),
            'selectedIds' => $selectedIds,
        ]);
    }

    /**
     * Menambahkan produk ke keranjang.
     */
    public function add(Request $request, Product $product): RedirectResponse
    {
        $error = $this->cart->add($request->user(), $product);

        return redirect()->back()->with(
            $error ? 'error' : 'success',
            $error ?? 'Produk berhasil ditambahkan ke keranjang.'
        );
    }

    /**
     * Menyimpan pilihan produk dari checkbox.
     */
    public function updateSelection(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'selected' => 'present|array',
            'selected.*' => 'required|integer|distinct',
        ]);

        $selectedIds = $this->cart->updateSelection(
            $request->user(),
            $validated['selected']
        );

        return response()->json([
            'success' => true,
            'selected' => $selectedIds,
            'message' => 'Pilihan produk berhasil disimpan.',
        ]);
    }

    /**
     * Memilih semua produk di keranjang.
     */
    public function selectAll(Request $request): JsonResponse
    {
        $selectedIds = $this->cart->selectAll($request->user());

        return response()->json([
            'success' => true,
            'selected' => $selectedIds,
            'message' => 'Semua produk berhasil dipilih.',
        ]);
    }

    /**
     * Menambah jumlah produk.
     */
    public function increase(Request $request, $id): RedirectResponse
    {
        $error = $this->cart->increase(
            $request->user(),
            (int) $id
        );

        $redirect = redirect()->route('cart.index');

        return $error
            ? $redirect->with('error', $error)
            : $redirect;
    }

    /**
     * Mengurangi jumlah produk.
     */
    public function decrease(Request $request, $id): RedirectResponse
    {
        $this->cart->decrease(
            $request->user(),
            (int) $id
        );

        return redirect()->route('cart.index');
    }

    /**
     * Menghapus produk.
     */
    public function remove(Request $request, $id): RedirectResponse
    {
        $this->cart->remove(
            $request->user(),
            (int) $id
        );

        return redirect()
            ->route('cart.index')
            ->with('success', 'Produk berhasil dihapus dari keranjang.');
    }
}
