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

<div class="page-header" style="margin-bottom:28px;">
    <h2>Dashboard Ringkasan Operasional</h2>
    <p>Selamat bertugas kembali, <strong>{{ Auth::user()->name }}</strong>. Berikut statistik toko terkini.</p>
</div>

{{-- STATISTIC WIDGETS --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="ico">📦</div>
        <div>
            <h3>{{ $totalProducts }}</h3>
            <p>Total Produk Aktif</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="ico">🏷️</div>
        <div>
            <h3>{{ $totalCategories }}</h3>
            <p>Kategori Produk</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="ico">🛒</div>
        <div>
            <h3>{{ $totalOrders }}</h3>
            <p>Total Semua Pesanan</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="ico">👥</div>
        <div>
            <h3>{{ $totalCustomers }}</h3>
            <p>Pelanggan (+{{ $newCustomers }} mggu ini)</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="ico" style="background:linear-gradient(135deg, #E8F8EE, #D1F2DD);">💰</div>
        <div>
            <h3 style="color:#14633F;">Rp {{ number_format($revenue, 0, ',', '.') }}</h3>
            <p>Pendapatan Bersih (Selesai)</p>
        </div>
    </div>

    <a href="{{ route('admin.password-resets.index') }}" class="stat-card" style="text-decoration:none;color:inherit;">
        <div class="ico" style="background:linear-gradient(135deg, #FFF3D6, #FFE4A0);">🔑</div>
        <div>
            <h3>{{ $pendingResets }}</h3>
            <p>Permintaan Reset Password</p>
        </div>
    </a>

    <div class="stat-card">
        <div class="ico" style="background:linear-gradient(135deg, #FDE7E4, #F9C0D3);">⚠️</div>
        <div>
            <h3 style="color:var(--c-danger);">{{ $outOfStock }}</h3>
            <p>Produk Habis Stok</p>
        </div>
    </div>
</div>

{{-- CHARTS & METRICS --}}
<div class="two-col">
    <div class="admin-card-glass">
        <h3>📊 Distribusi Status Pesanan</h3>
        @foreach($ordersByStatus as $s => $n)
            <div class="bar-row">
                <span class="lbl">{{ $statusLabels[$s] ?? ucfirst($s) }}</span>
                <span class="bar"><i style="width:{{ $n / $maxStatus * 100 }}%"></i></span>
                <strong>{{ $n }}</strong>
            </div>
        @endforeach
    </div>

    <div class="admin-card-glass">
        <h3>📈 Tren Pesanan (7 Hari Terakhir)</h3>
        @foreach($ordersPerDay as $d => $n)
            <div class="bar-row">
                <span class="lbl">{{ \Carbon\Carbon::parse($d)->format('d M') }}</span>
                <span class="bar"><i style="width:{{ $n / $maxDay * 100 }}%"></i></span>
                <strong>{{ $n }}</strong>
            </div>
        @endforeach
    </div>
</div>

<div class="two-col">
    <div class="admin-card-glass">
        <h3>🏆 Produk Terlaris</h3>
        @forelse($topProducts as $p)
            <div class="bar-row">
                <span class="lbl" style="width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $p->product_name }}</span>
                <span class="bar"><i style="width:{{ $p->sold / $maxSold * 100 }}%"></i></span>
                <strong>{{ $p->sold }} terjual</strong>
            </div>
        @empty
            <p class="muted">Belum ada data penjualan tercatat.</p>
        @endforelse
    </div>

    <div class="admin-card-glass">
        <h3>⚠️ Stok Perlu Restock (≤ 5)</h3>
        <ul class="mini-list">
            @forelse($lowStockProducts as $p)
                <li>
                    <a href="{{ route('admin.products.edit', $p) }}">{{ $p->brand }} {{ $p->name }}</a>
                    <span class="badge" style="background:#FDE7E4;color:#8E2B20;">Sisa: {{ $p->stock }}</span>
                </li>
            @empty
                <li class="muted">Seluruh stok produk berada dalam batas aman.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="admin-card-glass">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="margin:0;">🛒 Transaksi Pesanan Terbaru</h3>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline btn-sm">Buka Semua Pesanan →</a>
    </div>

    <ul class="mini-list">
        @forelse($recentOrders as $o)
            <li>
                <div>
                    <a href="{{ route('admin.orders.show', $o) }}">#{{ $o->id }} · {{ $o->user->name ?? 'Pelanggan' }}</a>
                    <div style="font-size:12px;color:var(--c-ink-soft);margin-top:2px;">{{ $o->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div style="text-align:right;">
                    <strong>Rp {{ number_format($o->total, 0, ',', '.') }}</strong>
                    <div style="margin-top:2px;">@include('user.orders._status', ['status' => $o->status])</div>
                </div>
            </li>
        @empty
            <li class="muted">Belum ada pesanan yang masuk.</li>
        @endforelse
    </ul>
</div>

@endsection
