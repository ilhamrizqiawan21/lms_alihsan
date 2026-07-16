<script setup>
import { Head } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import CalendarWorkspace from '../../../Components/Calendar/CalendarWorkspace.vue';
import AppShell from '../../../Layouts/AppShell.vue';

defineProps({
    calendar: { type: Object, required: true },
    monthEvents: { type: Array, default: () => [] },
    pageTitle: { type: String, default: 'Kalender & Reminder' },
});
</script>

<template>
    <Head :title="pageTitle" />

    <AppShell title="Kalender">
        <PageHeader :title="pageTitle" icon="bi-calendar3" />
        <CalendarWorkspace
            :calendar="calendar"
            :month-events="monthEvents"
            read-only
        />
    </AppShell>
</template>
