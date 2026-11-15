@extends('layouts.admin')

@section('title', 'Admin Dashboard - Beauty Store')
@section('page-title', 'Dashboard')

@push('styles')
<style>
    .stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:16px;margin-bottom:22px}
    .stat{display:flex;align-items:center;gap:16px}
    .stat .ico{width:50px;height:50px;border-radius:10px;background:var(--c-cream);display:flex;align-items:center;justify-content:center;font-size:22px}
    .stat h3{font-size:24px;margin:0 0 3px}.stat p{margin:0;color:var(--c-ink-soft);font-size:13px}
    .two-col{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:16px;margin-bottom:22px}
    .bar-row{display:flex;align-items:center;gap:10px;margin-bottom:9px;font-size:13px}
    .bar-row .lbl{width:90px;color:var(--c-ink-soft)}
    .bar-row .bar{flex:1;background:var(--c-bg);border-radius:6px;height:14px;overflow:hidden}
    .bar-row .bar i{display:block;height:100%;background:var(--c-pink-strong)}
    .mini-list{list-style:none;margin:0;padding:0;font-size:14px}
    .mini-list li{display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-bottom:1px solid var(--c-border)}
    .mini-list li:last-child{border-bottom:0}
</style>
@endpush

@section('content')

@php
    $statusLabels = ['pending' => 'Menunggu', 'processing' => 'Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'];
    $maxStatus = max(1, $ordersByStatus->max());
    $maxDay    = max(1, $ordersPerDay->max());
    $maxSold   = max(1, (int) ($topProducts->max('sold') ?? 1));
@endphp

<div class="page-header">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <strong>{{ Auth::user()->name }}</strong>.</p>
</div>

<div class="stat-grid">
    <div class="admin-card stat"><div class="ico">📦</div><div><h3>{{ $totalProducts }}</h3><p>Total Produk</p></div></div>
    <div class="admin-card stat"><div class="ico">🏷️</div><div><h3>{{ $totalCategories }}</h3><p>Total Kategori</p></div></div>
    <div class="admin-card stat"><div class="ico">🛒</div><div><h3>{{ $totalOrders }}</h3><p>Total Pesanan</p></div></div>
    <div class="admin-card stat"><div class="ico">👥</div><div><h3>{{ $totalCustomers }}</h3><p>Pelanggan (+{{ $newCustomers }} minggu ini)</p></div></div>
    <div class="admin-card stat"><div class="ico">💰</div><div><h3>Rp {{ number_format($revenue, 0, ',', '.') }}</h3><p>Pendapatan (pesanan selesai)</p></div></div>
    <a href="{{ route('admin.password-resets.index') }}" class="admin-card stat" style="text-decoration:none;color:inherit;">
        <div class="ico">🔑</div><div><h3>{{ $pendingResets }}</h3><p>Permintaan reset password</p></div>
    </a>
    <div class="admin-card stat"><div class="ico">⚠️</div><div><h3>{{ $outOfStock }}</h3><p>Produk stok habis</p></div></div>
</div>

<div class="two-col">
    <div class="admin-card">
        <h3 style="margin-bottom:14px;">Pesanan per Status</h3>
        @foreach($ordersByStatus as $s => $n)
            <div class="bar-row"><span class="lbl">{{ $statusLabels[$s] }}</span>
                <span class="bar"><i style="width:{{ $n / $maxStatus * 100 }}%"></i></span><strong>{{ $n }}</strong></div>
        @endforeach
    </div>

    <div class="admin-card">
        <h3 style="margin-bottom:14px;">Pesanan 7 Hari Terakhir</h3>
        @foreach($ordersPerDay as $d => $n)
            <div class="bar-row"><span class="lbl">{{ \Carbon\Carbon::parse($d)->format('d M') }}</span>
                <span class="bar"><i style="width:{{ $n / $maxDay * 100 }}%"></i></span><strong>{{ $n }}</strong></div>
        @endforeach
    </div>
</div>

<div class="two-col">
    <div class="admin-card">
        <h3 style="margin-bottom:10px;">Produk Terlaris</h3>
        @forelse($topProducts as $p)
            <div class="bar-row"><span class="lbl" style="width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $p->product_name }}</span>
                <span class="bar"><i style="width:{{ $p->sold / $maxSold * 100 }}%"></i></span><strong>{{ $p->sold }}</strong></div>
        @empty
            <p class="muted">Belum ada penjualan.</p>
        @endforelse
    </div>

    <div class="admin-card">
        <h3 style="margin-bottom:10px;">Stok Menipis (≤ 5)</h3>
        <ul class="mini-list">
            @forelse($lowStockProducts as $p)
                <li><a href="{{ route('admin.products.edit', $p) }}">{{ $p->brand }} {{ $p->name }}</a><strong>{{ $p->stock }}</strong></li>
            @empty
                <li class="muted">Semua stok aman.</li>
            @endforelse
        </ul>
    </div>
</div>

<div class="admin-card">
    <h3 style="margin-bottom:10px;">Pesanan Terbaru</h3>
    <ul class="mini-list">
        @forelse($recentOrders as $o)
            <li>
                <a href="{{ route('admin.orders.show', $o) }}">#{{ $o->id }} · {{ $o->user->name ?? '-' }}</a>
                <span>Rp {{ number_format($o->total, 0, ',', '.') }} · {{ $statusLabels[$o->status] ?? $o->status }}</span>
            </li>
        @empty
            <li class="muted">Belum ada pesanan.</li>
        @endforelse
    </ul>
</div>

@endsection