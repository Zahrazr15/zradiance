@extends('layouts.user')

@section('title', 'Profil Pengguna - Beauty Store')

@push('styles')
<style>
    .profile-container {
        max-width: 1100px;
        margin: 36px auto;
        padding: 0 32px 80px;
    }

    .profile-grid-layout {
        display: grid;
        grid-template-columns: 340px 1fr;
        gap: 32px;
        align-items: start;
    }

    /* Left Profile Card */
    .user-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 32px 24px;
        text-align: center;
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: 100px;
    }

    .avatar-wrapper {
        width: 100px;
        height: 100px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--c-pink), var(--c-pink-strong));
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        font-size: 42px;
        color: #fff;
        box-shadow: 0 6px 20px rgba(233, 129, 158, 0.35);
        border: 3px solid #fff;
    }

    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .user-card h1 {
        font-size: 24px;
        color: var(--c-ink-heading);
        margin-bottom: 4px;
    }

    .user-card .user-email {
        color: var(--c-ink-soft);
        font-size: 14px;
        margin-bottom: 16px;
    }

    .user-meta-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin: 20px 0;
        text-align: left;
        background: #FCF8F6;
        padding: 16px;
        border-radius: var(--radius-md);
        font-size: 13.5px;
    }

    .user-meta-row {
        display: flex;
        justify-content: space-between;
    }

    /* Right Content Cards */
    .profile-main-section {
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .section-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 30px;
        box-shadow: var(--shadow-sm);
    }

    .section-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--c-border-subtle);
        gap: 16px;
        flex-wrap: wrap;
    }

    .section-card-header h2 {
        font-size: 20px;
        color: var(--c-ink-heading);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Address Cards */
    .address-card-item {
        background: #FCF8F6;
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-md);
        padding: 20px;
        margin-bottom: 16px;
        transition: var(--transition);
    }
    .address-card-item:hover {
        border-color: var(--c-blush);
        background: #fff;
        box-shadow: var(--shadow-xs);
    }

    .address-card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .address-tag {
        font-weight: 800;
        font-size: 15px;
        color: var(--c-ink-heading);
    }

    .address-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
        margin-top: 14px;
        padding-top: 12px;
        border-top: 1px solid rgba(243, 217, 214, 0.6);
    }

    /* Recent Orders Table */
    .order-mini-table th {
        background: #FCF8F6;
        font-size: 12.5px;
        padding: 12px 14px;
        color: var(--c-ink-soft);
    }
    .order-mini-table td {
        padding: 14px;
        vertical-align: middle;
        font-size: 13.5px;
    }

    .order-status-tabs {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        margin: 0 0 18px;
        padding: 0 0 8px;
        border-bottom: 1px solid var(--c-border-subtle);
        scrollbar-width: thin;
    }

    .order-status-tab {
        flex: 0 0 auto;
        padding: 9px 14px;
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-pill);
        background: #fff;
        color: var(--c-ink-soft);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: var(--transition);
    }

    .order-status-tab:hover,
    .order-status-tab[aria-current="page"] {
        border-color: var(--c-pink);
        background: var(--c-pink-subtle);
        color: var(--c-pink-strong);
    }

    .order-status-tab:focus-visible {
        outline: 3px solid rgba(233, 129, 158, 0.3);
        outline-offset: 2px;
    }

    .order-table-wrap {
        overflow-x: auto;
    }

    .order-mini-table {
        min-width: 620px;
    }

    .profile-logout-form {
        margin-top: 12px;
    }

    .profile-logout-button {
        width: 100%;
        border-color: rgba(192, 57, 43, 0.35);
        color: var(--c-danger);
    }

    @media (max-width: 960px) {
        .profile-grid-layout { grid-template-columns: 1fr; }
        .user-card { position: static; }
    }

    @media (max-width: 600px) {
        .profile-container { padding: 0 16px 50px; margin-top: 20px; }
        .section-card { padding: 20px; }
    }
</style>
@endpush

@section('content')
<div class="profile-container">

    <div class="back-bar">
        <x-back-button :href="route('dashboard')" label="← Kembali ke Dashboard" />
    </div>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert-success" role="status" style="margin-bottom:24px;">
            <span>✓</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error" role="alert" style="margin-bottom:24px;">
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin:4px 0 0;padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-grid-layout">

        {{-- LEFT COLUMN: USER INFO --}}
        <aside class="user-card">
            <div class="avatar-wrapper">
                @if(Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" alt="Foto profil {{ Auth::user()->name }}">
                @else
                    👤
                @endif
            </div>

            <h1>{{ Auth::user()->name }}</h1>
            <div class="user-email">{{ Auth::user()->email }}</div>

            <div class="user-meta-list">
                <div class="user-meta-row">
                    <span style="color:var(--c-ink-soft);">Status Peran:</span>
                    <strong style="color:var(--c-ink-heading);">{{ ucfirst(Auth::user()->role) }}</strong>
                </div>
                <div class="user-meta-row">
                    <span style="color:var(--c-ink-soft);">Nomor HP:</span>
                    <strong>{{ Auth::user()->phone ?: 'Belum diisi' }}</strong>
                </div>
                <div class="user-meta-row">
                    <span style="color:var(--c-ink-soft);">Bergabung:</span>
                    <strong>{{ Auth::user()->created_at->format('M Y') }}</strong>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}" class="btn btn-primary" style="width:100%;margin-top:6px;">
                ✏️ Edit Profil & Foto
            </a>

            <form action="{{ route('logout') }}" method="POST" class="profile-logout-form" data-confirm="Yakin ingin logout?">
                @csrf
                <button type="submit" class="btn btn-outline profile-logout-button">Logout</button>
            </form>
        </aside>

        {{-- RIGHT COLUMN: ORDERS HISTORY & ADDRESSES --}}
        <main class="profile-main-section">

            {{-- 1. RIWAYAT PESANAN --}}
            @php
                $orderTabs = [
                    'all' => 'Semua',
                    'unpaid' => 'Belum Bayar',
                    'processing' => 'Diproses',
                    'shipped' => 'Dikirim',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                ];
                $requestedOrderFilter = request()->query('order_filter');
                $activeOrderFilter = is_string($requestedOrderFilter) && array_key_exists($requestedOrderFilter, $orderTabs)
                    ? $requestedOrderFilter
                    : 'all';
                $orderQuery = Auth::user()->orders()
                    ->select(['id', 'status', 'payment_status', 'total', 'created_at'])
                    ->latest();

                if ($activeOrderFilter === 'unpaid') {
                    $orderQuery->where('status', 'pending')
                        ->where('payment_status', 'unpaid');
                } elseif ($activeOrderFilter !== 'all') {
                    $orderQuery->where('status', $activeOrderFilter);
                }

                $recentOrders = $orderQuery->get();
                $orderCount = Auth::user()->orders()->count();
            @endphp
            <div class="section-card">
                <div class="section-card-header">
                    <h2>🛍️ Riwayat Pesanan</h2>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline btn-sm">
                        Lihat Semua Pesanan ({{ $orderCount }}) →
                    </a>
                </div>

                <nav class="order-status-tabs" aria-label="Filter status pesanan">
                    @foreach($orderTabs as $filter => $label)
                        <a
                            href="{{ route('profile', ['order_filter' => $filter]) }}"
                            class="order-status-tab"
                            @if($activeOrderFilter === $filter) aria-current="page" @endif
                        >{{ $label }}</a>
                    @endforeach
                </nav>

                @if($recentOrders->count() > 0)
                    <div class="table-wrap order-table-wrap" style="border:0;box-shadow:none;">
                        <table class="table order-mini-table">
                            <thead>
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total Tagihan</th>
                                    <th>Status</th>
                                    <th style="text-align:right;">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($recentOrders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td style="color:var(--c-ink-soft);">{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td><strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></td>
                                    <td>
                                        @include('user.orders._status', ['status' => $order->status])
                                    </td>
                                    <td style="text-align:right;">
                                        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline btn-sm" style="padding:4px 10px;font-size:12px;">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align:center;padding:30px 20px;color:var(--c-ink-soft);">
                        @if($activeOrderFilter === 'all')
                            <p style="margin-bottom:12px;">Belum ada riwayat pesanan.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-outline btn-sm">Mulai Belanja</a>
                        @else
                            <p style="margin-bottom:12px;">Belum ada pesanan pada kategori ini.</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- 2. BUKU ALAMAT PENGIRIMAN --}}
            <div class="section-card">
                <div class="section-card-header">
                    <h2>📍 Buku Alamat Pengiriman</h2>
                    <a href="{{ route('addresses.create') }}" class="btn btn-primary btn-sm">
                        + Tambah Alamat Baru
                    </a>
                </div>

                @forelse($addresses as $address)
                    <div class="address-card-item">
                        <div class="address-card-top">
                            <span class="address-tag">{{ $address->label }}</span>
                            @if($address->is_default)
                                <span class="badge" style="background:#E3F6EC;color:#14633F;">ALAMAT UTAMA</span>
                            @endif
                        </div>

                        <p style="margin:4px 0;font-weight:700;color:var(--c-ink-heading);">
                            {{ $address->recipient_name }} · <span style="font-weight:400;color:var(--c-ink-soft);">{{ $address->phone }}</span>
                        </p>

                        <p style="color:var(--c-ink);font-size:14px;line-height:1.6;margin:6px 0;">
                            {{ $address->detail }}, {{ $address->village_name }}, {{ $address->district_name }}, {{ $address->city_name }}, {{ $address->province_name }}
                            @if($address->postal_code) · {{ $address->postal_code }} @endif
                        </p>

                        {{-- AKSI ALAMAT --}}
                        <div class="address-actions">
                            <a href="{{ route('addresses.edit', $address->id) }}" class="btn btn-outline btn-sm">
                                Edit Alamat
                            </a>

                            @if(!$address->is_default)
                                <form action="{{ route('addresses.default', $address->id) }}" method="POST" style="margin:0;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-secondary btn-sm">
                                        Set Alamat Utama
                                    </button>
                                </form>
                            @endif

                            <form
                                action="{{ route('addresses.destroy', $address->id) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus alamat {{ $address->label }}?')"
                                style="margin:0;"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="color:var(--c-danger);background:rgba(192,57,43,0.08);border:0;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:40px 20px;color:var(--c-ink-soft);border:1px dashed var(--c-border);border-radius:var(--radius-md);">
                        <p style="margin-bottom:14px;">Belum ada alamat tersimpan.</p>
                        <a href="{{ route('addresses.create') }}" class="btn btn-outline btn-sm">
                            + Tambah Alamat Sekarang
                        </a>
                    </div>
                @endforelse
            </div>

        </main>

    </div>
</div>
@endsection
