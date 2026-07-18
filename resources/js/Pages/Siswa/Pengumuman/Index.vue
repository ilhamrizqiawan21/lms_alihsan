<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import AppShell from '../../../Layouts/AppShell.vue';
import { Badge, Card, EmptyState, Pagination } from '../../../Components/UI';

defineProps({
    pengumuman: { type: Object, default: () => ({ data: [], links: [] }) },
});
</script>

<template>
    <Head title="Pengumuman" />

    <AppShell title="Pengumuman">
        <PageHeader
            title="Pengumuman"
            icon="bi-megaphone-fill"
            subtitle="Informasi terbaru dari sekolah dan kelas."
        />

        <Card title="Daftar Pengumuman" icon="bi-list-ul" body-class="p-0">
            <div v-if="pengumuman.data?.length" class="announcement-list">
                <Link
                    v-for="item in pengumuman.data"
                    :key="item.id"
                    :href="item.show_url"
                    class="announcement-item"
                >
                    <div class="announcement-main">
                        <div class="announcement-title">{{ item.judul }}</div>
                        <div class="announcement-meta">
                            {{ item.creator }} - {{ item.created_at }}
                        </div>
                        <div class="announcement-text">{{ item.isi }}</div>
                    </div>
                    <div class="announcement-side">
                        <Badge color="info text-dark">{{ item.target_label }}</Badge>
                        <span class="read-link">Selengkapnya</span>
                    </div>
                </Link>
            </div>
            <EmptyState v-else title="Belum ada pengumuman" icon="bi-megaphone" />

            <template v-if="pengumuman.links?.length" #footer>
                <Pagination :links="pengumuman.links" />
            </template>
        </Card>
    </AppShell>
</template>

<style scoped>
.announcement-list {
    display: flex;
    flex-direction: column;
}

.announcement-item {
    align-items: flex-start;
    border-bottom: 1px solid var(--gray-200);
    color: var(--text-body);
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    padding: 1rem;
    text-decoration: none;
}

.announcement-item:hover {
    background: var(--primary-50);
    color: var(--text-strong);
}

.announcement-item:last-child {
    border-bottom: 0;
}

.announcement-main {
    min-width: 0;
}

.announcement-title {
    color: var(--text-strong);
    font-weight: 700;
    margin-bottom: 0.2rem;
}

.announcement-meta,
.announcement-text {
    color: var(--text-muted);
    font-size: 0.82rem;
}

.announcement-text {
    margin-top: 0.5rem;
}

.announcement-side {
    align-items: flex-end;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    gap: 0.5rem;
}

.read-link {
    color: var(--primary-600);
    font-size: 0.76rem;
    font-weight: 700;
}

@media (max-width: 575.98px) {
    .announcement-item {
        flex-direction: column;
    }

    .announcement-side {
        align-items: flex-start;
    }
}
</style>
