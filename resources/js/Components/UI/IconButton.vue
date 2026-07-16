<script setup>
import Button from './Button.vue';

defineProps({
    icon: { type: String, required: true },
    label: { type: String, required: true },
    color: { type: String, default: 'outline-secondary' },
    href: { type: String, default: '' },
    method: { type: String, default: 'get' },
    type: { type: String, default: 'button' },
});
</script>

<template>
    <Button
        class="btn-icon"
        :href="href"
        :method="method"
        :type="type"
        :color="color"
        size=""
        :icon="icon"
        :aria-label="label"
        :title="label"
    />
</template>
