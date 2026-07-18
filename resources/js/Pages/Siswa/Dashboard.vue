<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../../Components/AppShell/PageHeader.vue';
import AppShell from '../../Layouts/AppShell.vue';
import { Badge, Card, EmptyState, StatCard, TableWrapper } from '../../Components/UI';

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
                    <TableWrapper v-if="tugasTerbaru.length">
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
                    <EmptyState v-else title="Belum ada tugas" icon="bi-journal" />
                </Card>
            </div>

            <div class="col-md-6 mb-4">
                <Card title="Notifikasi" icon="bi-bell-fill" body-class="p-0">
                    <template v-if="notifikasi.length" #actions>
                        <a :href="links.notifikasi" class="text-decoration-none small" style="color: var(--primary-600);">Lihat Semua</a>
                    </template>
                    <TableWrapper v-if="notifikasi.length">
                        <table class="table table-hover mb-0">
                            <tbody>
                                <tr
                                    v-for="item in notifikasi"
                                    :key="item.id"
                                    :style="!item.is_read ? 'background: #fef2f2;' : ''"
                                >
                                    <td style="width: 40px; text-align: center;">
                                        <i class="bi" :class="iconFor(item.tipe).icon" :style="{ color: iconFor(item.tipe).color }" aria-hidden="true"></i>
                                    </td>
                                    <td>
                                        <strong style="font-size: 0.82rem;">{{ item.judul }}</strong>
                                        <Badge v-if="!item.is_read" color="danger" style="font-size: 0.55rem;">Baru</Badge>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ item.pesan }}</div>
                                    </td>
                                    <td class="text-end">
                                        <small class="text-muted">{{ item.created_at }}</small>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <EmptyState v-else title="Belum ada notifikasi" icon="bi-bell" />
                </Card>

                <Card title="Pengumuman" icon="bi-megaphone-fill" body-class="p-0">
                    <template v-if="pengumuman.length" #actions>
                        <Link :href="links.pengumuman" class="text-decoration-none small" style="color: var(--primary-600);">Lihat Semua</Link>
                    </template>
                    <TableWrapper v-if="pengumuman.length">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in pengumuman" :key="item.id">
                                    <td>
                                        <Link :href="item.show_url" class="text-decoration-none fw-semibold">
                                            {{ item.judul }}
                                        </Link>
                                    </td>
                                    <td>{{ item.created_at }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <EmptyState v-else title="Tidak ada pengumuman" icon="bi-megaphone" />
                </Card>
            </div>
        </div>
    </AppShell>
</template>
