<script setup>
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Button, Card, EmptyState, TableWrapper } from '../../../Components/UI';
import SubmissionGradeForm from './Partials/SubmissionGradeForm.vue';
import SubmissionRow from './Partials/SubmissionRow.vue';

const props = defineProps({
    kelasMapel: { type: Object, required: true },
    tugas: { type: Object, required: true },
    pengumpulan: { type: Array, default: () => [] },
});

const detail = ref(null);
const title = computed(() => `Pengumpulan: ${props.tugas.judul}`);

function statusColor(status) {
    return {
        sudah: 'success',
        dinilai: 'primary',
        terlambat: 'danger',
    }[status] ?? 'secondary';
}

function statusLabel(status) {
    return status ? status.replace(/\b\w/g, (char) => char.toUpperCase()) : '-';
}
</script>

<template>
    <Head title="Pengumpulan Tugas" />

    <AppShell title="Pengumpulan Tugas">
        <PageHeader :title="title" icon="bi-journal-fill">
            <template #actions>
                <span class="text-muted small">Deadline: {{ tugas.batas_waktu ?? '-' }}</span>
                <a :href="kelasMapel.export_excel_url" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i> Excel
                </a>
                <a :href="kelasMapel.export_pdf_url" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i> PDF
                </a>
                <a :href="kelasMapel.back_url" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Kembali
                </a>
            </template>
        </PageHeader>

        <Card body-class="p-0">
            <TableWrapper v-if="pengumpulan.length">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Status</th>
                            <th>Tanggal Kumpul</th>
                            <th>File</th>
                            <th>Jawaban</th>
                            <th>Nilai</th>
                            <th>Catatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <SubmissionRow
                            v-for="item in pengumpulan"
                            :key="item.id"
                            :item="item"
                            :status-color="statusColor"
                            :status-label="statusLabel"
                            @detail="detail = item"
                        />
                    </tbody>
                </table>
            </TableWrapper>
            <EmptyState v-else title="Belum ada pengumpulan" icon="bi-inbox" />
        </Card>

        <div v-if="detail" class="confirm-overlay" @click.self="detail = null">
            <div class="confirm-dialog" role="dialog" aria-modal="true" style="max-width:560px;text-align:left;">
                <h5 class="confirm-title">Detail Pengumpulan - {{ detail.siswa }}</h5>
                <div class="mb-3">
                    <p><strong>Status:</strong> <Badge :color="statusColor(detail.status)">{{ statusLabel(detail.status) }}</Badge></p>
                    <p><strong>Tanggal Kumpul:</strong> {{ detail.tanggal_kumpul ?? '-' }}</p>

                    <div v-if="detail.files.length" class="mb-3">
                        <strong>File:</strong>
                        <ul class="mb-0 mt-2">
                            <li v-for="file in detail.files" :key="file.id">
                                <a :href="file.url" target="_blank" rel="noopener">{{ file.name }}</a>
                            </li>
                        </ul>
                    </div>
                    <p v-else-if="detail.legacy_file_url">
                        <strong>File:</strong> <a :href="detail.legacy_file_url" target="_blank" rel="noopener">Download</a>
                    </p>

                    <div v-if="detail.teks_jawaban">
                        <strong>Jawaban Teks:</strong>
                        <div class="p-2 bg-light rounded mt-2">{{ detail.teks_jawaban }}</div>
                    </div>
                </div>

                <SubmissionGradeForm :item="detail" block />

                <div class="confirm-actions mt-3">
                    <Button type="button" color="outline-secondary" size="" @click="detail = null">Tutup</Button>
                </div>
            </div>
        </div>
    </AppShell>
</template>
