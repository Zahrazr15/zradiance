@props(['size' => 34, 'text' => true, 'background' => 'light', 'tight' => false])

@php
    // The supplied PNGs' visual contrast is reversed relative to their filenames.
    $logoFile = $background === 'dark' ? 'zradiance-dark' : 'zradiance-light';
    $logoFile .= ($tight ? '-tight' : '') . '.png';
@endphp
<span class="brand">
    <img
        class="brand-logo"
        src="{{ asset('assets/images/branding/' . $logoFile) }}"
        alt="ZRADIANCE"
        style="height:{{ $size }}px;"
    >
</span>