<script setup>
defineProps({
    responsive: { type: Boolean, default: true },
    minWidth: { type: [String, Number], default: 720 },
});
</script>

<template>
    <div
        :class="[responsive ? 'table-responsive' : '', 'app-table-wrapper']"
        :style="responsive ? { '--app-table-min-width': typeof minWidth === 'number' ? `${minWidth}px` : minWidth } : undefined"
    >
        <slot />
    </div>
</template>
