@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'help' => null,
    'wrapperClass' => 'mb-3',
])

@php($id = $attributes->get('id', str_replace(['[', ']'], '_', $name)))
@php($hasError = $errors->has($name))
@php($helpId = $help ? $id . 'Help' : null)
@php($errorId = $hasError ? $id . 'Error' : null)

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
            @if($attributes->has('required'))<span class="text-danger">*</span>@endif
        </label>
    @endif
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if($helpId || $errorId) aria-describedby="{{ trim($helpId . ' ' . $errorId) }}" @endif
        @if($hasError) aria-invalid="true" @endif
        {{ $attributes->class(['form-select', 'is-invalid' => $hasError]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $value => $text)
            <option value="{{ $value }}" @selected(old($name, $selected) == $value)>{{ $text }}</option>
        @endforeach
        {{ $slot }}
    </select>
    @error($name)
        <div id="{{ $errorId }}" class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @if($help)
        <div id="{{ $helpId }}" class="form-text">{{ $help }}</div>
    @endif
</div>
