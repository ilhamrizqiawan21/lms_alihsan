<script setup>
import { Head } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Button, Card, EmptyState, TableWrapper } from '../../../Components/UI';

defineProps({
    tugas: { type: Array, default: () => [] },
});

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
        <PageHeader title="Tugas Saya" icon="bi-journal-fill" />

        <Card title="Daftar Tugas" icon="bi-journal-fill" body-class="p-0">
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
                            <td>{{ item.mata_pelajaran }}</td>
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
