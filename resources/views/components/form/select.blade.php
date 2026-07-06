@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'help' => null,
])

@php($id = $attributes->get('id', str_replace(['[', ']'], '_', $name)))

<div class="mb-3">
    @if($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif
    <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'form-select']) }}>
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $value => $text)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>{{ $text }}</option>
        @endforeach
        {{ $slot }}
    </select>
    @error($name)
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @if($help)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>
