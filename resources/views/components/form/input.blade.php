@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'help' => null,
    'wrapperClass' => 'mb-3',
    'useOld' => true,
])

@php
    $id = $attributes->get('id', str_replace(['[', ']'], '_', $name));
    $inputValue = $useOld ? old($name, $value) : $value;
    $hasError = $errors->has($name);
    $helpId = $help ? $id . 'Help' : null;
    $errorId = $hasError ? $id . 'Error' : null;
@endphp

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($attributes->has('required'))<span class="text-danger">*</span>@endif
        </label>
    @endif
    <input
        id="{{ $id }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ $inputValue }}"
        placeholder="{{ $placeholder }}"
        @if($helpId || $errorId) aria-describedby="{{ trim($helpId . ' ' . $errorId) }}" @endif
        @if($hasError) aria-invalid="true" @endif
        {{ $attributes->class(['form-control', 'is-invalid' => $hasError]) }}
    >
    @error($name)
        <div id="{{ $errorId }}" class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @if($help)
        <div id="{{ $helpId }}" class="form-text">{{ $help }}</div>
    @endif
</div>
