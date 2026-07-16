<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import { TextareaInput, TextInput } from '../../../Components/Form';
import AppShell from '../../../Layouts/AppShell.vue';
import { Button, Card, EmptyState, IconButton, TableWrapper } from '../../../Components/UI';

const props = defineProps({
    kelasMapel: { type: Object, required: true },
    tugas: { type: Array, default: () => [] },
    totalSiswa: { type: Number, default: 0 },
});

const form = useForm({
    judul: '',
    deskripsi: '',
    batas_waktu: '',
});

function submit() {
    form.post(props.kelasMapel.store_url, {
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

    if (!confirmed) {
        return;
    }

    router.delete(item.delete_url, {
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="`Tugas: ${kelasMapel.mata_pelajaran} - ${kelasMapel.kelas}`" />

    <AppShell title="Tugas">
        <PageHeader
            :title="`Tugas ${kelasMapel.mata_pelajaran} - ${kelasMapel.kelas}`"
            icon="bi-journal-fill"
        >
            <template #actions>
                <a href="/guru/tugas" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Kembali
                </a>
            </template>
        </PageHeader>

        <div class="row">
            <div class="col-md-5 mb-4">
                <Card title="Buat Tugas Baru" icon="bi-plus-circle">
                    <form @submit.prevent="submit">
                        <TextInput
                            v-model="form.judul"
                            name="judul"
                            label="Judul"
                            required
                            :error="form.errors.judul"
                        />
                        <TextareaInput
                            v-model="form.deskripsi"
                            name="deskripsi"
                            label="Deskripsi"
                            :rows="3"
                            :error="form.errors.deskripsi"
                        />
                        <TextInput
                            v-model="form.batas_waktu"
                            type="datetime-local"
                            name="batas_waktu"
                            label="Deadline"
                            required
                            :error="form.errors.batas_waktu"
                        />
                        <Button
                            type="submit"
                            color="success"
                            size=""
                            icon="bi-save"
                            class="w-100"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Tugas' }}
                        </Button>
                    </form>
                </Card>
            </div>

            <div class="col-md-7 mb-4">
                <Card title="Daftar Tugas" icon="bi-list-ul" body-class="p-0">
                    <template #actions>
                        <small class="text-muted">{{ totalSiswa }} siswa</small>
                    </template>
                    <template #default>
                        <TableWrapper v-if="tugas.length">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
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
                                        <td style="white-space:nowrap;font-size:0.82rem;">{{ item.batas_waktu ?? '-' }}</td>
                                        <td>{{ item.sudah_mengumpulkan ?? 0 }}/{{ totalSiswa }}</td>
                                        <td>
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <a :href="item.pengumpulan_url" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye me-1" aria-hidden="true"></i> Nilai
                                                </a>
                                                <IconButton
                                                    icon="bi-trash"
                                                    :label="`Hapus ${item.judul}`"
                                                    color="outline-danger"
                                                    @click="destroy(item)"
                                                />
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </TableWrapper>
                        <EmptyState v-else title="Belum ada tugas" icon="bi-journal" />
                    </template>
                </Card>
            </div>
        </div>
    </AppShell>
</template>
