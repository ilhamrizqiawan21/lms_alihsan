@props([
    'title' => 'Data belum tersedia',
    'message' => null,
    'icon' => 'bi-inbox',
])

<div {{ $attributes->merge(['class' => 'empty-state']) }}>
    <div class="empty-state-icon"><i class="bi {{ $icon }}"></i></div>
    <div class="empty-state-title">{{ $title }}</div>
    @if($message)
        <div class="empty-state-message">{{ $message }}</div>
    @endif
    @if(trim($slot) !== '')
        <div class="empty-state-action">{{ $slot }}</div>
    @endif
</div>
