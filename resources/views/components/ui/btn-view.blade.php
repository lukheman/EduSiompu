@props([
    'size' => 'sm',
    'tooltip' => 'Lihat',
])

<x-ui.button
    variant="secondary"
    :size="$size"
    icon="fas fa-eye"
    title="{{ $tooltip }}"
    {{ $attributes }}
>
    {{ $slot->isEmpty() ? 'Detail' : $slot }}
</x-ui.button>
