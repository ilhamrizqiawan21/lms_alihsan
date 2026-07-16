<script setup>
import { Head } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Button, Card, EmptyState } from '../../../Components/UI';

defineProps({
    waliKelas: { type: Array, default: () => [] },
});
</script>

<template>
    <Head title="Wali Kelas" />

    <AppShell title="Wali Kelas">
        <PageHeader title="Wali Kelas" icon="bi-person-badge-fill" />

        <Card v-if="!waliKelas.length">
            <EmptyState
                title="Belum ada penugasan wali kelas"
                message="Anda belum ditugaskan sebagai wali kelas pada tahun ajaran aktif."
                icon="bi-person-badge"
            />
        </Card>

        <div v-else class="row gy-4">
            <div v-for="item in waliKelas" :key="item.id" class="col-md-6 col-xl-4">
                <Card :title="item.kelas" icon="bi-building">
                    <div class="d-flex flex-column gap-2">
                        <div class="text-muted small">Tahun Ajaran {{ item.tahun_ajaran }}</div>
                        <div class="d-flex flex-wrap gap-2">
                            <Badge color="primary">{{ item.absensi_count }} absensi</Badge>
                            <Badge color="info text-dark">{{ item.pertemuan_count }} pertemuan</Badge>
                            <Badge color="warning text-dark">{{ item.penanganan_aktif_count }} penanganan aktif</Badge>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <Button :href="item.absensi_url" color="outline-primary" icon="bi-clipboard-check">Absensi Harian</Button>
                            <Button :href="item.pertemuan_url" color="outline-secondary" icon="bi-calendar-event">Pertemuan</Button>
                            <Button :href="item.penanganan_url" color="outline-danger" icon="bi-heart-pulse">Penanganan Siswa</Button>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AppShell>
</template>
