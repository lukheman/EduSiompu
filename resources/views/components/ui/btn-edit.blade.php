@props([
    'size' => 'sm', // 'sm', 'md', 'lg'
    'tooltip' => 'Edit',
    'iconOnly' => true,
])

@php
    $sizeStyles = [
        'sm' => 'width: 32px; height: 32px; font-size: 0.8rem;',
        'md' => 'width: 38px; height: 38px; font-size: 0.9rem;',
        'lg' => 'width: 44px; height: 44px; font-size: 1rem;',
    ];

    $btnSize = $sizeStyles[$size] ?? $sizeStyles['sm'];
@endphp

<button
    {{ $attributes->merge(['class' => 'action-btn action-btn-edit', 'type' => 'button']) }}
    style="{{ $btnSize }}"
    title="{{ $tooltip }}"
>
    <i class="fas fa-edit"></i>
    @if(!$iconOnly)
        <span class="ms-1">{{ $slot->isEmpty() ? 'Edit' : $slot }}</span>
    @endif
</button>
