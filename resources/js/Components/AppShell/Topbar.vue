<script setup>
import { Link, router } from '@inertiajs/vue3';

defineProps({
    school: { type: Object, required: true },
    user: { type: Object, default: null },
    pageTitle: { type: String, default: 'Dashboard' },
    notifications: { type: Object, default: () => ({}) },
    sidebarOpen: { type: Boolean, default: false },
});

defineEmits(['toggle-sidebar']);

function logout() {
    router.post('/logout');
}

function profileHref(role) {
    if (role === 'admin') {
        return '/admin/pengaturan-akun';
    }

    if (role === 'guru') {
        return '/guru/pengaturan';
    }

    if (role === 'siswa') {
        return '/siswa/pengaturan';
    }

    return null;
}

function profileIsInertia(role) {
    return ['admin', 'guru', 'siswa'].includes(role);
}
</script>

<template>
    <header class="topbar">
        <button
            class="topbar-toggle-btn"
            type="button"
            aria-label="Buka menu"
            aria-controls="sidebar"
            :aria-expanded="sidebarOpen.toString()"
            @click="$emit('toggle-sidebar')"
        >
            <i class="bi bi-list" aria-hidden="true"></i>
        </button>

        <div class="topbar-brand">
            <div class="topbar-logo-icon">
                <img :src="school.logo_url" :alt="`Logo ${school.name}`" class="app-logo-sm" width="32" height="32" decoding="async">
            </div>
            <div class="topbar-title">
                <span class="topbar-title-main">{{ school.app_name }}</span>
                <span class="topbar-title-sub">{{ school.name }}</span>
            </div>
        </div>

        <div class="topbar-context">
            <span class="topbar-context-label">{{ user?.role_label ?? '-' }}</span>
            <span class="topbar-context-title">{{ pageTitle }}</span>
        </div>

        <div class="topbar-actions">
            <div v-if="notifications.route" class="dropdown">
                <button class="btn btn-sm position-relative topbar-icon-btn" type="button" data-bs-toggle="dropdown" title="Notifikasi" aria-label="Notifikasi">
                    <i class="bi bi-bell-fill" aria-hidden="true"></i>
                    <span v-if="notifications.unread_count > 0" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-count">
                        {{ notifications.unread_count > 99 ? '99+' : notifications.unread_count }}
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end notification-menu">
                    <li class="dropdown-item-text d-flex justify-content-between align-items-center">
                        <strong class="notification-title">Notifikasi</strong>
                        <Link
                            v-if="notifications.unread_count > 0 && notifications.mark_all_route"
                            :href="notifications.mark_all_route"
                            method="post"
                            as="button"
                            type="button"
                            class="btn btn-link btn-sm text-decoration-none notification-mark-all"
                        >
                            Tandai semua dibaca
                        </Link>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li v-for="notification in notifications.latest ?? []" :key="notification.id">
                        <Link
                            v-if="notification.mark_read_route"
                            :href="notification.mark_read_route"
                            method="post"
                            as="button"
                            type="button"
                            class="dropdown-item notification-link"
                            :class="{ unread: !notification.is_read }"
                        >
                            <div class="notification-item-title">{{ notification.judul }}</div>
                            <div class="notification-item-message">{{ notification.pesan }}</div>
                            <small class="notification-item-time">{{ notification.created_at }}</small>
                        </Link>
                    </li>
                    <li v-if="!notifications.latest?.length">
                        <span class="dropdown-item-text text-muted text-center notification-action-link">Belum ada notifikasi</span>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><Link :href="notifications.route" class="dropdown-item text-center notification-action-link">Lihat Semua Notifikasi</Link></li>
                </ul>
            </div>

            <span class="d-none d-lg-inline me-2 topbar-user-name">{{ user?.nama_lengkap ?? '-' }}</span>
            <div class="dropdown">
                <button class="btn btn-sm dropdown-toggle topbar-account-btn" type="button" data-bs-toggle="dropdown" aria-label="Menu akun">
                    <i class="bi bi-person-circle me-1" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><span class="dropdown-item-text fw-bold">{{ user?.nama_lengkap ?? '-' }}</span></li>
                    <li><span class="dropdown-item-text text-muted small">{{ user?.username ?? '-' }} - {{ user?.role_label ?? '-' }}</span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li v-if="profileHref(user?.role)">
                        <Link v-if="profileIsInertia(user?.role)" :href="profileHref(user?.role)" class="dropdown-item">
                            <i class="bi bi-person-gear me-1" aria-hidden="true"></i> Pengaturan
                        </Link>
                        <a v-else :href="profileHref(user?.role)" class="dropdown-item">
                            <i class="bi bi-person-gear me-1" aria-hidden="true"></i> Pengaturan
                        </a>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item text-danger" @click="logout">
                            <i class="bi bi-box-arrow-right me-1" aria-hidden="true"></i> Logout
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </header>
</template>
