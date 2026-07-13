<x-app-layout>

    <div class="sg-page-header">
        <h1 class="sg-page-title">Add Entity to Watchlist</h1>
        <p class="sg-page-desc">Define surveillance parameters for a key supplier, partner, or global supply chain asset.</p>
    </div>

    @if ($errors->any())
        <div class="sg-alert mb-4">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div style="margin-left:8px">
                <span style="font-weight:700">Please correct the following errors:</span>
                <ul style="margin:4px 0 0 0;padding-left:16px">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="sg-form" style="max-width:800px">
        <form action="{{ route('watchlists.store') }}" method="POST" class="sg-form-card">
            @csrf

            <div class="sg-form-head">
                <h2 class="sg-form-title">Surveillance Configuration</h2>
            </div>

            <div class="sg-form-body">
                <div class="sg-form-row">
                    <div class="sg-form-group">
                        <label class="sg-label">Country <sup>*</sup></label>
                        <select name="country_id" class="sg-select" required>
                            <option value="">-- Select Country --</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                    {{ $country->country_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-label">Entity Name / Company <sup>*</sup></label>
                        <input type="text" name="company_name" class="sg-input" placeholder="e.g. Acme Shipping Corp" value="{{ old('company_name') }}" required>
                    </div>
                </div>

                <div class="sg-form-row">
                    <div class="sg-form-group">
                        <label class="sg-label">Industry / Sector</label>
                        <input type="text" name="industry" class="sg-input" placeholder="e.g. Semiconductor Manufacturing" value="{{ old('industry') }}">
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-label">Surveillance Priority <sup>*</sup></label>
                        <select name="priority" class="sg-select" required>
                            <option value="1" {{ old('priority') == 1 ? 'selected' : '' }}>P1 — Critical Threat</option>
                            <option value="2" {{ old('priority') == 2 ? 'selected' : '' }}>P2 — High Threat</option>
                            <option value="3" {{ old('priority', 3) == 3 ? 'selected' : '' }}>P3 — Medium Risk</option>
                            <option value="4" {{ old('priority') == 4 ? 'selected' : '' }}>P4 — Low Risk</option>
                            <option value="5" {{ old('priority') == 5 ? 'selected' : '' }}>P5 — Minimal Concern</option>
                        </select>
                    </div>
                </div>

                <div class="sg-form-row">
                    <div class="sg-form-group">
                        <label class="sg-label">Surveillance Status <sup>*</sup></label>
                        <select name="status" class="sg-select" required>
                            <option value="Monitoring" {{ old('status') === 'Monitoring' ? 'selected' : '' }}>Monitoring</option>
                            <option value="Critical" {{ old('status') === 'Critical' ? 'selected' : '' }}>Critical</option>
                            <option value="Resolved" {{ old('status') === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                    </div>

                    <div class="sg-form-group">
                        <label class="sg-label">Active Surveillance <sup>*</sup></label>
                        <select name="is_active" class="sg-select">
                            <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="sg-form-group" style="margin-bottom:0">
                    <label class="sg-label">Incident Notes / Log Description</label>
                    <textarea name="notes" rows="4" class="sg-textarea" placeholder="Detail any critical risks, supplier bottlenecks, or current news impact...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="sg-form-footer">
                <a href="{{ route('watchlists.index') }}" class="sg-btn sg-btn-outline">Cancel</a>
                <button type="submit" class="sg-btn sg-btn-primary">Save Configuration</button>
            </div>
        </form>
    </div>

</x-app-layout>