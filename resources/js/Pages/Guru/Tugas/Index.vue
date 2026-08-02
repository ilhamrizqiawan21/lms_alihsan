<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import { TextareaInput, TextInput } from '../../../Components/Form';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Button, Card, EmptyState, IconButton, TableWrapper } from '../../../Components/UI';

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
        <PageHeader
            title="Tugas & Nilai"
            subtitle="Buat tugas dan pilih kelas tujuan sesuai penugasan."
            icon="bi-journal-fill"
        />

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
                            <label class="form-label">Kelas Tujuan <span class="text-danger">*</span></label>
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
                    <TableWrapper v-if="tugas.length" class="d-none d-md-block">
                        <table class="table table-hover mb-0">
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
                                <tr v-for="item in tugas" :key="item.id">
                                    <td>
                                        <strong>{{ item.judul }}</strong>
                                        <div v-if="item.deskripsi" class="text-muted small">{{ item.deskripsi }}</div>
                                    </td>
                                    <td>{{ item.kelas }}</td>
                                    <td>{{ item.mata_pelajaran }}</td>
                                    <td class="text-nowrap small">{{ item.batas_waktu ?? '-' }}</td>
                                    <td>{{ item.sudah_mengumpulkan ?? 0 }}/{{ item.total_siswa ?? 0 }}</td>
                                    <td>
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
                    <div v-if="tugas.length" class="app-mobile-list d-md-none">
                        <div v-for="item in tugas" :key="item.id" class="app-mobile-list-item">
                            <div class="app-mobile-list-row">
                                <span class="app-mobile-list-title">{{ item.judul }}</span>
                                <Badge color="primary">{{ item.sudah_mengumpulkan ?? 0 }}/{{ item.total_siswa ?? 0 }}</Badge>
                            </div>
                            <span v-if="item.deskripsi" class="app-mobile-list-meta">{{ item.deskripsi }}</span>
                            <span class="app-mobile-list-meta">{{ item.kelas }} - {{ item.mata_pelajaran }}</span>
                            <div class="app-mobile-list-row">
                                <span class="app-mobile-list-meta">Deadline {{ item.batas_waktu ?? '-' }}</span>
                                <span class="d-inline-flex align-items-center gap-1">
                                    <a v-if="item.pengumpulan_url" :href="item.pengumpulan_url" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1" aria-hidden="true"></i> Nilai
                                    </a>
                                    <IconButton icon="bi-trash" :label="`Hapus ${item.judul}`" color="outline-danger" @click="destroy(item)" />
                                </span>
                            </div>
                        </div>
                    </div>
                    <EmptyState v-else title="Belum ada tugas" icon="bi-journal" />
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

.assignment-submit {
    margin-top: 0.25rem;
}
</style>
