<script setup>
import { router } from '@inertiajs/vue3';
import AppShell from '../../../Layouts/AppShell.vue';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import { Card, EmptyState } from '../../../Components/UI';

defineProps({
    title: { type: String, default: 'Rekap Nilai Siswa' },
    semester: { type: String, default: '1' },
    kelasMapel: { type: Array, default: () => [] },
    nilai: { type: Object, default: () => ({ data: [], current_page: 1, last_page: 1, from: 0, to: 0, total: 0 }) },
});

const props = defineProps;
</script>

<template>
    <AppShell title="Rekap Nilai">
        <PageHeader title="Rekap Nilai Siswa" subtitle="Rekap nilai dari kelas dan mata pelajaran yang Anda ampu." icon="bi-file-earmark-bar-graph-fill" />

        <Card class="mb-4">
            <form class="row g-3 align-items-end" @submit.prevent="router.get('/guru/rekap-nilai', { kelas_mapel_id: $refs.kelas.value || undefined, semester: $refs.semester.value }, { preserveState: true, replace: true })">
                <div class="col-md-5">
                    <label class="form-label">Kelas & Mata Pelajaran</label>
                    <select ref="kelas" class="form-select" :value="new URLSearchParams(window.location.search).get('kelas_mapel_id') || ''">
                        <option value="">Semua Kelas & Mapel</option>
                        <option v-for="item in kelasMapel" :key="item.id" :value="item.id">{{ item.label }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Semester</label>
                    <select ref="semester" class="form-select">
                        <option value="1" :selected="semester === '1'">Semester 1</option>
                        <option value="2" :selected="semester === '2'">Semester 2</option>
                    </select>
                </div>
                <div class="col-md-2"><button class="btn btn-primary w-100" type="submit"><i class="bi bi-search me-1"></i>Tampilkan</button></div>
                <div class="col-md-2"><a href="/guru/rekap-nilai" class="btn btn-outline-secondary w-100">Reset</a></div>
            </form>
        </Card>

        <Card body-class="p-0">
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom"><strong>Data Nilai</strong><span class="badge text-bg-secondary">{{ nilai.total ?? 0 }} siswa</span></div>
            <div v-if="!(nilai.data?.length)" class="p-5"><EmptyState title="Tidak ada data nilai." icon="bi-inbox" /></div>
            <div v-else class="table-responsive"><table class="table table-hover align-middle mb-0" style="min-width: 1050px"><thead class="table-light"><tr><th>#</th><th>Nama Siswa</th><th>Kelas</th><th>Mapel</th><th>SUM1</th><th>SUM2</th><th>SUM3</th><th>SUM4</th><th>Harian</th><th>STS</th><th>SAS</th><th>SAT</th><th>Rata²</th><th>Predikat</th></tr></thead><tbody><tr v-for="(row, index) in nilai.data" :key="row.id"><td>{{ (nilai.from || 1) + index }}</td><td>{{ row.siswa?.user?.nama_lengkap || row.siswa?.nis || '-' }}</td><td>{{ row.siswa?.kelas?.nama_kelas || '-' }}</td><td>{{ row.kelas_mapel?.mata_pelajaran?.nama_mapel || row.kelasMapel?.mataPelajaran?.nama_mapel || '-' }}</td><td>{{ row.sum1 ?? '-' }}</td><td>{{ row.sum2 ?? '-' }}</td><td>{{ row.sum3 ?? '-' }}</td><td>{{ row.sum4 ?? '-' }}</td><td>{{ row.nilai_harian ?? '-' }}</td><td>{{ row.sts ?? '-' }}</td><td>{{ row.sas ?? '-' }}</td><td>{{ row.sat ?? '-' }}</td><td><strong>{{ row.rata_akhir != null ? Number(row.rata_akhir).toFixed(1) : '-' }}</strong></td><td><span v-if="row.rata_akhir != null" class="badge" :class="row.rata_akhir >= 92 ? 'text-bg-success' : row.rata_akhir >= 83 ? 'text-bg-primary' : row.rata_akhir >= 75 ? 'text-bg-warning' : 'text-bg-danger'">{{ row.rata_akhir >= 92 ? 'A' : row.rata_akhir >= 83 ? 'B' : row.rata_akhir >= 75 ? 'C' : 'D' }}</span><span v-else>-</span></td></tr></tbody></table></div>
            <div v-if="nilai.last_page > 1" class="p-3 border-top d-flex justify-content-between align-items-center"><span class="text-muted small">Halaman {{ nilai.current_page }} dari {{ nilai.last_page }}</span><div class="d-flex gap-2"><button class="btn btn-sm btn-outline-secondary" :disabled="nilai.current_page <= 1" @click="router.get('/guru/rekap-nilai', { ...Object.fromEntries(new URLSearchParams(window.location.search)), page: nilai.current_page - 1 }, { preserveState: true })">Sebelumnya</button><button class="btn btn-sm btn-outline-secondary" :disabled="nilai.current_page >= nilai.last_page" @click="router.get('/guru/rekap-nilai', { ...Object.fromEntries(new URLSearchParams(window.location.search)), page: nilai.current_page + 1 }, { preserveState: true })">Berikutnya</button></div></div>
        </Card>
    </AppShell>
</template>
