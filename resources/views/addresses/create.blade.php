
@extends('layouts.user')

@section('title', 'Tambah Alamat - Beauty Store')

@push('styles')
    <style>
        .address-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .address-card {
            padding: 35px;
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            border-radius: 15px;
        }

        .address-header {
            margin-bottom: 25px;
        }

        .address-header h1 {
            margin: 0 0 8px;
            color: var(--c-ink);
            font-size: 25px;
        }

        .address-header p {
            margin: 0;
            color: var(--c-ink-soft);
            font-size: 14px;
        }

        .address-card .btn {
            display: inline-block;
            margin-top: 10px;
            padding: 11px 20px;
            border: 0;
            border-radius: 8px;
            text-decoration: none;
            font: inherit;
            cursor: pointer;
        }

        @media (max-width: 600px) {
            .address-container {
                margin: 30px auto;
            }

            .address-card {
                padding: 22px;
            }
        }
    </style>
@endpush

@section('content')
    <div class="address-container">

        <x-back-button
            :href="route('profile')"
            label="Kembali ke Profil"
        />

        <div class="address-card">

            <div class="address-header">
                <h1>Tambah Alamat Baru</h1>
                <p>Lengkapi informasi penerima dan alamat pengiriman.</p>
            </div>

            @if($errors->any())
                <div style="padding:15px; margin-bottom:20px; background:#fff0f0; color:#b42318; border-radius:8px;">
                    <strong>Periksa kembali data berikut:</strong>
                    <ul style="margin:8px 0 0; padding-left:20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('addresses._form')

        </div>
    </div>
@endsection
