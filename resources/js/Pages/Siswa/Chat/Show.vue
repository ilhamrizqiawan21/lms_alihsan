<script setup>
import { Head } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import ChatRoom from '../../../Components/Chat/ChatRoom.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Button } from '../../../Components/UI';

defineProps({
    room: { type: Object, required: true },
    messages: { type: Array, default: () => [] },
    sendUrl: { type: String, required: true },
    backUrl: { type: String, required: true },
    emptyMessage: { type: String, default: 'Belum ada pesan.' },
});
</script>

<template>
    <Head :title="`Chat: ${room.title}`" />

    <AppShell :title="`Chat: ${room.title}`">
        <PageHeader
            :title="room.title"
            :subtitle="room.subtitle"
            icon="bi-chat-dots-fill"
        >
            <template #actions>
                <Button :href="backUrl" color="outline-secondary" icon="bi-arrow-left">Kembali</Button>
            </template>
        </PageHeader>

        <ChatRoom
            :room="room"
            :messages="messages"
            :send-url="sendUrl"
            :empty-message="emptyMessage"
        />
    </AppShell>
</template>
