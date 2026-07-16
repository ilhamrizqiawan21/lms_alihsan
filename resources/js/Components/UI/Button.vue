<script setup>
import { Link } from '@inertiajs/vue3';

defineOptions({
    inheritAttrs: false,
});

defineProps({
    type: { type: String, default: 'button' },
    color: { type: String, default: 'primary' },
    size: { type: String, default: 'sm' },
    icon: { type: String, default: '' },
    href: { type: String, default: '' },
    method: { type: String, default: 'get' },
    as: { type: String, default: '' },
});
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        :method="method"
        :as="as || (method.toLowerCase() === 'get' ? 'a' : 'button')"
        class="btn"
        :class="[`btn-${color}`, size ? `btn-${size}` : '']"
        v-bind="$attrs"
    >
        <i v-if="icon" class="bi" :class="[icon, $slots.default ? 'me-1' : '']" aria-hidden="true"></i>
        <slot />
    </Link>
    <button
        v-else
        :type="type"
        class="btn"
        :class="[`btn-${color}`, size ? `btn-${size}` : '']"
        v-bind="$attrs"
    >
        <i v-if="icon" class="bi" :class="[icon, $slots.default ? 'me-1' : '']" aria-hidden="true"></i>
        <slot />
    </button>
</template>
