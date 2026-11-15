@props(['title' => ''])

@php
    $t = strtolower($title);
    $type = match (true) {
        str_contains($t, 'makeup') => 'makeup',
        str_contains($t, 'skin')   => 'skincare',
        str_contains($t, 'body')   => 'body',
        str_contains($t, 'hair')   => 'hair',
        default                    => 'default',
    };
@endphp

<span class="cat-icon cat-icon-{{ $type }}" aria-hidden="true">
    <svg viewBox="0 0 48 48" width="34" height="34" fill="none" stroke="currentColor"
         stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        @if($type === 'makeup')
            <rect x="18" y="24" width="12" height="14" rx="2"/>
            <rect x="16" y="38" width="16" height="4" rx="1"/>
            <path d="M20 24V15l8-6v15"/>
        @elseif($type === 'skincare')
            <rect x="16" y="20" width="16" height="20" rx="4"/>
            <rect x="21" y="14" width="6" height="6"/>
            <path d="M24 14V8M21 8h6"/>
        @elseif($type === 'body')
            <path d="M24 8c6 8 10 12 10 18a10 10 0 0 1-20 0c0-6 4-10 10-18z"/>
        @elseif($type === 'hair')
            <rect x="8" y="18" width="32" height="8" rx="2"/>
            <path d="M12 26v8M18 26v8M24 26v8M30 26v8M36 26v8"/>
        @else
            <path d="M24 8l3.5 10.5L38 22l-10.5 3.5L24 36l-3.5-10.5L10 22l10.5-3.5z"/>
        @endif
    </svg>
</span>