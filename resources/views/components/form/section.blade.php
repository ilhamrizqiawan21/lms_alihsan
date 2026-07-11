@props([
    'title' => null,
    'description' => null,
    'icon' => null,
])

<section {{ $attributes->merge(['class' => 'form-section']) }}>
    @if($title || $description || $icon)
        <div class="form-section-header">
            @if($icon)
                <span class="form-section-icon"><i class="bi {{ $icon }}"></i></span>
            @endif
            <div>
                @if($title)
                    <div class="form-section-title">{{ $title }}</div>
                @endif
                @if($description)
                    <div class="form-section-description">{{ $description }}</div>
                @endif
            </div>
        </div>
    @endif
    <div class="form-section-body">
        {{ $slot }}
    </div>
</section>
