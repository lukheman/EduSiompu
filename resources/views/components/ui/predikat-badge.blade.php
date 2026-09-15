@props(['nilai' => null])

@if($nilai)
    <x-ui.badge :variant="$nilai === 'A' ? 'success' : ($nilai === 'B' ? 'primary' : ($nilai === 'C' ? 'warning' : 'danger'))">
        {{ $nilai }}
    </x-ui.badge>
@else
    -
@endif
