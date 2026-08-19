<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ConfirmDialog from '../Components/AppShell/ConfirmDialog.vue';
import CommandPalette from '../Components/AppShell/CommandPalette.vue';
import NavigationLoading from '../Components/AppShell/NavigationLoading.vue';
import Sidebar from '../Components/AppShell/Sidebar.vue';
import ToastStack from '../Components/AppShell/ToastStack.vue';
import Topbar from '../Components/AppShell/Topbar.vue';
import { sidebarMenu } from '../Components/AppShell/sidebarMenu';

const props = defineProps({
    title: { type: String, default: '' },
});

const page = usePage();
const sidebarOpen = ref(false);
const commandOpen = ref(false);
const school = computed(() => page.props.school ?? {});
const user = computed(() => page.props.auth?.user ?? null);
const notifications = computed(() => page.props.notifications ?? {});
const capabilities = computed(() => page.props.capabilities ?? {});
const pageTitle = computed(() => props.title || document.title.replace(' - LMS Sekolah', '') || 'Dashboard');
const shellClass = computed(() => `app-shell app-shell-${user.value?.role ?? 'guest'}`);
const commandItems = computed(() => sidebarMenu(user.value?.role, capabilities.value));

onMounted(() => {
    sidebarOpen.value = window.innerWidth >= 992;
    window.addEventListener('keydown', openCommandShortcut);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', openCommandShortcut);
});

function openCommandShortcut(event) {
    const target = event.target;
    const typing = ['INPUT', 'TEXTAREA', 'SELECT'].includes(target?.tagName) || target?.isContentEditable;
    if (!typing && (event.key === '/' || ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k'))) {
        event.preventDefault();
        commandOpen.value = true;
    }
}

function closeSidebar() {
    sidebarOpen.value = false;
}
</script>

<template>
    <div :class="shellClass" @keydown.esc.window="closeSidebar">
        <a href="#mainContent" class="skip-link">Lewati ke konten utama</a>

        <div class="sidebar-overlay" :class="{ show: sidebarOpen }" @click="closeSidebar"></div>

        <Topbar
            :school="school"
            :user="user"
            :page-title="pageTitle"
            :notifications="notifications"
            :sidebar-open="sidebarOpen"
            @toggle-sidebar="sidebarOpen = !sidebarOpen"
            @open-command="commandOpen = true"
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
        <CommandPalette v-model:open="commandOpen" :items="commandItems" />
        <NavigationLoading />
    </div>
</template>
