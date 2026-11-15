
@extends('layouts.admin')

@section('title', 'Notifikasi Admin - Beauty Store')
@section('page-title', 'Notifikasi')

@section('content')
@php
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp

<div class="page-header">
    <h2>Notifikasi</h2>
    <p>Informasi pesanan dan pembayaran terbaru.</p>
</div>

<div class="admin-card" style="margin-bottom:18px;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
    <div>
        <strong>{{ $unreadCount }}</strong> notifikasi belum dibaca
    </div>

    @if($unreadCount > 0)
        <form action="{{ route('admin.notifications.read-all') }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-primary">
                Tandai semua dibaca
            </button>
        </form>
    @endif
</div>

<div class="admin-card">
    @forelse($notifications as $notification)
        @php
            $data = $notification->data;
            $isUnread = is_null($notification->read_at);
        @endphp

        <div style="
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            gap:16px;
            padding:16px 0;
            border-bottom:1px solid var(--c-border);
            {{ $isUnread ? 'background:var(--c-cream);padding-left:12px;padding-right:12px;border-radius:8px;' : '' }}
        ">
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
                    <strong>{{ $data['title'] ?? 'Notifikasi' }}</strong>

                    @if($isUnread)
                        <span class="cart-badge" style="position:static;display:inline-block;">
                            Baru
                        </span>
                    @endif
                </div>

                <p style="margin:0 0 8px;color:var(--c-ink-soft);">
                    {{ $data['message'] ?? 'Ada pembaruan pesanan.' }}
                </p>

                @if(!empty($data['order_id']))
                    <small>
                        Pesanan #{{ $data['order_id'] }}
                        @if(isset($data['total']))
                            · Rp {{ number_format((float) $data['total'], 0, ',', '.') }}
                        @endif
                    </small>
                @endif

                <div style="margin-top:6px;">
                    <small class="muted">
                        {{ $notification->created_at->format('d M Y, H:i') }}
                    </small>
                </div>
            </div>

            <div style="flex-shrink:0;">
                @if($isUnread)
                    <form
                        action="{{ route('admin.notifications.read', $notification->id) }}"
                        method="POST"
                    >
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary">
                            Lihat
                        </button>
                    </form>
                @else
                    <a
                        href="{{ $data['url'] ?? route('admin.notifications.index') }}"
                        class="btn btn-secondary"
                    >
                        Buka
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:40px 16px;">
            <h3>Belum ada notifikasi</h3>
            <p class="muted">Notifikasi pesanan dan pembayaran akan muncul di sini.</p>
        </div>
    @endforelse

    <div style="margin-top:18px;">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
