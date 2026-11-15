
@extends('layouts.user')

@section('title', 'Pesanan Saya - Beauty Store')

@section('content')

<div class="page-wrap">

    <div class="page-header">
        <h2>Pesanan Saya</h2>
        <p>Riwayat dan status pesananmu.</p>
    </div>

    <div class="card">

        @if($orders->count() > 0)

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status Pesanan</th>
                            <th>Status Pembayaran</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td><strong>#{{ $order->id }}</strong></td>

                            <td>
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>

                            <td>{{ $order->items_count }} produk</td>

                            <td>
                                Rp {{ number_format($order->total, 0, ',', '.') }}
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
                                        'paid' => 'Sudah Dibayar',
                                        'failed' => 'Gagal',
                                        'expired' => 'Kedaluwarsa',
                                        'refunded' => 'Dikembalikan',
                                    ];

                                    $paymentStatus = $order->payment_status ?? 'unpaid';
                                @endphp

                                <span>
                                    {{ $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}
                                </span>
                            </td>

                            <td>
                                <a href="{{ route('orders.show', $order) }}"
                                   class="btn btn-secondary btn-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        @else

            <div class="empty-state">
                <div style="font-size: 40px; margin-bottom: 12px;">🛍️</div>
                <p style="margin-bottom: 15px;">Kamu belum punya pesanan.</p>
                <a href="{{ route('categories.index') }}" class="btn btn-primary">
                    Mulai belanja
                </a>
            </div>

        @endif

    </div>

</div>

@endsection
