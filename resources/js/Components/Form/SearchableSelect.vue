<script setup>
import { computed, nextTick, onBeforeUnmount, ref, watch } from 'vue';
import InputError from './InputError.vue';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    modelValue: { type: [String, Number, Boolean], default: '' },
    name: { type: String, required: true },
    label: { type: String, default: '' },
    options: { type: [Array, Object], default: () => [] },
    placeholder: { type: String, default: 'Pilih...' },
    searchPlaceholder: { type: String, default: 'Cari...' },
    emptyText: { type: String, default: 'Tidak ada pilihan' },
    help: { type: String, default: '' },
    error: { type: [String, Array], default: '' },
    wrapperClass: { type: String, default: 'mb-3' },
    required: { type: Boolean, default: false },
    clearable: { type: Boolean, default: true },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const query = ref('');
const root = ref(null);
const searchInput = ref(null);

const inputId = computed(() => props.name.replaceAll('[', '_').replaceAll(']', '_'));
const helpId = computed(() => props.help ? `${inputId.value}Help` : null);
const errorId = computed(() => props.error ? `${inputId.value}Error` : null);
const describedBy = computed(() => [helpId.value, errorId.value].filter(Boolean).join(' ') || null);
const listboxId = computed(() => `${inputId.value}Listbox`);

const normalizedOptions = computed(() => Array.isArray(props.options)
    ? props.options
    : Object.entries(props.options).map(([value, label]) => ({ value, label })));

const selectedOption = computed(() => normalizedOptions.value.find((option) => String(option.value) === String(props.modelValue)));

const filteredOptions = computed(() => {
    const term = query.value.trim().toLowerCase();

    if (!term) {
        return normalizedOptions.value;
    }

    return normalizedOptions.value.filter((option) => String(option.label).toLowerCase().includes(term));
});

watch(open, async (value) => {
    if (value) {
        query.value = '';
        await nextTick();
        searchInput.value?.focus();
    }
});

function toggle() {
    open.value = !open.value;
}

function close() {
    open.value = false;
}

function selectOption(option) {
    emit('update:modelValue', option.value);
    close();
}

function clearValue() {
    emit('update:modelValue', '');
    close();
}

function handleOutsideClick(event) {
    if (root.value && !root.value.contains(event.target)) {
        close();
    }
}

document.addEventListener('click', handleOutsideClick);

onBeforeUnmount(() => {
    document.removeEventListener('click', handleOutsideClick);
});
</script>

<template>
    <div ref="root" :class="wrapperClass">
        <label v-if="label" :for="inputId" class="form-label">
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>

        <div class="searchable-select">
            <button
                :id="inputId"
                type="button"
                class="form-select searchable-select-toggle"
                :class="{ 'is-invalid': error }"
                :aria-expanded="open ? 'true' : 'false'"
                :aria-controls="listboxId"
                :aria-describedby="describedBy"
                :aria-invalid="error ? 'true' : null"
                v-bind="$attrs"
                @click.stop="toggle"
            >
                <span :class="{ 'text-muted': !selectedOption }">
                    {{ selectedOption?.label ?? placeholder }}
                </span>
            </button>

            <input
                type="hidden"
                :name="name"
                :value="modelValue"
                :required="required"
            />

            <div v-if="open" class="searchable-select-menu" @click.stop>
                <input
                    ref="searchInput"
                    v-model="query"
                    type="search"
                    class="form-control form-control-sm"
                    :placeholder="searchPlaceholder"
                    @keydown.esc.prevent="close"
                />

                <div :id="listboxId" class="searchable-select-options" role="listbox">
                    <button
                        v-if="clearable"
                        type="button"
                        class="searchable-select-option text-muted"
                        @click="clearValue"
                    >
                        {{ placeholder }}
                    </button>
                    <button
                        v-for="option in filteredOptions"
                        :key="option.value"
                        type="button"
                        class="searchable-select-option"
                        :class="{ active: String(option.value) === String(modelValue) }"
                        role="option"
                        :aria-selected="String(option.value) === String(modelValue)"
                        @click="selectOption(option)"
                    >
                        {{ option.label }}
                    </button>
                    <div v-if="filteredOptions.length === 0" class="searchable-select-empty">
                        {{ emptyText }}
                    </div>
                </div>
            </div>
        </div>

        <InputError :id="errorId" :message="error" />
        <div v-if="help" :id="helpId" class="form-text">{{ help }}</div>
    </div>
</template>
