<?php

namespace App\Http\Controllers;

use App\Exceptions\StockException;
use App\Models\Order;
use App\Services\AdminNotificationService;
use App\Services\MidtransService;
use App\Services\OrderStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserOrderController extends Controller
{
    /**
     * Daftar pesanan milik user yang sedang login.
     */
    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->withCount('items')
            ->latest()
            ->get();

        return view('user.orders.index', compact('orders'));
    }

    /**
     * Detail pesanan hanya dapat dilihat pemiliknya.
     */
    public function show(Request $request, Order $order)
    {
        abort_unless(
            $order->user_id === $request->user()->id,
            403
        );

        $order->load('items');

        return view('user.orders.show', compact('order'));
    }

    /**
     * Membuat atau memperbarui Snap Token untuk pembayaran.
     */
    public function pay(
        Request $request,
        Order $order,
        MidtransService $midtrans
    ) {
        abort_unless(
            $order->user_id === $request->user()->id,
            403
        );

        // Periksa kelayakan pembayaran sebelum menghubungi Midtrans.
        if ($order->payment_method === 'cod') {
            return back()->withErrors([
                'payment' => 'Pesanan COD tidak memerlukan pembayaran melalui Midtrans.',
            ]);
        }

        if ($order->status !== 'pending') {
            return back()->withErrors([
                'payment' => 'Pembayaran hanya tersedia untuk pesanan yang masih menunggu.',
            ]);
        }

        if ($order->payment_status !== 'unpaid') {
            return back()->withErrors([
                'payment' => 'Status pembayaran pesanan ini tidak memungkinkan pembayaran ulang.',
            ]);
        }

        try {
            // Ambil data terbaru sebelum membuat token.
            $order->refresh();

            if (
                $order->status !== 'pending' ||
                $order->payment_status !== 'unpaid' ||
                $order->payment_method === 'cod'
            ) {
                return back()->withErrors([
                    'payment' => 'Pesanan sudah berubah dan tidak dapat dibayar.',
                ]);
            }

            // Buat token baru dari data pesanan terbaru.
            $snapToken = $midtrans->createSnapToken($order);

            // Periksa kembali status pesanan sebelum menyimpan token.
            $saved = DB::transaction(function () use (
                $request,
                $order,
                $snapToken
            ) {
                $lockedOrder = Order::where(
                    'user_id',
                    $request->user()->id
                )
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                if (
                    $lockedOrder->status !== 'pending' ||
                    $lockedOrder->payment_status !== 'unpaid' ||
                    $lockedOrder->payment_method === 'cod'
                ) {
                    return false;
                }

                $lockedOrder->update([
                    'snap_token' => $snapToken,
                ]);

                return true;
            });

            if (!$saved) {
                return back()->withErrors([
                    'payment' => 'Status pesanan berubah. Silakan periksa kembali detail pesanan.',
                ]);
            }

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Token pembayaran berhasil diperbarui. Silakan lanjutkan pembayaran.');

        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'payment' => 'Gagal menyiapkan pembayaran. Silakan coba kembali beberapa saat lagi.',
            ]);
        }
    }

    /**
     * Membatalkan pesanan oleh pelanggan.
     */
    public function cancel(
        Request $request,
        Order $order,
        OrderStockService $stockService,
        AdminNotificationService $adminNotification
    ) {
        abort_unless(
            $order->user_id === $request->user()->id,
            403
        );

        try {
            DB::transaction(function () use (
                $request,
                $order,
                $stockService
            ) {
                // Kunci pesanan agar tidak berbenturan
                // dengan proses webhook pembayaran.
                $lockedOrder = Order::where(
                    'user_id',
                    $request->user()->id
                )
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                if ($lockedOrder->status !== 'pending') {
                    throw ValidationException::withMessages([
                        'status' => 'Pesanan hanya bisa dibatalkan selama masih menunggu.',
                    ]);
                }

                if ($lockedOrder->payment_status === 'paid') {
                    throw ValidationException::withMessages([
                        'status' => 'Pesanan yang sudah dibayar tidak bisa dibatalkan langsung. Silakan hubungi admin untuk proses refund.',
                    ]);
                }

                // Kembalikan stok jika sebelumnya pernah dipotong.
                $stockService->restore($lockedOrder);

                $lockedOrder->update([
                    'status' => 'cancelled',
                ]);
            });

        } catch (StockException $e) {
            return back()->withErrors([
                'status' => $e->getMessage(),
            ]);
        }

        /*
         * Transaksi pembatalan sudah berhasil commit.
         * Beri tahu seluruh admin mengenai pembatalan pesanan.
         */
        try {
            $order->refresh();

            $adminNotification->notifyOrderEvent(
                $order,
                'order_cancelled'
            );
        } catch (Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }
}
