<script setup>
defineProps({
    title: { type: String, default: 'Data belum tersedia' },
    message: { type: String, default: '' },
    icon: { type: String, default: 'bi-inbox' },
});
</script>

<template>
    <div class="empty-state">
        <div class="empty-state-icon">
            <i class="bi" :class="icon" aria-hidden="true"></i>
        </div>
        <div class="empty-state-title">{{ title }}</div>
        <div v-if="message" class="empty-state-message">{{ message }}</div>
        <div v-if="$slots.default" class="empty-state-action">
            <slot />
        </div>
    </div>
</template>
