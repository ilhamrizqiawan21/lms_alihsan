<script setup>
import ActionQueue from './ActionQueue.vue';

defineProps({
    title: { type: String, default: 'Agenda' },
    items: { type: Array, default: () => [] },
    emptyTitle: { type: String, default: 'Agenda kosong' },
});
</script>

<template>
    <ActionQueue
        :title="title"
        icon="bi-calendar-week"
        :items="items"
        :empty-title="emptyTitle"
        empty-message="Tidak ada jadwal atau deadline yang perlu ditindaklanjuti."
    />
</template>
