<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import {
    ActionQueue,
    CourseCard,
    DashboardHero,
    MetricStrip,
    QuickActionBar,
} from '../../Components/UI';

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
const metrics = computed(() => [
    { label: 'Kelas dan mapel', value: props.statistik.total_kelas_mapel ?? 0, icon: 'bi-diagram-3-fill', tone: 'primary', href: '/guru/materi' },
    { label: 'Siswa diajar', value: props.statistik.total_siswa ?? 0, icon: 'bi-people-fill', tone: 'info' },
    { label: 'Perlu dinilai', value: totalPerluDinilai.value, icon: 'bi-pencil-square', tone: 'warning', href: '/guru/tugas' },
    { label: 'Kehadiran rendah', value: props.siswaJarangMasuk.length, icon: 'bi-person-exclamation', tone: 'danger' },
]);
const quickActions = [
    { label: 'Buat Materi', href: '/guru/materi', icon: 'bi-file-earmark-plus', color: 'primary' },
    { label: 'Buat Tugas', href: '/guru/tugas', icon: 'bi-journal-plus', color: 'light' },
    { label: 'Absensi', href: '/guru/absensi', icon: 'bi-clipboard-check', color: 'light' },
    { label: 'Chat', href: '/guru/chat', icon: 'bi-chat-dots', color: 'light' },
];
const gradingItems = computed(() => props.tugasPerluDinilai.map((item) => ({
    id: item.id,
    title: item.judul,
    meta: `${item.kelas} - ${item.mata_pelajaran}`,
    detail: 'Sudah masuk, belum dinilai',
    href: item.url,
    badge: item.total,
    badgeColor: 'info',
    icon: 'bi-pencil-square',
    accent: '#2563eb',
})));
const missingItems = computed(() => props.tugasBelumDikumpulkan.map((item) => ({
    id: item.id,
    title: item.judul,
    meta: `${item.kelas} - ${item.mata_pelajaran}`,
    detail: `Deadline ${item.batas_waktu}`,
    href: item.url,
    badge: `${item.belum}/${item.total_siswa}`,
    badgeColor: 'warning text-dark',
    icon: 'bi-exclamation-circle',
    accent: '#f59e0b',
})));
const attendanceItems = computed(() => props.siswaJarangMasuk.map((item) => ({
    id: item.id,
    title: item.nama,
    meta: `${item.kelas} - NIS ${item.nis}`,
    detail: `${item.total_absensi} catatan absensi, ${item.total_alpha} alpha`,
    href: item.url,
    badge: `${item.persen_hadir}%`,
    badgeColor: item.persen_hadir < 60 ? 'danger' : 'warning text-dark',
    icon: 'bi-person-exclamation',
    accent: '#dc2626',
})));
</script>

<template>
    <Head title="Dashboard Guru" />

    <AppShell title="Dashboard Guru">
        <DashboardHero
            eyebrow="Teaching Cockpit"
            :title="`Selamat datang, ${user?.nama_lengkap ?? 'Guru'}`"
            :subtitle="`${totalPerluDinilai} pengumpulan perlu dinilai dan ${totalBelumMengumpulkan} tugas belum lengkap.`"
            icon="bi-person-workspace"
            tone="teacher"
        >
            <template #actions>
                <QuickActionBar :actions="quickActions" />
            </template>
        </DashboardHero>

        <MetricStrip :items="metrics" />

        <div class="dashboard-grid dashboard-grid-teacher">
            <ActionQueue
                title="Perlu Dinilai"
                icon="bi-pencil-square"
                :items="gradingItems"
                empty-title="Tidak ada antrean nilai"
                empty-message="Semua pengumpulan yang masuk sudah dinilai."
            />
            <ActionQueue
                title="Belum Mengumpulkan"
                icon="bi-exclamation-circle"
                :items="missingItems"
                empty-title="Tidak ada tunggakan tugas"
                empty-message="Semua tugas lewat deadline sudah lengkap dikumpulkan."
            />
            <ActionQueue
                title="Siswa Perlu Perhatian"
                icon="bi-person-exclamation"
                :items="attendanceItems"
                empty-title="Kehadiran aman"
                empty-message="Belum ada siswa dengan kehadiran di bawah 75% dalam 60 hari terakhir."
            />
        </div>

        <section class="workspace-panel">
            <header class="workspace-panel-header">
                <span class="workspace-panel-title">
                    <i class="bi bi-book" aria-hidden="true"></i>
                    Kelas dan Mapel Diampu
                </span>
                <Link href="/guru/materi" class="app-card-action-link">Kelola Materi</Link>
            </header>
            <div v-if="kelasMapel.length" class="course-card-grid">
                <CourseCard
                    v-for="item in kelasMapel"
                    :key="item.id"
                    :title="item.mata_pelajaran"
                    :subtitle="item.kelas"
                    :meta="`Semester ${item.semester}`"
                    :href="item.workspace_url"
                    icon="bi-book"
                    :badges="[{ label: `${item.materi_count} materi`, color: 'primary' }, { label: `${item.tugas_count} tugas`, color: 'info' }]"
                />
            </div>
            <ActionQueue v-else :items="[]" empty-title="Belum ada penugasan mengajar semester ini" icon="bi-book" />
        </section>
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
