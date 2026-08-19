<script setup>
import { Link } from '@inertiajs/vue3';
import Badge from './Badge.vue';

defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: '' },
    meta: { type: String, default: '' },
    href: { type: String, default: '' },
    icon: { type: String, default: 'bi-book' },
    accent: { type: String, default: '#2563eb' },
    badges: { type: Array, default: () => [] },
    stats: { type: Array, default: () => [] },
});
</script>

<template>
    <component
        :is="href ? Link : 'article'"
        :href="href"
        class="course-card"
        :class="{ 'course-card-link': href }"
        :style="{ '--course-accent': accent }"
    >
        <span class="course-card-mark">
            <i class="bi" :class="icon" aria-hidden="true"></i>
        </span>
        <span class="course-card-body">
            <span class="course-card-kicker">{{ meta }}</span>
            <strong>{{ title }}</strong>
            <small v-if="subtitle">{{ subtitle }}</small>
            <span v-if="badges.length" class="course-card-badges">
                <Badge
                    v-for="badge in badges"
                    :key="badge.label || badge"
                    :color="badge.color || 'secondary'"
                >
                    {{ badge.label || badge }}
                </Badge>
            </span>
            <span v-if="stats.length" class="course-card-stats">
                <span v-for="stat in stats" :key="stat.label">
                    <b>{{ stat.value }}</b> {{ stat.label }}
                </span>
            </span>
        </span>
    </component>
</template>
