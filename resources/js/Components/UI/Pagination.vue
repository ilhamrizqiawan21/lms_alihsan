<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: { type: Array, default: () => [] },
});

function cleanLabel(label) {
    return String(label)
        .replace('&laquo;', '‹')
        .replace('&raquo;', '›')
        .replace(/<[^>]*>/g, '');
}
</script>

<template>
    <nav v-if="links.length > 3" aria-label="Navigasi halaman">
        <ul class="pagination mb-0">
            <li
                v-for="(link, index) in links"
                :key="`${link.label}-${index}`"
                class="page-item"
                :class="{ active: link.active, disabled: !link.url }"
            >
                <Link
                    v-if="link.url"
                    class="page-link"
                    :href="link.url"
                    preserve-scroll
                >
                    {{ cleanLabel(link.label) }}
                </Link>
                <span v-else class="page-link">{{ cleanLabel(link.label) }}</span>
            </li>
        </ul>
    </nav>
</template>
