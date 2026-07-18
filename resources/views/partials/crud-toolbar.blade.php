@props([
    'searchPlaceholder' => 'Search...',
    'searchValue' => '',
    'filters' => null,
    'showRefresh' => true,
    'showExport' => true,
    'showImport' => false,
    'importAction' => '',
    'importLabel' => 'Import',
    'showAdd' => false,
    'addAction' => '',
    'addLabel' => 'Add',
])

<div class="sg-crud-toolbar">
    <form method="GET" action="{{ request()->url() }}" class="sg-crud-toolbar-form">
        <div class="sg-crud-toolbar-search">
            <i data-lucide="search" class="sg-crud-search-icon"></i>
            <input type="text" 
                   name="search" 
                   class="sg-crud-search-input" 
                   placeholder="{{ $searchPlaceholder }}"
                   value="{{ $searchValue }}"
                   autocomplete="off">
        </div>
        
        @if($filters)
        <div class="sg-crud-toolbar-filters">
            {{ $filters }}
        </div>
        @endif
        
        <div class="sg-crud-toolbar-actions">
            <button type="submit" class="sg-btn sg-btn-sm sg-btn-primary">
                <i data-lucide="search" class="w-4 h-4"></i>
                Search
            </button>
            
            @if(request()->hasAny(['search']))
            <a href="{{ request()->url() }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                <i data-lucide="x" class="w-4 h-4"></i>
                Clear
            </a>
            @endif
            
            @if($showRefresh)
            <button type="button" onclick="window.location.reload()" class="sg-btn sg-btn-sm sg-btn-secondary">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                Refresh
            </button>
            @endif
            
            @if($showExport)
            <button type="button" onclick="exportTableToCSV('export.csv')" class="sg-btn sg-btn-sm sg-btn-secondary">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export
            </button>
            @endif
            
            @if($showImport && $importAction)
            <a href="{{ $importAction }}" class="sg-btn sg-btn-sm sg-btn-outline-orange">
                <i data-lucide="download" class="w-4 h-4"></i>
                {{ $importLabel }}
            </a>
            @endif
            
            @if($showAdd && $addAction)
            <a href="{{ $addAction }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                <i data-lucide="plus" class="w-4 h-4"></i>
                {{ $addLabel }}
            </a>
            @endif
        </div>
    </form>
</div>
