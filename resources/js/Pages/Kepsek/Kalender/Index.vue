<script setup>
import { Head } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import CalendarWorkspace from '../../../Components/Calendar/CalendarWorkspace.vue';
import AppShell from '../../../Layouts/AppShell.vue';

defineProps({
    calendar: { type: Object, required: true },
    monthEvents: { type: Array, default: () => [] },
    storeUrl: { type: String, required: true },
    createTitle: { type: String, default: 'Tambah Event Sekolah' },
    fixedScope: { type: String, default: 'school' },
    pageTitle: { type: String, default: 'Kalender & Monitoring Event Sekolah' },
});
</script>

<template>
    <Head :title="pageTitle" />

    <AppShell title="Kalender">
        <PageHeader :title="pageTitle" icon="bi-calendar3" />
        <CalendarWorkspace
            :calendar="calendar"
            :month-events="monthEvents"
            :store-url="storeUrl"
            :create-title="createTitle"
            :fixed-scope="fixedScope"
        />
    </AppShell>
</template>
