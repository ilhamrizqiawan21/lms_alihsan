<script setup>
defineProps({
    color: { type: String, default: 'primary' },
    icon: { type: String, default: '' },
    label: { type: String, default: '' },
});
</script>

<template>
    <span class="badge app-badge" :class="`bg-${color}`" :aria-label="label || null">
        <i v-if="icon" class="bi me-1" :class="icon" aria-hidden="true"></i>
        <slot />
    </span>
</template>
