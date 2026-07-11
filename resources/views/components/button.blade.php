@props([
    'type' => 'button',
    'color' => 'primary',
    'size' => 'sm',
    'icon' => null,
    'href' => null,
])

@php
    $classes = trim('btn btn-' . $color . ($size ? ' btn-' . $size : ''));
    $hasLabel = trim($slot) !== '';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="bi {{ $icon }} {{ $hasLabel ? 'me-1' : '' }}" aria-hidden="true"></i>@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)<i class="bi {{ $icon }} {{ $hasLabel ? 'me-1' : '' }}" aria-hidden="true"></i>@endif
        {{ $slot }}
    </button>
@endif
