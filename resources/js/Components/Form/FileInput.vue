<script setup>
import { computed } from 'vue';
import InputError from './InputError.vue';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    name: { type: String, required: true },
    label: { type: String, default: '' },
    help: { type: String, default: '' },
    acceptLabel: { type: String, default: '' },
    maxSize: { type: String, default: '' },
    error: { type: [String, Array], default: '' },
    wrapperClass: { type: String, default: 'mb-3' },
    required: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);
const inputId = computed(() => props.name.replaceAll('[', '_').replaceAll(']', '_'));
const meta = computed(() => [props.acceptLabel, props.maxSize ? `Maks. ${props.maxSize}` : ''].filter(Boolean).join(' | '));
const helpText = computed(() => props.help || meta.value);
const helpId = computed(() => helpText.value ? `${inputId.value}Help` : null);
const errorId = computed(() => props.error ? `${inputId.value}Error` : null);
const describedBy = computed(() => [helpId.value, errorId.value].filter(Boolean).join(' ') || null);
</script>

<template>
    <div :class="wrapperClass">
        <label v-if="label" :for="inputId" class="form-label">
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>
        <div v-if="meta" class="form-file-meta">{{ meta }}</div>
        <input
            :id="inputId"
            type="file"
            :name="name"
            :required="required"
            class="form-control"
            :class="{ 'is-invalid': error }"
            :aria-describedby="describedBy"
            :aria-invalid="error ? 'true' : null"
            v-bind="$attrs"
            @change="emit('update:modelValue', $event.target.files?.[0] ?? null)"
        >
        <InputError :id="errorId" :message="error" />
        <div v-if="helpText" :id="helpId" class="form-text">{{ helpText }}</div>
    </div>
</template>
