<script setup>
import { Head, router } from '@inertiajs/vue3';
import { reactive } from 'vue';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import { SelectInput, TextInput } from '../../../Components/Form';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Card, EmptyState, IconButton, Pagination, TableWrapper } from '../../../Components/UI';

const props = defineProps({
    users: { type: Object, default: () => ({ data: [], links: [], meta: {} }) },
    roles: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const filterForm = reactive({
    search: props.filters.search ?? '',
    role_id: props.filters.role_id ?? '',
});

function roleLabel(role) {
    return role ? role.replaceAll('_', ' ').replace(/\b\w/g, (char) => char.toUpperCase()) : '-';
}

function applyFilters() {
    router.get('/admin/users', cleanFilters(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function resetFilters() {
    filterForm.search = '';
    filterForm.role_id = '';
    router.get('/admin/users', {}, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function cleanFilters() {
    return Object.fromEntries(Object.entries(filterForm).filter(([, value]) => value !== '' && value !== null));
}

async function toggleActive(user) {
    const action = user.is_active ? 'Nonaktifkan' : 'Aktifkan';
    const confirmed = await window.confirmDialog?.(`${action} user ini?`, {
        title: `${action} User`,
        confirmText: `Ya, ${action.toLowerCase()}`,
    });

    if (!confirmed) {
        return;
    }

    router.post(`/admin/users/${user.id}/toggle-active`, {}, {
        preserveScroll: true,
        preserveState: true,
    });
}

async function destroy(user) {
    const confirmed = await window.confirmDialog?.('Hapus user ini?', {
        title: 'Hapus User',
        confirmText: 'Ya, hapus',
        danger: true,
    });

    if (!confirmed) {
        return;
    }

    router.delete(`/admin/users/${user.id}`, {
        preserveScroll: true,
        preserveState: true,
    });
}
</script>

<template>
    <Head title="Guru & Staf" />

    <AppShell title="Guru & Staf">
        <PageHeader
            title="Guru & Staf"
            subtitle="Kelola akun guru, staf, dan admin sekolah."
            icon="bi-people-fill"
        />

        <Card title="Daftar Guru & Staf" icon="bi-people-fill">
            <template #actions>
                <a href="/admin/users/create" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Tambah Guru/Staf
                </a>
            </template>

            <form class="row g-2 app-table-filter mb-3" @submit.prevent="applyFilters">
                <div class="col-md-4">
                    <TextInput
                        v-model="filterForm.search"
                        name="search"
                        wrapper-class="mb-0"
                        placeholder="Cari username/nama..."
                    />
                </div>
                <div class="col-md-3">
                    <SelectInput
                        v-model="filterForm.role_id"
                        name="role_id"
                        wrapper-class="mb-0"
                        :options="roles.map((role) => ({ value: role.id, label: roleLabel(role.nama_role) }))"
                        placeholder="Semua Role"
                    />
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-primary w-100" type="submit">
                        <i class="bi bi-search me-1" aria-hidden="true"></i> Cari
                    </button>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-sm btn-outline-secondary w-100" type="button" @click="resetFilters">
                        <i class="bi bi-x-circle me-1" aria-hidden="true"></i> Reset
                    </button>
                </div>
            </form>

            <TableWrapper v-if="users.data?.length">
                <table class="table table-hover app-table mb-0">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="table-action-column">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users.data" :key="user.id">
                            <td><strong>{{ user.username }}</strong></td>
                            <td>{{ user.nama_lengkap }}</td>
                            <td>{{ user.email ?? '-' }}</td>
                            <td><Badge color="primary">{{ roleLabel(user.role?.nama_role) }}</Badge></td>
                            <td>
                                <Badge :color="user.is_active ? 'success' : 'danger'">
                                    {{ user.is_active ? 'Aktif' : 'Nonaktif' }}
                                </Badge>
                            </td>
                            <td class="table-action-column">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a
                                        :href="`/admin/users/${user.id}/edit`"
                                        class="btn btn-sm btn-warning btn-icon"
                                        :title="`Edit ${user.nama_lengkap}`"
                                        :aria-label="`Edit ${user.nama_lengkap}`"
                                    >
                                        <i class="bi bi-pencil" aria-hidden="true"></i>
                                    </a>
                                    <IconButton
                                        :icon="user.is_active ? 'bi-pause-fill' : 'bi-play-fill'"
                                        :label="`${user.is_active ? 'Nonaktifkan' : 'Aktifkan'} ${user.nama_lengkap}`"
                                        :color="user.is_active ? 'outline-secondary' : 'outline-success'"
                                        @click="toggleActive(user)"
                                    />
                                    <IconButton
                                        icon="bi-trash"
                                        :label="`Hapus ${user.nama_lengkap}`"
                                        color="outline-danger"
                                        @click="destroy(user)"
                                    />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </TableWrapper>

            <EmptyState v-else title="Tidak ada data guru atau staf" icon="bi-people" />

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                <div class="text-muted small">
                    <template v-if="users.meta?.total">
                        Menampilkan {{ users.meta.from }}-{{ users.meta.to }} dari {{ users.meta.total }} data
                    </template>
                </div>
                <Pagination :links="users.links ?? []" />
            </div>
        </Card>
    </AppShell>
</template>
