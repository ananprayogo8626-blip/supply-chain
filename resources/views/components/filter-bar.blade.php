@props(['filters' => [], 'activeFilters' => []])

<div class="sg-filter-bar">
    <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
        <span style="font-size:13px; font-weight:600; color:var(--sg-text-secondary);">Filters:</span>
        @foreach($filters as $filter)
            <div style="display:flex; align-items:center; gap:8px;">
                <label style="font-size:13px; color:var(--sg-text-primary);">{{ $filter['label'] ?? $filter }}</label>
                @if(isset($filter['type']) && $filter['type'] === 'select')
                    <select name="{{ $filter['name'] }}" style="padding:6px 12px; border-radius:6px; border:1px solid var(--sg-border); background:rgba(30, 41, 59, 0.5); color:var(--sg-text-primary); font-size:13px;">
                        @foreach($filter['options'] ?? [] as $option)
                            <option value="{{ $option['value'] ?? $option }}" {{ (request($filter['name']) == ($option['value'] ?? $option)) ? 'selected' : '' }}>
                                {{ $option['label'] ?? $option }}
                            </option>
                        @endforeach
                    </select>
                @elseif(isset($filter['type']) && $filter['type'] === 'date')
                    <input type="date" name="{{ $filter['name'] }}" value="{{ request($filter['name']) }}" style="padding:6px 12px; border-radius:6px; border:1px solid var(--sg-border); background:rgba(30, 41, 59, 0.5); color:var(--sg-text-primary); font-size:13px;">
                @endif
            </div>
        @endforeach
        @if(!empty($activeFilters))
            <a href="{{ request()->url() }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                <i data-lucide="x" class="w-4 h-4"></i>
                Clear All
            </a>
        @endif
    </div>
</div>
