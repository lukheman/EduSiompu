@props([
    'variant' => 'success', // success, danger, warning, info
    'message' => null,
])

@php
    $bgStyle = match($variant) {
        'success' => 'background: var(--success-color); color: white;',
        'danger' => 'background: var(--danger-color); color: white;',
        'warning' => 'background: var(--warning-color); color: white;',
        'info' => 'background: #0ea5e9; color: white;', // custom info color
        default => 'background: var(--success-color); color: white;',
    };
    $icon = match($variant) {
        'success' => 'fas fa-check-circle',
        'danger' => 'fas fa-times-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'info' => 'fas fa-info-circle',
        default => 'fas fa-check-circle',
    };
@endphp

<div x-data="{ show: true }" 
     x-show="show" 
     x-init="setTimeout(() => show = false, 4000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-x-8"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="toast-container position-fixed top-0 end-0 p-3" 
     style="z-index: 1080; margin-top: 70px;">
    
    <div class="toast show align-items-center border-0 shadow-lg rounded-3" style="{{ $bgStyle }}" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center gap-2" style="font-weight: 500;">
                <i class="{{ $icon }} fs-5"></i>
                {{ $message ?? $slot }}
            </div>
            <button type="button" class="btn-close btn-close-white me-3 m-auto" @click="show = false" aria-label="Close"></button>
        </div>
    </div>
</div>
