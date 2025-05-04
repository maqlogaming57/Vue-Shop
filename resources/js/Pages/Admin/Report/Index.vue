<script setup>
import {Head} from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import ReportList from "@/Pages/Admin/Report/Report.vue";
defineProps({
    categories:Object,
})
</script>

<template>
    <AdminLayout>
            <Head title="Report" />
            <ReportList :categories="categories"></ReportList>
    </AdminLayout>
</template>
