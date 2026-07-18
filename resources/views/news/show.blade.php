<x-app-layout>
    <!-- Detail Header -->
    <div class="sg-crud-header">
        <div class="sg-crud-header-content">
            <div class="sg-crud-header-left">
                <div style="display:flex; align-items:center; gap:16px;">
                    @if($news->country && $news->country->flag)
                        <img src="{{ $news->country->flag }}" alt="{{ $news->country->country_name }}"
                             onerror="this.onerror=null;this.src='https://flagcdn.com/w80/un.png';"
                             style="width:80px; height:52px; object-fit:cover; border-radius:8px; border:1px solid var(--sg-border);">
                    @endif
                    <div>
                        <h1 class="sg-crud-title">News Article</h1>
                        <p class="sg-crud-description">
                            {{ $news->country->country_name ?? 'Unknown' }} • {{ $news->source ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="sg-crud-header-actions">
                <a href="{{ route('news.index') }}" class="sg-btn sg-btn-sm sg-btn-secondary">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Back to List
                </a>
                <a href="{{ route('news.edit', $news->id) }}" class="sg-btn sg-btn-sm sg-btn-gradient">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    Edit
                </a>
                @if($news->url)
                <a href="{{ $news->url }}" target="_blank" rel="noopener" class="sg-btn sg-btn-sm sg-btn-teal">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    Read Original
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- News Image -->
    <div class="sg-data-card" style="margin-bottom:24px; padding:20px; display:flex; justify-content:center; align-items:center; background: rgba(0,0,0,0.2);">
        @if($news->image)
            <img src="{{ $news->image }}" alt="{{ $news->title }}" style="max-width:100%; max-height:320px; object-fit:contain; border-radius:10px; border:1px solid var(--sg-border);" loading="lazy" onerror="this.src='https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800&q=80'">
        @else
            <img src="https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=800&q=80" alt="{{ $news->title }}" style="max-width:100%; max-height:320px; object-fit:contain; border-radius:10px; border:1px solid var(--sg-border);" loading="lazy">
        @endif
    </div>

    <!-- Article Content -->
    <div class="sg-data-card" style="margin-bottom:24px;">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="newspaper" class="w-5 h-5 text-purple-400"></i>
                <h2 class="sg-data-title">Article Details</h2>
            </div>
        </div>
        <div style="padding:24px;">
            <h1 style="font-size:28px; font-weight:700; color:var(--sg-text-primary); margin:0 0 16px 0; line-height:1.3;">
                {{ $news->title }}
            </h1>
            
            <div style="display:flex; gap:12px; align-items:center; margin-bottom:20px;">
                @php 
                    $sentiment = $news->sentiment ?? 'Neutral';
                    $sentimentClass = match($sentiment) {
                        'Positive' => 'low',
                        'Negative' => 'critical',
                        default => 'medium'
                    };
                @endphp
                <span class="sg-badge {{ $sentimentClass }}">{{ $sentiment }}</span>
                <span style="font-size:13px; color:var(--sg-text-muted);">
                    {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->format('M d, Y H:i') : '—' }}
                </span>
            </div>

            <div style="font-size:15px; color:var(--sg-text-primary); line-height:1.7; max-width:800px;">
                @if($news->summary)
                    <p>{{ $news->summary }}</p>
                @else
                    <p style="color:var(--sg-text-secondary);">No description available for this article.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Metadata Grid -->
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px; margin-bottom:24px;">
        
        <!-- Source Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="globe" class="w-5 h-5 text-blue-400"></i>
                    <h2 class="sg-data-title">Source Information</h2>
                </div>
            </div>
            <div style="padding:20px;">
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Source</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">{{ $news->source ?? '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Country</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">
                            {{ $news->country->country_name ?? 'Unknown' }}
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--sg-text-secondary); font-size:13px;">Category</span>
                        <span style="color:var(--sg-text-primary); font-weight:600;">{{ $news->category ?? 'General' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sentiment Card -->
        <div class="sg-data-card">
            <div class="sg-data-head">
                <div class="sg-data-head-left">
                    <i data-lucide="brain" class="w-5 h-5 text-pink-400"></i>
                    <h2 class="sg-data-title">AI Sentiment Analysis</h2>
                </div>
            </div>
            <div style="padding:20px;">
                <div style="text-align:center; padding:20px;">
                    <div style="width:100px; height:100px; background:{{ $sentiment === 'Positive' ? 'rgba(16, 185, 129, 0.1)' : ($sentiment === 'Negative' ? 'rgba(239, 68, 68, 0.1)' : 'rgba(30, 41, 59, 0.5)') }}; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                        <i data-lucide="{{ $sentiment === 'Positive' ? 'smile' : ($sentiment === 'Negative' ? 'frown' : 'meh') }}" class="w-12 h-12" style="color:{{ $sentiment === 'Positive' ? '#10b981' : ($sentiment === 'Negative' ? '#ef4444' : '#94a3b8') }}"></i>
                    </div>
                    <div style="font-size:28px; font-weight:800; color:{{ $sentiment === 'Positive' ? '#10b981' : ($sentiment === 'Negative' ? '#ef4444' : '#94a3b8') }};">
                        {{ $sentiment }}
                    </div>
                    <div style="font-size:13px; color:var(--sg-text-secondary); margin-top:8px;">
                        AI-powered sentiment
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Record Information -->
    <div class="sg-data-card">
        <div class="sg-data-head">
            <div class="sg-data-head-left">
                <i data-lucide="clock" class="w-5 h-5 text-green-400"></i>
                <h2 class="sg-data-title">Record Information</h2>
            </div>
        </div>
        <div style="padding:20px;">
            <div style="display:flex; flex-direction:column; gap:12px;">
                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Published Date</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">
                        {{ $news->published_at ? \Carbon\Carbon::parse($news->published_at)->format('M d, Y H:i:s') : '—' }}
                    </span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--sg-border);">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Record ID</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">#{{ $news->id }}</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--sg-text-secondary); font-size:13px;">Data Source</span>
                    <span style="color:var(--sg-text-primary); font-weight:600;">GNews API</span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
