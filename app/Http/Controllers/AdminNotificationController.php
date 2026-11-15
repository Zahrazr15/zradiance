<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    /**
     * Menampilkan notifikasi admin.
     */
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function read(
        Request $request,
        string $notification
    ): RedirectResponse {
        $item = $request->user()
            ->notifications()
            ->whereKey($notification)
            ->firstOrFail();

        $item->markAsRead();

        $url = $item->data['url'] ?? route('admin.notifications.index');

        return redirect()->to($url);
    }

    /**
     * Menandai seluruh notifikasi sebagai sudah dibaca.
     */
    public function readAll(Request $request): RedirectResponse
    {
        $request->user()
            ->unreadNotifications()
            ->update([
                'read_at' => now(),
            ]);

        return back()->with(
            'success',
            'Semua notifikasi berhasil ditandai sudah dibaca.'
        );
    }
}
