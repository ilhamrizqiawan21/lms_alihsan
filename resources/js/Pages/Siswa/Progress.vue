<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import PageHeader from '../../Components/AppShell/PageHeader.vue';
import AppShell from '../../Layouts/AppShell.vue';
import { Badge, Card, EmptyState, StatCard, TableWrapper } from '../../Components/UI';

const props = defineProps({
    header: { type: Object, required: true },
    stats: { type: Object, required: true },
    subjectScores: { type: Array, default: () => [] },
});

const chartCanvas = ref(null);
let chartInstance = null;

const subtitle = computed(() => [
    props.header.nama,
    props.header.kelas,
    props.header.tahun_ajaran ? `TA ${props.header.tahun_ajaran}` : null,
    `Semester ${props.header.semester_label}`,
].filter(Boolean).join(' - '));

function scoreColor(value) {
    return Number(value ?? 0) >= 75 ? '#16a34a' : '#ef4444';
}

function scoreBadge(value) {
    if (value === null || value === undefined) return { color: 'secondary', label: '-' };
    if (value >= 92) return { color: 'success', label: 'A' };
    if (value >= 83) return { color: 'info', label: 'B' };
    if (value >= 75) return { color: 'warning text-dark', label: 'C' };
    return { color: 'danger', label: 'D' };
}

async function renderChart() {
    if (!chartCanvas.value || !props.subjectScores.length) {
        return;
    }

    const { Chart, registerables } = await import('chart.js');
    Chart.register(...registerables);

    chartInstance?.destroy();
    chartInstance = new Chart(chartCanvas.value, {
        type: 'bar',
        data: {
            labels: props.subjectScores.map((item) => item.nama_mapel),
            datasets: [{
                label: 'Rata-rata',
                data: props.subjectScores.map((item) => item.rata ?? 0),
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderColor: '#16a34a',
                borderWidth: 1,
                borderRadius: 8,
            }],
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true, max: 100 } },
            plugins: { legend: { display: false } },
        },
    });
}

onMounted(() => nextTick(renderChart));
watch(() => props.subjectScores, () => nextTick(renderChart), { deep: true });
onBeforeUnmount(() => chartInstance?.destroy());
</script>

<template>
    <Head title="Progress Saya" />

    <AppShell title="Progress Saya">
        <PageHeader
            title="Progress Belajar"
            icon="bi-graph-up-arrow"
            :subtitle="subtitle"
        />

        <div class="stats-grid">
            <StatCard label="Rata-rata Nilai (GPA)" :value="stats.gpa" icon="bi-star-fill" />

            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-calendar-check-fill" aria-hidden="true"></i></div>
                <div>
                    <div class="stat-label">Kehadiran {{ stats.bulan_label }}</div>
                    <div class="stat-number">{{ stats.persen_hadir }}%</div>
                    <div class="progress mt-1">
                        <div class="progress-bar" :style="{ width: `${stats.persen_hadir}%` }"></div>
                    </div>
                    <small class="text-muted">H:{{ stats.hadir }} S:{{ stats.sakit }} I:{{ stats.izin }} A:{{ stats.alpha }}</small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="bi bi-clipboard-check-fill" aria-hidden="true"></i></div>
                <div>
                    <div class="stat-label">Penyelesaian Tugas</div>
                    <div class="stat-number">{{ stats.persen_tugas }}%</div>
                    <div class="progress mt-1">
                        <div class="progress-bar" :style="{ width: `${stats.persen_tugas}%` }"></div>
                    </div>
                    <small class="text-muted">{{ stats.selesai }} dari {{ stats.total_tugas }} tugas</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <Card title="Nilai per Mata Pelajaran" icon="bi-bar-chart-fill">
                    <canvas v-if="subjectScores.length" ref="chartCanvas" height="250"></canvas>
                    <EmptyState v-else title="Belum ada data nilai." icon="bi-bar-chart" />
                </Card>
            </div>
            <div class="col-md-6 mb-4">
                <Card title="Detail Nilai" icon="bi-list-ul" body-class="p-0">
                    <TableWrapper v-if="subjectScores.length">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Rata-rata</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in subjectScores" :key="item.nama_mapel">
                                    <td>{{ item.nama_mapel }}</td>
                                    <td class="text-center fw-bold" :style="{ color: scoreColor(item.rata) }">
                                        {{ item.rata_label }}
                                    </td>
                                    <td class="text-center">
                                        <Badge :color="scoreBadge(item.rata).color">{{ scoreBadge(item.rata).label }}</Badge>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <EmptyState v-else title="Belum ada nilai" icon="bi-bar-chart" />
                </Card>
            </div>
        </div>
    </AppShell>
</template>
