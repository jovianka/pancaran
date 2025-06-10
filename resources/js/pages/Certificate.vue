<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import CardBoxCertificate from '@/components/CardBoxCertificate.vue';
import SearchBox from '@/components/SearchBoxCertificate.vue';
import { ref } from 'vue'
import skpTotal from '@/components/skpTotal.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Certificate',
        href: '/certificate',
    },
];

interface Certificate {
  id: number;
  skp: number;
}

interface EventUser{
    id: number;
    status: string;
    user: {
        id: number;
        name: string;
        avatar: string;
    }
    event_id: number;
    role:{
        id: number;
        name: string;
        quota: number;
        event_id: number;
        skp_detail_id: number;
    }
}

interface Event {
    id: number;
    name: string;
    description: string;
    poster: string;
    event_level: string;
    requirements: string;
    start_date: Date;
    end_date: Date;
    job_description: string;
    created_at: Date;
    updated_at: Date;
    parent_id: number | null;
    event_users : EventUser[];
    tags: Tag[];
}

interface Tag{
    id: number;
    name: string;
    created_at: Date;
    updated_at: Date;
    pivot?:TagPivot;
}

interface TagPivot{
    id: number;
    created_at: Date;
    updated_at: Date;
    tag_id: number;
    event_id: number;
}

interface Registration{
    id: number;
    poster: string;
    type: string;
    status: string;
    created_at: Date;
    updated_at: Date;
    event: Event;
}

defineProps<{
    registrations: Registration[]
    certificate: Certificate[]
}>();

</script>

<template>
    <Head title="Certificate" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 sm:px-8 pt-6">
            <h2 class="text-xl font-semibold tracking-tight">Certificate</h2>
        </div>
        <div class="flex flex-col md:flex-row md:justify-between md:items-center w-full gap-4 mb-4 pr-4">
            <SearchBox />
            <skpTotal :certificate="certificate"/>
        </div>
        <div class="flex h-full flex-1 flex-col gap-4 bg rounded-xl p-4">
            <div class="grid auto-rows-min gap-10 px-0 lg:px-10 md:grid-cols-3">
                <article class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full aspect-video overflow-hidden rounded-lg mb-5">
            <img class="w-full h-auto" src="https://images.unsplash.com/photo-1613376023733-0a73315d9b06?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="poster">
        </div>
        <div class="space-y-2">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Program Kreativitas Anime<Link class="hover:underline" href="#"><!--{{ registration.event.name ?? 'Tidak ada nama' }}--></Link></h2>
            <div href="#" class="flex items-center space-x-2">
                <!-- <img class="w-7 h-7 rounded-full" :src="registration?.event?.event_users?.find(eu => eu.role?.name === 'admin')?.user?.avatar ?? 'https://d3t3ozftmdmh3i.cloudfront.net/production/podcast_uploaded_nologo400/2042763/2042763-1625923912928-d2e76fac0ba79.jpg'" alt="Admin Profile" /> -->
                <!-- <span class="font-medium dark:text-white" >
                    {{ registration.event.event_users?.find(eu => eu.role?.name === 'admin')?.user?.name ?? 'Tidak ada admin' }}
                </span> -->
                <p class="font-light text-gray-500 dark:text-gray-400">SKP: 34 <!--{{ certificate.skp }}--></p>
            </div>
            <div class="flex flex-wrap gap-2 w-full">
                <!-- <div v-for="tag in registration.event.tags.slice(0, 3)" :key="tag.id" class="bg-blue-100 text-gray-500 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-primary-200 dark:text-primary-800">
                    {{ tag.name ?? 'Tidak ada tag' }}
                </div> -->
            </div>
        </div>
    </article>
            </div>
        </div>
    </AppLayout>
</template>