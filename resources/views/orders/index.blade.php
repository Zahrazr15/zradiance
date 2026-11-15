
@extends('layouts.admin')

@section('title', 'Pesanan - Admin')
@section('page-title', 'Pesanan')

@section('content')

<div class="page-header">
    <div>
        <h2>Manajemen Pesanan</h2>
        <p>Kelola dan pantau pesanan pelanggan Beauty Store.</p>
    </div>
</div>

@php
    $paymentLabels = [
        'unpaid' => 'Belum Dibayar',
        'paid' => 'Sudah Dibayar',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
        'refunded' => 'Dikembalikan',
    ];

    $statusLabels = [
        'pending' => 'Menunggu',
        'processing' => 'Diproses',
        'shipped' => 'Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
    ];
@endphp

<div class="admin-card">

    {{-- FILTER DAN PENCARIAN --}}
    <form
        method="GET"
        action="{{ route('admin.orders.index') }}"
        class="toolbar"
    >
        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            class="input"
            placeholder="Cari ID, penerima, pelanggan, atau HP..."
        >

        <select name="status" class="input">
            <option value="">Semua status pesanan</option>

            @foreach ($statuses as $s)
                <option
                    value="{{ $s }}"
                    @selected(request('status') === $s)
                >
                    {{ $statusLabels[$s] ?? ucfirst($s) }}
                </option>
            @endforeach
        </select>

        <input
            type="date"
            name="date_from"
            value="{{ request('date_from') }}"
            class="input"
            aria-label="Tanggal mulai"
        >

        <input
            type="date"
            name="date_to"
            value="{{ request('date_to') }}"
            class="input"
            aria-label="Tanggal akhir"
        >

        <button type="submit" class="btn btn-primary">
            Cari
        </button>

        @if (request()->hasAny(['q', 'status', 'date_from', 'date_to']))
            <a
                href="{{ route('admin.orders.index') }}"
                class="btn btn-secondary"
            >
                Reset
            </a>
        @endif
    </form>

    {{-- RINGKASAN HASIL --}}
    <div class="order-result-info">
        Menampilkan {{ $orders->count() }} dari {{ $orders->total() }} pesanan.
    </div>

    @if ($orders->count() > 0)

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">

                <thead>
                    <tr style="background:var(--c-bg);text-align:left;">
                        <th style="padding:14px;">ID Pesanan</th>
                        <th style="padding:14px;">Pelanggan</th>
                        <th style="padding:14px;">Penerima</th>
                        <th style="padding:14px;">Total</th>
                        <th style="padding:14px;">Status Pesanan</th>
                        <th style="padding:14px;">Pembayaran</th>
                        <th style="padding:14px;">Tanggal</th>
                        <th style="padding:14px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($orders as $order)

                        @php
                            $status = strtolower($order->status ?? 'pending');
                            $paymentStatus = $order->payment_status ?? 'unpaid';

                            $statusStyles = [
                                'pending' => ['#fef3c7', '#92400e'],
                                'processing' => ['#dbeafe', '#1e40af'],
                                'shipped' => ['#e0e7ff', '#3730a3'],
                                'completed' => ['#dcfce7', '#166534'],
                                'cancelled' => ['#fee2e2', '#991b1b'],
                            ];

                            $paymentStyles = [
                                'unpaid' => ['#fef3c7', '#92400e'],
                                'paid' => ['#dcfce7', '#166534'],
                                'failed' => ['#fee2e2', '#991b1b'],
                                'expired' => ['#f3f4f6', '#374151'],
                                'refunded' => ['#e0e7ff', '#3730a3'],
                            ];

                            $statusStyle = $statusStyles[$status] ?? ['#f3f4f6', '#374151'];
                            $paymentStyle = $paymentStyles[$paymentStatus] ?? ['#f3f4f6', '#374151'];

                            $stockProblem = $order->payment_method !== 'cod'
                                && $paymentStatus === 'paid'
                                && $order->stock_deducted_at === null
                                && $status !== 'cancelled';
                        @endphp

                        <tr style="border-top:1px solid var(--c-border);">

                            {{-- ID PESANAN --}}
                            <td style="padding:14px;">
                                <strong>#{{ $order->id }}</strong>
                            </td>

                            {{-- PELANGGAN --}}
                            <td style="padding:14px;">
                                <div style="display:flex;align-items:center;gap:10px;">

                                    <div class="customer-avatar">
                                        {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                                    </div>

                                    <div>
                                        <strong style="display:block;color:var(--c-ink);">
                                            {{ $order->user->name ?? 'User' }}
                                        </strong>

                                        <span style="font-size:12px;color:var(--c-ink-soft);">
                                            {{ $order->user->email ?? '-' }}
                                        </span>
                                    </div>

                                </div>
                            </td>

                            {{-- PENERIMA --}}
                            <td style="padding:14px;">
                                <strong>{{ $order->recipient_name }}</strong>

                                <div style="font-size:12px;color:var(--c-ink-soft);margin-top:4px;">
                                    {{ $order->phone }}
                                </div>
                            </td>

                            {{-- TOTAL --}}
                            <td style="padding:14px;white-space:nowrap;">
                                <strong style="color:#d97706;">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </strong>
                            </td>

                            {{-- STATUS PESANAN --}}
                            <td style="padding:14px;">
                                <span
                                    class="order-badge"
                                    style="background:{{ $statusStyle[0] }};color:{{ $statusStyle[1] }};"
                                >
                                    {{ $statusLabels[$status] ?? ucfirst($status) }}
                                </span>
                            </td>

                            {{-- STATUS PEMBAYARAN --}}
                            <td style="padding:14px;">
                                <span
                                    class="order-badge"
                                    style="background:{{ $paymentStyle[0] }};color:{{ $paymentStyle[1] }};"
                                >
                                    {{ $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}
                                </span>

                                @if ($stockProblem)
                                    <div class="stock-problem-label">
                                        Perlu pemeriksaan stok
                                    </div>
                                @endif
                            </td>

                            {{-- TANGGAL --}}
                            <td style="padding:14px;white-space:nowrap;">
                                <span style="font-size:13px;color:var(--c-ink-soft);">
                                    {{ $order->created_at->format('d/m/Y H:i') }}
                                </span>
                            </td>

                            {{-- AKSI --}}
                            <td style="padding:14px;">
                                <a
                                    href="{{ route('admin.orders.show', $order) }}"
                                    class="btn"
                                    style="background:var(--c-border);color:var(--c-ink);"
                                >
                                    Detail
                                </a>
                            </td>

                        </tr>

                    @endforeach
                </tbody>

            </table>
        </div>

        {{-- PAGINASI --}}
        @if ($orders->hasPages())
            <div class="pager">
                <span>
                    Halaman {{ $orders->currentPage() }}
                    dari {{ $orders->lastPage() }}
                </span>

                <div class="row-actions">
                    @if ($orders->previousPageUrl())
                        <a
                            href="{{ $orders->previousPageUrl() }}"
                            class="btn btn-secondary btn-sm"
                        >
                            &larr; Sebelumnya
                        </a>
                    @endif

                    @if ($orders->nextPageUrl())
                        <a
                            href="{{ $orders->nextPageUrl() }}"
                            class="btn btn-secondary btn-sm"
                        >
                            Berikutnya &rarr;
                        </a>
                    @endif
                </div>
            </div>
        @endif

    @else

        <div class="empty-orders">
            <div style="font-size:42px;margin-bottom:15px;">
                🛒
            </div>

            @if (request()->hasAny(['q', 'status', 'date_from', 'date_to']))
                <h3>Pesanan Tidak Ditemukan</h3>
                <p>Tidak ada pesanan yang sesuai dengan pencarian atau filter.</p>

                <a
                    href="{{ route('admin.orders.index') }}"
                    class="btn btn-secondary"
                >
                    Reset Filter
                </a>
            @else
                <h3>Belum Ada Pesanan</h3>
                <p>Pesanan pelanggan akan muncul di halaman ini.</p>
            @endif
        </div>

    @endif

</div>

<style>
    .order-result-info {
        margin: 15px 0;
        font-size: 13px;
        color: var(--c-ink-soft);
    }

    .customer-avatar {
        width: 38px;
        height: 38px;
        flex-shrink: 0;
        border-radius: 50%;
        background: var(--c-cream);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 700;
        color: var(--c-ink);
    }

    .order-badge {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .stock-problem-label {
        margin-top: 6px;
        font-size: 11px;
        font-weight: 600;
        color: #b45309;
        white-space: nowrap;
    }

    .empty-orders {
        text-align: center;
        padding: 50px 20px;
        color: var(--c-ink-soft);
    }

    .empty-orders h3 {
        margin-bottom: 8px;
        color: var(--c-ink);
    }

    .empty-orders p {
        margin-bottom: 18px;
    }
</style>

@endsection