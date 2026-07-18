@props([
    'searchPlaceholder' => 'Search...',
    'searchValue' => '',
    'showRefresh' => false,
    'showExport' => false,
    'showImport' => false,
    'showAdd' => false,
    'addRoute' => null,
    'refreshAction' => null,
    'exportAction' => null,
    'importAction' => null,
])

<div class="sg-crud-toolbar">
    <div class="sg-crud-toolbar-content">
        <div class="sg-crud-toolbar-left">
            <form method="GET" style="display:flex; gap:12px; flex:1;">
                <div style="position:relative; flex:1; max-width:400px;">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="{{ $searchPlaceholder }}" 
                        value="{{ $searchValue }}"
                        style="width:100%; padding:10px 16px 10px 40px; border-radius:8px; border:1px solid var(--sg-border); background:rgba(30, 41, 59, 0.5); color:var(--sg-text-primary); font-size:14px;"
                    >
                    <i data-lucide="search" class="w-4 h-4" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--sg-text-muted);"></i>
                </div>
                <button type="submit" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="filter" class="w-4 h-4"></i>
                    Filter
                </button>
                @if($searchValue)
                    <a href="{{ request()->url() }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        Clear
                    </a>
                @endif
            </form>
        </div>
        <div class="sg-crud-toolbar-right">
            <div style="display:flex; gap:8px;">
                @if($showRefresh)
                    <button @if($refreshAction) onclick="{{ $refreshAction }}" @else onclick="location.reload()" @endif class="sg-btn sg-btn-sm sg-btn-secondary">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        Refresh
                    </button>
                @endif
                @if($showExport)
                    <button @if($exportAction) onclick="{{ $exportAction }}" @else onclick="alert('Export functionality coming soon')" @endif class="sg-btn sg-btn-sm sg-btn-secondary">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Export
                    </button>
                @endif
                @if($showImport)
                    <button @if($importAction) onclick="{{ $importAction }}" @else onclick="alert('Import functionality coming soon')" @endif class="sg-btn sg-btn-sm sg-btn-secondary">
                        <i data-lucide="upload" class="w-4 h-4"></i>
                        Import
                    </button>
                @endif
                @if($showAdd && $addRoute)
                    <a href="{{ $addRoute }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Add New
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
