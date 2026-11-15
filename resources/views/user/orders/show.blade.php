@extends('layouts.user')

@section('title', 'Pesanan #' . $order->id . ' - Beauty Store')

@push('styles')
<style>
    .order-detail-page {
        max-width: 1100px;
        margin: 36px auto;
        padding: 0 32px 80px;
    }

    .order-header-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }

    .order-header-box h1 {
        font-size: 30px;
        color: var(--c-ink-heading);
        margin-bottom: 4px;
    }

    .order-detail-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border-subtle);
        border-radius: var(--radius-lg);
        padding: 30px;
        box-shadow: var(--shadow-sm);
        margin-bottom: 24px;
    }

    .order-detail-card h2 {
        font-size: 19px;
        color: var(--c-ink-heading);
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--c-border-subtle);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .order-grid-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
    }

    .meta-box {
        background: #FCF8F6;
        padding: 16px;
        border-radius: var(--radius-md);
        border: 1px solid var(--c-border-subtle);
    }
    .meta-box .label {
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--c-ink-light);
        margin-bottom: 4px;
    }
    .meta-box .val {
        font-size: 14.5px;
        color: var(--c-ink-heading);
        font-weight: 700;
    }

    .order-items-table th {
        background: #FCF8F6;
        padding: 14px 16px;
        font-size: 13px;
        font-weight: 800;
        color: var(--c-ink-heading);
    }
    .order-items-table td {
        padding: 16px;
        border-top: 1px solid var(--c-border-subtle);
        vertical-align: middle;
    }

    .payment-action-card {
        background: linear-gradient(135deg, rgba(250, 172, 191, 0.2) 0%, rgba(255, 234, 187, 0.3) 100%);
        border: 1.5px solid var(--c-border);
        border-radius: var(--radius-lg);
        padding: 28px;
        margin-bottom: 24px;
    }

    @media (max-width: 768px) {
        .order-detail-page { padding: 0 16px 50px; margin-top: 20px; }
        .order-detail-card { padding: 20px; }
    }
</style>
@endpush

@section('content')
<div class="order-detail-page">

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert-success" style="margin-bottom:20px;">
            <span>✓</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->has('status'))
        <div class="alert-error" style="margin-bottom:20px;">
            {{ $errors->first('status') }}
        </div>
    @endif

    @if ($errors->has('payment'))
        <div class="alert-error" style="margin-bottom:20px;">
            {{ $errors->first('payment') }}
        </div>
    @endif

    @php
        $paymentLabels = [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Sudah Dibayar (Lunas)',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            'refunded' => 'Dikembalikan (Refund)',
        ];

        $paymentStatus = $order->payment_status ?? 'unpaid';

        $canCancel = $order->status === 'pending'
            && !in_array($paymentStatus, ['paid', 'refunded'], true);

        $canPay = $order->payment_method !== 'cod'
            && $order->status === 'pending'
            && $paymentStatus === 'unpaid';

        $hasSnapToken = $canPay && !empty($order->snap_token);
    @endphp

    <div class="back-bar">
        <x-back-button :href="route('orders.index')" label="← Kembali ke Riwayat Pesanan" />
    </div>

    {{-- HEADER PESANAN --}}
    <div class="order-header-box">
        <div>
            <h1>Pesanan #{{ $order->id }}</h1>
            <p style="color:var(--c-ink-soft);font-size:14.5px;">
                Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WIB
            </p>
        </div>

        <div style="display:flex;align-items:center;gap:12px;">
            @include('user.orders._status', [
                'status' => $order->status
            ])

            @if ($canCancel)
                <form action="{{ route('orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm" style="color:var(--c-danger);border-color:var(--c-danger);">
                        Batalkan Pesanan
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- PAYMENT ACTIONS (MIDTRANS SNAP / COD) --}}
    <div class="payment-action-card">
        <h3 style="font-size:18px;color:var(--c-ink-heading);margin-bottom:8px;">
            💳 Status & Metode Pembayaran
        </h3>

        <div style="font-size:14.5px;color:var(--c-ink-soft);margin-bottom:12px;">
            Metode: <strong>{{ $order->payment_method === 'cod' ? 'Cash on Delivery (COD)' : 'Pembayaran Online (Midtrans)' }}</strong>
            · Status: <strong>{{ $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}</strong>
        </div>

        @if ($order->payment_method === 'cod')
            <p style="margin:0;font-size:14px;color:var(--c-ink);">
                💵 Pesanan menggunakan metode Bayar di Tempat (COD). Silakan siapkan uang pas saat kurir tiba di alamat Anda.
            </p>
        @elseif ($paymentStatus === 'paid')
            <p style="margin:0;font-size:14px;color:#14633F;font-weight:700;">
                ✓ Pembayaran Anda telah berhasil diverifikasi. Pesanan sedang disiapkan untuk dikirim.
            </p>
        @elseif ($order->status === 'cancelled')
            <p style="margin:0;font-size:14px;color:var(--c-danger);">
                Pesanan telah dibatalkan. Pembayaran ditutup.
            </p>
        @elseif (in_array($paymentStatus, ['expired', 'failed'], true))
            <p style="margin:0;font-size:14px;color:var(--c-danger);">
                Sesi pembayaran telah kedaluwarsa atau gagal. Silakan hubungi admin jika butuh bantuan lanjutan.
            </p>
        @elseif ($canPay && !$hasSnapToken)
            <p style="margin-bottom:12px;font-size:14px;color:var(--c-ink);">
                Pesanan menunggu pembayaran. Klik tombol di bawah untuk menyiapkan transaksi digital.
            </p>
            <form action="{{ route('orders.pay', $order) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    Siapkan Transaksi Pembayaran
                </button>
            </form>
        @elseif ($hasSnapToken)
            <p style="margin-bottom:14px;font-size:14px;color:var(--c-ink);">
                Transaksi sudah siap. Lanjutkan pembayaran instan Anda sekarang via popup Midtrans:
            </p>
            <button type="button" id="pay-button" class="btn btn-primary">
                Bayar Sekarang 🔒
            </button>
        @endif
    </div>

    {{-- INFORMASI PENGIRIMAN --}}
    <div class="order-detail-card">
        <h2>🚚 Informasi Pengiriman & Penerima</h2>

        <div class="order-grid-meta">
            <div class="meta-box">
                <div class="label">Nama Penerima</div>
                <div class="val">{{ $order->recipient_name }}</div>
            </div>

            <div class="meta-box">
                <div class="label">Nomor Telepon</div>
                <div class="val">{{ $order->phone }}</div>
            </div>

            <div class="meta-box">
                <div class="label">Jasa Kurir & Layanan</div>
                <div class="val">{{ strtoupper($order->shipping_courier) }} - {{ $order->shipping_service }}</div>
            </div>

            <div class="meta-box">
                <div class="label">Nomor Resi Pengiriman</div>
                <div class="val">
                    @if ($order->tracking_number)
                        <span style="font-family:monospace;letter-spacing:1px;color:var(--c-pink-strong);">
                            {{ $order->tracking_number }}
                        </span>
                    @else
                        <span style="color:var(--c-ink-soft);font-weight:400;">Belum diterbitkan</span>
                    @endif
                </div>
            </div>
        </div>

        <div style="margin-top:20px;padding:16px;background:#FCF8F6;border-radius:var(--radius-md);font-size:14px;line-height:1.6;">
            <strong>Alamat Lengkap Tujuan:</strong><br>
            {{ $order->address_detail ?? $order->address }}<br>
            {{ $order->district }}, {{ $order->city }}, {{ $order->province }} {{ $order->postal_code }}
            @if ($order->delivery_note)
                <div style="margin-top:8px;color:var(--c-ink-soft);">
                    <strong>Catatan untuk Kurir:</strong> {{ $order->delivery_note }}
                </div>
            @endif
        </div>
    </div>

    {{-- DAFTAR PRODUK PESANAN --}}
    <div class="order-detail-card">
        <h2>🛍️ Rincian Produk yang Dibeli</h2>

        <div class="table-wrap" style="border:0;box-shadow:none;">
            <table class="table order-items-table">
                <thead>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th style="text-align:center;">Jumlah</th>
                        <th style="text-align:right;">Harga Satuan</th>
                        <th style="text-align:right;">Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->product_name }}</strong>
                        </td>
                        <td style="color:var(--c-ink-soft);font-size:13px;">
                            {{ $item->category_title ?? '-' }}
                        </td>
                        <td style="text-align:center;font-weight:700;">
                            {{ $item->quantity }}
                        </td>
                        <td style="text-align:right;">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </td>
                        <td style="text-align:right;font-weight:800;">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;padding-top:18px;">
                            <strong>Subtotal Produk:</strong>
                        </td>
                        <td style="text-align:right;padding-top:18px;font-weight:800;">
                            Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align:right;border-top:0;">
                            <strong>Ongkos Kirim:</strong>
                        </td>
                        <td style="text-align:right;border-top:0;font-weight:800;">
                            Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr style="font-size:18px;color:var(--c-pink-strong);">
                        <td colspan="4" style="text-align:right;border-top:2px solid var(--c-border);">
                            <strong>Grand Total Pembayaran:</strong>
                        </td>
                        <td style="text-align:right;border-top:2px solid var(--c-border);font-weight:800;">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

{{-- MIDTRANS SNAP SCRIPT INTEGRATION --}}
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
                    alert('Pembayaran berhasil diproses. Status akan otomatis diperbarui setelah verifikasi.');
                    window.location.reload();
                },
                onPending: function () {
                    alert('Pembayaran Anda sedang menunggu konfirmasi.');
                    window.location.reload();
                },
                onError: function () {
                    alert('Pembayaran gagal. Silakan coba kembali.');
                },
                onClose: function () {
                    console.log('Popup Midtrans ditutup oleh pengguna.');
                }
            });
        });
    </script>
@endif
@endsection
