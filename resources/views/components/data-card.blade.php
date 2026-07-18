@props(['title' => '', 'count' => 0, 'icon' => '', 'iconColor' => 'text-orange-400'])

<div class="sg-data-card">
    <div class="sg-data-head">
        <div class="sg-data-head-left">
            @if($icon)
                <i data-lucide="{{ $icon }}" class="w-5 h-5 {{ $iconColor }}"></i>
            @endif
            <h2 class="sg-data-title">
                {{ $title }}
                @if($count > 0)
                    <span class="sg-count-badge">{{ $count }} entries</span>
                @endif
            </h2>
        </div>
        <div class="sg-data-head-right">
            {{ $slot }}
        </div>
    </div>
    <div style="padding:20px;">
        {{ $content ?? '' }}
    </div>
</div>
