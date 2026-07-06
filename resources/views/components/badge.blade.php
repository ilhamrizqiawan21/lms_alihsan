@props([
    'color' => 'primary',
    'icon' => null,
])

<span {{ $attributes->merge(['class' => 'badge app-badge bg-' . $color]) }}>
    @if($icon)<i class="bi {{ $icon }} me-1"></i>@endif
    {{ $slot }}
</span>
