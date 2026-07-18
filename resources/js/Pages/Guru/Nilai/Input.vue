<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Button, Card, EmptyState, TableWrapper } from '../../../Components/UI';

const props = defineProps({
    kelasMapel: { type: Object, required: true },
    tahunAjaran: { type: Object, default: null },
    semester: { type: String, default: '1' },
    students: { type: Array, default: () => [] },
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

const form = useForm({
    semester: props.semester,
    nilai: buildNilai(),
});

const title = computed(() => `Input Nilai - ${props.kelasMapel.mata_pelajaran}`);

watch(() => props.students, () => {
    form.semester = props.semester;
    form.nilai = buildNilai();
}, { deep: true });

function buildNilai() {
    return Object.fromEntries(props.students.map((student) => [
        String(student.id),
        { ...student.scores },
    ]));
}

function scoreClass(value) {
    if (value === null || value === undefined || value === '') {
        return '';
    }

    if (value >= 92) return 'excellent';
    if (value >= 83) return 'good';
    if (value >= 75) return 'fair';
    return 'low';
}

function formatScore(value) {
    if (value === null || value === undefined || value === '') {
        return null;
    }

    return Number(value).toFixed(1);
}

function handleScoreKeyup(event) {
    const input = event.target;

    if (input.value.length < 3) {
        return;
    }

    const inputs = Array.from(document.querySelectorAll('.score-input'));
    const next = inputs[inputs.indexOf(input) + 1];

    next?.focus();
}

function submit() {
    form.post(props.kelasMapel.store_url, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="title" />

    <AppShell title="Input Nilai">
        <PageHeader title="Input Nilai" icon="bi-pencil-square">
            <template #actions>
                <Badge color="primary">{{ kelasMapel.mata_pelajaran }}</Badge>
                <Badge color="secondary">{{ kelasMapel.kelas }}</Badge>
                <Badge color="info">
                    <template v-if="tahunAjaran">TA {{ tahunAjaran.tahun }}</template>
                    <template v-else>-</template>
                    &middot; Semester {{ semester }}
                </Badge>
                <a :href="kelasMapel.export_excel_url" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i> Excel
                </a>
                <a :href="kelasMapel.export_pdf_url" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i> PDF
                </a>
            </template>
        </PageHeader>

        <form @submit.prevent="submit">
            <Card title="Input Nilai Kurikulum Merdeka" icon="bi-table" body-class="p-0">
                <template #actions>
                    <Button type="submit" color="success" icon="bi-save" :disabled="form.processing">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Semua' }}
                    </Button>
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
                                <th
                                    v-for="field in fieldGroups"
                                    :key="field.key"
                                    class="text-center w-score"
                                >
                                    {{ field.label }}
                                </th>
                                <th class="text-center w-score-total">Auto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="student in students" :key="student.id">
                                <td class="text-center text-muted">{{ student.no }}</td>
                                <td><code>{{ student.nis }}</code></td>
                                <td>{{ student.nama }}</td>
                                <td v-for="field in fieldGroups" :key="`${student.id}-${field.key}`" class="text-center">
                                    <span
                                        v-if="field.readonly"
                                        class="score-result readonly-score"
                                        :class="scoreClass(form.nilai[String(student.id)][field.key])"
                                        :title="'Nilai harian dihitung otomatis dari nilai tugas'"
                                    >
                                        {{ formatScore(form.nilai[String(student.id)][field.key]) ?? '-' }}
                                    </span>
                                    <input
                                        v-else
                                        v-model="form.nilai[String(student.id)][field.key]"
                                        type="number"
                                        class="form-control form-control-sm score-input"
                                        :class="{ 'border-danger border-opacity-25': student.rata_akhir && field.key === 'sat' }"
                                        min="0"
                                        max="100"
                                        step="0.01"
                                        placeholder="-"
                                        @keyup="handleScoreKeyup"
                                        @focus="$event.target.select()"
                                    >
                                </td>
                                <td class="text-center">
                                    <strong
                                        v-if="formatScore(student.rata_akhir)"
                                        class="score-result"
                                        :class="scoreClass(student.rata_akhir)"
                                    >
                                        {{ formatScore(student.rata_akhir) }}
                                    </strong>
                                    <span v-else class="text-muted">-</span>
                                </td>
                            </tr>
                            <tr v-if="!students.length">
                                <td colspan="12">
                                    <EmptyState title="Tidak ada siswa di kelas ini." icon="bi-people" />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </TableWrapper>

                <template #footer>
                    <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <a href="/guru/nilai" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Kembali
                        </a>
                        <div class="d-flex align-items-center gap-3">
                            <span class="text-muted text-xs">{{ students.length }} siswa</span>
                            <Button type="submit" color="success" icon="bi-save" :disabled="form.processing">
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Semua' }}
                            </Button>
                        </div>
                    </div>
                </template>
            </Card>
        </form>
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
