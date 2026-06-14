@props([
    'size' => 'sm',
    'tooltip' => 'Edit',
])

<x-ui.button
    variant="primary"
    :size="$size"
    icon="fas fa-edit"
    title="{{ $tooltip }}"
    {{ $attributes }}
>
    {{ $slot->isEmpty() ? 'Edit' : $slot }}
</x-ui.button>
