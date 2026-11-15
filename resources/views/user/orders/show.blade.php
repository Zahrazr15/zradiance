
@extends('layouts.user')

@section('title', 'Pesanan #' . $order->id . ' - Beauty Store')

@section('content')

<div class="page-wrap">

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('status'))
        <div class="alert alert-danger">
            {{ $errors->first('status') }}
        </div>
    @endif

    @if ($errors->has('payment'))
        <div class="alert alert-danger">
            {{ $errors->first('payment') }}
        </div>
    @endif

    @php
        $paymentLabels = [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            'refunded' => 'Dikembalikan',
        ];

        $paymentStatus = $order->payment_status ?? 'unpaid';

        $canCancel = $order->status === 'pending'
            && !in_array($paymentStatus, ['paid', 'refunded'], true);

        $canPay = $order->payment_method !== 'cod'
            && $order->status === 'pending'
            && $paymentStatus === 'unpaid';

        $hasSnapToken = $canPay && !empty($order->snap_token);
    @endphp

    <x-back-button :href="route('orders.index')" label="Semua Pesanan" />

    {{-- HEADER PESANAN --}}
    <div class="page-header-row">
        <div class="page-header">
            <h2>Pesanan #{{ $order->id }}</h2>

            <p>
                Dibuat pada {{ $order->created_at->format('d M Y, H:i') }}
            </p>
        </div>

        @include('user.orders._status', [
            'status' => $order->status
        ])
    </div>

    {{-- PEMBATALAN PESANAN --}}
    @if ($canCancel)
        <div class="card" style="margin-bottom:20px;">
            <p style="margin-bottom:12px;">
                Pesanan masih menunggu. Kamu masih bisa membatalkannya.
            </p>

            <form
                action="{{ route('orders.cancel', $order) }}"
                method="POST"
                onsubmit="return confirm('Apakah kamu yakin ingin membatalkan pesanan ini?')"
            >
                @csrf

                <button type="submit" class="btn btn-danger">
                    Batalkan Pesanan
                </button>
            </form>
        </div>
    @endif

    {{-- INFORMASI PENGIRIMAN --}}
    <div class="card" style="margin-bottom:20px;">
        <h3 style="margin-bottom:15px;">Informasi Pengiriman</h3>

        <div class="order-meta">
            <div>
                <div class="meta-label">Penerima</div>
                <strong>{{ $order->recipient_name }}</strong>
            </div>

            <div>
                <div class="meta-label">No. telepon</div>
                <strong>{{ $order->phone }}</strong>
            </div>

            <div>
                <div class="meta-label">Alamat pengiriman</div>

                <strong>
                    {{ $order->address_detail ?? $order->address }}
                </strong>

                <div>
                    {{ $order->district }},
                    {{ $order->city }},
                    {{ $order->province }}
                    {{ $order->postal_code }}
                </div>

                @if ($order->delivery_note)
                    <div style="margin-top:8px;">
                        <strong>Catatan Kurir:</strong>
                        {{ $order->delivery_note }}
                    </div>
                @endif
            </div>
        </div>

        <div style="margin-top:18px;">
            <div class="meta-label">Kurir</div>

            <strong>
                {{ strtoupper($order->shipping_courier ?? '-') }}
                {{ $order->shipping_service ?? '' }}
            </strong>

            @if ($order->shipping_etd)
                <div>
                    Estimasi pengiriman: {{ $order->shipping_etd }}
                </div>
            @endif
        </div>
    </div>

    {{-- RINCIAN PRODUK --}}
    <div class="card">
        <h3 style="margin-bottom:15px;">Rincian Pesanan</h3>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>

                            <td>
                                Rp {{ number_format($item->price, 0, ',', '.') }}
                            </td>

                            <td>{{ $item->quantity }}</td>

                            <td class="text-right">
                                Rp {{ number_format(
                                    $item->price * $item->quantity,
                                    0,
                                    ',',
                                    '.'
                                ) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                Tidak ada produk dalam pesanan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- TOTAL --}}
        <div style="max-width:350px;margin:20px 0 0 auto;">

            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                <span>Subtotal produk</span>

                <strong>
                    Rp {{ number_format(
                        $order->total - ($order->shipping_cost ?? 0),
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </div>

            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                <span>Ongkos kirim</span>

                <strong>
                    Rp {{ number_format(
                        $order->shipping_cost ?? 0,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </div>

            <hr>

            <div style="display:flex;justify-content:space-between;margin-top:10px;">
                <strong>Total pembayaran</strong>

                <strong>
                    Rp {{ number_format(
                        $order->total,
                        0,
                        ',',
                        '.'
                    ) }}
                </strong>
            </div>
        </div>
    </div>

    {{-- INFORMASI PEMBAYARAN --}}
    <div class="card" style="margin-top:20px;">
        <h3 style="margin-bottom:12px;">Informasi Pembayaran</h3>

        <p>
            <strong>Metode:</strong>

            @switch($order->payment_method)
                @case('online')
                    Pembayaran Online
                    @break

                @case('qris')
                    QRIS
                    @break

                @case('bank_transfer')
                    Transfer Bank
                    @break

                @case('e_wallet')
                    E-Wallet
                    @break

                @case('cod')
                    Cash on Delivery (COD)
                    @break

                @default
                    {{ $order->payment_method ?? '-' }}
            @endswitch
        </p>

        <p>
            <strong>Status pembayaran:</strong>
            {{ $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}
        </p>

        @if ($order->payment_reference)
            <p>
                <strong>Referensi pembayaran:</strong>
                {{ $order->payment_reference }}
            </p>
        @endif

        {{-- PEMBAYARAN COD --}}
        @if ($order->payment_method === 'cod')

            <p style="margin-top:12px;">
                Pembayaran dilakukan saat pesanan diterima.
            </p>

        {{-- PEMBAYARAN BERHASIL --}}
        @elseif ($paymentStatus === 'paid')

            <p style="margin-top:12px;">
                Pembayaran sudah berhasil diterima.
            </p>

        {{-- PEMBAYARAN DIKEMBALIKAN --}}
        @elseif ($paymentStatus === 'refunded')

            <p style="margin-top:12px;">
                Pembayaran telah dikembalikan. Hubungi admin jika membutuhkan informasi lebih lanjut.
            </p>

        {{-- PESANAN DIBATALKAN --}}
        @elseif ($order->status === 'cancelled')

            <p style="margin-top:12px;">
                Pesanan sudah dibatalkan. Pembayaran tidak dapat dilanjutkan.
            </p>

        {{-- PEMBAYARAN GAGAL ATAU KEDALUWARSA --}}
        @elseif (in_array($paymentStatus, ['expired', 'failed'], true))

            <p style="margin-top:12px;">
                Pembayaran tidak dapat dilanjutkan dengan transaksi ini.
                Silakan hubungi admin jika membutuhkan bantuan.
            </p>

        {{-- SIAPKAN SNAP TOKEN --}}
        @elseif ($canPay && !$hasSnapToken)

            <p style="margin-top:12px;">
                Pesanan menunggu pembayaran. Siapkan transaksi untuk melanjutkan pembayaran.
            </p>

            <form
                action="{{ route('orders.pay', $order) }}"
                method="POST"
                style="margin-top:12px;"
            >
                @csrf

                <button type="submit" class="btn btn-primary">
                    Siapkan Pembayaran
                </button>
            </form>

        {{-- BUKA PEMBAYARAN MIDTRANS --}}
        @elseif ($hasSnapToken)

            <p style="margin-top:12px;">
                Transaksi pembayaran sudah disiapkan.
                Lanjutkan pembayaran melalui Midtrans.
            </p>

            <button
                type="button"
                id="pay-button"
                class="btn btn-primary"
                style="margin-top:12px;"
            >
                Bayar Sekarang
            </button>

        @endif
    </div>

</div>

{{-- MIDTRANS SNAP --}}
@if ($hasSnapToken)
    <script
        src="{{ config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('midtrans.client_key') }}">
    </script>

    <script>
        document.getElementById('pay-button')?.addEventListener('click', function () {
            snap.pay(@json($order->snap_token), {
                onSuccess: function () {
                    alert(
                        'Pembayaran berhasil diproses. Status akan diperbarui setelah konfirmasi Midtrans.'
                    );

                    window.location.reload();
                },

                onPending: function () {
                    alert(
                        'Pembayaran sedang menunggu konfirmasi.'
                    );

                    window.location.reload();
                },

                onError: function () {
                    alert(
                        'Pembayaran gagal. Silakan coba kembali.'
                    );
                },

                onClose: function () {
                    console.log('Popup pembayaran ditutup.');
                }
            });
        });
    </script>
@endif

@endsection
