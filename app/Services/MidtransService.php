<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use RuntimeException;

class MidtransService
{
    public function createSnapToken(Order $order): string
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = (bool) config('midtrans.is_sanitized');
        Config::$is3ds = (bool) config('midtrans.is_3ds');

        if (empty(Config::$serverKey)) {
            throw new RuntimeException(
                'Midtrans Server Key belum dikonfigurasi.'
            );
        }

        $order->loadMissing(['user', 'items']);

        if (!$order->user) {
            throw new RuntimeException(
                'Data pengguna pesanan tidak ditemukan.'
            );
        }

        if ($order->payment_method === 'cod') {
            throw new RuntimeException(
                'Pesanan COD tidak memerlukan token pembayaran Midtrans.'
            );
        }

        if ($order->status === 'cancelled') {
            throw new RuntimeException(
                'Pesanan yang dibatalkan tidak dapat dibayar.'
            );
        }

        if ($order->payment_status === 'paid') {
            throw new RuntimeException(
                'Pesanan ini sudah dibayar.'
            );
        }

        if ($order->payment_status === 'refunded') {
            throw new RuntimeException(
                'Pesanan yang sudah dikembalikan dananya tidak dapat dibayar.'
            );
        }

        if ($order->items->isEmpty()) {
            throw new RuntimeException(
                'Pesanan tidak memiliki detail produk.'
            );
        }

        $itemDetails = $order->items->map(function ($item) {
            $price = (int) round((float) $item->price);
            $quantity = (int) $item->quantity;

            if ($price < 0 || $quantity < 1) {
                throw new RuntimeException(
                    'Harga atau jumlah produk dalam pesanan tidak valid.'
                );
            }

            $name = trim((string) $item->product_name);

            if ($name === '') {
                $name = 'Produk Beauty Store';
            }

            return [
                'id' => (string) $item->product_id,
                'price' => $price,
                'quantity' => $quantity,
                'name' => substr($name, 0, 50),
            ];
        })->values()->all();

        // Tambahkan ongkos kirim sebagai item pembayaran.
        $shippingCost = (int) $order->shipping_cost;

        if ($shippingCost < 0) {
            throw new RuntimeException(
                'Ongkos kirim pesanan tidak valid.'
            );
        }

        if ($shippingCost > 0) {
            $itemDetails[] = [
                'id' => 'SHIPPING',
                'price' => $shippingCost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        // Hitung total dari rincian item.
        $grossAmount = array_sum(array_map(
            fn ($item) => $item['price'] * $item['quantity'],
            $itemDetails
        ));

        $orderTotal = (int) round((float) $order->total);

        if ($grossAmount < 1 || $grossAmount !== $orderTotal) {
            throw new RuntimeException(
                'Total rincian pembayaran tidak sesuai dengan total pesanan.'
            );
        }

        $customerName = trim((string) $order->recipient_name);
        $customerEmail = trim((string) $order->user->email);
        $customerPhone = trim((string) $order->phone);

        if ($customerName === '' || $customerEmail === '') {
            throw new RuntimeException(
                'Nama penerima atau email pelanggan belum tersedia.'
            );
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $order->id,
                'gross_amount' => $grossAmount,
            ],

            'item_details' => $itemDetails,

            'customer_details' => [
                'first_name' => substr($customerName, 0, 50),
                'email' => $customerEmail,
                'phone' => $customerPhone,
            ],
        ];

        return Snap::getSnapToken($params);
    }
}
