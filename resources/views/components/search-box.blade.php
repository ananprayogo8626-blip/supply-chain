@props(['placeholder' => 'Search...', 'value' => '', 'name' => 'search'])

<div style="position:relative;">
    <input 
        type="text" 
        name="{{ $name }}" 
        placeholder="{{ $placeholder }}" 
        value="{{ $value }}"
        style="width:100%; padding:10px 16px 10px 40px; border-radius:8px; border:1px solid var(--sg-border); background:rgba(30, 41, 59, 0.5); color:var(--sg-text-primary); font-size:14px;"
    >
    <i data-lucide="search" class="w-4 h-4" style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:var(--sg-text-muted);"></i>
</div>
