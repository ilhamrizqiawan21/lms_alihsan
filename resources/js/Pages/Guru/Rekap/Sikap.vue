<script setup>
import { router } from '@inertiajs/vue3';
import AppShell from '../../../Layouts/AppShell.vue';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import { Card, EmptyState } from '../../../Components/UI';

const props = defineProps({
    title: { type: String, default: 'Rekap Sikap Spiritual & Sosial' },
    semester: { type: String, default: '1' },
    kelasMapel: { type: Array, default: () => [] },
    sikapSpiritual: { type: Array, default: () => [] },
    sikapSosial: { type: Array, default: () => [] },
});

function filter() {
    const kelas = document.querySelector('#kelas-mapel')?.value || undefined;
    const semester = document.querySelector('#semester')?.value || '1';
    router.get('/guru/rekap-sikap', { kelas_mapel_id: kelas, semester }, { preserveState: true, replace: true });
}

function reset() { router.get('/guru/rekap-sikap', {}, { preserveState: true, replace: true }); }

const spiritualFields = [
    ['taqwa', 'Taqwa'], ['kejujuran', 'Kejujuran'], ['disiplin', 'Disiplin'], ['sabar', 'Sabar'], ['syukur', 'Syukur'], ['tawadhu', 'Tawadhu'],
];
const sosialFields = [
    ['empati', 'Empati'], ['kerjasama', 'Kerja Sama'], ['toleransi', 'Toleransi'], ['percaya_diri', 'Percaya Diri'], ['komunikasi', 'Komunikasi'],
];
function badgeClass(value) { return value >= 4 ? 'text-bg-success' : value >= 3 ? 'text-bg-warning' : 'text-bg-danger'; }
</script>

<template>
    <AppShell title="Rekap Sikap">
        <PageHeader :title="title" subtitle="Rekap sikap spiritual dan sosial dari kelas yang Anda ampu." icon="bi-file-earmark-text-fill" />
        <Card class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-5"><label class="form-label">Kelas & Mata Pelajaran</label><select id="kelas-mapel" class="form-select"><option value="">Semua Kelas & Mapel</option><option v-for="item in kelasMapel" :key="item.id" :value="item.id">{{ item.label }}</option></select></div>
                <div class="col-md-3"><label class="form-label">Semester</label><select id="semester" class="form-select"><option value="1" :selected="semester === '1'">Semester 1</option><option value="2" :selected="semester === '2'">Semester 2</option></select></div>
                <div class="col-md-2"><button class="btn btn-primary w-100" @click="filter"><i class="bi bi-search me-1"></i>Tampilkan</button></div>
                <div class="col-md-2"><button class="btn btn-outline-secondary w-100" @click="reset">Reset</button></div>
            </div>
        </Card>

        <Card class="mb-4" body-class="p-0">
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom"><strong><i class="bi bi-star-fill me-2"></i>Sikap Spiritual (KI-1)</strong><span class="badge text-bg-secondary">{{ sikapSpiritual.length }} siswa</span></div>
            <div v-if="!sikapSpiritual.length" class="p-5"><EmptyState title="Belum ada data spiritual." icon="bi-inbox" /></div>
            <div v-else class="table-responsive"><table class="table table-hover align-middle mb-0" style="min-width: 900px"><thead class="table-light"><tr><th>#</th><th>Nama Siswa</th><th>Kelas</th><th v-for="field in spiritualFields" :key="field[0]" class="text-center">{{ field[1] }}</th><th>Rata²</th></tr></thead><tbody><tr v-for="(row, index) in sikapSpiritual" :key="row.siswa.id"><td>{{ index + 1 }}</td><td>{{ row.siswa.nama }}</td><td>{{ row.siswa.kelas || '-' }}</td><td v-for="field in spiritualFields" :key="field[0]" class="text-center"><span class="badge" :class="badgeClass(row.nilai[field[0]])">{{ row.nilai[field[0]] ?? '-' }}</span></td><td><strong>{{ row.rata ?? '-' }}</strong></td></tr></tbody></table></div>
        </Card>

        <Card body-class="p-0">
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom"><strong><i class="bi bi-people-fill me-2"></i>Sikap Sosial (KI-2)</strong><span class="badge text-bg-secondary">{{ sikapSosial.length }} siswa</span></div>
            <div v-if="!sikapSosial.length" class="p-5"><EmptyState title="Belum ada data sosial." icon="bi-inbox" /></div>
            <div v-else class="table-responsive"><table class="table table-hover align-middle mb-0" style="min-width: 850px"><thead class="table-light"><tr><th>#</th><th>Nama Siswa</th><th>Kelas</th><th v-for="field in sosialFields" :key="field[0]" class="text-center">{{ field[1] }}</th><th>Rata²</th></tr></thead><tbody><tr v-for="(row, index) in sikapSosial" :key="row.siswa.id"><td>{{ index + 1 }}</td><td>{{ row.siswa.nama }}</td><td>{{ row.siswa.kelas || '-' }}</td><td v-for="field in sosialFields" :key="field[0]" class="text-center"><span class="badge" :class="badgeClass(row.nilai[field[0]])">{{ row.nilai[field[0]] ?? '-' }}</span></td><td><strong>{{ row.rata ?? '-' }}</strong></td></tr></tbody></table></div>
        </Card>
    </AppShell>
</template>
