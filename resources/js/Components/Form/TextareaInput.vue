<script setup>
import { computed } from 'vue';
import InputError from './InputError.vue';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    modelValue: { type: String, default: '' },
    name: { type: String, required: true },
    label: { type: String, default: '' },
    placeholder: { type: String, default: '' },
    help: { type: String, default: '' },
    error: { type: [String, Array], default: '' },
    rows: { type: Number, default: 3 },
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
        <textarea
            :id="inputId"
            :name="name"
            :rows="rows"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            class="form-control"
            :class="{ 'is-invalid': error }"
            :aria-describedby="describedBy"
            :aria-invalid="error ? 'true' : null"
            v-bind="$attrs"
            @input="emit('update:modelValue', $event.target.value)"
        ></textarea>
        <InputError :id="errorId" :message="error" />
        <div v-if="help" :id="helpId" class="form-text">{{ help }}</div>
    </div>
</template>
