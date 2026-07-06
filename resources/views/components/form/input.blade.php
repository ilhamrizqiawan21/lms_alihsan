@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'help' => null,
])

@php($id = $attributes->get('id', str_replace(['[', ']'], '_', $name)))

<div class="mb-3">
    @if($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif
    <input
        id="{{ $id }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-control']) }}
    >
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>
