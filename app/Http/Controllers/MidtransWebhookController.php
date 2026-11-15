<?php

namespace App\Http\Controllers;

use App\Exceptions\StockException;
use App\Models\Order;
use App\Services\AdminNotificationService;
use App\Services\OrderStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Throwable;

class MidtransWebhookController extends Controller
{
    public function __construct(
        private OrderStockService $stockService,
        private AdminNotificationService $adminNotification
    ) {}

    public function handle(Request $request)
    {
        try {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = (bool) config('midtrans.is_production');
            Config::$isSanitized = (bool) config('midtrans.is_sanitized');
            Config::$is3ds = (bool) config('midtrans.is_3ds');

            if (empty(Config::$serverKey)) {
                Log::error('Midtrans server key is not configured.');

                return response()->json([
                    'message' => 'Payment configuration error.',
                ], 500);
            }

            // SDK Midtrans memverifikasi signature notifikasi.
            $notification = new Notification();

            $orderCode = $notification->order_id ?? null;

            if (
                !is_string($orderCode) ||
                !preg_match('/^ORDER-(\d+)$/', $orderCode, $matches)
            ) {
                return response()->json([
                    'message' => 'Invalid order ID.',
                ], 400);
            }

            $order = Order::find($matches[1]);

            if (!$order) {
                return response()->json([
                    'message' => 'Order not found.',
                ], 404);
            }

            // Verifikasi nominal transaksi.
            if (
                !is_numeric($notification->gross_amount ?? null) ||
                (int) round((float) $notification->gross_amount) !==
                (int) round((float) $order->total)
            ) {
                Log::warning('Midtrans amount mismatch.', [
                    'order_id' => $order->id,
                    'notification_amount' => $notification->gross_amount ?? null,
                    'order_amount' => $order->total,
                ]);

                return response()->json([
                    'message' => 'Transaction amount mismatch.',
                ], 400);
            }

            $transactionStatus = $notification->transaction_status ?? null;
            $fraudStatus = $notification->fraud_status ?? null;
            $transactionId = $notification->transaction_id ?? null;

            $paymentMethod = match ($notification->payment_type ?? '') {
                'qris' => 'qris',
                'bank_transfer', 'echannel' => 'bank_transfer',
                'gopay', 'shopeepay', 'dana', 'ovo', 'linkaja' => 'e_wallet',
                default => $notification->payment_type ?? null,
            };

            $isPaid = $transactionStatus === 'settlement'
                || (
                    $transactionStatus === 'capture'
                    && $fraudStatus === 'accept'
                );

            /*
             * Penanda agar notifikasi hanya dikirim ketika
             * pembayaran benar-benar baru dikonfirmasi.
             */
            $shouldNotifyAdmin = false;

            /*
             * PEMBAYARAN BERHASIL
             */
            if ($isPaid) {
                try {
                    $result = DB::transaction(function () use (
                        $order,
                        $paymentMethod,
                        $transactionId
                    ) {
                        $lockedOrder = Order::lockForUpdate()
                            ->findOrFail($order->id);

                        if ($lockedOrder->payment_method === 'cod') {
                            return 'cod';
                        }

                        if ($lockedOrder->payment_status === 'paid') {
                            return 'already_paid';
                        }

                        if ($lockedOrder->payment_status === 'refunded') {
                            return 'already_refunded';
                        }

                        if ($lockedOrder->status === 'cancelled') {
                            $lockedOrder->payment_status = 'paid';
                            $lockedOrder->payment_method =
                                $paymentMethod ?? $lockedOrder->payment_method;
                            $lockedOrder->payment_reference =
                                $transactionId ?? $lockedOrder->payment_reference;
                            $lockedOrder->paid_at ??= now();
                            $lockedOrder->save();

                            return 'cancelled_paid';
                        }

                        // Pengurangan stok dan perubahan pembayaran
                        // berada dalam transaksi database yang sama.
                        $this->stockService->deduct($lockedOrder);

                        $lockedOrder->payment_status = 'paid';
                        $lockedOrder->payment_method =
                            $paymentMethod ?? $lockedOrder->payment_method;
                        $lockedOrder->payment_reference =
                            $transactionId ?? $lockedOrder->payment_reference;
                        $lockedOrder->paid_at ??= now();
                        $lockedOrder->save();

                        return 'processed';
                    });

                    if (in_array($result, [
                        'processed',
                        'cancelled_paid',
                    ], true)) {
                        $shouldNotifyAdmin = true;
                    }

                    if ($result === 'cancelled_paid') {
                        Log::critical(
                            'Payment received for cancelled order. Manual refund required.',
                            [
                                'order_id' => $order->id,
                                'transaction_id' => $transactionId,
                            ]
                        );
                    } elseif ($result === 'cod') {
                        Log::critical(
                            'Unexpected Midtrans payment notification for COD order.',
                            [
                                'order_id' => $order->id,
                                'transaction_id' => $transactionId,
                            ]
                        );
                    }

                } catch (StockException $e) {
                    /*
                     * Pembayaran memang sudah diterima Midtrans.
                     * Catat sebagai paid meskipun stok gagal dikurangi.
                     * Pesanan harus ditangani admin secara manual.
                     */
                    $shouldNotifyAdmin = DB::transaction(function () use (
                        $order,
                        $paymentMethod,
                        $transactionId
                    ) {
                        $lockedOrder = Order::lockForUpdate()
                            ->findOrFail($order->id);

                        if (
                            $lockedOrder->payment_status === 'paid' ||
                            $lockedOrder->payment_status === 'refunded'
                        ) {
                            return false;
                        }

                        $lockedOrder->payment_status = 'paid';
                        $lockedOrder->payment_method =
                            $paymentMethod ?? $lockedOrder->payment_method;
                        $lockedOrder->payment_reference =
                            $transactionId ?? $lockedOrder->payment_reference;
                        $lockedOrder->paid_at ??= now();
                        $lockedOrder->save();

                        return true;
                    });

                    Log::critical(
                        'Payment confirmed but stock deduction failed. Manual order review required.',
                        [
                            'order_id' => $order->id,
                            'transaction_id' => $transactionId,
                            'error' => $e->getMessage(),
                        ]
                    );
                }
            }

            /*
             * PEMBAYARAN MENUNGGU
             */
            elseif ($transactionStatus === 'pending') {
                DB::transaction(function () use (
                    $order,
                    $paymentMethod
                ) {
                    $lockedOrder = Order::lockForUpdate()
                        ->findOrFail($order->id);

                    if (
                        $lockedOrder->payment_method === 'cod' ||
                        $lockedOrder->payment_status === 'paid' ||
                        $lockedOrder->payment_status === 'refunded' ||
                        $lockedOrder->status === 'cancelled'
                    ) {
                        return;
                    }

                    $lockedOrder->payment_status = 'unpaid';

                    if ($paymentMethod) {
                        $lockedOrder->payment_method = $paymentMethod;
                    }

                    $lockedOrder->save();
                });
            }

            /*
             * PEMBAYARAN GAGAL ATAU KEDALUWARSA
             */
            elseif (in_array($transactionStatus, [
                'deny',
                'cancel',
                'expire',
            ], true)) {
                DB::transaction(function () use (
                    $order,
                    $transactionStatus
                ) {
                    $lockedOrder = Order::lockForUpdate()
                        ->findOrFail($order->id);

                    if (
                        $lockedOrder->payment_method === 'cod' ||
                        $lockedOrder->payment_status === 'paid' ||
                        $lockedOrder->payment_status === 'refunded' ||
                        $lockedOrder->status === 'cancelled'
                    ) {
                        return;
                    }

                    $lockedOrder->payment_status =
                        $transactionStatus === 'expire'
                            ? 'expired'
                            : 'failed';

                    $lockedOrder->save();
                });
            }

            /*
             * REFUND PENUH
             */
            elseif ($transactionStatus === 'refund') {
                DB::transaction(function () use (
                    $order,
                    $transactionId
                ) {
                    $lockedOrder = Order::lockForUpdate()
                        ->findOrFail($order->id);

                    if ($lockedOrder->payment_method === 'cod') {
                        Log::warning(
                            'Unexpected refund notification for COD order.',
                            ['order_id' => $lockedOrder->id]
                        );

                        return;
                    }

                    if ($lockedOrder->payment_status !== 'paid') {
                        Log::warning(
                            'Refund notification received for an order that is not marked paid.',
                            [
                                'order_id' => $lockedOrder->id,
                                'payment_status' => $lockedOrder->payment_status,
                            ]
                        );

                        return;
                    }

                    $lockedOrder->payment_status = 'refunded';

                    if ($transactionId) {
                        $lockedOrder->payment_reference = $transactionId;
                    }

                    $lockedOrder->save();

                    Log::info('Midtrans refund notification processed.', [
                        'order_id' => $lockedOrder->id,
                        'transaction_id' => $transactionId,
                    ]);
                });
            }

            /*
             * REFUND SEBAGIAN
             *
             * Status database belum memiliki partial_refund.
             * Jangan mengubahnya menjadi refunded karena belum tentu
             * seluruh nominal pesanan sudah dikembalikan.
             */
            elseif ($transactionStatus === 'partial_refund') {
                Log::warning(
                    'Partial refund notification requires manual review.',
                    [
                        'order_id' => $order->id,
                        'transaction_id' => $transactionId,
                    ]
                );
            }

            /*
             * KIRIM NOTIFIKASI SETELAH TRANSAKSI DATABASE SELESAI.
             * Kegagalan notifikasi tidak membatalkan pembayaran.
             */
            if ($shouldNotifyAdmin) {
                try {
                    $order->refresh();

                    $this->adminNotification->notifyOrderEvent(
                        $order,
                        'payment_success'
                    );
                } catch (Throwable $e) {
                    Log::error('Failed to send admin payment notification.', [
                        'order_id' => $order->id,
                        'message' => $e->getMessage(),
                    ]);
                }
            }

            return response()->json([
                'message' => 'Notification processed.',
            ]);

        } catch (Throwable $e) {
            Log::error('Midtrans webhook error.', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Webhook processing failed.',
            ], 500);
        }
    }
}
