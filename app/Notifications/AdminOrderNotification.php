<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminOrderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
        public string $event
    ) {}

    /**
     * Saluran pengiriman notifikasi.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Data yang disimpan ke tabel notifications.
     */
    public function toArray(object $notifiable): array
    {
        $orderNumber = '#' . $this->order->id;

        $notification = match ($this->event) {
            'new_order' => [
                'title' => 'Pesanan Baru',
                'message' => "Pesanan {$orderNumber} telah dibuat oleh pelanggan.",
            ],

            'payment_success' => [
                'title' => 'Pembayaran Berhasil',
                'message' => "Pembayaran pesanan {$orderNumber} telah berhasil dikonfirmasi.",
            ],

            'order_cancelled' => [
                'title' => 'Pesanan Dibatalkan',
                'message' => "Pesanan {$orderNumber} telah dibatalkan.",
            ],

            default => [
                'title' => 'Informasi Pesanan',
                'message' => "Ada pembaruan pada pesanan {$orderNumber}.",
            ],
        };

        return [
            'event' => $this->event,
            'title' => $notification['title'],
            'message' => $notification['message'],
            'order_id' => $this->order->id,
            'recipient_name' => $this->order->recipient_name,
            'total' => $this->order->total,
            'status' => $this->order->status,
            'payment_status' => $this->order->payment_status,
            'url' => route('admin.orders.show', $this->order->id),
        ];
    }
}
