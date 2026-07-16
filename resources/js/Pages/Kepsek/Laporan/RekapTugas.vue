<script setup>
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import { SearchableSelect, TextInput } from '../../../Components/Form';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Button, Card, EmptyState, Pagination } from '../../../Components/UI';

const props = defineProps({
    tugas: { type: Object, required: true },
    kelasOptions: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    resetUrl: { type: String, required: true },
});

const filterForm = reactive({
    kelas_id: props.filters.kelas_id ?? '',
    search: props.filters.search ?? '',
});

function cleanFilters() {
    return Object.fromEntries(Object.entries(filterForm).filter(([, value]) => value !== '' && value !== null));
}

function applyFilters() {
    router.get(props.resetUrl, cleanFilters(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    filterForm.kelas_id = '';
    filterForm.search = '';

    router.get(props.resetUrl, {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function progressColor(value) {
    if (value >= 80) return 'bg-success';
    if (value >= 50) return 'bg-warning';
    return 'bg-danger';
}
</script>

<template>
    <Head title="Rekap Tugas" />

    <AppShell title="Rekap Tugas">
        <PageHeader
            title="Rekap Tugas Per Kelas"
            icon="bi-journal-check"
        />

        <Card title="Filter" icon="bi-funnel" class="mb-3">
            <form class="row g-2 app-table-filter" @submit.prevent="applyFilters">
                <div class="col-md-3">
                    <SearchableSelect
                        v-model="filterForm.kelas_id"
                        name="kelas_id"
                        wrapper-class="mb-0"
                        placeholder="Semua Kelas"
                        search-placeholder="Cari kelas..."
                        :options="kelasOptions"
                    />
                </div>
                <div class="col-md-3">
                    <TextInput
                        v-model="filterForm.search"
                        name="search"
                        wrapper-class="mb-0"
                        placeholder="Cari judul tugas..."
                    />
                </div>
                <div class="col-md-2">
                    <Button type="submit" color="primary" icon="bi-search" class="w-100">Filter</Button>
                </div>
                <div class="col-md-1">
                    <Button
                        type="button"
                        color="outline-secondary"
                        icon="bi-arrow-clockwise"
                        class="w-100"
                        title="Reset filter"
                        aria-label="Reset filter rekap tugas"
                        @click="resetFilters"
                    />
                </div>
            </form>
        </Card>

        <div v-if="tugas.data.length" class="row">
            <div
                v-for="item in tugas.data"
                :key="item.id"
                class="col-md-6 col-lg-4 mb-3"
            >
                <Card :class="{ 'border border-danger': item.is_past_due && item.belum_kumpul > 0 }">
                    <template #actions>
                        <Badge v-if="item.batas_waktu" :color="item.is_past_due ? 'danger' : 'success'">
                            {{ item.is_past_due ? 'Tutup' : 'Aktif' }}
                        </Badge>
                    </template>

                    <h5 class="tugas-title">
                        {{ item.judul_ringkas }}
                        <Badge v-if="item.kategori_nilai && item.kategori_nilai !== 'NH'" color="info" class="ms-1">{{ item.kategori_nilai }}</Badge>
                    </h5>

                    <div class="text-sm mb-1">
                        <i class="bi bi-book text-primary me-1" aria-hidden="true"></i>
                        {{ item.mapel }}
                        <span class="text-muted mx-1">-</span>
                        {{ item.kelas }}
                    </div>
                    <div class="text-muted tugas-meta mb-2">
                        <i class="bi bi-person me-1" aria-hidden="true"></i>{{ item.guru }}
                        <template v-if="item.batas_waktu">
                            <span class="mx-1">-</span>
                            <i class="bi bi-clock me-1" aria-hidden="true"></i>{{ item.batas_waktu }}
                        </template>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex justify-content-between text-center">
                        <div>
                            <div class="metric">{{ item.total_siswa }}</div>
                            <small class="text-muted">Total</small>
                        </div>
                        <div>
                            <div class="metric text-primary">{{ item.sudah_kumpul }}</div>
                            <small class="text-muted">Sudah</small>
                        </div>
                        <div>
                            <div class="metric text-danger">{{ item.belum_kumpul }}</div>
                            <small class="text-muted">Belum</small>
                        </div>
                        <div>
                            <div class="metric">{{ item.rata_nilai ?? '-' }}</div>
                            <small class="text-muted">Rata</small>
                        </div>
                    </div>

                    <template v-if="item.persen_kumpul !== null">
                        <div class="progress mt-2 tugas-progress">
                            <div
                                class="progress-bar"
                                :class="progressColor(item.persen_kumpul)"
                                :style="{ width: `${item.persen_kumpul}%` }"
                            ></div>
                        </div>
                        <small class="text-muted tugas-percent">{{ item.persen_kumpul }}% terkumpul</small>
                    </template>
                </Card>
            </div>
        </div>

        <EmptyState v-else title="Tidak ada data tugas." icon="bi-journal-check" />

        <div v-if="tugas.links?.length" class="d-flex justify-content-center mt-3">
            <Pagination :links="tugas.links" />
        </div>
    </AppShell>
</template>

<style scoped>
.tugas-title {
    font-size: 0.95rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.tugas-meta {
    font-size: 0.75rem;
}

.metric {
    font-size: 1.1rem;
    font-weight: 700;
}

.tugas-progress {
    height: 6px;
}

.tugas-percent {
    font-size: 0.65rem;
}
</style>
