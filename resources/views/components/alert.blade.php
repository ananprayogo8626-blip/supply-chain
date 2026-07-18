@props(['type' => 'success', 'message' => '', 'dismissible' => false])

@php
    $icon = match($type) {
        'success' => 'check-circle',
        'error' => 'alert-circle',
        'warning' => 'alert-triangle',
        'info' => 'info',
        default => 'info'
    };
    $colorClass = match($type) {
        'success' => 'sg-flash-success',
        'error' => 'sg-flash-error',
        'warning' => 'sg-flash-warning',
        'info' => 'sg-flash-info',
        default => 'sg-flash-info'
    };
@endphp

<div class="sg-flash {{ $colorClass }}" style="display:flex; align-items:center; gap:12px; padding:16px; border-radius:8px; margin-bottom:16px;">
    <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
    <span style="flex:1; color:var(--sg-text-primary);">{{ $message }}</span>
    @if($dismissible)
        <button onclick="this.parentElement.remove()" style="background:none; border:none; color:var(--sg-text-muted); cursor:pointer; padding:4px;">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    @endif
</div>
