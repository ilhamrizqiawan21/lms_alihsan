@props([
    'name' => null,
    'messages' => null,
])

@php
    $items = collect($messages ?? []);

    if ($name && $errors->has($name)) {
        $items = collect($errors->get($name));
    }
@endphp

@if($items->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'invalid-feedback d-block']) }}>
        @if($items->count() === 1)
            {{ $items->first() }}
        @else
            <ul class="mb-0 ps-3">
                @foreach($items as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif
