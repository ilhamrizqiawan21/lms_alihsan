<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import { FileInput, TextareaInput, TextInput } from '../../../Components/Form';
import AppShell from '../../../Layouts/AppShell.vue';
import { Button, Card, EmptyState, IconButton, TableWrapper } from '../../../Components/UI';

const props = defineProps({
    kelasMapel: { type: Object, required: true },
    materi: { type: Array, default: () => [] },
});

const fileInputKey = ref(0);
const form = useForm({
    judul: '',
    deskripsi: '',
    file_materi: null,
});

function submit() {
    form.post(props.kelasMapel.store_url, {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            fileInputKey.value += 1;
        },
    });
}

async function destroy(item) {
    const confirmed = await window.confirmDialog?.('Hapus materi ini?', {
        title: 'Hapus Materi',
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
    <Head :title="`Materi: ${kelasMapel.mata_pelajaran} - ${kelasMapel.kelas}`" />

    <AppShell title="Materi">
        <PageHeader
            :title="`Materi ${kelasMapel.mata_pelajaran} - ${kelasMapel.kelas}`"
            icon="bi-file-earmark-text-fill"
        >
            <template #actions>
                <a :href="kelasMapel.back_url" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Kembali
                </a>
            </template>
        </PageHeader>

        <div class="row">
            <div class="col-md-5 mb-4">
                <Card title="Upload Materi" icon="bi-cloud-upload-fill">
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
                        <FileInput
                            :key="fileInputKey"
                            v-model="form.file_materi"
                            name="file_materi"
                            label="File Materi"
                            accept=".jpg,.jpeg,.pdf,image/jpeg,application/pdf"
                            accept-label="JPG, JPEG, PDF"
                            max-size="5MB"
                            required
                            :error="form.errors.file_materi"
                        />
                        <Button
                            type="submit"
                            color="success"
                            size=""
                            icon="bi-upload"
                            class="w-100"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Mengupload...' : 'Upload' }}
                        </Button>
                    </form>
                </Card>
            </div>

            <div class="col-md-7 mb-4">
                <Card title="Daftar Materi" icon="bi-list-ul" body-class="p-0">
                    <template #default>
                        <TableWrapper v-if="materi.length">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in materi" :key="item.id">
                                        <td><strong>{{ item.judul }}</strong></td>
                                        <td style="font-size:0.82rem;">{{ item.deskripsi_ringkas }}</td>
                                        <td style="white-space:nowrap;font-size:0.82rem;">{{ item.tanggal }}</td>
                                        <td>
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <a
                                                    v-if="item.download_url"
                                                    :href="item.download_url"
                                                    class="btn btn-sm btn-outline-primary btn-icon"
                                                    target="_blank"
                                                    rel="noopener"
                                                    :title="`Download ${item.judul}`"
                                                    :aria-label="`Download ${item.judul}`"
                                                >
                                                    <i class="bi bi-download" aria-hidden="true"></i>
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
                        <EmptyState v-else title="Belum ada materi" icon="bi-file-earmark-text" />
                    </template>
                </Card>
            </div>
        </div>
    </AppShell>
</template>
