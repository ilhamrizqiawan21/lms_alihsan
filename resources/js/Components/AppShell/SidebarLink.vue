<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    entry: { type: Object, required: true },
    active: { type: Boolean, default: false },
});
</script>

<template>
    <Link v-if="entry.inertia" :href="entry.href" class="nav-link" :class="{ active }">
        <i class="bi" :class="entry.icon" aria-hidden="true"></i>
        <span>{{ entry.label }}</span>
    </Link>
    <a v-else :href="entry.href" class="nav-link" :class="{ active }">
        <i class="bi" :class="entry.icon" aria-hidden="true"></i>
        <span>{{ entry.label }}</span>
    </a>
</template>
