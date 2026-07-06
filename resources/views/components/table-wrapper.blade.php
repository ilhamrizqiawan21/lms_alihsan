@props([
    'responsive' => true,
])

@if($responsive)
    <div {{ $attributes->merge(['class' => 'table-responsive app-table-wrapper']) }}>
        {{ $slot }}
    </div>
@else
    <div {{ $attributes->merge(['class' => 'app-table-wrapper']) }}>
        {{ $slot }}
    </div>
@endif
