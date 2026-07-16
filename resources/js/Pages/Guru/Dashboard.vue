<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import PageHeader from '../../Components/AppShell/PageHeader.vue';
import AppShell from '../../Layouts/AppShell.vue';
import { Card, EmptyState, StatCard, TableWrapper } from '../../Components/UI';

const page = usePage();
const user = page.props.auth?.user;

defineProps({
    statistik: { type: Object, default: () => ({}) },
    kelasMapel: { type: Array, default: () => [] },
    pengumuman: { type: Array, default: () => [] },
    notifikasi: { type: Array, default: () => [] },
    unreadNotifCount: { type: Number, default: 0 },
});
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
