@props([
    'size' => 'sm', // 'sm', 'md', 'lg'
    'tooltip' => 'View',
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
    {{ $attributes->merge(['class' => 'action-btn action-btn-view', 'type' => 'button']) }}
    style="{{ $btnSize }}"
    title="{{ $tooltip }}"
>
    <i class="fas fa-eye"></i>
    @if(!$iconOnly)
        <span class="ms-1">{{ $slot->isEmpty() ? 'View' : $slot }}</span>
    @endif
</button>
