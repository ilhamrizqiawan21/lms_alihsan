<script setup>
defineProps({
    title: { type: String, default: '' },
    icon: { type: String, default: '' },
    headerClass: { type: String, default: '' },
    bodyClass: { type: String, default: '' },
    footerClass: { type: String, default: '' },
});
</script>

<template>
    <div class="card app-card">
        <div v-if="title || icon || $slots.actions" class="card-header" :class="headerClass">
            <div class="app-card-title">
                <i v-if="icon" class="bi" :class="icon" aria-hidden="true"></i>
                <span v-if="title">{{ title }}</span>
            </div>
            <div v-if="$slots.actions" class="app-card-actions">
                <slot name="actions" />
            </div>
        </div>

        <div class="card-body" :class="bodyClass">
            <slot />
        </div>

        <div v-if="$slots.footer" class="card-footer" :class="footerClass">
            <slot name="footer" />
        </div>
    </div>
</template>
