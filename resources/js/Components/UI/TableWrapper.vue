<script setup>
defineProps({
    responsive: { type: Boolean, default: true },
});
</script>

<template>
    <div :class="[responsive ? 'table-responsive' : '', 'app-table-wrapper']">
        <slot />
    </div>
</template>
