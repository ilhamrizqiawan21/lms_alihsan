<script setup>
import { Head } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Card, EmptyState } from '../../../Components/UI';

defineProps({
    rekap: { type: Array, default: () => [] },
    exportUrls: { type: Object, default: () => ({}) },
});

function progressColor(value) {
    if (value >= 90) return 'bg-success';
    if (value >= 75) return 'bg-warning';
    return 'bg-danger';
}
</script>

<template>
    <Head title="Rekap Absensi" />

    <AppShell title="Rekap Absensi">
        <PageHeader
            title="Rekap Absensi Per Kelas"
            icon="bi-file-earmark-bar-graph-fill"
        >
            <template v-if="rekap.length" #actions>
                <a :href="exportUrls.excel" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i> Excel
                </a>
                <a :href="exportUrls.pdf" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i> PDF
                </a>
            </template>
        </PageHeader>

        <div v-if="rekap.length" class="row">
            <div
                v-for="item in rekap"
                :key="item.kelas_id"
                class="col-md-6 col-lg-4 mb-3"
            >
                <Card>
                    <template #actions>
                        <Badge color="secondary">{{ item.jumlah_siswa }} siswa</Badge>
                    </template>

                    <template #default>
                        <h5 class="rekap-title">{{ item.kelas }}</h5>

                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-check-circle-fill text-success me-1" aria-hidden="true"></i> Total Hadir</span>
                            <strong>{{ item.total_hadir }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="bi bi-bar-chart-fill text-primary me-1" aria-hidden="true"></i> Total Absensi</span>
                            <strong>{{ item.total_absensi }}</strong>
                        </div>
                        <div class="progress rekap-progress">
                            <div
                                class="progress-bar"
                                :class="progressColor(item.persen)"
                                :style="{ width: `${item.persen}%` }"
                            >
                                {{ item.persen }}%
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">Persentase kehadiran</small>
                    </template>
                </Card>
            </div>
        </div>

        <EmptyState v-else title="Tidak ada data rekap absensi." icon="bi-file-earmark-bar-graph" />
    </AppShell>
</template>

<style scoped>
.rekap-title {
    margin-bottom: 1rem;
    font-size: 1rem;
    font-weight: 700;
}

.rekap-progress {
    height: 20px;
}
</style>
