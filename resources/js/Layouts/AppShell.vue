<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ConfirmDialog from '../Components/AppShell/ConfirmDialog.vue';
import NavigationLoading from '../Components/AppShell/NavigationLoading.vue';
import Sidebar from '../Components/AppShell/Sidebar.vue';
import ToastStack from '../Components/AppShell/ToastStack.vue';
import Topbar from '../Components/AppShell/Topbar.vue';

const props = defineProps({
    title: { type: String, default: '' },
});

const page = usePage();
const sidebarOpen = ref(window.innerWidth >= 992);
const school = computed(() => page.props.school ?? {});
const user = computed(() => page.props.auth?.user ?? null);
const notifications = computed(() => page.props.notifications ?? {});
const capabilities = computed(() => page.props.capabilities ?? {});
const pageTitle = computed(() => props.title || document.title.replace(' - LMS Sekolah', '') || 'Dashboard');

function closeSidebar() {
    sidebarOpen.value = false;
}
</script>

<template>
    <div @keydown.esc.window="closeSidebar">
        <a href="#mainContent" class="skip-link">Lewati ke konten utama</a>

        <div class="sidebar-overlay" :class="{ show: sidebarOpen }" @click="closeSidebar"></div>

        <Topbar
            :school="school"
            :user="user"
            :page-title="pageTitle"
            :notifications="notifications"
            :sidebar-open="sidebarOpen"
            @toggle-sidebar="sidebarOpen = !sidebarOpen"
        />

        <Sidebar
            :open="sidebarOpen"
            :school="school"
            :user="user"
            :capabilities="capabilities"
        />

        <main id="mainContent" class="main-content" tabindex="-1">
            <div class="page-content">
                <slot />
            </div>
            <footer>
                &copy; {{ new Date().getFullYear() }} {{ school.name }} - {{ school.app_name }}
            </footer>
        </main>

        <ToastStack />
        <ConfirmDialog />
        <NavigationLoading />
    </div>
</template>
