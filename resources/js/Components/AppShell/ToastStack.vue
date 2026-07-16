<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const toasts = ref([]);
const flash = computed(() => page.props.flash ?? {});
let nextId = 1;

function addToast(message, type = 'success') {
    if (!message) {
        return;
    }

    const id = nextId++;
    toasts.value.push({ id, message, type, removing: false });

    setTimeout(() => removeToast(id), 4000);
}

function removeToast(id) {
    const toast = toasts.value.find((item) => item.id === id);

    if (!toast) {
        return;
    }

    toast.removing = true;
    setTimeout(() => {
        toasts.value = toasts.value.filter((item) => item.id !== id);
    }, 400);
}

function consumeFlash(value) {
    addToast(value.success, 'success');
    addToast(value.error, 'error');
    addToast(value.warning, 'warning');
}

onMounted(() => {
    window.showToast = addToast;
    consumeFlash(flash.value);
});

watch(flash, consumeFlash, { deep: true });
</script>

<template>
    <div class="toast-container" aria-live="polite" aria-atomic="true">
        <div
            v-for="toast in toasts"
            :key="toast.id"
            class="toast-item"
            :class="[toast.type, { removing: toast.removing }]"
            :role="toast.type === 'error' ? 'alert' : 'status'"
        >
            <i
                class="bi"
                :class="{
                    'bi-check-circle-fill': toast.type === 'success',
                    'bi-x-circle-fill': toast.type === 'error',
                    'bi-exclamation-triangle-fill': toast.type === 'warning',
                    'bi-info-circle-fill': toast.type === 'info',
                }"
                aria-hidden="true"
            ></i>
            <span>{{ toast.message }}</span>
        </div>
    </div>
</template>
