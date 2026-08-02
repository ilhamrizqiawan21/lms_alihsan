<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../../Components/AppShell/PageHeader.vue';
import AppShell from '../../Layouts/AppShell.vue';
import { Badge, Card, EmptyState, InfoListItem, StatCard, TableWrapper } from '../../Components/UI';

defineProps({
    stats: { type: Object, required: true },
    tugasTerbaru: { type: Array, default: () => [] },
    notifikasi: { type: Array, default: () => [] },
    pengumuman: { type: Array, default: () => [] },
    links: { type: Object, default: () => ({}) },
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
</script>

<template>
    <Head title="Dashboard Siswa" />

    <AppShell title="Dashboard Siswa">
        <PageHeader
            title="Dashboard Siswa"
            icon="bi-speedometer2"
            subtitle="Ringkasan tugas, materi, dan kabar kelas terbaru."
        />

        <div class="stats-grid">
            <StatCard label="Total Tugas" :value="stats.total_tugas ?? 0" icon="bi-journal-fill" />
            <StatCard label="Tugas Selesai" :value="stats.tugas_selesai ?? 0" icon="bi-check-circle-fill" />
            <StatCard label="Belum Dikerjakan" :value="stats.tugas_belum ?? 0" icon="bi-exclamation-circle-fill" />
            <StatCard label="Total Materi" :value="stats.total_materi ?? 0" icon="bi-file-earmark-text-fill" />
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <Card title="Tugas Terbaru" icon="bi-journal-fill" body-class="p-0">
                    <TableWrapper v-if="tugasTerbaru.length" class="d-none d-md-block">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tugas</th>
                                    <th>Mapel</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in tugasTerbaru" :key="item.id">
                                    <td>{{ item.judul }}</td>
                                    <td>{{ item.mata_pelajaran }}</td>
                                    <td>{{ item.batas_waktu }}</td>
                                    <td>
                                        <Badge :color="item.selesai ? 'success' : 'warning text-dark'">
                                            {{ item.selesai ? 'Selesai' : 'Belum' }}
                                        </Badge>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <div v-if="tugasTerbaru.length" class="app-mobile-list d-md-none">
                        <div v-for="item in tugasTerbaru" :key="item.id" class="app-mobile-list-item">
                            <div class="app-mobile-list-row">
                                <span class="app-mobile-list-title">{{ item.judul }}</span>
                                <Badge :color="item.selesai ? 'success' : 'warning text-dark'">
                                    {{ item.selesai ? 'Selesai' : 'Belum' }}
                                </Badge>
                            </div>
                            <span class="app-mobile-list-meta">{{ item.mata_pelajaran }}</span>
                            <span class="app-mobile-list-meta">Deadline {{ item.batas_waktu }}</span>
                        </div>
                    </div>
                    <EmptyState v-else title="Belum ada tugas" icon="bi-journal" />
                </Card>
            </div>

            <div class="col-md-6 mb-4">
                <Card title="Notifikasi" icon="bi-bell-fill" body-class="p-0">
                    <template v-if="notifikasi.length" #actions>
                        <a :href="links.notifikasi" class="app-card-action-link">
                            Lihat Semua
                        </a>
                    </template>
                    <div v-if="notifikasi.length" class="app-list">
                        <InfoListItem
                            v-for="item in notifikasi"
                            :key="item.id"
                            :title="item.judul"
                            :message="item.pesan"
                            :meta="item.created_at"
                            :icon="iconFor(item.tipe).icon"
                            :accent="iconFor(item.tipe).color"
                            :unread="!item.is_read"
                            compact
                        />
                    </div>
                    <EmptyState v-else title="Belum ada notifikasi" icon="bi-bell" />
                </Card>

                <Card title="Pengumuman" icon="bi-megaphone-fill" body-class="p-0">
                    <template v-if="pengumuman.length" #actions>
                        <Link :href="links.pengumuman" class="app-card-action-link">
                            Lihat Semua
                        </Link>
                    </template>
                    <div v-if="pengumuman.length" class="app-list">
                        <InfoListItem
                            v-for="item in pengumuman"
                            :key="item.id"
                            :title="item.judul"
                            :meta="item.created_at"
                            :href="item.show_url"
                            icon="bi-megaphone-fill"
                            accent="var(--primary-500)"
                            compact
                        />
                    </div>
                    <EmptyState v-else title="Tidak ada pengumuman" icon="bi-megaphone" />
                </Card>
            </div>
        </div>
    </AppShell>
</template>
