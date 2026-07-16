<script setup>
import { Head } from '@inertiajs/vue3';
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Card, EmptyState, StatCard, TableWrapper } from '../../../Components/UI';

const props = defineProps({
    siswaPerKelas: { type: Array, default: () => [] },
    totalGuru: { type: Number, default: 0 },
    absensiBulanan: { type: Array, default: () => [] },
    distribusiNilai: { type: Array, default: () => [] },
});

const siswaCanvas = ref(null);
const nilaiCanvas = ref(null);
const absensiCanvas = ref(null);
let siswaChart = null;
let nilaiChart = null;
let absensiChart = null;

async function chartJs() {
    const { Chart, registerables } = await import('chart.js');
    Chart.register(...registerables);
    return Chart;
}

async function renderCharts() {
    const Chart = await chartJs();

    if (siswaCanvas.value && props.siswaPerKelas.length) {
        siswaChart?.destroy();
        siswaChart = new Chart(siswaCanvas.value, {
            type: 'bar',
            data: {
                labels: props.siswaPerKelas.map((item) => item.label),
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: props.siswaPerKelas.map((item) => item.jumlah),
                    backgroundColor: '#198754',
                    borderRadius: 8,
                }],
            },
            options: { responsive: true, plugins: { legend: { display: false } } },
        });
    }

    if (nilaiCanvas.value && props.distribusiNilai.length) {
        nilaiChart?.destroy();
        nilaiChart = new Chart(nilaiCanvas.value, {
            type: 'doughnut',
            data: {
                labels: props.distribusiNilai.map((item) => item.label),
                datasets: [{
                    data: props.distribusiNilai.map((item) => item.value),
                    backgroundColor: props.distribusiNilai.map((item) => item.color),
                }],
            },
        });
    }

    if (absensiCanvas.value && props.absensiBulanan.length) {
        absensiChart?.destroy();
        absensiChart = new Chart(absensiCanvas.value, {
            type: 'line',
            data: {
                labels: props.absensiBulanan.map((item) => item.bulan),
                datasets: [{
                    label: 'Persentase Hadir',
                    data: props.absensiBulanan.map((item) => item.persentase),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25,135,84,0.16)',
                    tension: 0.35,
                    fill: true,
                }],
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true, max: 100 } },
            },
        });
    }
}

onMounted(() => nextTick(renderCharts));
watch(() => [props.siswaPerKelas, props.distribusiNilai, props.absensiBulanan], () => nextTick(renderCharts), { deep: true });
onBeforeUnmount(() => {
    siswaChart?.destroy();
    nilaiChart?.destroy();
    absensiChart?.destroy();
});
</script>

<template>
    <Head title="Statistik" />

    <AppShell title="Statistik">
        <PageHeader
            title="Statistik"
            icon="bi-graph-up-arrow"
            subtitle="Pantau ringkasan statistik siswa, guru, absensi, dan nilai."
        />

        <div class="stats-grid">
            <StatCard label="Total Guru" :value="totalGuru" icon="bi-person-workspace" />
            <StatCard label="Total Kelas" :value="siswaPerKelas.length" icon="bi-building" />
            <StatCard label="Total Siswa Aktif" :value="siswaPerKelas.reduce((total, item) => total + item.jumlah, 0)" icon="bi-people-fill" />
            <StatCard label="Data Nilai" :value="distribusiNilai.reduce((total, item) => total + item.value, 0)" icon="bi-bar-chart-fill" />
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-4">
                <Card title="Jumlah Siswa Per Kelas" icon="bi-people-fill">
                    <canvas v-if="siswaPerKelas.length" ref="siswaCanvas" height="250"></canvas>
                    <EmptyState v-else title="Belum ada data siswa." icon="bi-people" />
                </Card>
            </div>

            <div class="col-md-6 mb-4">
                <Card title="Distribusi Nilai" icon="bi-bar-chart-fill">
                    <canvas v-if="distribusiNilai.some((item) => item.value > 0)" ref="nilaiCanvas" height="250"></canvas>
                    <EmptyState v-else title="Belum ada data nilai." icon="bi-bar-chart" />
                </Card>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <Card title="Tren Kehadiran 6 Bulan Terakhir" icon="bi-clipboard-check-fill">
                    <canvas v-if="absensiBulanan.length" ref="absensiCanvas" height="220"></canvas>
                    <EmptyState v-else title="Belum ada data absensi." icon="bi-clipboard-check" />
                </Card>
            </div>

            <div class="col-lg-5 mb-4">
                <Card title="Statistik Absensi Bulanan" icon="bi-list-ul" body-class="p-0">
                    <TableWrapper v-if="absensiBulanan.length">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Hadir</th>
                                    <th>Total</th>
                                    <th>Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in absensiBulanan" :key="item.bulan">
                                    <td><strong>{{ item.bulan }}</strong></td>
                                    <td>{{ item.hadir }}</td>
                                    <td>{{ item.total }}</td>
                                    <td>{{ item.persentase }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <EmptyState v-else title="Tidak ada data" icon="bi-list-ul" />
                </Card>
            </div>
        </div>
    </AppShell>
</template>
