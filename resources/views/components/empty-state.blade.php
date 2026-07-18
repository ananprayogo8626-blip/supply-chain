@props(['title' => 'No Data Found', 'description' => 'There are no records to display.', 'icon' => 'inbox', 'actionText' => null, 'actionUrl' => null])

<div class="sg-empty-state" style="padding:60px 24px; text-align:center;">
    <div style="width:80px; height:80px; background:rgba(30, 41, 59, 0.5); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px;">
        <i data-lucide="{{ $icon }}" class="w-10 h-10" style="color:var(--sg-text-muted);"></i>
    </div>
    <h3 style="font-size:20px; font-weight:600; color:var(--sg-text-primary); margin:0 0 8px 0;">{{ $title }}</h3>
    <p style="color:var(--sg-text-secondary); margin:0 0 24px 0;">{{ $description }}</p>
    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="sg-btn sg-btn-gradient">
            <i data-lucide="plus" class="w-4 h-4"></i>
            {{ $actionText }}
        </a>
    @endif
</div>
