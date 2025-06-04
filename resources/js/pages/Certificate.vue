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
  id: number
  skp: number
  // properti lainnya...
}

const certificates = ref<Certificate[]>([
  { id: 1, skp: 5 },
  { id: 2, skp: 10 },
  { id: 3, skp: 20 },
  // ...dst
])

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
            <skpTotal :certificates="certificates"/>
        </div>
        <div class="flex h-full flex-1 flex-col gap-4 bg rounded-xl p-4">
            <div class="grid auto-rows-min gap-10 px-0 lg:px-10 md:grid-cols-3">
            </div>
        </div>
    </AppLayout>
</template>