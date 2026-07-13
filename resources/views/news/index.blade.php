<x-app-layout>

    <div class="sg-page-header">
        <div class="sg-page-header-row">
            <div>
                <h1 class="sg-page-title">News & Events</h1>
                <p class="sg-page-desc">Supply chain news with AI sentiment analysis, synced from GNews API.</p>
            </div>
            <div class="sg-data-actions">
                <a href="{{ route('news.sync') }}"
                   onclick="return confirm('Sync latest supply chain news from GNews API?')"
                   class="sg-btn sg-btn-teal" id="btn-sync-news">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Sync News API
                </a>
                <a href="{{ route('news.create') }}" class="sg-btn sg-btn-outline" id="btn-add-news">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Article
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="sg-flash sg-flash-success mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="sg-flash sg-flash-error mb-4">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="sg-panel">
        <div class="sg-panel-head" style="padding:0 0 20px 0;margin-bottom:20px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between">
            <div>
                <h2 class="sg-panel-title">Latest Articles</h2>
                <p class="sg-panel-sub">Analyzing the geopolitical, natural, and economic factors impacting global logistics.</p>
            </div>
            <div style="font-size:12px;color:#64748b">Source: GNews API + Sentiment Analysis</div>
        </div>

        <div class="sg-grid-3">
            @forelse($news as $item)
                <div class="sg-news-card" style="display:flex;flex-direction:column;justify-content:space-between;height:100%">
                    <div>
                        @if($item->image)
                            <img src="{{ $item->image }}" class="sg-news-img" alt="News Image" loading="lazy" onerror="this.outerHTML='<div class=\'sg-news-img\' style=\'background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#94a3b8\'><svg fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\' style=\'width:36px;height:36px\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>'">
                        @else
                            <div class="sg-news-img" style="background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#94a3b8">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:36px;height:36px"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif

                        <div class="sg-news-body">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                                <span class="sg-news-source">{{ $item->source ?? 'Unknown' }}</span>
                                @if($item->sentiment === 'Positive')
                                    <span class="sg-sentiment positive">Positive</span>
                                @elseif($item->sentiment === 'Negative')
                                    <span class="sg-sentiment negative">Negative</span>
                                @else
                                    <span class="sg-sentiment neutral">Neutral</span>
                                @endif
                            </div>

                            <h3 class="sg-news-title" style="margin-top:0">
                                @if($item->url)
                                    <a href="{{ $item->url }}" target="_blank" rel="noopener" style="color:inherit;text-decoration:none">
                                        {{ $item->title }}
                                    </a>
                                @else
                                    {{ $item->title }}
                                @endif
                            </h3>
                            <p class="sg-news-summary" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;line-height:1.5;min-height:56px">
                                {{ $item->summary }}
                            </p>
                        </div>
                    </div>

                    <div style="padding:0 16px 16px 16px">
                        <div class="sg-news-meta" style="border-top:1px solid #f1f5f9;padding-top:12px;margin-bottom:12px">
                            <div style="display:flex;align-items:center;gap:6px">
                                @if($item->country && $item->country->flag)
                                    <img src="{{ $item->country->flag }}" style="width:20px;height:14px;object-fit:cover;border-radius:2px;border:1px solid #e2e8f0">
                                @endif
                                <span style="font-weight:600;font-size:12px;color:#475569">{{ $item->country->country_name ?? 'Global' }}</span>
                            </div>
                            <span>{{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('M d, Y') : '—' }}</span>
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between">
                            <div style="display:flex;align-items:center;gap:4px">
                                <span style="font-size:11px;color:#94a3b8">Risk Impact:</span>
                                <span style="font-weight:700;font-size:12px;color:{{ $item->impact_score >= 70 ? '#dc2626' : ($item->impact_score >= 40 ? '#ea580c' : '#16a34a') }}">{{ $item->impact_score ?? 0 }}%</span>
                            </div>
                            <div style="display:flex;gap:6px">
                                <a href="{{ route('news.edit', $item->id) }}" class="sg-btn sg-btn-xs sg-btn-warning" id="edit-news-{{ $item->id }}">Edit</a>
                                <form action="{{ route('news.destroy', $item->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this article?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="sg-btn sg-btn-xs sg-btn-danger" id="del-news-{{ $item->id }}">Del</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: span 3 / span 3; text-align: center; padding: 60px 16px; color: #94a3b8; font-size: 14px;">
                    <div class="sg-empty-icon" style="margin-bottom:14px">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:26px;height:26px;color:#94a3b8;margin:0 auto"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <p>No news articles yet. Click "Sync News API" to fetch from GNews.</p>
                    <a href="{{ route('news.sync') }}" onclick="return confirm('Sync news from GNews API?')" class="sg-btn sg-btn-teal" id="btn-sync-empty">Sync Now</a>
                </div>
            @endforelse
        </div>
    </div>

</x-app-layout>