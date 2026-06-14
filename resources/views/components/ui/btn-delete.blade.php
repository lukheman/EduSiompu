@props([
    'size' => 'sm',
    'tooltip' => 'Hapus',
])

<x-ui.button
    variant="danger"
    :size="$size"
    icon="fas fa-trash-alt"
    title="{{ $tooltip }}"
    {{ $attributes }}
>
    {{ $slot->isEmpty() ? 'Hapus' : $slot }}
</x-ui.button>
