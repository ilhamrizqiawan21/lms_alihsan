<script setup>
import { Head } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Card, EmptyState } from '../../../Components/UI';

defineProps({
    kelasMapel: { type: Object, required: true },
    materi: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="`Materi: ${kelasMapel.mata_pelajaran}`" />

    <AppShell title="Materi">
        <PageHeader
            :title="kelasMapel.mata_pelajaran"
            :subtitle="`Guru: ${kelasMapel.guru}`"
            icon="bi-file-earmark-text-fill"
        >
            <template #actions>
                <a :href="kelasMapel.back_url" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Kembali
                </a>
            </template>
        </PageHeader>

        <div v-if="materi.length" class="row">
            <div v-for="item in materi" :key="item.id" class="col-md-6 mb-4">
                <Card :title="item.judul" class="h-100">
                    <p class="text-muted" style="font-size:0.85rem;">{{ item.deskripsi }}</p>
                    <small class="text-muted">{{ item.tanggal }}</small>

                    <template v-if="item.download_url" #footer>
                        <a :href="item.download_url" class="btn btn-sm btn-success" target="_blank" rel="noopener">
                            <i class="bi bi-download me-1" aria-hidden="true"></i> Download
                        </a>
                    </template>
                </Card>
            </div>
        </div>

        <Card v-else>
            <EmptyState title="Belum ada materi." icon="bi-file-earmark-text" />
        </Card>
    </AppShell>
</template>
