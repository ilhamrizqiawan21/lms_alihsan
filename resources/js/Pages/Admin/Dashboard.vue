<script setup>
import { Head } from '@inertiajs/vue3';
import PageHeader from '../../Components/AppShell/PageHeader.vue';
import AppShell from '../../Layouts/AppShell.vue';
import { Badge, Card, EmptyState, StatCard, TableWrapper } from '../../Components/UI';

defineProps({
    statistik: { type: Object, default: () => ({}) },
    loginTerbaru: { type: Array, default: () => [] },
    pengumuman: { type: Array, default: () => [] },
});

function roleBadgeColor(role) {
    return {
        admin: 'danger',
        guru: 'primary',
        siswa: 'success',
        kepala_sekolah: 'warning',
    }[role] ?? 'secondary';
}
</script>

<template>
    <Head title="Dashboard Admin" />

    <AppShell title="Dashboard Admin">
        <PageHeader title="Dashboard Admin" icon="bi-speedometer2">
            <template #actions>
                <nav class="breadcrumb mb-0" aria-label="breadcrumb">
                    <span class="breadcrumb-item active">Dashboard</span>
                </nav>
            </template>
        </PageHeader>

        <div class="stats-grid">
            <StatCard label="Total Siswa" :value="statistik.total_siswa ?? 0" icon="bi-mortarboard-fill" />
            <StatCard label="Total Guru" :value="statistik.total_guru ?? 0" icon="bi-person-workspace" />
            <StatCard label="Total Kelas" :value="statistik.total_kelas ?? 0" icon="bi-building" />
            <StatCard label="Mata Pelajaran" :value="statistik.total_mapel ?? 0" icon="bi-book-fill" />
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <Card title="Login Terbaru" icon="bi-clock-history" body-class="p-0">
                    <TableWrapper v-if="loginTerbaru.length">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Role</th>
                                    <th>Waktu</th>
                                    <th>IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="log in loginTerbaru" :key="log.id">
                                    <td><strong>{{ log.nama_lengkap }}</strong></td>
                                    <td><Badge :color="roleBadgeColor(log.role)">{{ log.role }}</Badge></td>
                                    <td class="text-muted small">{{ log.login_time ?? '-' }}</td>
                                    <td class="text-muted small">{{ log.ip_address ?? '-' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <EmptyState
                        v-else
                        title="Belum ada data login"
                        icon="bi-clock-history"
                    />
                </Card>
            </div>

            <div class="col-md-6 mb-4">
                <Card title="Pengumuman Terbaru" icon="bi-megaphone-fill" body-class="p-0">
                    <div v-if="pengumuman.length">
                        <div
                            v-for="item in pengumuman"
                            :key="item.id"
                            style="border-left:4px solid var(--primary-500); padding:0.8rem 1rem; border-bottom:1px solid var(--gray-200);"
                        >
                            <strong>{{ item.judul }}</strong>
                            <div class="text-muted small mt-1">
                                {{ item.created_at ?? '-' }} - {{ item.creator }}
                            </div>
                            <div class="mt-1" style="font-size:0.85rem;">{{ item.isi }}</div>
                        </div>
                    </div>
                    <EmptyState
                        v-else
                        title="Belum ada pengumuman"
                        icon="bi-megaphone"
                    />
                </Card>
            </div>
        </div>
    </AppShell>
</template>
