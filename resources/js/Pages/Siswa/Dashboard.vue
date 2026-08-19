<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppShell from '../../Layouts/AppShell.vue';
import { computed } from 'vue';
import {
    ActionQueue,
    CourseCard,
    DashboardHero,
    MetricStrip,
    QuickActionBar,
} from '../../Components/UI';

const props = defineProps({
    stats: { type: Object, required: true },
    courses: { type: Array, default: () => [] },
    tugasTerbaru: { type: Array, default: () => [] },
    notifikasi: { type: Array, default: () => [] },
    pengumuman: { type: Array, default: () => [] },
    links: { type: Object, default: () => ({}) },
});

const iconMap = {
    tugas_baru: { icon: 'bi-journal-plus', color: '#3b82f6' },
    nilai_baru: { icon: 'bi-bar-chart-fill', color: '#22c55e' },
    chat_baru: { icon: 'bi-chat-dots-fill', color: '#8b5cf6' },
    komentar_tugas: { icon: 'bi-chat-square-text-fill', color: '#f59e0b' },
    kumpul_tugas: { icon: 'bi-check-circle-fill', color: '#06b6d4' },
    absensi: { icon: 'bi-clipboard-check-fill', color: '#ef4444' },
};

function iconFor(type) {
    return iconMap[type] ?? { icon: 'bi-bell-fill', color: '#6b7280' };
}

const metrics = computed(() => [
    { label: 'Total tugas', value: props.stats.total_tugas ?? 0, icon: 'bi-journal-fill', tone: 'primary', href: props.links.tugas },
    { label: 'Selesai', value: props.stats.tugas_selesai ?? 0, icon: 'bi-check-circle-fill', tone: 'success', href: props.links.tugas },
    { label: 'Belum', value: props.stats.tugas_belum ?? 0, icon: 'bi-exclamation-circle-fill', tone: 'warning', href: props.links.tugas },
    { label: 'Materi', value: props.stats.total_materi ?? 0, icon: 'bi-file-earmark-text-fill', tone: 'info', href: props.links.materi },
]);
const quickActions = computed(() => [
    { label: 'Tugas Saya', href: props.links.tugas || '/siswa/tugas', icon: 'bi-journal-check', color: 'primary' },
    { label: 'Materi', href: props.links.materi || '/siswa/materi', icon: 'bi-file-earmark-text', color: 'light' },
    { label: 'Nilai', href: '/siswa/nilai', icon: 'bi-bar-chart', color: 'light' },
    { label: 'Chat', href: '/siswa/chat', icon: 'bi-chat-dots', color: 'light' },
]);
const taskItems = computed(() => props.tugasTerbaru.map((item) => ({
    id: item.id,
    title: item.judul,
    meta: item.mata_pelajaran,
    detail: `Deadline ${item.batas_waktu}`,
    href: item.show_url || props.links.tugas || '/siswa/tugas',
    badge: item.selesai ? 'Selesai' : 'Belum',
    badgeColor: item.selesai ? 'success' : 'warning text-dark',
    icon: item.selesai ? 'bi-check-circle' : 'bi-journal-text',
    accent: item.selesai ? '#16a34a' : '#f59e0b',
})));
const notificationItems = computed(() => props.notifikasi.map((item) => ({
    id: item.id,
    title: item.judul,
    meta: item.created_at,
    detail: item.pesan,
    href: props.links.notifikasi,
    badge: item.is_read ? '' : 'Baru',
    badgeColor: 'danger',
    icon: iconFor(item.tipe).icon,
    accent: iconFor(item.tipe).color,
})));
const announcementItems = computed(() => props.pengumuman.map((item) => ({
    id: item.id,
    title: item.judul,
    meta: item.created_at,
    href: item.show_url,
    icon: 'bi-megaphone-fill',
    accent: '#2563eb',
})));
</script>

<template>
    <Head title="Dashboard Siswa" />

    <AppShell title="Dashboard Siswa">
        <DashboardHero
            eyebrow="Hari Ini"
            title="Siap lanjut belajar?"
            :subtitle="`${stats.tugas_belum ?? 0} tugas belum dikerjakan dan ${stats.total_materi ?? 0} materi tersedia untuk dipelajari.`"
            icon="bi-mortarboard"
            tone="student"
        >
            <template #actions>
                <QuickActionBar :actions="quickActions" />
            </template>
        </DashboardHero>

        <MetricStrip :items="metrics" />

        <div class="dashboard-grid dashboard-grid-student">
            <ActionQueue
                title="Tugas Terdekat"
                icon="bi-journal-check"
                :items="taskItems"
                empty-title="Belum ada tugas"
            />
            <ActionQueue
                title="Notifikasi"
                icon="bi-bell-fill"
                :items="notificationItems"
                empty-title="Belum ada notifikasi"
            >
                <template v-if="notifikasi.length" #actions>
                    <Link :href="links.notifikasi" class="app-card-action-link">Lihat Semua</Link>
                </template>
            </ActionQueue>
        </div>

        <section class="workspace-panel">
            <header class="workspace-panel-header">
                <span class="workspace-panel-title">
                    <i class="bi bi-compass" aria-hidden="true"></i>
                    Lanjutkan Belajar
                </span>
                <Link :href="links.materi || '/siswa/materi'" class="app-card-action-link">Buka Materi</Link>
            </header>
            <div class="course-card-grid">
                <CourseCard
                    v-for="(course, index) in courses"
                    :key="course.id"
                    :title="course.title"
                    :subtitle="course.subtitle"
                    :meta="course.meta"
                    :href="course.href"
                    icon="bi-book"
                    :badges="course.badges"
                    :accent="['#2563eb', '#16a34a', '#f59e0b', '#dc2626'][index % 4]"
                />
            </div>
            <ActionQueue v-if="!courses.length" :items="[]" empty-title="Belum ada kelas aktif" icon="bi-book" />
        </section>

        <ActionQueue
            title="Pengumuman"
            icon="bi-megaphone-fill"
            :items="announcementItems"
            empty-title="Tidak ada pengumuman"
        >
            <template v-if="pengumuman.length" #actions>
                <Link :href="links.pengumuman" class="app-card-action-link">Lihat Semua</Link>
            </template>
        </ActionQueue>
    </AppShell>
</template>
