<script setup>
import { Head } from '@inertiajs/vue3';
import PageHeader from '../../../Components/AppShell/PageHeader.vue';
import CalendarWorkspace from '../../../Components/Calendar/CalendarWorkspace.vue';
import AcademicTimeline from '../../../Components/Calendar/AcademicTimeline.vue';
import AppShell from '../../../Layouts/AppShell.vue';

defineProps({
    calendar: { type: Object, required: true },
    monthEvents: { type: Array, default: () => [] },
    timelineEvents: { type: Array, default: () => [] },
    pageTitle: { type: String, default: 'Kalender dan Monitoring Event Sekolah' },
});
</script>

<template>
    <Head :title="pageTitle" />
    <AppShell title="Kalender">
        <PageHeader :title="pageTitle" icon="bi-calendar3" subtitle="Pantau agenda sekolah tanpa mengubah data kalender." />
        <CalendarWorkspace :calendar="calendar" :month-events="monthEvents" :read-only="true" fixed-scope="school" />
        <AcademicTimeline :events="timelineEvents" class="mt-4" />
    </AppShell>
</template>
