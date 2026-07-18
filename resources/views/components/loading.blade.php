@props(['size' => 'medium'])

<div class="sg-loading" style="display:flex; align-items:center; justify-content:center; padding:40px;">
    @if($size === 'small')
        <div style="width:24px; height:24px; border:3px solid var(--sg-border); border-top-color:var(--accent-orange); border-radius:50%; animation:spin 0.8s linear infinite;"></div>
    @elseif($size === 'large')
        <div style="width:48px; height:48px; border:4px solid var(--sg-border); border-top-color:var(--accent-orange); border-radius:50%; animation:spin 1s linear infinite;"></div>
    @else
        <div style="width:36px; height:36px; border:3px solid var(--sg-border); border-top-color:var(--accent-orange); border-radius:50%; animation:spin 0.9s linear infinite;"></div>
    @endif
</div>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
