@extends('layouts.user')

@section('title', 'Pesanan Saya - Beauty Store')

@push('styles')
<style>
    .orders-page {
        max-width: 1200px;
        margin: 36px auto;
        padding: 0 32px 80px;
    }

    .orders-header {
        margin-bottom: 28px;
    }

    .orders-header h1 {
        font-size: 32px;
        color: var(--c-ink-heading);
        margin-bottom: 6px;
    }

    .orders-header p {
        color: var(--c-ink-soft);
        font-size: 15px;
    }

    .orders-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 28px;
        box-shadow: var(--shadow-sm);
    }

    .orders-table th {
        background: #FCF8F6;
        color: var(--c-ink-heading);
        font-weight: 800;
        font-size: 13px;
        padding: 16px;
    }

    .orders-table td {
        padding: 18px 16px;
        vertical-align: middle;
    }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: var(--radius-pill);
        font-size: 12px;
        font-weight: 700;
    }
    .pay-paid { background: #E3F6EC; color: #14633F; }
    .pay-unpaid { background: #FFF3D6; color: #8A5A00; }
    .pay-failed, .pay-expired { background: #FDE7E4; color: #8E2B20; }
    .pay-refunded { background: #E0EEFC; color: #1B538C; }

    @media (max-width: 768px) {
        .orders-page { padding: 0 16px 50px; margin-top: 20px; }
        .orders-card { padding: 18px 12px; }
    }
</style>
@endpush

@section('content')
<div class="orders-page">

    <div class="orders-header">
        <h1>Riwayat Pesanan Saya</h1>
        <p>Pantau status transaksi, resi ekspedisi, dan riwayat pesanan kosmetik Anda.</p>
    </div>

    <div class="orders-card">
        @if($orders->count() > 0)
            <div class="table-wrap" style="border:0;box-shadow:none;">
                <table class="table orders-table">
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Tanggal Pembelian</th>
                            <th>Jumlah Item</th>
                            <th>Total Tagihan</th>
                            <th>Status Pengiriman</th>
                            <th>Status Pembayaran</th>
                            <th style="text-align:right;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong style="color:var(--c-ink-heading);font-size:15px;">#{{ $order->id }}</strong>
                            </td>

                            <td style="color:var(--c-ink-soft);font-size:13.5px;">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>

                            <td style="font-weight:700;">
                                {{ $order->items_count }} Produk
                            </td>

                            <td>
                                <strong style="color:var(--c-pink-strong);font-size:15.5px;">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </strong>
                            </td>

                            <td>
                                @include('user.orders._status', [
                                    'status' => $order->status
                                ])
                            </td>

                            <td>
                                @php
                                    $paymentLabels = [
                                        'unpaid' => 'Belum Dibayar',
                                        'paid' => 'Lunas',
                                        'failed' => 'Gagal',
                                        'expired' => 'Kedaluwarsa',
                                        'refunded' => 'Dikembalikan',
                                    ];

                                    $paymentStatus = $order->payment_status ?? 'unpaid';
                                    $payClass = 'pay-' . $paymentStatus;
                                @endphp

                                <span class="payment-badge {{ $payClass }}">
                                    {{ $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}
                                </span>
                            </td>

                            <td style="text-align:right;">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline btn-sm">
                                    Lihat Detail →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div style="font-size: 52px; margin-bottom: 14px;">🛍️</div>
                <h3 style="font-size:20px;color:var(--c-ink-heading);margin-bottom:8px;">Belum Ada Riwayat Pesanan</h3>
                <p style="margin-bottom: 22px;color:var(--c-ink-soft);">Anda belum memiliki transaksi pesanan kecantikan di akun ini.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    Mulai Belanja Sekarang
                </a>
            </div>
        @endif
    </div>

</div>
@endsection
