@php
    $labels = [
        'pending'    => 'Menunggu',
        'processing' => 'Diproses',
        'shipped'    => 'Dikirim',
        'completed'  => 'Selesai',
        'cancelled'  => 'Dibatalkan',
    ];
@endphp

<span class="badge status-{{ $status }}">{{ $labels[$status] ?? ucfirst($status) }}</span>