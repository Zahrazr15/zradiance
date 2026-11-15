@extends('layouts.admin')

@section('title', 'Admin Dashboard - Beauty Store')
@section('page-title', 'Dashboard Ringkasan')

@push('styles')
<style>
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        padding: 22px;
        display: flex;
        align-items: center;
        gap: 18px;
        box-shadow: var(--shadow-xs);
        transition: var(--transition);
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-sm);
        border-color: var(--c-blush);
    }

    .stat-card .ico {
        width: 54px;
        height: 54px;
        border-radius: var(--radius-md);
        background: linear-gradient(135deg, #FFF9EE, var(--c-cream));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(255, 234, 187, 0.4);
    }

    .stat-card h3 {
        font-size: 26px;
        margin: 0 0 2px;
        color: var(--c-ink-heading);
    }
    .stat-card p {
        margin: 0;
        color: var(--c-ink-soft);
        font-size: 13px;
        font-weight: 600;
    }

    .two-col {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 24px;
        margin-bottom: 28px;
    }

    .admin-card-glass {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 26px;
        box-shadow: var(--shadow-sm);
    }

    .admin-card-glass h3 {
        font-size: 18px;
        color: var(--c-ink-heading);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bar-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        font-size: 13.5px;
    }
    .bar-row .lbl {
        width: 100px;
        color: var(--c-ink-soft);
        font-weight: 600;
    }
    .bar-row .bar {
        flex: 1;
        background: #F8F2F4;
        border-radius: 999px;
        height: 12px;
        overflow: hidden;
    }
    .bar-row .bar i {
        display: block;
        height: 100%;
        background: linear-gradient(90deg, var(--c-pink), var(--c-pink-strong));
        border-radius: 999px;
    }

    .mini-list {
        list-style: none;
        margin: 0;
        padding: 0;
        font-size: 14px;
    }
    .mini-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--c-border-subtle);
    }
    .mini-list li:last-child { border-bottom: 0; }
    .mini-list a {
        color: var(--c-ink-heading);
        font-weight: 700;
        text-decoration: none;
        transition: color 0.2s;
    }
    .mini-list a:hover { color: var(--c-pink-strong); }
</style>
@endpush

@section('content')

@php
    $statusLabels = [
        'pending' => 'Menunggu',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan'
    ];
    $maxStatus = max(1, $ordersByStatus->max());
    $maxDay    = max(1, $ordersPerDay->max());
    $maxSold   = max(1, (int) ($topProducts->max('sold') ?? 1));
@endphp

<div class="admin-dashboard">
    <div class="admin-dashboard-header">
        <div class="page-header">
            <p class="admin-dashboard-eyebrow">Ringkasan operasional</p>
            <h2>Selamat datang, {{ Auth::user()->name }}</h2>
            <p>Pantau pesanan, katalog, dan aktivitas toko dari satu tempat.</p>
        </div>
        <div class="admin-dashboard-actions">
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline btn-sm">Kelola produk</a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm">Lihat pesanan</a>
        </div>
    </div>

    {{-- STATISTIC WIDGETS --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="ico">📦</div>
            <div>
                <h3>{{ $totalProducts }}</h3>
                <p>Produk terdaftar</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="ico">🏷️</div>
            <div>
                <h3>{{ $totalCategories }}</h3>
                <p>Kategori produk</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="ico">🛒</div>
            <div>
                <h3>{{ $totalOrders }}</h3>
                <p>Total pesanan</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="ico">⏳</div>
            <div>
                <h3>{{ $ordersByStatus['pending'] ?? 0 }}</h3>
                <p>Pesanan menunggu</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="ico">✓</div>
            <div>
                <h3>{{ $ordersByStatus['completed'] ?? 0 }}</h3>
                <p>Pesanan selesai</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="ico">👥</div>
            <div>
                <h3>{{ $totalCustomers }}</h3>
                <p>Pelanggan (+{{ $newCustomers }} pekan ini)</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="ico">💰</div>
            <div>
                <h3>Rp {{ number_format($revenue, 0, ',', '.') }}</h3>
                <p>Nilai pesanan selesai</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="ico">⚠️</div>
            <div>
                <h3>{{ $outOfStock }}</h3>
                <p>Produk habis stok</p>
            </div>
        </div>

        <a href="{{ route('admin.password-resets.index') }}" class="stat-card stat-card-link">
            <div class="ico">🔑</div>
            <div>
                <h3>{{ $pendingResets }}</h3>
                <p>Permintaan reset password</p>
            </div>
        </a>
    </div>

    {{-- CHARTS & METRICS --}}
    <div class="two-col">
        <section class="admin-card-glass" aria-labelledby="orders-by-status-title">
            <h3 id="orders-by-status-title">Distribusi status pesanan</h3>
            @foreach($ordersByStatus as $s => $n)
                <div class="bar-row">
                    <span class="lbl">{{ $statusLabels[$s] ?? ucfirst($s) }}</span>
                    <span class="bar"><i style="width:{{ $n / $maxStatus * 100 }}%"></i></span>
                    <strong>{{ $n }}</strong>
                </div>
            @endforeach
        </section>

        <section class="admin-card-glass" aria-labelledby="orders-per-day-title">
            <h3 id="orders-per-day-title">Tren pesanan · 7 hari terakhir</h3>
            @foreach($ordersPerDay as $d => $n)
                <div class="bar-row">
                    <span class="lbl">{{ \Carbon\Carbon::parse($d)->format('d M') }}</span>
                    <span class="bar"><i style="width:{{ $n / $maxDay * 100 }}%"></i></span>
                    <strong>{{ $n }}</strong>
                </div>
            @endforeach
        </section>
    </div>

    <div class="two-col">
        <section class="admin-card-glass" aria-labelledby="top-products-title">
            <h3 id="top-products-title">Produk terlaris</h3>
            @forelse($topProducts as $p)
                <div class="bar-row">
                    <span class="lbl product-bar-label">{{ $p->product_name }}</span>
                    <span class="bar"><i style="width:{{ $p->sold / $maxSold * 100 }}%"></i></span>
                    <strong>{{ $p->sold }} terjual</strong>
                </div>
            @empty
                <p class="muted">Belum ada data penjualan tercatat.</p>
            @endforelse
        </section>

        <section class="admin-card-glass" aria-labelledby="low-stock-title">
            <h3 id="low-stock-title">Stok perlu restock <span>(≤ 5)</span></h3>
            <ul class="mini-list">
                @forelse($lowStockProducts as $p)
                    <li>
                        <a href="{{ route('admin.products.edit', $p) }}">{{ $p->brand }} {{ $p->name }}</a>
                        <span class="badge low-stock-badge">Sisa: {{ $p->stock }}</span>
                    </li>
                @empty
                    <li class="muted">Seluruh stok produk berada dalam batas aman.</li>
                @endforelse
            </ul>
        </section>
    </div>

    <section class="admin-card-glass recent-orders-card" aria-labelledby="recent-orders-title">
        <div class="admin-card-heading">
            <div>
                <h3 id="recent-orders-title">Pesanan terbaru</h3>
                <p>Lima transaksi terakhir yang tercatat.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline btn-sm">Semua pesanan →</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-data-table">
                <thead>
                    <tr>
                        <th>Nomor pesanan</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $o)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $o) }}" class="admin-order-link">#{{ $o->id }}</a>
                            </td>
                            <td>{{ $o->user->name ?? 'Pelanggan' }}</td>
                            <td>{{ $o->created_at->format('d M Y, H:i') }}</td>
                            <td class="admin-order-total">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
                            <td>@include('user.orders._status', ['status' => $o->status])</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="admin-table-empty">Belum ada pesanan yang masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@endsection
