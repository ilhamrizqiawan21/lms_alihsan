<script setup>
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';

const props = defineProps({
    type: { type: String, required: true },
    title: { type: String, required: true },
    kelasList: { type: Array, default: () => [] },
    kelasId: { type: [Number, String, null], default: null },
    semester: { type: [Number, String, null], default: 1 },
    bulan: { type: String, default: '' },
    kelasNama: { type: String, default: '' },
    rekap: { type: Array, default: () => [] },
    tanggalList: { type: Array, default: () => [] },
    mapelList: { type: Array, default: () => [] },
    tugasList: { type: Array, default: () => [] },
});

function reload() {
    const params = { kelas_id: props.kelasId || undefined, semester: props.semester || undefined };
    if (props.type === 'absensi') params.bulan = props.bulan || undefined;
    router.get(window.location.pathname, params, { preserveState: true, replace: true });
}

function exportUrl(format) {
    const map = {
        absensi: `/admin/export/absensi/${format}`,
        nilai: `/admin/export/nilai/${format}`,
        sikap: `/admin/export/sikap/${format}`,
        tugas: `/admin/export/tugas/${format}`,
    };
    const base = map[props.type];
    const query = new URLSearchParams({ ...(props.kelasId ? { kelas_id: props.kelasId } : {}), ...(props.semester ? { semester: props.semester } : {}) });
    if (props.type === 'absensi' && props.bulan) query.set('bulan', props.bulan);
    return `${base}?${query.toString()}`;
}

const empty = computed(() => props.rekap.length === 0 && props.tugasList.length === 0);
</script>

<template>
    <AppShell :title="title">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">{{ title }}</h1>
                <p class="text-muted mb-0">Rekap akademik terintegrasi untuk administrasi sekolah.</p>
            </div>
            <div class="d-flex gap-2">
                <a v-if="kelasId" :href="exportUrl('excel')" class="btn btn-outline-success"><i class="bi bi-file-earmark-excel me-1"></i>Excel</a>
                <a v-if="kelasId" :href="exportUrl('pdf')" class="btn btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i>PDF</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label">Kelas</label>
                        <select v-model="props.kelasId" class="form-select" @change="reload">
                            <option :value="null">Pilih kelas</option>
                            <option v-for="kelas in kelasList" :key="kelas.id" :value="kelas.id">{{ kelas.tingkat }} {{ kelas.nama_kelas }}</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Semester</label>
                        <select v-model="props.semester" class="form-select" @change="reload">
                            <option value="1">Semester 1</option>
                            <option value="2">Semester 2</option>
                        </select>
                    </div>
                    <div v-if="type === 'absensi'" class="col-md-3">
                        <label class="form-label">Bulan</label>
                        <input v-model="props.bulan" type="month" class="form-control" @change="reload">
                    </div>
                </div>
            </div>
        </div>

        <div v-if="kelasNama" class="alert alert-light border mb-3"><strong>{{ kelasNama }}</strong> · Semester {{ semester }}</div>

        <div v-if="empty" class="card border-0 shadow-sm">
            <div class="card-body text-center py-5 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                Belum ada data untuk filter yang dipilih.
            </div>
        </div>

        <div v-else class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr v-if="type === 'absensi'">
                            <th>NIS</th><th>Nama</th><th v-for="tanggal in tanggalList" :key="tanggal" class="text-center">{{ tanggal.slice(8) }}</th><th class="text-center">H</th><th class="text-center">S</th><th class="text-center">I</th><th class="text-center">A</th>
                        </tr>
                        <tr v-else-if="type === 'nilai'">
                            <th>NIS</th><th>Nama</th><th v-for="mapel in mapelList" :key="mapel.kelas_mapel_id" class="text-center">{{ mapel.nama_mapel }}</th><th class="text-center">Rata-rata</th>
                        </tr>
                        <tr v-else-if="type === 'sikap'">
                            <th>NIS</th><th>Nama</th><th colspan="6" class="text-center">Spiritual</th><th colspan="5" class="text-center">Sosial</th>
                        </tr>
                        <tr v-else><th>Mata Pelajaran</th><th>Guru</th><th class="text-center">Terkumpul</th><th class="text-center">Total Siswa</th></tr>
                        <tr v-if="type === 'sikap'" class="table-light"><th></th><th></th><th>Taqwa</th><th>Jujur</th><th>Disiplin</th><th>Sabar</th><th>Syukur</th><th>Tawadhu</th><th>Empati</th><th>Kerja Sama</th><th>Toleransi</th><th>Percaya Diri</th><th>Komunikasi</th></tr>
                    </thead>
                    <tbody>
                        <template v-if="type === 'tugas'">
                            <tr v-for="tugas in tugasList" :key="tugas.id">
                                <td>{{ tugas.kelas_mapel?.mata_pelajaran?.nama_mapel || tugas.kelasMapel?.mataPelajaran?.nama_mapel || '-' }}</td>
                                <td>{{ tugas.kelas_mapel?.guru?.nama_lengkap || tugas.kelasMapel?.guru?.nama_lengkap || '-' }}</td>
                                <td class="text-center">{{ tugas.sudah_kumpul }}</td><td class="text-center">{{ tugas.total_siswa }}</td>
                            </tr>
                        </template>
                        <template v-else-if="type === 'absensi'">
                            <tr v-for="row in rekap" :key="row.nis"><td>{{ row.nis }}</td><td class="fw-semibold">{{ row.nama }}</td><td v-for="tanggal in tanggalList" :key="tanggal" class="text-center">{{ row.absensi?.[tanggal] ? row.absensi[tanggal].charAt(0).toUpperCase() : '-' }}</td><td class="text-center">{{ row.hadir }}</td><td class="text-center">{{ row.sakit }}</td><td class="text-center">{{ row.izin }}</td><td class="text-center">{{ row.alpha }}</td></tr>
                        </template>
                        <template v-else-if="type === 'nilai'">
                            <tr v-for="row in rekap" :key="row.nis"><td>{{ row.nis }}</td><td class="fw-semibold">{{ row.nama }}</td><td v-for="mapel in mapelList" :key="mapel.kelas_mapel_id" class="text-center">{{ row.nilai?.[mapel.id] ?? '-' }}</td><td class="text-center fw-bold">{{ row.rata ?? '-' }}</td></tr>
                        </template>
                        <template v-else>
                            <tr v-for="row in rekap" :key="row.nis"><td>{{ row.nis }}</td><td class="fw-semibold">{{ row.nama }}</td><td>{{ row.spiritual?.taqwa || '-' }}</td><td>{{ row.spiritual?.kejujuran || '-' }}</td><td>{{ row.spiritual?.disiplin || '-' }}</td><td>{{ row.spiritual?.sabar || '-' }}</td><td>{{ row.spiritual?.syukur || '-' }}</td><td>{{ row.spiritual?.tawadhu || '-' }}</td><td>{{ row.sosial?.empati || '-' }}</td><td>{{ row.sosial?.kerjasama || '-' }}</td><td>{{ row.sosial?.toleransi || '-' }}</td><td>{{ row.sosial?.percaya_diri || '-' }}</td><td>{{ row.sosial?.komunikasi || '-' }}</td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </AppShell>
</template>
