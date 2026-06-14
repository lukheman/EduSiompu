@props([
    'brandName' => 'EduSiompu',
    'brandIcon' => 'fas fa-graduation-cap'
])

<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="{{ $brandIcon }}"></i>
        <span>{{ $brandName }}</span>
    </div>
    <div class="sidebar-menu">
        {{ $slot }}
    </div>
</div>
