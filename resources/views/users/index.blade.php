@extends('layouts.admin')

@section('title', 'Pengguna - Admin')

@section('page-title', 'Pengguna')

@section('content')

<div class="page-header-row">
    <div class="page-header">
        <h2>Manajemen Pengguna</h2>
        <p>Kelola akun admin dan pelanggan Beauty Store.</p>
    </div>

    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Tambah Pengguna</a>
</div>

<div class="admin-card">

    {{-- PENCARIAN + FILTER --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="toolbar">
        <input type="search" name="q" value="{{ request('q') }}"
               placeholder="Cari nama atau email..." class="input">

        <select name="role" class="input">
            <option value="">Semua peran</option>
            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            <option value="user" @selected(request('role') === 'user')>User</option>
        </select>

        <button type="submit" class="btn btn-primary">Cari</button>

        @if(request()->hasAny(['q', 'role']))
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>

    @if($users->count() > 0)

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Peran</th>
                        <th>Pesanan</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="avatar">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <strong>
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <small>(Anda)</small>
                                        @endif
                                    </strong>
                                    <div class="muted">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        <td>{{ $user->orders_count }}</td>

                        <td>{{ $user->created_at?->format('d M Y') }}</td>

                        <td>
                            <div class="row-actions">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary btn-sm">Edit</a>

                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{-- PAGINASI SEDERHANA --}}
        @if($users->hasPages())
            <div class="pager">
                <span>Halaman {{ $users->currentPage() }} dari {{ $users->lastPage() }}</span>

                <div class="row-actions">
                    @if($users->previousPageUrl())
                        <a href="{{ $users->previousPageUrl() }}" class="btn btn-secondary btn-sm">&larr; Sebelumnya</a>
                    @endif
                    @if($users->nextPageUrl())
                        <a href="{{ $users->nextPageUrl() }}" class="btn btn-secondary btn-sm">Berikutnya &rarr;</a>
                    @endif
                </div>
            </div>
        @endif

    @else

        <div class="empty-state">
            <div style="font-size: 40px; margin-bottom: 12px;">👥</div>
            <p>Pengguna tidak ditemukan.</p>
        </div>

    @endif

</div>

@endsection