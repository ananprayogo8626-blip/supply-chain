@props(['items' => []])

<nav class="sg-breadcrumb" style="display:flex; align-items:center; gap:8px; padding:16px 0; margin-bottom:16px;">
    <a href="{{ route('dashboard') }}" style="color:var(--sg-text-secondary); text-decoration:none; font-size:13px; display:flex; align-items:center; gap:4px;">
        <i data-lucide="home" class="w-4 h-4"></i>
        Home
    </a>
    @foreach($items as $index => $item)
        <i data-lucide="chevron-right" class="w-4 h-4" style="color:var(--sg-text-muted);"></i>
        @if(isset($item['url']) && $item['url'])
            <a href="{{ $item['url'] }}" style="color:var(--sg-text-secondary); text-decoration:none; font-size:13px;">
                {{ $item['label'] }}
            </a>
        @else
            <span style="color:var(--sg-text-primary); font-size:13px; font-weight:600;">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
