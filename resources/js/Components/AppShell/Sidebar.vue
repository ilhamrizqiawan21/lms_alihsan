<script setup>
import { computed } from 'vue';
import SidebarLink from './SidebarLink.vue';
import { sidebarMenu } from './sidebarMenu';

const props = defineProps({
    open: { type: Boolean, default: false },
    school: { type: Object, required: true },
    user: { type: Object, default: null },
    capabilities: { type: Object, default: () => ({}) },
});

const path = computed(() => window.location.pathname);
const menu = computed(() => sidebarMenu(props.user?.role, props.capabilities));

function isActive(entry) {
    return entry.activePrefixes?.some((prefix) => path.value === prefix || path.value.startsWith(`${prefix}/`));
}
</script>

<template>
    <aside id="sidebar" class="sidebar" :class="{ 'sidebar-open': open }">
        <div class="sidebar-header">
            <div class="sidebar-logo-icon">
                <img :src="school.logo_url" :alt="`Logo ${school.name}`" class="app-logo-md" width="36" height="36" decoding="async">
            </div>
            <div class="sidebar-logo-text">
                <span class="sidebar-logo-title">{{ school.app_name }}</span>
                <span class="sidebar-logo-sub">{{ school.name }}</span>
            </div>
        </div>

        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><i class="bi bi-person-fill" aria-hidden="true"></i></div>
            <div>
                <div class="sidebar-user-name">{{ user?.nama_lengkap ?? '-' }}</div>
                <div class="sidebar-user-role">{{ user?.role_label ?? '-' }}</div>
            </div>
        </div>

        <nav class="sidebar-nav" aria-label="Navigasi utama">
            <ul class="sidebar-menu">
                <li v-for="(entry, index) in menu" :key="`${entry.type}-${entry.label}-${index}`">
                    <div v-if="entry.type === 'section'" class="nav-section">{{ entry.label }}</div>
                    <SidebarLink
                        v-else
                        :entry="entry"
                        :active="isActive(entry)"
                    />
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-footer-title">{{ school.short_name }}</div>
            <div class="sidebar-footer-sub">Tahun {{ new Date().getFullYear() }}</div>
        </div>
    </aside>
</template>
