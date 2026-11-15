@extends('layouts.admin')

@section('title', 'Edit Pengguna - Admin')

@section('page-title', 'Edit Pengguna')

@section('content')

<div class="form-narrow">

    <div class="page-header" style="text-align:center;">
        <h2>Edit Pengguna</h2>
        <p>Perbarui data akun {{ $user->name }}.</p>
    </div>

    <div class="admin-card">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            @include('users._form')
        </form>
    </div>

</div>

@endsection