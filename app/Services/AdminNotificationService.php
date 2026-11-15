<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Notifications\AdminOrderNotification;
use InvalidArgumentException;

class AdminNotificationService
{
    /**
     * Kirim notifikasi peristiwa pesanan kepada seluruh admin.
     */
    public function notifyOrderEvent(
        Order $order,
        string $event
    ): void {
        $allowedEvents = [
            'new_order',
            'payment_success',
            'order_cancelled',
        ];

        if (! in_array($event, $allowedEvents, true)) {
            throw new InvalidArgumentException(
                "Event notifikasi '{$event}' tidak dikenal."
            );
        }

        User::query()
            ->where('role', 'admin')
            ->each(function (User $admin) use ($order, $event) {

                // Hindari mengirim notifikasi yang sama berulang kali.
                $alreadySent = $admin->notifications()
                    ->where(
                        'type',
                        AdminOrderNotification::class
                    )
                    ->where('data->event', $event)
                    ->where('data->order_id', $order->id)
                    ->exists();

                if ($alreadySent) {
                    return;
                }

                $admin->notify(
                    new AdminOrderNotification($order, $event)
                );
            });
    }
}
