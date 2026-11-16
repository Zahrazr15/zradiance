@props(['size' => 34, 'text' => true, 'background' => 'light'])

@php
    // The supplied PNGs' visual contrast is reversed relative to their filenames.
    $logoFile = $background === 'dark' ? 'zradiance-dark.png' : 'zradiance-light.png';
@endphp
<span class="brand">
    <img
        class="brand-logo"
        src="{{ asset('assets/images/branding/' . $logoFile) }}"
        alt="ZRADIANCE"
        style="height:{{ $size }}px;"
    >
</span>