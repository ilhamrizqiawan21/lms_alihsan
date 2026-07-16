<script setup>
import { computed } from 'vue';
import InputError from './InputError.vue';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    modelValue: { type: [String, Number, Boolean], default: '' },
    name: { type: String, required: true },
    label: { type: String, default: '' },
    options: { type: [Array, Object], default: () => [] },
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
const normalizedOptions = computed(() => Array.isArray(props.options)
    ? props.options
    : Object.entries(props.options).map(([value, label]) => ({ value, label })));
</script>

<template>
    <div :class="wrapperClass">
        <label v-if="label" :for="inputId" class="form-label">
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>
        <select
            :id="inputId"
            :name="name"
            :value="modelValue"
            :required="required"
            class="form-select"
            :class="{ 'is-invalid': error }"
            :aria-describedby="describedBy"
            :aria-invalid="error ? 'true' : null"
            v-bind="$attrs"
            @change="emit('update:modelValue', $event.target.value)"
        >
            <option v-if="placeholder" value="">{{ placeholder }}</option>
            <option
                v-for="option in normalizedOptions"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
            <slot />
        </select>
        <InputError :id="errorId" :message="error" />
        <div v-if="help" :id="helpId" class="form-text">{{ help }}</div>
    </div>
</template>
