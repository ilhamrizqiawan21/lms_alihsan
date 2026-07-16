<script setup>
defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    icon: { type: String, default: 'bi-bar-chart-fill' },
});
</script>

<template>
    <div class="stat-card">
        <div class="stat-icon">
            <i class="bi" :class="icon" aria-hidden="true"></i>
        </div>
        <div>
            <div class="stat-number">{{ value }}</div>
            <div class="stat-label">{{ label }}</div>
        </div>
    </div>
</template>
