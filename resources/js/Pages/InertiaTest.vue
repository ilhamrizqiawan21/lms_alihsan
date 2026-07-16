<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import PageHeader from '../Components/AppShell/PageHeader.vue';
import { FileInput, SelectInput, TextareaInput, TextInput } from '../Components/Form';
import AppShell from '../Layouts/AppShell.vue';
import { Badge, Button, Card, EmptyState, IconButton, Pagination, StatCard, TableWrapper } from '../Components/UI';

const page = usePage();
const school = page.props.school;
const user = page.props.auth?.user;
const demoForm = ref({
    nama: user?.nama_lengkap ?? '',
    role: user?.role ?? '',
    catatan: '',
    file: null,
});
const demoLinks = [
    { url: null, label: '&laquo; Previous', active: false },
    { url: '/admin/inertia-test?page=1', label: '1', active: true },
    { url: '/admin/inertia-test?page=2', label: '2', active: false },
    { url: '/admin/inertia-test?page=2', label: 'Next &raquo;', active: false },
];
</script>

<template>
    <Head title="Tes Inertia + Vue" />

    <AppShell title="Tes Inertia + Vue">
        <PageHeader
            title="Tes Inertia + Vue"
            subtitle="Fondasi Inertia sudah aktif tanpa memindahkan halaman Blade lama."
            icon="bi-lightning-charge-fill"
        >
            <template #actions>
                <Button color="outline-primary" icon="bi-arrow-clockwise" href="/admin/inertia-test">Refresh</Button>
                <IconButton icon="bi-check2-circle" label="Contoh tombol ikon" color="outline-success" />
            </template>
        </PageHeader>

        <div class="stats-grid">
            <StatCard label="Status" value="Aktif" icon="bi-check-circle-fill" />
            <StatCard label="Role" :value="user?.role_label ?? '-'" icon="bi-person-badge-fill" />
        </div>

        <Card title="Shared Props" icon="bi-mortarboard-fill">
            <template #actions>
                <Badge color="success" icon="bi-check-circle-fill">Vue Shell</Badge>
            </template>
            <div>
                <dl class="row mb-0">
                    <dt class="col-sm-3">Sekolah</dt>
                    <dd class="col-sm-9">{{ school?.name ?? '-' }}</dd>
                    <dt class="col-sm-3">User</dt>
                    <dd class="col-sm-9">{{ user?.nama_lengkap ?? '-' }}</dd>
                    <dt class="col-sm-3">Username</dt>
                    <dd class="col-sm-9">{{ user?.username ?? '-' }}</dd>
                </dl>
            </div>
        </Card>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <Card title="Komponen Table" icon="bi-table" body-class="p-0">
                    <TableWrapper>
                        <table class="table table-hover app-table mb-0">
                            <thead>
                                <tr>
                                    <th>Komponen</th>
                                    <th>Status</th>
                                    <th class="table-action-column">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Card, Badge, Button</td>
                                    <td><Badge color="success">Siap</Badge></td>
                                    <td class="table-action-column"><IconButton icon="bi-eye" label="Lihat" /></td>
                                </tr>
                                <tr>
                                    <td>Form Inputs</td>
                                    <td><Badge color="primary">Siap</Badge></td>
                                    <td class="table-action-column"><IconButton icon="bi-pencil" label="Edit" color="outline-primary" /></td>
                                </tr>
                            </tbody>
                        </table>
                    </TableWrapper>
                    <template #footer>
                        <Pagination :links="demoLinks" />
                    </template>
                </Card>
            </div>

            <div class="col-lg-5 mb-4">
                <Card title="Komponen Form" icon="bi-ui-checks">
                    <TextInput v-model="demoForm.nama" name="nama" label="Nama" required />
                    <SelectInput
                        v-model="demoForm.role"
                        name="role"
                        label="Role"
                        :options="{ admin: 'Admin', guru: 'Guru', siswa: 'Siswa', kepala_sekolah: 'Kepala Sekolah' }"
                        placeholder="Pilih role"
                    />
                    <TextareaInput v-model="demoForm.catatan" name="catatan" label="Catatan" placeholder="Contoh textarea Vue" />
                    <FileInput v-model="demoForm.file" name="lampiran" label="Lampiran" accept-label=".pdf, .jpg, .png" max-size="2MB" />
                </Card>
            </div>
        </div>

        <EmptyState
            title="Komponen kosong siap dipakai"
            message="Empty state Vue mengikuti class visual yang sama dengan Blade."
            icon="bi-inbox"
        />
    </AppShell>
</template>
