@props([
    'title' => '',
    'description' => '',
    'icon' => 'layout-dashboard',
    'iconColor' => 'text-orange-500',
    'actions' => null,
])

<div class="sg-crud-header">
    <div class="sg-crud-header-content">
        <div class="sg-crud-header-left">
            <h1 class="sg-crud-title">
                <i data-lucide="{{ $icon }}" class="sg-crud-title-icon {{ $iconColor }}"></i>
                {{ $title }}
            </h1>
            <p class="sg-crud-description">{{ $description }}</p>
        </div>
        @if($actions)
        <div class="sg-crud-header-actions">
            {{ $actions }}
        </div>
        @endif
    </div>
</div>
