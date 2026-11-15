@props(['size' => 34, 'text' => true])

<span class="brand">
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 40 40" aria-hidden="true">
        <circle cx="20" cy="20" r="20" fill="#FAACBF"/>
        <g fill="#FFEABB" stroke="#9E3B5C" stroke-width="1.4">
            <ellipse cx="20" cy="11.5" rx="4.2" ry="7"/>
            <ellipse cx="20" cy="11.5" rx="4.2" ry="7" transform="rotate(72 20 20)"/>
            <ellipse cx="20" cy="11.5" rx="4.2" ry="7" transform="rotate(144 20 20)"/>
            <ellipse cx="20" cy="11.5" rx="4.2" ry="7" transform="rotate(216 20 20)"/>
            <ellipse cx="20" cy="11.5" rx="4.2" ry="7" transform="rotate(288 20 20)"/>
        </g>
        <circle cx="20" cy="20" r="3.2" fill="#9E3B5C"/>
    </svg>
    @if($text)
        <span class="brand-text logo-text">Beauty<em>Store</em></span>
    @endif
</span>