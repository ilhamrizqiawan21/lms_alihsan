<script setup>
import { computed, nextTick, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    open: { type: Boolean, default: false },
    items: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:open']);
const query = ref('');
const input = ref(null);
const activeIndex = ref(0);

const results = computed(() => {
    const term = query.value.trim().toLowerCase();
    const commands = props.items.filter((item) => item.type === 'item');
    return term ? commands.filter((item) => item.label.toLowerCase().includes(term)) : commands;
});

watch(() => props.open, async (isOpen) => {
    if (!isOpen) return;
    query.value = '';
    activeIndex.value = 0;
    await nextTick();
    input.value?.focus();
});

watch(results, () => {
    activeIndex.value = 0;
});

function close() {
    emit('update:open', false);
}

function visit(item) {
    close();
    router.visit(item.href);
}

function moveActive(direction) {
    if (!results.value.length) return;
    activeIndex.value = (activeIndex.value + direction + results.value.length) % results.value.length;
}

function visitActive() {
    const item = results.value[activeIndex.value];
    if (item) visit(item);
}
</script>

<template>
    <div v-if="open" class="command-palette-backdrop" @click.self="close">
        <section class="command-palette" role="dialog" aria-modal="true" aria-label="Akses cepat">
            <div class="command-palette-input">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input
                    ref="input"
                    v-model="query"
                    type="search"
                    placeholder="Cari menu..."
                    @keydown.esc="close"
                    @keydown.down.prevent="moveActive(1)"
                    @keydown.up.prevent="moveActive(-1)"
                    @keydown.enter.prevent="visitActive"
                >
                <kbd>Esc</kbd>
            </div>
            <div class="command-palette-results">
                <button v-for="(item, index) in results" :key="item.href" type="button" class="command-palette-item" :class="{ 'is-active': index === activeIndex }" @mouseenter="activeIndex = index" @click="visit(item)">
                    <i class="bi" :class="item.icon" aria-hidden="true"></i>
                    <span>{{ item.label }}</span>
                    <i class="bi bi-arrow-return-left command-palette-enter" aria-hidden="true"></i>
                </button>
                <p v-if="!results.length" class="command-palette-empty">Menu tidak ditemukan.</p>
            </div>
        </section>
    </div>
</template>
