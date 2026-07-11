@props([
    'color' => 'primary',
    'icon' => null,
    'label' => null,
])

<span {{ $attributes->merge(['class' => 'badge app-badge bg-' . $color, 'aria-label' => $label]) }}>
    @if($icon)<i class="bi {{ $icon }} me-1" aria-hidden="true"></i>@endif
    {{ $slot }}
</span>
