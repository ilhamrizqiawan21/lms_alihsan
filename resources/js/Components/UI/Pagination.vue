<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: { type: Array, default: () => [] },
});
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
                    v-html="link.label"
                />
                <span v-else class="page-link" v-html="link.label"></span>
            </li>
        </ul>
    </nav>
</template>
