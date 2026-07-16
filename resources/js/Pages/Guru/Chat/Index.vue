<script setup>
import { Head } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import RoomGrid from '../../../Components/Chat/RoomGrid.vue';
import AppShell from '../../../Layouts/AppShell.vue';

defineProps({
    rooms: { type: Array, default: () => [] },
    emptyMessage: { type: String, default: 'Anda belum memiliki penugasan.' },
});
</script>

<template>
    <Head title="Chat Kelas" />

    <AppShell title="Chat Kelas">
        <PageHeader title="Chat Kelas" icon="bi-chat-dots-fill" />
        <RoomGrid :rooms="rooms" :empty-message="emptyMessage" />
    </AppShell>
</template>
