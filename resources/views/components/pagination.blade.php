@props(['paginator' => null])

@if($paginator && $paginator->hasPages())
    <div class="sg-pagination" style="display:flex; justify-content:center; align-items:center; gap:8px; padding:20px;">
        {{-- Previous Button --}}
        @if($paginator->onFirstPage())
            <button disabled class="sg-btn sg-btn-sm sg-btn-secondary" style="opacity:0.5;">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>
        @endif

        {{-- Page Numbers --}}
        @foreach($paginator->getUrlRange(1, $paginator->lastPage()) as $url => $page)
            @if($page == $paginator->currentPage())
                <span class="sg-btn sg-btn-sm sg-btn-gradient">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="sg-btn sg-btn-sm sg-btn-secondary">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next Button --}}
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
        @else
            <button disabled class="sg-btn sg-btn-sm sg-btn-secondary" style="opacity:0.5;">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        @endif

        {{-- Info --}}
        <span style="font-size:13px; color:var(--sg-text-muted); margin-left:12px;">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
            ({{ $paginator->total() }} total)
        </span>
    </div>
@endif
