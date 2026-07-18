@props(['title' => '', 'subtitle' => '', 'icon' => '', 'iconColor' => 'text-orange-400'])

<div class="sg-page-header">
    <div style="display:flex; align-items:center; gap:16px;">
        @if($icon)
            <div style="width:56px; height:56px; background:rgba(30, 41, 59, 0.5); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                <i data-lucide="{{ $icon }}" class="w-7 h-7 {{ $iconColor }}"></i>
            </div>
        @endif
        <div>
            <h1 class="sg-page-title">{{ $title }}</h1>
            @if($subtitle)
                <p class="sg-page-subtitle">{{ $subtitle }}</p>
            @endif
        </div>
    </div>
    {{ $slot }}
</div>
