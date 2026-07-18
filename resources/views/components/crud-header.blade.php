@props(['title' => '', 'description' => '', 'icon' => '', 'iconColor' => 'text-orange-400'])

<div class="sg-crud-header">
    <div class="sg-crud-header-content">
        <div class="sg-crud-header-left">
            <div style="display:flex; align-items:center; gap:16px;">
                @if($icon)
                    <div style="width:48px; height:48px; background:rgba(30, 41, 59, 0.5); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="{{ $icon }}" class="w-6 h-6 {{ $iconColor }}"></i>
                    </div>
                @endif
                <div>
                    <h1 class="sg-crud-title">{{ $title }}</h1>
                    <p class="sg-crud-description">{{ $description }}</p>
                </div>
            </div>
        </div>
        <div class="sg-crud-header-actions">
            {{ $slot }}
        </div>
    </div>
</div>
