@props(['href', 'label' => 'Kembali'])

<div class="back-bar">
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn-back']) }}>{{ $label }}</a>
</div>