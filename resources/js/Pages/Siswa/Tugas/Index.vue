<script setup>
import { Head } from '@inertiajs/vue3';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Button, Card, DashboardHero, EmptyState, QuickActionBar, TableWrapper } from '../../../Components/UI';

const props = defineProps({
    tugas: { type: Array, default: () => [] },
});

const openTasks = () => props.tugas.filter((item) => !item.status).length;

function statusColor(status) {
    return {
        sudah: 'success',
        dinilai: 'primary',
        terlambat: 'danger',
    }[status] ?? 'warning text-dark';
}

function statusLabel(status) {
    return status ? status.replace(/\b\w/g, (char) => char.toUpperCase()) : 'Belum Dikumpul';
}
</script>

<template>
    <Head title="Tugas Saya" />

    <AppShell title="Tugas Saya">
        <DashboardHero
            eyebrow="Pembelajaran Saya"
            title="Tugas Saya"
            :subtitle="`${openTasks()} tugas belum dikumpulkan. Buka detail tugas untuk mengirim jawaban.`"
            icon="bi-journal-check"
            tone="student"
        >
            <template #actions>
                <QuickActionBar :actions="[{ label: 'Materi', href: '/siswa/materi', icon: 'bi-file-earmark-text', color: 'light' }]" />
            </template>
        </DashboardHero>

        <Card title="Daftar Tugas" icon="bi-journal-fill" body-class="p-0">
            <div v-if="tugas.length" class="p-3 border-bottom bg-light-subtle">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <span class="text-muted small">Tugas yang sudah dikumpulkan dan yang masih menunggu.</span>
                    <span class="badge bg-soft-primary">{{ openTasks() }} belum dikumpulkan</span>
                </div>
            </div>
            <TableWrapper v-if="tugas.length">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Mapel</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in tugas" :key="item.id">
                            <td><a :href="item.show_url" class="text-decoration-none fw-bold">{{ item.judul }}</a></td>
                            <td>
                                <a v-if="item.workspace_url" :href="item.workspace_url" class="text-decoration-none">{{ item.mata_pelajaran }}</a>
                                <span v-else>{{ item.mata_pelajaran }}</span>
                            </td>
                            <td>{{ item.batas_waktu }}</td>
                            <td><Badge :color="statusColor(item.status)">{{ statusLabel(item.status) }}</Badge></td>
                            <td>{{ item.nilai }}</td>
                            <td>
                                <Button :href="item.show_url" color="info" icon="bi-eye">Detail</Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </TableWrapper>
            <EmptyState v-else title="Belum ada tugas" icon="bi-journal" />
        </Card>
    </AppShell>
</template>
