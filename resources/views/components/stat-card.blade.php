@props([
    'label',
    'value',
    'icon' => 'bi-bar-chart-fill',
])

<div {{ $attributes->merge(['class' => 'stat-card']) }}>
    <div class="stat-icon"><i class="bi {{ $icon }}" aria-hidden="true"></i></div>
    <div>
        <div class="stat-number">{{ $value }}</div>
        <div class="stat-label">{{ $label }}</div>
    </div>
</div>
