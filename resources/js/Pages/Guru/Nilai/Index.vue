<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Button, Card, EmptyState, TableWrapper } from '../../../Components/UI';

const props = defineProps({
    kelasMapel: { type: Array, default: () => [] },
    tahunAjaran: { type: Object, default: null },
    semester: { type: String, default: '1' },
    groups: { type: Array, default: () => [] },
    storeUrl: { type: String, required: true },
});

const fieldGroups = [
    { key: 'sum1', label: 'SUM1' },
    { key: 'sum2', label: 'SUM2' },
    { key: 'sum3', label: 'SUM3' },
    { key: 'sum4', label: 'SUM4' },
    { key: 'nilai_harian', label: 'Dari Tugas', readonly: true },
    { key: 'sts', label: 'Nilai' },
    { key: 'sas', label: 'Nilai' },
    { key: 'sat', label: 'Nilai' },
];

const selectedKelasMapelId = ref(props.kelasMapel[0]?.id ?? null);

const form = useForm({
    semester: props.semester,
    kelas_mapel_ids: selectedKelasMapelId.value ? [selectedKelasMapelId.value] : [],
    nilai: buildNilai(),
});

const activeGroup = computed(() => props.groups.find((group) => group.kelas_mapel_id === selectedKelasMapelId.value) ?? null);

watch(() => props.groups, () => {
    form.semester = props.semester;
    selectedKelasMapelId.value = props.kelasMapel[0]?.id ?? null;
    form.kelas_mapel_ids = selectedKelasMapelId.value ? [selectedKelasMapelId.value] : [];
    form.nilai = buildNilai();
}, { deep: true });

watch(selectedKelasMapelId, (value) => {
    form.kelas_mapel_ids = value ? [value] : [];
});

function buildNilai() {
    return Object.fromEntries(props.groups.map((group) => [
        String(group.kelas_mapel_id),
        Object.fromEntries(group.students.map((student) => [
            String(student.id),
            { ...student.scores },
        ])),
    ]));
}

function scoreClass(value) {
    if (value === null || value === undefined || value === '') return '';
    if (value >= 92) return 'excellent';
    if (value >= 83) return 'good';
    if (value >= 75) return 'fair';
    return 'low';
}

function formatScore(value) {
    if (value === null || value === undefined || value === '') return null;
    return Number(value).toFixed(1);
}

function handleScoreKeyup(event) {
    const input = event.target;
    if (input.value.length < 3) return;

    const inputs = Array.from(document.querySelectorAll('.score-input'));
    const next = inputs[inputs.indexOf(input) + 1];
    next?.focus();
}

function submit() {
    form.kelas_mapel_ids = selectedKelasMapelId.value ? [selectedKelasMapelId.value] : [];
    form.post(props.storeUrl, { preserveScroll: true });
}
</script>

<template>
    <Head title="Nilai" />

    <AppShell title="Nilai">
        <PageHeader
            title="Input Nilai"
            subtitle="Pilih kelas penugasan, lalu input nilai siswa."
            icon="bi-pencil-square"
        >
            <template #actions>
                <Badge color="info">
                    <template v-if="tahunAjaran">TA {{ tahunAjaran.tahun }}</template>
                    <template v-else>-</template>
                    &middot; Semester {{ semester }}
                </Badge>
            </template>
        </PageHeader>

        <form v-if="kelasMapel.length" @submit.prevent="submit">
            <Card title="Kelas & Mata Pelajaran" icon="bi-funnel" class="mb-4">
                <label for="kelas-mapel" class="form-label">Kelas Aktif</label>
                <select id="kelas-mapel" v-model="selectedKelasMapelId" class="form-select">
                    <option v-for="item in kelasMapel" :key="item.id" :value="item.id">
                        {{ item.label }}
                    </option>
                </select>
                <div v-if="form.errors.kelas_mapel_ids" class="text-danger small mt-2">
                    {{ form.errors.kelas_mapel_ids }}
                </div>
            </Card>

            <div class="d-flex justify-content-end mb-3">
                <Button type="submit" color="success" icon="bi-save" :disabled="form.processing || !activeGroup">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Nilai' }}
                </Button>
            </div>

            <Card
                v-if="activeGroup"
                :title="`${activeGroup.mata_pelajaran} - ${activeGroup.kelas}`"
                icon="bi-table"
                body-class="p-0"
                class="mb-4"
            >
                <template #actions>
                    <div class="d-flex align-items-center gap-2">
                        <a :href="activeGroup.export_excel_url" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i> Excel
                        </a>
                        <a :href="activeGroup.export_pdf_url" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i> PDF
                        </a>
                    </div>
                </template>

                <TableWrapper>
                    <table class="table table-bordered table-hover app-table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center w-row-number">#</th>
                                <th class="min-w-nis">NIS</th>
                                <th class="min-w-student">Nama Siswa</th>
                                <th colspan="4" class="text-center bg-soft-success">Sumatif Harian</th>
                                <th class="text-center bg-soft-success">Nilai Harian</th>
                                <th class="text-center bg-soft-warning">STS</th>
                                <th class="text-center bg-soft-warning">SAS</th>
                                <th class="text-center bg-soft-danger">SAT</th>
                                <th class="text-center bg-soft-muted">Rata-rata Akhir</th>
                            </tr>
                            <tr class="table-light">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th v-for="field in fieldGroups" :key="field.key" class="text-center w-score">
                                    {{ field.label }}
                                </th>
                                <th class="text-center w-score-total">Auto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="student in activeGroup.students" :key="`${activeGroup.kelas_mapel_id}-${student.id}`">
                                <td class="text-center text-muted">{{ student.no }}</td>
                                <td><code>{{ student.nis }}</code></td>
                                <td>{{ student.nama }}</td>
                                <td v-for="field in fieldGroups" :key="`${activeGroup.kelas_mapel_id}-${student.id}-${field.key}`" class="text-center">
                                    <span
                                        v-if="field.readonly"
                                        class="score-result readonly-score"
                                        :class="scoreClass(form.nilai[String(activeGroup.kelas_mapel_id)][String(student.id)][field.key])"
                                    >
                                        {{ formatScore(form.nilai[String(activeGroup.kelas_mapel_id)][String(student.id)][field.key]) ?? '-' }}
                                    </span>
                                    <input
                                        v-else
                                        v-model="form.nilai[String(activeGroup.kelas_mapel_id)][String(student.id)][field.key]"
                                        type="number"
                                        class="form-control form-control-sm score-input"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        placeholder="-"
                                        @keyup="handleScoreKeyup"
                                        @focus="$event.target.select()"
                                    >
                                </td>
                                <td class="text-center">
                                    <strong v-if="formatScore(student.rata_akhir)" class="score-result" :class="scoreClass(student.rata_akhir)">
                                        {{ formatScore(student.rata_akhir) }}
                                    </strong>
                                    <span v-else class="text-muted">-</span>
                                </td>
                            </tr>
                            <tr v-if="!activeGroup.students.length">
                                <td colspan="12">
                                    <EmptyState title="Tidak ada siswa di kelas ini." icon="bi-people" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </TableWrapper>
            </Card>

            <Card v-else>
                <EmptyState title="Pilih kelas penugasan." icon="bi-funnel" />
            </Card>

            <div class="d-flex justify-content-end">
                <Button type="submit" color="success" icon="bi-save" :disabled="form.processing || !activeGroup">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Nilai' }}
                </Button>
            </div>
        </form>

        <Card v-else>
            <EmptyState title="Anda belum memiliki penugasan" icon="bi-bar-chart" />
        </Card>
    </AppShell>
</template>

<style scoped>
.readonly-score {
    display: inline-flex;
    min-width: 68px;
    min-height: 31px;
    align-items: center;
    justify-content: center;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: #f8f9fa;
    font-weight: 700;
}
</style>
