@php
    $labels = [
        'pending'    => '⏳ Menunggu',
        'processing' => '📦 Diproses',
        'shipped'    => '🚚 Dikirim',
        'completed'  => '✅ Selesai',
        'cancelled'  => '✕ Dibatalkan',
    ];
@endphp

<span class="badge status-{{ $status }}" style="font-weight:700;padding:6px 12px;border-radius:var(--radius-pill);display:inline-flex;align-items:center;gap:4px;">
    {{ $labels[$status] ?? ucfirst($status) }}
</span>
