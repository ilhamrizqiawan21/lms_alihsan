@props([
    'name',
    'label' => null,
    'help' => null,
    'acceptLabel' => null,
    'maxSize' => null,
    'wrapperClass' => 'mb-3',
])

@php
    $id = $attributes->get('id', str_replace(['[', ']'], '_', $name));
    $hasError = $errors->has($name);
    $meta = collect([$acceptLabel, $maxSize ? 'Maks. ' . $maxSize : null])->filter()->implode(' | ');
    $helpText = $help ?: $meta;
    $helpId = $helpText ? $id . 'Help' : null;
    $errorId = $hasError ? $id . 'Error' : null;
@endphp

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($attributes->has('required'))<span class="text-danger">*</span>@endif
        </label>
    @endif
    @if($meta)
        <div class="form-file-meta">{{ $meta }}</div>
    @endif
    <input
        id="{{ $id }}"
        type="file"
        name="{{ $name }}"
        @if($helpId || $errorId) aria-describedby="{{ trim($helpId . ' ' . $errorId) }}" @endif
        @if($hasError) aria-invalid="true" @endif
        {{ $attributes->class(['form-control', 'is-invalid' => $hasError]) }}
    >
    @error($name)
        <div id="{{ $errorId }}" class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @if($helpText)
        <div id="{{ $helpId }}" class="form-text">{{ $helpText }}</div>
    @endif
</div>
