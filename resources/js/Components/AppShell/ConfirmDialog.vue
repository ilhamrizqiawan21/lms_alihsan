<script setup>
import { onMounted, onUnmounted, ref } from 'vue';

const visible = ref(false);
const options = ref({});
let resolver = null;
let previousFocus = null;

function close(result) {
    visible.value = false;

    if (resolver) {
        resolver(result);
        resolver = null;
    }

    if (previousFocus && typeof previousFocus.focus === 'function') {
        previousFocus.focus();
    }
}

function confirm(message, config = {}) {
    previousFocus = document.activeElement;
    options.value = {
        message,
        title: config.title || 'Konfirmasi',
        confirmText: config.confirmText || 'Ya, lanjutkan',
        cancelText: config.cancelText || 'Batal',
        danger: config.danger === true,
    };
    visible.value = true;

    return new Promise((resolve) => {
        resolver = resolve;
    });
}

function handleKeydown(event) {
    if (!visible.value) {
        return;
    }

    if (event.key === 'Escape') {
        close(false);
    }
}

onMounted(() => {
    window.confirmAction = (message, callback, config = {}) => {
        confirm(message, config).then(callback);
    };
    window.confirmDialog = confirm;
    document.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <div v-if="visible" class="confirm-overlay" @click.self="close(false)">
        <div class="confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="confirmTitle" aria-describedby="confirmMessage">
            <div class="confirm-icon" :class="options.danger ? 'danger' : 'warning'">
                <i class="bi" :class="options.danger ? 'bi-exclamation-triangle-fill' : 'bi-question-circle-fill'" aria-hidden="true"></i>
            </div>
            <h5 id="confirmTitle" class="confirm-title">{{ options.title }}</h5>
            <p id="confirmMessage" class="confirm-message">{{ options.message }}</p>
            <div class="confirm-actions">
                <button type="button" class="btn btn-outline-secondary" @click="close(false)">{{ options.cancelText }}</button>
                <button type="button" class="btn" :class="options.danger ? 'btn-danger' : 'btn-success'" autofocus @click="close(true)">
                    {{ options.confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>
