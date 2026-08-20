<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { TextareaInput, TextInput } from '../../../Components/Form';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Button, Card, DashboardHero, EmptyState, IconButton, MetricStrip, TableWrapper } from '../../../Components/UI';

const props = defineProps({
    kelasMapel: { type: Array, default: () => [] },
    tugas: { type: Array, default: () => [] },
    storeUrl: { type: String, required: true },
});

const form = useForm({
    kelas_mapel_ids: [],
    judul: '',
    deskripsi: '',
    batas_waktu: '',
});

const search = ref('');

const filteredTugas = computed(() => {
    const keyword = search.value.trim().toLowerCase();

    if (!keyword) {
        return props.tugas;
    }

    return props.tugas.filter((item) => [
        item.judul,
        item.deskripsi,
        item.kelas,
        item.mata_pelajaran,
    ].filter(Boolean).join(' ').toLowerCase().includes(keyword));
});

const metrics = computed(() => {
    const submitted = props.tugas.reduce((total, item) => total + Number(item.sudah_mengumpulkan || 0), 0);
    const pendingGrades = props.tugas.reduce((total, item) => total + Number(item.perlu_dinilai || 0), 0);
    const overdue = props.tugas.filter((item) => item.is_overdue).length;

    return [
        { label: 'Tugas aktif', value: props.tugas.length, icon: 'bi-journal-check', tone: 'primary' },
        { label: 'Penugasan', value: props.kelasMapel.length, icon: 'bi-diagram-3', tone: 'info' },
        { label: 'Pengumpulan', value: submitted, icon: 'bi-inbox-fill', tone: 'success' },
        { label: 'Perlu dinilai', value: pendingGrades, icon: 'bi-pencil-square', tone: pendingGrades ? 'danger' : 'muted' },
        { label: 'Lewat deadline', value: overdue, icon: 'bi-exclamation-triangle', tone: overdue ? 'warning' : 'muted' },
    ];
});

function toggleAllCourses() {
    if (form.kelas_mapel_ids.length === props.kelasMapel.length) {
        form.kelas_mapel_ids = [];
        return;
    }

    form.kelas_mapel_ids = props.kelasMapel.map((item) => item.id);
}

function submit() {
    form.post(props.storeUrl, {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

async function destroy(item) {
    const confirmed = await window.confirmDialog?.('Hapus tugas ini?', {
        title: 'Hapus Tugas',
        confirmText: 'Ya, hapus',
        danger: true,
    });

    if (!confirmed) return;

    router.delete(item.delete_url, { preserveScroll: true });
}
</script>

<template>
    <Head title="Tugas" />

    <AppShell title="Tugas">
        <DashboardHero
            eyebrow="Teaching Workspace"
            title="Penugasan Guru"
            subtitle="Buat, bagikan, dan pantau tugas lintas kelas dari satu tempat."
            icon="bi-journal-fill"
            tone="teacher"
        >
            <template #actions>
                <a v-if="kelasMapel.length" :href="kelasMapel[0].href" class="btn btn-light btn-sm">
                    <i class="bi bi-arrow-right-circle me-1" aria-hidden="true"></i> Buka kelas pertama
                </a>
            </template>
        </DashboardHero>

        <MetricStrip v-if="kelasMapel.length" :items="metrics" />

        <div v-if="kelasMapel.length" class="row">
            <div class="col-md-5 mb-4">
                <Card title="Buat Tugas Baru" icon="bi-plus-circle">
                    <form @submit.prevent="submit">
                        <div class="form-help-panel">
                            <i class="bi bi-lightbulb-fill" aria-hidden="true"></i>
                            <span>
                                <span class="form-help-panel-title">Pilih satu atau beberapa kelas tujuan.</span>
                                Tugas akan dibuat untuk setiap kelas dan mata pelajaran yang dipilih.
                            </span>
                        </div>

                        <TextInput v-model="form.judul" name="judul" label="Judul" required :error="form.errors.judul" />
                        <TextareaInput v-model="form.deskripsi" name="deskripsi" label="Deskripsi" :rows="3" :error="form.errors.deskripsi" />
                        <TextInput v-model="form.batas_waktu" type="date" name="batas_waktu" label="Deadline" required :error="form.errors.batas_waktu" />

                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                                <label class="form-label mb-0">Kelas Tujuan <span class="text-danger">*</span></label>
                                <button class="btn btn-sm btn-outline-secondary" type="button" @click="toggleAllCourses">
                                    {{ form.kelas_mapel_ids.length === kelasMapel.length ? 'Kosongkan' : 'Pilih semua' }}
                                </button>
                            </div>
                            <div class="assignment-list">
                                <label
                                    v-for="item in kelasMapel"
                                    :key="item.id"
                                    class="assignment-option"
                                    :class="{ selected: form.kelas_mapel_ids.includes(item.id) }"
                                >
                                    <input
                                        v-model="form.kelas_mapel_ids"
                                        class="form-check-input"
                                        type="checkbox"
                                        :value="item.id"
                                    >
                                    <span>{{ item.label }}</span>
                                    <a :href="item.href" class="assignment-option-link" @click.stop>
                                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i>
                                        <span class="visually-hidden">Buka {{ item.label }}</span>
                                    </a>
                                </label>
                            </div>
                            <div v-if="form.errors.kelas_mapel_ids" class="text-danger small mt-1">
                                {{ form.errors.kelas_mapel_ids }}
                            </div>
                        </div>

                        <Button type="submit" color="success" size="" icon="bi-save" class="w-100 assignment-submit" :disabled="form.processing">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Tugas' }}
                        </Button>
                    </form>
                </Card>
            </div>

            <div class="col-md-7 mb-4">
                <Card title="Daftar Tugas" icon="bi-list-ul" body-class="p-0">
                    <template #actions>
                        <div class="assignment-search">
                            <i class="bi bi-search" aria-hidden="true"></i>
                            <input v-model="search" class="form-control form-control-sm" type="search" placeholder="Cari tugas" aria-label="Cari tugas">
                        </div>
                    </template>

                    <TableWrapper v-if="filteredTugas.length" :min-width="980" class="d-none d-md-block">
                        <table class="table table-hover mb-0 app-table-proportional assignment-table">
                            <colgroup>
                                <col style="width:28%">
                                <col style="width:11%">
                                <col style="width:13%">
                                <col style="width:12%">
                                <col style="width:21%">
                                <col style="width:15%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Kelas</th>
                                    <th>Mapel</th>
                                    <th>Deadline</th>
                                    <th>Kumpul</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in filteredTugas" :key="item.id">
                                    <td class="app-table-judul">
                                        <strong>{{ item.judul }}</strong>
                                        <div v-if="item.deskripsi" class="text-muted small assignment-description">{{ item.deskripsi }}</div>
                                    </td>
                                    <td>{{ item.kelas }}</td>
                                    <td>{{ item.mata_pelajaran }}</td>
                                    <td class="text-nowrap small">
                                        <Badge :color="item.is_overdue ? 'danger' : 'secondary'">{{ item.batas_waktu ?? '-' }}</Badge>
                                    </td>
                                    <td>
                                        <div class="assignment-progress">
                                            <span>{{ item.sudah_mengumpulkan ?? 0 }}/{{ item.total_siswa ?? 0 }}</span>
                                            <div class="progress" role="progressbar" :aria-valuenow="item.progress_percent" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar" :style="{ width: `${item.progress_percent || 0}%` }"></div>
                                            </div>
                                            <small v-if="item.perlu_dinilai" class="text-danger">{{ item.perlu_dinilai }} perlu dinilai</small>
                                        </div>
                                    </td>
                                    <td class="assignment-actions">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            <a v-if="item.pengumpulan_url" :href="item.pengumpulan_url" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye me-1" aria-hidden="true"></i> Nilai
                                            </a>
                                            <IconButton icon="bi-trash" :label="`Hapus ${item.judul}`" color="outline-danger" @click="destroy(item)" />
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <div v-if="filteredTugas.length" class="app-mobile-list d-md-none">
                        <div v-for="item in filteredTugas" :key="item.id" class="app-mobile-list-item">
                            <div class="app-mobile-list-row">
                                <span class="app-mobile-list-title">{{ item.judul }}</span>
                                <Badge color="primary">{{ item.sudah_mengumpulkan ?? 0 }}/{{ item.total_siswa ?? 0 }}</Badge>
                            </div>
                            <span v-if="item.deskripsi" class="app-mobile-list-meta">{{ item.deskripsi }}</span>
                            <span class="app-mobile-list-meta">{{ item.kelas }} - {{ item.mata_pelajaran }}</span>
                            <div class="progress my-2" role="progressbar" :aria-valuenow="item.progress_percent" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" :style="{ width: `${item.progress_percent || 0}%` }"></div>
                            </div>
                            <div class="app-mobile-list-row">
                                <span class="app-mobile-list-meta">
                                    Deadline {{ item.batas_waktu ?? '-' }}
                                    <span v-if="item.perlu_dinilai" class="text-danger">- {{ item.perlu_dinilai }} perlu dinilai</span>
                                </span>
                                <span class="d-inline-flex align-items-center gap-1">
                                    <a v-if="item.pengumpulan_url" :href="item.pengumpulan_url" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1" aria-hidden="true"></i> Nilai
                                    </a>
                                    <IconButton icon="bi-trash" :label="`Hapus ${item.judul}`" color="outline-danger" @click="destroy(item)" />
                                </span>
                            </div>
                        </div>
                    </div>
                    <EmptyState v-else :title="search ? 'Tugas tidak ditemukan' : 'Belum ada tugas'" icon="bi-journal" />
                </Card>
            </div>
        </div>

        <Card v-else>
            <EmptyState title="Anda belum memiliki penugasan mengajar semester ini" icon="bi-journal" />
        </Card>
    </AppShell>
</template>

<style scoped>
.assignment-list {
    display: grid;
    gap: 8px;
}

.assignment-option {
    display: flex;
    gap: 8px;
    align-items: flex-start;
    padding: 9px 10px;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    background: var(--surface-card);
    cursor: pointer;
    transition: var(--transition-fast);
}

.assignment-option input {
    margin-top: 2px;
}

.assignment-option:hover,
.assignment-option.selected {
    border-color: var(--primary-300);
    background: var(--primary-50);
}

.assignment-option span {
    color: var(--text-body);
    font-size: 0.84rem;
    line-height: 1.35;
}

.assignment-option-link {
    margin-left: auto;
    color: var(--text-muted);
    line-height: 1;
}

.assignment-option-link:hover {
    color: var(--primary-600);
}

.assignment-submit {
    margin-top: 0.25rem;
}

.assignment-search {
    position: relative;
    width: min(220px, 100%);
}

.assignment-search .bi {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.8rem;
}

.assignment-search .form-control {
    padding-left: 30px;
}

.assignment-table {
    width: 100%;
    min-width: 980px;
}

.assignment-table th,
.assignment-table td {
    padding: 0.72rem 0.7rem;
}

.assignment-table td.app-table-judul {
    max-width: 0;
}

.assignment-table td.app-table-judul strong,
.assignment-description {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
}

.assignment-table td.app-table-judul strong {
    -webkit-line-clamp: 2;
}

.assignment-description {
    -webkit-line-clamp: 2;
}

.assignment-progress {
    display: grid;
    gap: 4px;
    min-width: 0;
}

.assignment-progress .progress,
.app-mobile-list .progress {
    height: 6px;
    background: var(--gray-100);
}

.assignment-actions {
    white-space: nowrap !important;
}

.assignment-actions .d-inline-flex {
    max-width: 100%;
}

@media (max-width: 1199.98px) and (min-width: 768px) {
    .assignment-table {
        min-width: 900px;
    }

    .assignment-table th,
    .assignment-table td {
        padding-left: 0.55rem;
        padding-right: 0.55rem;
    }
}

@media (max-width: 767.98px) {
    .assignment-search {
        width: 100%;
    }
}
</style>
