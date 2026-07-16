<script setup>
import { Link } from '@inertiajs/vue3';
import { Card, EmptyState } from '../UI';

defineProps({
    rooms: { type: Array, default: () => [] },
    emptyMessage: { type: String, default: 'Belum ada data.' },
});
</script>

<template>
    <EmptyState
        v-if="rooms.length === 0"
        :title="emptyMessage"
        icon="bi-chat-dots"
    />

    <Card v-else title="Pilih Kelas" icon="bi-book">
        <div class="row">
            <div
                v-for="room in rooms"
                :key="room.id"
                class="col-md-4 mb-3"
            >
                <Link :href="room.url" class="chat-room-tile text-decoration-none">
                    <span class="chat-room-icon">
                        <i class="bi bi-chat-dots-fill" aria-hidden="true"></i>
                    </span>
                    <strong>{{ room.title }}</strong>
                    <span class="text-muted">{{ room.subtitle }}</span>
                </Link>
            </div>
        </div>
    </Card>
</template>

<style scoped>
.chat-room-tile {
    display: flex;
    min-height: 150px;
    height: 100%;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    color: var(--gray-800);
    padding: 1.25rem;
    text-align: center;
    transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

.chat-room-tile:hover {
    border-color: var(--primary-300);
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    color: var(--gray-900);
    transform: translateY(-1px);
}

.chat-room-icon {
    color: var(--primary-500);
    font-size: 2rem;
}

.chat-room-tile .text-muted {
    font-size: 0.8rem;
}
</style>
