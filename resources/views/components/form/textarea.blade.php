@props([
    'name',
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'help' => null,
    'rows' => 3,
    'wrapperClass' => 'mb-3',
    'useOld' => true,
])

@php($id = $attributes->get('id', str_replace(['[', ']'], '_', $name)))
@php($textareaValue = $useOld ? old($name, $value) : $value)
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
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @if($helpId || $errorId) aria-describedby="{{ trim($helpId . ' ' . $errorId) }}" @endif
        @if($hasError) aria-invalid="true" @endif
        {{ $attributes->class(['form-control', 'is-invalid' => $hasError]) }}
    >{{ $textareaValue }}</textarea>
    @error($name)
        <div id="{{ $errorId }}" class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    @if($help)
        <div id="{{ $helpId }}" class="form-text">{{ $help }}</div>
    @endif
</div>
