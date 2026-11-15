
@extends('layouts.admin')

@section('title', 'Detail Pesanan - Admin')
@section('page-title', 'Detail Pesanan')

@section('content')

<x-back-button :href="route('admin.orders.index')" label="Kembali ke Pesanan" />

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<div class="page-header">
    <div>
        <h2>Detail Pesanan #{{ $order->id }}</h2>
        <p>Informasi lengkap mengenai pesanan pelanggan.</p>
    </div>
</div>

@php
    $status = $order->status ?? 'pending';
    $paymentStatus = $order->payment_status ?? 'unpaid';

    $statusLabels = [
        'pending' => 'Menunggu',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];

    $paymentLabels = [
        'unpaid' => 'Belum Dibayar',
        'paid' => 'Sudah Dibayar',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
        'refunded' => 'Dikembalikan',
    ];

    $isCod = $order->payment_method === 'cod';

    $allowedNextStatuses = [
        'pending' => [],
        'processing' => ['shipped'],
        'shipped' => ['completed'],
        'completed' => [],
        'cancelled' => [],
    ];

    if ($status === 'pending') {
        if ($isCod) {
            $nextStatuses = ['processing', 'cancelled'];
        } elseif ($paymentStatus === 'paid') {
            $nextStatuses = ['processing'];
        } else {
            $nextStatuses = ['cancelled'];
        }
    } else {
        $nextStatuses = $allowedNextStatuses[$status] ?? [];
    }

    $canUpdate = count($nextStatuses) > 0;

    $stockProblem = !$isCod
        && $paymentStatus === 'paid'
        && $order->stock_deducted_at === null
        && $status !== 'cancelled';

    $paymentMethodLabels = [
        'online' => 'Pembayaran Online',
        'qris' => 'QRIS',
        'bank_transfer' => 'Transfer Bank',
        'e_wallet' => 'E-Wallet',
        'cod' => 'Cash on Delivery (COD)',
    ];
@endphp

<div class="order-detail-grid">

    {{-- INFORMASI PESANAN --}}
    <div class="admin-card">
        <h3 class="section-heading">Informasi Pesanan</h3>

        <div class="order-info-grid">

            <div>
                <span class="meta-label">ID Pesanan</span>
                <strong>#{{ $order->id }}</strong>
            </div>

            <div>
                <span class="meta-label">Tanggal Pesanan</span>
                <strong>{{ $order->created_at->format('d F Y, H:i') }}</strong>
            </div>

            <div>
                <span class="meta-label">Pelanggan</span>
                <strong>{{ $order->user->name ?? 'User' }}</strong>
                <div>{{ $order->user->email ?? '-' }}</div>
            </div>

            <div>
                <span class="meta-label">Nama Penerima</span>
                <strong>{{ $order->recipient_name }}</strong>
            </div>

            <div>
                <span class="meta-label">Nomor Telepon</span>
                <strong>{{ $order->phone }}</strong>
            </div>

            <div class="full-width">
                <span class="meta-label">Alamat Pengiriman</span>

                <div style="line-height:1.6;">
                    {{ $order->address_detail ?? $order->address }}
                    <br>
                    {{ $order->district }},
                    {{ $order->city }},
                    {{ $order->province }}
                    {{ $order->postal_code }}
                </div>

                @if ($order->delivery_note)
                    <p style="margin-top:8px;">
                        <strong>Catatan Kurir:</strong>
                        {{ $order->delivery_note }}
                    </p>
                @endif
            </div>

            <div>
                <span class="meta-label">Kurir</span>
                <strong>
                    {{ strtoupper($order->shipping_courier ?? '-') }}
                    {{ $order->shipping_service ?? '' }}
                </strong>
            </div>

            <div>
                <span class="meta-label">Estimasi Pengiriman</span>
                <strong>{{ $order->shipping_etd ?? '-' }}</strong>
            </div>

            <div>
                <span class="meta-label">Berat Pengiriman</span>
                <strong>
                    {{ number_format($order->shipping_weight ?? 0, 0, ',', '.') }} gram
                </strong>
            </div>

        </div>
    </div>

    {{-- STATUS PESANAN --}}
    <div class="admin-card">
        <h3 class="section-heading">Status Pesanan</h3>

        <div class="status-display">
            {{ $statusLabels[$status] ?? ucfirst($status) }}
        </div>

        <h4 style="margin-bottom:10px;">Status Pembayaran</h4>

        <p style="margin-bottom:15px;">
            <strong>
                {{ $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}
            </strong>
        </p>

        <p style="margin-bottom:15px;">
            <strong>Metode:</strong>
            {{ $paymentMethodLabels[$order->payment_method] ?? ($order->payment_method ?? '-') }}
        </p>

        @if ($order->payment_reference)
            <p style="margin-bottom:15px;">
                <strong>Referensi:</strong><br>
                {{ $order->payment_reference }}
            </p>
        @endif

        @if ($stockProblem)
            <div class="stock-warning">
                <strong>Perlu Pemeriksaan Stok</strong>
                <p>
                    Pembayaran sudah diterima, tetapi stok belum berhasil dipotong.
                    Periksa ketersediaan produk sebelum memproses pesanan.
                </p>
            </div>
        @endif

        @if ($canUpdate)
            <form
                action="{{ route('admin.orders.update', $order) }}"
                method="POST"
                onsubmit="return confirm('Apakah kamu yakin ingin mengubah status pesanan?')"
            >
                @csrf
                @method('PUT')

                <label style="display:block;margin-bottom:7px;font-weight:600;">
                    Ubah Status
                </label>

                <select
                    name="status"
                    required
                    style="
                        width:100%;
                        padding:11px;
                        border:1px solid var(--c-border);
                        border-radius:7px;
                        margin-bottom:15px;
                    "
                >
                    @foreach ($nextStatuses as $nextStatus)
                        <option value="{{ $nextStatus }}">
                            {{ $statusLabels[$nextStatus] ?? ucfirst($nextStatus) }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary" style="width:100%;">
                    Simpan Status
                </button>
            </form>
        @else
            <p class="status-hint">
                @if ($status === 'pending' && !$isCod && $paymentStatus !== 'paid')
                    Pesanan online belum dibayar. Admin hanya dapat membatalkan pesanan ini.
                @else
                    Tidak ada perubahan status yang tersedia.
                @endif
            </p>
        @endif

        @if ($isCod && $status === 'pending')
            <p class="status-hint" style="margin-top:12px;">
                Pesanan COD dapat diproses. Stok akan dipotong ketika status berubah menjadi Diproses.
            </p>
        @endif

        @if (!$isCod && $status === 'pending' && $paymentStatus === 'paid')
            <p class="status-hint" style="margin-top:12px;">
                Pembayaran telah diterima. Pesanan dapat mulai diproses.
            </p>
        @endif

    </div>
</div>

{{-- DAFTAR PRODUK --}}
<div class="admin-card" style="margin-top:20px;">
    <h3 class="section-heading">Produk yang Dipesan</h3>

    @if ($order->items->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:var(--c-bg);text-align:left;">
                        <th style="padding:14px;">Produk</th>
                        <th style="padding:14px;">Harga</th>
                        <th style="padding:14px;">Jumlah</th>
                        <th style="padding:14px;">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($order->items as $item)
                        <tr style="border-top:1px solid var(--c-border);">
                            <td style="padding:14px;">
                                <strong>{{ $item->product_name }}</strong>
                            </td>

                            <td style="padding:14px;white-space:nowrap;">
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </td>

                            <td style="padding:14px;">
                                {{ $item->quantity }}
                            </td>

                            <td style="padding:14px;white-space:nowrap;">
                                <strong>
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </strong>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p style="text-align:center;padding:35px;color:var(--c-ink-soft);">
            Tidak ada produk dalam pesanan ini.
        </p>
    @endif

    {{-- TOTAL PEMBAYARAN --}}
    <div class="order-total">
        <div class="total-row">
            <span>Subtotal Produk</span>
            <strong>
                Rp {{ number_format($order->total - ($order->shipping_cost ?? 0), 0, ',', '.') }}
            </strong>
        </div>

        <div class="total-row">
            <span>Ongkos Kirim</span>
            <strong>
                Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}
            </strong>
        </div>

        <hr>

        <div class="total-row total-grand">
            <strong>Total Pesanan</strong>
            <strong>
                Rp {{ number_format($order->total, 0, ',', '.') }}
            </strong>
        </div>
    </div>
</div>

<style>
    .order-detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
    }

    .section-heading {
        font-size: 19px;
        margin-bottom: 20px;
        color: var(--c-ink);
    }

    .order-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .meta-label {
        display: block;
        color: var(--c-ink-soft);
        font-size: 13px;
        margin-bottom: 5px;
    }

    .status-display {
        padding: 12px;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        margin-bottom: 20px;
        background: #dbeafe;
        color: #1e40af;
    }

    .status-hint {
        font-size: 13px;
        color: var(--c-ink-soft);
    }

    .stock-warning {
        padding: 12px;
        margin-bottom: 18px;
        border-radius: 8px;
        background: #fff7ed;
        color: #9a3412;
        font-size: 13px;
    }

    .stock-warning p {
        margin: 6px 0 0;
        line-height: 1.5;
    }

    .order-total {
        max-width: 380px;
        margin: 20px 0 0 auto;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 10px;
    }

    .total-grand {
        margin-top: 12px;
        font-size: 18px;
        color: #d97706;
    }

    @media (max-width: 850px) {
        .order-detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 550px) {
        .order-info-grid {
            grid-template-columns: 1fr;
        }

        .full-width {
            grid-column: auto;
        }
    }
</style>

@endsection
