<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import SearchBox from '@/components/SearchBox.vue';
import CardBox from '@/components/CardBox.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Explore',
        href: '/explore',
    },
];
</script>

<template>
    <Head title="Explore" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative max-h-min sm:flex items-center flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <SearchBox />
                <div class="flex flex-1 gap-[3vw] min-w-min px-2 w-full justify-center mb-2 lg:mb-0 text-xs lg:text-base">
                    <a href="#">All</a>
                    <a href="#">Committee</a>
                    <a href="#">Seminar</a>
                    <a href="#">Competition</a>
                    <a href="#">Volunteer</a>
                </div>
            </div>
            <div class="grid auto-rows-min gap-10 px-0 lg:px-10 md:grid-cols-3">
                <CardBox />
                <CardBox />
                <CardBox />
                <CardBox />
                <CardBox />
                <CardBox />
            </div>
        </div>
    </AppLayout>
</template>
