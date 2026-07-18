@props(['title' => 'Confirm Delete', 'message' => 'Are you sure you want to delete this item?', 'confirmText' => 'Delete', 'cancelText' => 'Cancel', 'route' => '', 'method' => 'DELETE'])

<div x-data="{ open: false }" class="sg-confirm-delete">
    <button @click="open = true" class="sg-btn sg-btn-sm sg-btn-danger">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
        Delete
    </button>

    <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center" style="background:rgba(0,0,0,0.7);">
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="sg-modal" style="background:rgba(15, 23, 42, 0.95); border:1px solid var(--sg-border); border-radius:16px; padding:32px; max-width:400px; width:90%;">
            <div style="display:flex; align-items:center; gap:16px; margin-bottom:16px;">
                <div style="width:48px; height:48px; background:rgba(239, 68, 68, 0.2); border-radius:50%; display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-red-400"></i>
                </div>
                <h3 style="font-size:18px; font-weight:700; color:var(--sg-text-primary); margin:0;">{{ $title }}</h3>
            </div>
            <p style="color:var(--sg-text-secondary); margin:0 0 24px 0; line-height:1.5;">{{ $message }}</p>
            <div style="display:flex; gap:12px; justify-content:flex-end;">
                <button @click="open = false" class="sg-btn sg-btn-sm sg-btn-secondary">
                    {{ $cancelText }}
                </button>
                <form method="POST" action="{{ $route }}">
                    @csrf
                    @method($method)
                    <button type="submit" class="sg-btn sg-btn-sm sg-btn-danger">
                        {{ $confirmText }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
