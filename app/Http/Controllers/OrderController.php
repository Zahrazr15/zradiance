<?php

namespace App\Http\Controllers;

use App\Exceptions\StockException;
use App\Models\Order;
use App\Services\OrderStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function __construct(
        private OrderStockService $stockService
    ) {}

    public function index(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $orders = Order::with('user')
            ->withCount('items')
            ->when($request->q, function ($query, $search) {
                $search = trim($search);
                $idPart = ltrim($search, '#');

                $query->where(function ($w) use ($search, $idPart) {
                    $w->where('recipient_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });

                    if (ctype_digit($idPart)) {
                        $w->orWhere('id', (int) $idPart);
                    }
                });
            })
            ->when(
                in_array($request->status, Order::STATUSES, true),
                fn ($query) => $query->where('status', $request->status)
            )
            ->when(
                $request->date_from,
                fn ($query, $date) => $query->whereDate('created_at', '>=', $date)
            )
            ->when(
                $request->date_to,
                fn ($query, $date) => $query->whereDate('created_at', '<=', $date)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $statuses = Order::STATUSES;

        return view('orders.index', compact('orders', 'statuses'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items']);

        return view('orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(Order::STATUSES)],
        ]);

        $newStatus = $validated['status'];

        try {
            DB::transaction(function () use ($order, $newStatus) {
                $lockedOrder = Order::lockForUpdate()
                    ->findOrFail($order->id);

                $currentStatus = $lockedOrder->status;

                if ($currentStatus === $newStatus) {
                    return;
                }

                if ($currentStatus === 'cancelled') {
                    throw new StockException(
                        'Pesanan yang sudah dibatalkan tidak dapat diproses kembali.'
                    );
                }

                if ($currentStatus === 'completed') {
                    throw new StockException(
                        'Pesanan yang sudah selesai tidak dapat diubah.'
                    );
                }

                // Pembatalan hanya diperbolehkan saat masih menunggu.
                if ($newStatus === 'cancelled') {
                    if ($currentStatus !== 'pending') {
                        throw new StockException(
                            'Pesanan yang sudah diproses atau dikirim tidak dapat dibatalkan langsung.'
                        );
                    }

                    if ($lockedOrder->payment_status === 'paid') {
                        throw new StockException(
                            'Pesanan sudah dibayar. Lakukan proses refund sebelum pembatalan.'
                        );
                    }

                    $this->stockService->restore($lockedOrder);

                    $lockedOrder->update([
                        'status' => 'cancelled',
                    ]);

                    return;
                }

                // Perubahan status harus mengikuti urutan.
                $allowedTransitions = [
                    'pending' => ['processing'],
                    'processing' => ['shipped'],
                    'shipped' => ['completed'],
                ];

                if (!in_array(
                    $newStatus,
                    $allowedTransitions[$currentStatus] ?? [],
                    true
                )) {
                    throw new StockException(
                        'Perubahan status pesanan tidak sesuai urutan.'
                    );
                }

                $isCod = $lockedOrder->payment_method === 'cod';
                $isPaid = $lockedOrder->payment_status === 'paid';

                // COD boleh mulai diproses, pembayaran online wajib lunas.
                if (!$isCod && !$isPaid) {
                    throw new StockException(
                        'Pesanan online belum dibayar. Status belum dapat diproses.'
                    );
                }

                // Potong stok satu kali sebelum pesanan diproses.
                if ($newStatus === 'processing') {
                    $this->stockService->deduct($lockedOrder);
                }

                $lockedOrder->update([
                    'status' => $newStatus,
                ]);
            });
        } catch (StockException $e) {
            return back()->withErrors([
                'status' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
