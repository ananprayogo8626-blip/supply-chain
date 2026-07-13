@props(['title', 'description' => null])

<div class="sg-page-header">
    <h1 class="sg-page-title">{{ $title }}</h1>
    @if($description)
        <p class="sg-page-desc">{{ $description }}</p>
    @endif
</div>
