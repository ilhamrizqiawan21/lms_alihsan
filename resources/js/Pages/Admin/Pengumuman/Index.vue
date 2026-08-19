<script setup>
import { computed, ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AppShell from '../../../Layouts/AppShell.vue';

const page = usePage();
const props = defineProps({
    pengumuman: { type: Object, default: () => ({ data: [] }) },
    kelas: { type: Array, default: () => [] },
    targetKelasOptions: { type: Array, default: () => [] },
    routePrefix: { type: String, default: 'admin.pengumuman' },
});

const showForm = ref(false);
const form = useForm({ judul: '', isi: '', target: 'semua', target_kelas_ids: [] });
const isAdmin = computed(() => page.props.auth?.user?.role === 'admin');

function submit() {
    form.post('/admin/pengumuman', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            showForm.value = false;
        },
    });
}

function remove(id) {
    if (window.confirm('Hapus pengumuman ini?')) {
        form.delete(`/admin/pengumuman/${id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <AppShell title="Pengumuman">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div><h1 class="h3 mb-1">Pengumuman</h1><p class="text-muted mb-0">Kelola informasi resmi sekolah dan distribusi kepada pengguna.</p></div>
            <button class="btn btn-success" type="button" @click="showForm = !showForm"><i class="bi bi-plus-lg me-1"></i>{{ showForm ? 'Tutup Form' : 'Buat Pengumuman' }}</button>
        </div>

        <div v-if="showForm" class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="mb-3">Pengumuman Baru</h5>
                <form @submit.prevent="submit">
                    <div class="row g-3">
                        <div class="col-md-8"><label class="form-label">Judul</label><input v-model="form.judul" class="form-control" maxlength="200" required><div v-if="form.errors.judul" class="text-danger small mt-1">{{ form.errors.judul }}</div></div>
                        <div class="col-md-4"><label class="form-label">Target</label><select v-model="form.target" class="form-select"><option value="semua">Semua</option><option value="guru">Guru</option><option value="siswa">Siswa</option><option value="kelas_mapel">Kelas tertentu</option></select></div>
                        <div v-if="form.target === 'kelas_mapel'" class="col-12"><label class="form-label">Kelas Tujuan</label><select v-model="form.target_kelas_ids" class="form-select" multiple size="5"><option v-for="kelasItem in targetKelasOptions" :key="kelasItem.id" :value="kelasItem.id">{{ kelasItem.tingkat }} {{ kelasItem.nama_kelas }}</option></select><div v-if="form.errors.target_kelas_ids" class="text-danger small mt-1">{{ form.errors.target_kelas_ids }}</div></div>
                        <div class="col-12"><label class="form-label">Isi</label><textarea v-model="form.isi" class="form-control" rows="6" required></textarea><div v-if="form.errors.isi" class="text-danger small mt-1">{{ form.errors.isi }}</div></div>
                        <div class="col-12 d-flex justify-content-end"><button class="btn btn-success" :disabled="form.processing">{{ form.processing ? 'Mempublikasikan...' : 'Publikasikan' }}</button></div>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="!pengumuman.data?.length" class="card border-0 shadow-sm"><div class="card-body text-center py-5 text-muted"><i class="bi bi-megaphone fs-1 d-block mb-3"></i>Belum ada pengumuman.</div></div>
        <div v-else class="d-grid gap-3">
            <article v-for="item in pengumuman.data" :key="item.id" class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between gap-3"><div><h5 class="mb-1">{{ item.judul }}</h5><div class="small text-muted">{{ item.creator?.nama_lengkap || '-' }} · {{ new Date(item.created_at).toLocaleDateString('id-ID') }}</div></div><span class="badge bg-light text-dark align-self-start">{{ item.target }}</span></div>
                    <p class="mt-3 mb-3 text-secondary" style="white-space: pre-line">{{ item.isi }}</p>
                    <div class="d-flex gap-2"><Link :href="`/admin/pengumuman/${item.id}`" class="btn btn-sm btn-outline-primary">Detail</Link><button v-if="isAdmin" class="btn btn-sm btn-outline-danger" type="button" @click="remove(item.id)">Hapus</button></div>
                </div>
            </article>
        </div>
    </AppShell>
</template>
