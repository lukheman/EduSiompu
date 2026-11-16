@props([
    'nama' => null,
    'size' => 34,
])

@php
    $ikon = \App\Support\MapelIcon::untuk($nama);
@endphp

<span {{ $attributes->merge(['class' => 'd-inline-flex align-items-center justify-content-center rounded-3 flex-shrink-0']) }}
    style="width: {{ $size }}px; height: {{ $size }}px; background: {{ $ikon['warna'] }}1f; color: {{ $ikon['warna'] }};"
    title="{{ $nama }}">
    <i class="{{ $ikon['icon'] }}"></i>
</span>
