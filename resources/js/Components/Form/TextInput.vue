<script setup>
import { computed } from 'vue';
import InputError from './InputError.vue';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    name: { type: String, required: true },
    label: { type: String, default: '' },
    type: { type: String, default: 'text' },
    placeholder: { type: String, default: '' },
    help: { type: String, default: '' },
    error: { type: [String, Array], default: '' },
    wrapperClass: { type: String, default: 'mb-3' },
    required: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);
const inputId = computed(() => props.name.replaceAll('[', '_').replaceAll(']', '_'));
const helpId = computed(() => props.help ? `${inputId.value}Help` : null);
const errorId = computed(() => props.error ? `${inputId.value}Error` : null);
const describedBy = computed(() => [helpId.value, errorId.value].filter(Boolean).join(' ') || null);
</script>

<template>
    <div :class="wrapperClass">
        <label v-if="label" :for="inputId" class="form-label">
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>
        <input
            :id="inputId"
            :type="type"
            :name="name"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            class="form-control"
            :class="{ 'is-invalid': error }"
            :aria-describedby="describedBy"
            :aria-invalid="error ? 'true' : null"
            v-bind="$attrs"
            @input="emit('update:modelValue', $event.target.value)"
        >
        <InputError :id="errorId" :message="error" />
        <div v-if="help" :id="helpId" class="form-text">{{ help }}</div>
    </div>
</template>
