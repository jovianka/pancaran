<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';

import AppLayout from '@/layouts/AppLayout.vue';
import DefaultPageLayout from '@/layouts/DefaultPageLayout.vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Activity',
        href: '/activity',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Activity" />
        <DefaultPageLayout>
            <Heading title="Activity" description="Manage your events" />
        </DefaultPageLayout>
    </AppLayout>
</template>
