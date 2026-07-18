@props(['title' => '', 'value' => '', 'icon' => '', 'iconColor' => 'text-orange-400', 'trend' => null, 'trendColor' => null])

<div class="sg-stat-card">
    <div style="padding:20px;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
            <div style="display:flex; align-items:center; gap:12px;">
                @if($icon)
                    <div style="width:40px; height:40px; background:rgba(30, 41, 59, 0.5); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5 {{ $iconColor }}"></i>
                    </div>
                @endif
                <span style="font-size:12px; font-weight:600; text-transform:uppercase; color:var(--sg-text-secondary);">{{ $title }}</span>
            </div>
            @if($trend)
                <span style="font-size:12px; font-weight:600; color:{{ $trendColor ?? 'var(--sg-success)' }};">{{ $trend }}</span>
            @endif
        </div>
        <div style="font-size:28px; font-weight:700; color:var(--sg-text-primary);">{{ $value }}</div>
    </div>
</div>
