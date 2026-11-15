@extends('layouts.admin')

@section('title', 'Reset Password - Admin')
@section('page-title', 'Permintaan Reset Password')

@section('content')

<div class="page-header">
    <h2>Permintaan Reset Password</h2>
    <p>Setujui atau tolak permintaan reset password dari pengguna.</p>
</div>

<div class="admin-card">
    <form method="GET" class="toolbar">
        <select name="status" class="input" onchange="this.form.submit()">
            @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $v => $l)
                <option value="{{ $v }}" @selected($status === $v)>{{ $l }}</option>
            @endforeach
        </select>
    </form>

    @if($requests->count() > 0)
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Pengguna</th><th>Catatan</th><th>Diminta</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                @foreach($requests as $r)
                    <tr>
                        <td>
                            <strong>{{ $r->user->name }}</strong>
                            <div class="muted">{{ $r->user->email }}</div>
                        </td>
                        <td>{{ $r->note ?: '-' }}</td>
                        <td>{{ $r->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <span class="badge">{{ ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'][$r->status] }}</span>
                            @if($r->handler)<div class="muted">oleh {{ $r->handler->name }}</div>@endif
                        </td>
                        <td>
                            @if($r->status === 'pending')
                                <div class="row-actions">
                                    <form action="{{ route('admin.password-resets.approve', $r) }}" method="POST"
                                          data-confirm="Setujui reset password untuk {{ $r->user->name }}? Password lamanya langsung diganti.">
                                        @csrf
                                        <button class="btn btn-success btn-sm">Setujui</button>
                                    </form>
                                    <form action="{{ route('admin.password-resets.reject', $r) }}" method="POST"
                                          data-confirm="Tolak permintaan ini?">
                                        @csrf
                                        <button class="btn btn-danger btn-sm">Tolak</button>
                                    </form>
                                </div>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="pager">
                <span>Halaman {{ $requests->currentPage() }} dari {{ $requests->lastPage() }}</span>
                <div class="row-actions">
                    @if($requests->previousPageUrl())<a href="{{ $requests->previousPageUrl() }}" class="btn btn-secondary btn-sm">Sebelumnya</a>@endif
                    @if($requests->nextPageUrl())<a href="{{ $requests->nextPageUrl() }}" class="btn btn-secondary btn-sm">Berikutnya</a>@endif
                </div>
            </div>
        @endif
    @else
        <div class="empty-state"><p>Tidak ada permintaan.</p></div>
    @endif
</div>
@endsection