<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PasswordResetRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function __invoke()
    {
        // Jumlah pesanan per status (status yang kosong tetap tampil = 0)
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $ordersByStatus = collect(Order::STATUSES)
            ->mapWithKeys(fn ($s) => [$s => (int) ($statusCounts[$s] ?? 0)]);

        // Pesanan 7 hari terakhir
        $perDay = Order::where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $ordersPerDay = collect(range(6, 0))
            ->map(fn ($i) => now()->subDays($i)->toDateString())
            ->mapWithKeys(fn ($d) => [$d => (int) ($perDay[$d] ?? 0)]);

        // Produk terlaris (pesanan batal tidak dihitung)
        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('order_items.product_name, SUM(order_items.quantity) as sold')
            ->groupBy('order_items.product_name')
            ->orderByDesc('sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'totalProducts'    => Product::count(),
            'totalCategories'  => Category::count(),
            'totalOrders'      => Order::count(),
            'totalCustomers'   => User::where('role', 'user')->count(),
            'newCustomers'     => User::where('role', 'user')->where('created_at', '>=', now()->subDays(7))->count(),
            'revenue' => (float) Order::where('status', 'completed')
                ->where('payment_status', 'paid')
                ->sum('total'),
            'pendingResets'    => PasswordResetRequest::where('status', 'pending')->count(),
            'outOfStock'       => Product::where('stock', '<=', 0)->count(),
            'lowStockProducts' => Product::where('stock', '>', 0)->where('stock', '<=', 5)->orderBy('stock')->limit(5)->get(),
            'recentOrders'     => Order::with('user')->latest()->limit(5)->get(),
            'ordersByStatus'   => $ordersByStatus,
            'ordersPerDay'     => $ordersPerDay,
            'topProducts'      => $topProducts,
        ]);
    }
}