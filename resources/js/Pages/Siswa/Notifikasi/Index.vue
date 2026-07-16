<script setup>
import { Head, router } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Button, Card, EmptyState, IconButton, Pagination, TableWrapper } from '../../../Components/UI';

const props = defineProps({
    notifikasi: { type: Object, required: true },
    unreadCount: { type: Number, default: 0 },
    markAllReadUrl: { type: String, required: true },
});

const iconMap = {
    tugas_baru: { icon: 'bi-journal-plus', color: '#3b82f6' },
    nilai_baru: { icon: 'bi-bar-chart-fill', color: '#22c55e' },
    chat_baru: { icon: 'bi-chat-dots-fill', color: '#8b5cf6' },
    komentar_tugas: { icon: 'bi-chat-square-text-fill', color: '#f59e0b' },
    kumpul_tugas: { icon: 'bi-check-circle-fill', color: '#06b6d4' },
    absensi: { icon: 'bi-clipboard-check-fill', color: '#ef4444' },
};

function iconFor(type) {
    return iconMap[type] ?? { icon: 'bi-bell-fill', color: '#6b7280' };
}

function markRead(item) {
    router.post(item.mark_read_url, {}, {
        preserveScroll: true,
    });
}

function markAllRead() {
    router.post(props.markAllReadUrl, {}, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head title="Notifikasi" />

    <AppShell title="Notifikasi">
        <PageHeader
            title="Notifikasi"
            subtitle="Daftar notifikasi Anda"
            icon="bi-bell-fill"
        >
            <template v-if="unreadCount > 0" #actions>
                <Button
                    type="button"
                    color="outline-primary"
                    icon="bi-check-all"
                    @click="markAllRead"
                >
                    Tandai Semua Sudah Dibaca
                </Button>
            </template>
        </PageHeader>

        <Card body-class="p-0">
            <TableWrapper v-if="notifikasi.data.length">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>Judul</th>
                            <th>Pesan</th>
                            <th>Waktu</th>
                            <th style="width: 80px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="item in notifikasi.data"
                            :key="item.id"
                            :class="{ 'table-active': !item.is_read }"
                            :style="!item.is_read ? 'font-weight: 600;' : ''"
                        >
                            <td class="text-center">
                                <span
                                    class="notification-icon"
                                    :style="{
                                        background: `${iconFor(item.tipe).color}15`,
                                        color: iconFor(item.tipe).color,
                                    }"
                                >
                                    <i class="bi" :class="iconFor(item.tipe).icon" aria-hidden="true"></i>
                                </span>
                            </td>
                            <td>
                                <div>{{ item.judul }}</div>
                                <Badge v-if="!item.is_read" color="danger" style="font-size:0.6rem;">Baru</Badge>
                            </td>
                            <td style="max-width: 300px;">
                                <span class="text-muted" style="font-size: 0.82rem;">{{ item.pesan_ringkas }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ item.created_at }}</small>
                            </td>
                            <td>
                                <IconButton
                                    :icon="item.link ? 'bi-arrow-right' : 'bi-check2'"
                                    :label="item.link ? `Lihat notifikasi ${item.judul}` : `Tandai dibaca ${item.judul}`"
                                    color="outline-primary"
                                    @click="markRead(item)"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </TableWrapper>

            <EmptyState
                v-else
                title="Belum ada notifikasi."
                icon="bi-bell-slash"
            />

            <template v-if="notifikasi.links?.length" #footer>
                <Pagination :links="notifikasi.links" />
            </template>
        </Card>
    </AppShell>
</template>

<style scoped>
.notification-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    font-size: 1rem;
}
</style>
