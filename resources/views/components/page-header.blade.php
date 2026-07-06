@props([
    'title',
    'icon' => null,
    'subtitle' => null,
])

<div {{ $attributes->merge(['class' => 'page-header app-page-header']) }}>
    <div>
        <h4>
            @if($icon)
                <i class="bi {{ $icon }} me-2"></i>
            @endif
            {{ $title }}
        </h4>
        @if($subtitle)
            <p class="app-page-subtitle">{{ $subtitle }}</p>
        @endif
    </div>

    @if(trim($slot) !== '')
        <div class="app-page-actions">
            {{ $slot }}
        </div>
    @endif
</div>
