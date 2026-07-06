@props([
    'title' => null,
    'icon' => null,
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
])

<div {{ $attributes->merge(['class' => 'card app-card']) }}>
    @if($title || $icon || isset($actions))
        <div class="card-header {{ $headerClass }}">
            <div class="app-card-title">
                @if($icon)
                    <i class="bi {{ $icon }}"></i>
                @endif
                @if($title)
                    <span>{{ $title }}</span>
                @endif
            </div>
            @isset($actions)
                <div class="app-card-actions">{{ $actions }}</div>
            @endisset
        </div>
    @endif

    <div class="card-body {{ $bodyClass }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="card-footer {{ $footerClass }}">
            {{ $footer }}
        </div>
    @endisset
</div>
