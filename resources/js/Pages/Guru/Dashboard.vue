<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import PageHeader from '../../Components/AppShell/PageHeader.vue';
import AppShell from '../../Layouts/AppShell.vue';
import { Badge, Card, EmptyState, StatCard, TableWrapper } from '../../Components/UI';

const page = usePage();
const user = page.props.auth?.user;

const props = defineProps({
    statistik: { type: Object, default: () => ({}) },
    kelasMapel: { type: Array, default: () => [] },
    tugasBelumDikumpulkan: { type: Array, default: () => [] },
    siswaJarangMasuk: { type: Array, default: () => [] },
    tugasPerluDinilai: { type: Array, default: () => [] },
    pengumuman: { type: Array, default: () => [] },
    notifikasi: { type: Array, default: () => [] },
    unreadNotifCount: { type: Number, default: 0 },
});

const totalBelumMengumpulkan = computed(() => props.tugasBelumDikumpulkan.reduce((total, item) => total + (item.belum ?? 0), 0));
const totalPerluDinilai = computed(() => props.tugasPerluDinilai.reduce((total, item) => total + (item.total ?? 0), 0));
</script>

<template>
    <Head title="Dashboard Guru" />

    <AppShell title="Dashboard Guru">
        <PageHeader
            title="Dashboard Guru"
            :subtitle="`Selamat datang, ${user?.nama_lengkap ?? 'Guru'}`"
            icon="bi-speedometer2"
        />

        <div class="stats-grid">
            <StatCard label="Kelas & Mapel" :value="statistik.total_kelas_mapel ?? 0" icon="bi-diagram-3-fill" />
            <StatCard label="Total Siswa Diajar" :value="statistik.total_siswa ?? 0" icon="bi-people-fill" />
            <StatCard label="Total Materi" :value="statistik.total_materi ?? 0" icon="bi-file-earmark-text-fill" />
            <StatCard label="Total Tugas" :value="statistik.total_tugas ?? 0" icon="bi-journal-fill" />
            <StatCard label="Belum Mengumpulkan" :value="totalBelumMengumpulkan" icon="bi-exclamation-circle-fill" />
            <StatCard label="Perlu Dinilai" :value="totalPerluDinilai" icon="bi-pencil-square" />
            <StatCard label="Kehadiran Rendah" :value="siswaJarangMasuk.length" icon="bi-person-exclamation" />
        </div>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <Card title="Belum Mengumpulkan" icon="bi-exclamation-circle" body-class="p-0">
                    <div v-if="tugasBelumDikumpulkan.length" class="priority-list">
                        <Link
                            v-for="item in tugasBelumDikumpulkan"
                            :key="item.id"
                            :href="item.url"
                            class="priority-item"
                        >
                            <div class="priority-main">
                                <strong>{{ item.judul }}</strong>
                                <span>{{ item.kelas }} - {{ item.mata_pelajaran }}</span>
                                <small>Deadline {{ item.batas_waktu }}</small>
                            </div>
                            <Badge color="warning">{{ item.belum }}/{{ item.total_siswa }}</Badge>
                        </Link>
                    </div>
                    <EmptyState
                        v-else
                        title="Tidak ada tunggakan tugas"
                        message="Semua tugas lewat deadline sudah lengkap dikumpulkan."
                        icon="bi-check-circle"
                    />
                </Card>
            </div>

            <div class="col-lg-4 mb-4">
                <Card title="Siswa Perlu Perhatian" icon="bi-person-exclamation" body-class="p-0">
                    <div v-if="siswaJarangMasuk.length" class="priority-list">
                        <Link
                            v-for="item in siswaJarangMasuk"
                            :key="item.id"
                            :href="item.url"
                            class="priority-item"
                        >
                            <div class="priority-main">
                                <strong>{{ item.nama }}</strong>
                                <span>{{ item.kelas }} - NIS {{ item.nis }}</span>
                                <small>{{ item.total_absensi }} catatan absensi, {{ item.total_alpha }} alpha</small>
                            </div>
                            <Badge :color="item.persen_hadir < 60 ? 'danger' : 'warning'">
                                {{ item.persen_hadir }}%
                            </Badge>
                        </Link>
                    </div>
                    <EmptyState
                        v-else
                        title="Kehadiran aman"
                        message="Belum ada siswa dengan kehadiran di bawah 75% dalam 60 hari terakhir."
                        icon="bi-people"
                    />
                </Card>
            </div>

            <div class="col-lg-4 mb-4">
                <Card title="Perlu Dinilai" icon="bi-pencil-square" body-class="p-0">
                    <div v-if="tugasPerluDinilai.length" class="priority-list">
                        <Link
                            v-for="item in tugasPerluDinilai"
                            :key="item.id"
                            :href="item.url"
                            class="priority-item"
                        >
                            <div class="priority-main">
                                <strong>{{ item.judul }}</strong>
                                <span>{{ item.kelas }} - {{ item.mata_pelajaran }}</span>
                                <small>Sudah masuk, belum dinilai</small>
                            </div>
                            <Badge color="info">{{ item.total }}</Badge>
                        </Link>
                    </div>
                    <EmptyState
                        v-else
                        title="Tidak ada antrean nilai"
                        message="Semua pengumpulan yang masuk sudah dinilai."
                        icon="bi-check2-square"
                    />
                </Card>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7 mb-4">
                <Card title="Kelas & Mapel Diampu" icon="bi-book" body-class="p-0">
                    <TableWrapper v-if="kelasMapel.length">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Semester</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in kelasMapel" :key="item.id">
                                    <td><strong>{{ item.kelas }}</strong></td>
                                    <td>{{ item.mata_pelajaran }}</td>
                                    <td>{{ item.semester }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <EmptyState
                        v-else
                        title="Belum ada penugasan mengajar semester ini"
                        icon="bi-book"
                    />
                </Card>
            </div>

            <div class="col-md-5 mb-4">
                <Card title="Notifikasi" icon="bi-bell-fill">
                    <template #actions>
                        <Link
                            v-if="unreadNotifCount > 0"
                            href="/guru/notifikasi"
                            class="text-decoration-none small"
                            style="color: var(--primary-600);"
                        >
                            Lihat Semua
                        </Link>
                    </template>
                    <template #default>
                        <div v-if="notifikasi.length">
                            <div
                                v-for="item in notifikasi"
                                :key="item.id"
                                :style="{
                                    borderLeft: `3px solid ${item.is_read ? '#d1d5db' : '#ef4444'}`,
                                    padding: '0.5rem 0.75rem',
                                    marginBottom: '0.5rem',
                                    background: item.is_read ? '#f9fafb' : '#fef2f2',
                                    borderRadius: '0 6px 6px 0',
                                }"
                            >
                                <strong style="font-size:0.85rem;">{{ item.judul }}</strong>
                                <div class="text-muted" style="font-size:0.7rem;">{{ item.pesan }}</div>
                                <small class="text-muted" style="font-size:0.65rem;">{{ item.created_at ?? '' }}</small>
                            </div>
                        </div>
                        <EmptyState v-else title="Belum ada notifikasi" icon="bi-bell" />
                    </template>
                </Card>

                <Card title="Pengumuman" icon="bi-megaphone">
                    <div v-if="pengumuman.length">
                        <div
                            v-for="item in pengumuman"
                            :key="item.id"
                            style="border-left:3px solid var(--primary-500);padding:0.5rem 0.75rem;margin-bottom:0.5rem;background:#f9fafb;border-radius:0 6px 6px 0;"
                        >
                            <strong style="font-size:0.85rem;">{{ item.judul }}</strong>
                            <div class="text-muted" style="font-size:0.7rem;">{{ item.created_at ?? '' }}</div>
                        </div>
                    </div>
                    <EmptyState v-else title="Belum ada pengumuman" icon="bi-megaphone" />
                </Card>
            </div>
        </div>
    </AppShell>
</template>

<style scoped>
.priority-list {
    display: flex;
    flex-direction: column;
}

.priority-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.85rem 1rem;
    border-bottom: 1px solid var(--gray-200);
    color: var(--text-body);
    text-decoration: none;
    transition: var(--transition-fast);
}

.priority-item:last-child {
    border-bottom: 0;
}

.priority-item:hover {
    background: var(--primary-50);
    color: var(--text-strong);
}

.priority-main {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

.priority-main strong,
.priority-main span,
.priority-main small {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.priority-main strong {
    color: var(--text-strong);
    font-size: 0.86rem;
}

.priority-main span {
    color: var(--text-muted);
    font-size: 0.76rem;
}

.priority-main small {
    color: var(--gray-500);
    font-size: 0.7rem;
}
</style>
