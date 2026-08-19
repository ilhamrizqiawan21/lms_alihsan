<script setup>
import { Link } from '@inertiajs/vue3';
import AppShell from '../../../Layouts/AppShell.vue';

const props = defineProps({
    pengumuman: { type: Object, required: true },
    targetKelasLabels: { type: Array, default: () => [] },
});
</script>

<template>
    <AppShell title="Detail Pengumuman">
        <div class="mb-3"><Link href="/admin/pengumuman" class="text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Kembali ke Pengumuman</Link></div>
        <article class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3"><div><h1 class="h3 mb-1">{{ pengumuman.judul }}</h1><div class="text-muted small">{{ pengumuman.creator?.nama_lengkap || '-' }} · {{ new Date(pengumuman.created_at).toLocaleString('id-ID') }}</div></div><span class="badge bg-success">{{ pengumuman.target }}</span></div>
                <hr>
                <div class="text-secondary" style="white-space: pre-line">{{ pengumuman.isi }}</div>
                <div v-if="targetKelasLabels.length" class="mt-4"><strong>Kelas tujuan:</strong> {{ targetKelasLabels.join(', ') }}</div>
            </div>
        </article>
    </AppShell>
</template>
