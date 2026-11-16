@props(['href', 'label' => 'Kembali'])

<div class="back-bar">
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn-back']) }}>{{ trim(str_replace('←', '', $label)) }}</a>
</div>