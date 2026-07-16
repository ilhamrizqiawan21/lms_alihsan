<script setup>
import { Head } from '@inertiajs/vue3';
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import PageHeader from '../../Components/AppShell/PageHeader.vue';
import AppShell from '../../Layouts/AppShell.vue';
import { Badge, Card, EmptyState, StatCard, TableWrapper } from '../../Components/UI';

const props = defineProps({
    statistik: { type: Object, default: () => ({}) },
    absensiMingguan: { type: Array, default: () => [] },
    rataNilaiPerMapel: { type: Array, default: () => [] },
    pengumuman: { type: Array, default: () => [] },
    loginTerbaru: { type: Array, default: () => [] },
});

const absensiCanvas = ref(null);
let absensiChart = null;

async function renderAbsensiChart() {
    if (!absensiCanvas.value || !props.absensiMingguan.length) {
        return;
    }

    const { Chart, registerables } = await import('chart.js');
    Chart.register(...registerables);

    absensiChart?.destroy();
    absensiChart = new Chart(absensiCanvas.value, {
        type: 'bar',
        data: {
            labels: props.absensiMingguan.map((item) => item.tanggal),
            datasets: [
                { label: 'Hadir', data: props.absensiMingguan.map((item) => item.hadir), backgroundColor: '#198754' },
                { label: 'Sakit', data: props.absensiMingguan.map((item) => item.sakit), backgroundColor: '#ffc107' },
                { label: 'Izin', data: props.absensiMingguan.map((item) => item.izin), backgroundColor: '#0d6efd' },
                { label: 'Alpa', data: props.absensiMingguan.map((item) => item.alpha), backgroundColor: '#dc3545' },
            ],
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } },
        },
    });
}

function roleBadgeColor(role) {
    return {
        admin: 'danger',
        guru: 'primary',
        siswa: 'success',
        kepala_sekolah: 'warning',
    }[role] ?? 'secondary';
}

onMounted(() => nextTick(renderAbsensiChart));
watch(() => props.absensiMingguan, () => nextTick(renderAbsensiChart), { deep: true });
onBeforeUnmount(() => absensiChart?.destroy());
</script>

<template>
    <Head title="Dashboard Kepala Sekolah" />

    <AppShell title="Dashboard Kepala Sekolah">
        <PageHeader
            title="Dashboard Kepala Sekolah"
            icon="bi-speedometer2"
            subtitle="Pantau ringkasan sekolah, absensi, dan pengumuman terbaru."
        />

        <div class="stats-grid">
            <StatCard label="Total Siswa" :value="statistik.total_siswa ?? 0" icon="bi-people-fill" />
            <StatCard label="Total Guru" :value="statistik.total_guru ?? 0" icon="bi-person-workspace" />
            <StatCard label="Total Kelas" :value="statistik.total_kelas ?? 0" icon="bi-building" />
            <StatCard label="Mata Pelajaran" :value="statistik.total_mapel ?? 0" icon="bi-book-fill" />
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <Card title="Statistik Absensi (7 Hari Terakhir)" icon="bi-clipboard-check-fill">
                    <canvas v-if="absensiMingguan.length" ref="absensiCanvas" height="200"></canvas>
                    <EmptyState v-else title="Belum ada data absensi." icon="bi-clipboard-check" />
                </Card>
            </div>

            <div class="col-md-6 mb-4">
                <Card title="Pengumuman Terbaru" icon="bi-megaphone-fill" body-class="p-0">
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
                                    <td>{{ item.judul }}</td>
                                    <td>{{ item.created_at }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <EmptyState v-else title="Belum ada pengumuman" icon="bi-megaphone" />
                </Card>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <Card title="Rata-rata Nilai per Mata Pelajaran" icon="bi-bar-chart-fill" body-class="p-0">
                    <TableWrapper v-if="rataNilaiPerMapel.length">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th class="text-center">Rata-rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in rataNilaiPerMapel" :key="item.nama_mapel">
                                    <td>{{ item.nama_mapel }}</td>
                                    <td class="text-center fw-bold">{{ item.rata_rata }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <EmptyState v-else title="Belum ada data nilai" icon="bi-bar-chart" />
                </Card>
            </div>

            <div class="col-md-6 mb-4">
                <Card title="Login Terbaru" icon="bi-clock-history" body-class="p-0">
                    <TableWrapper v-if="loginTerbaru.length">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Waktu</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in loginTerbaru" :key="log.id">
                                    <td><strong>{{ log.nama_lengkap }}</strong></td>
                                    <td><Badge :color="roleBadgeColor(log.role)">{{ log.role }}</Badge></td>
                                    <td class="text-muted small">{{ log.login_time }}</td>
                                    <td class="text-muted small">{{ log.ip_address ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <EmptyState v-else title="Belum ada data login" icon="bi-clock-history" />
                </Card>
            </div>
        </div>
    </AppShell>
</template>
