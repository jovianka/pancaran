<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import SearchBox from '@/components/SearchBox.vue';
import CardBox from '@/components/CardBox.vue';
import {shallowRef, toRefs, watch} from 'vue';
import { useScroll } from '@vueuse/core'


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Explore',
        href: '/explore',
    },
];

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

const showNav = shallowRef(true)
const { directions } = useScroll(window)
const { top: toTop, bottom: toBottom } = toRefs(directions)

watch([toTop, toBottom], ([newToTop, newToBottom]) => {
  if (newToTop) {
    showNav.value = true
  } else if (newToBottom) {
    showNav.value = false
  }
})


</script>

<template>
    <Head title="Explore" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <nav :class="['sticky max-md:top-16 top-12 bg-white dark:bg-gray-800 max-h-min flex flex-col justify-center items-center flex-1 rounded-b-xl border-b border-sidebar-border/70 dark:border-sidebar-border md:min-h-min', showNav ? 'opacity-100 visible' : 'opacity-0 invisible']">
            <SearchBox />
        </nav>
        <div class="flex h-full flex-1 flex-col gap-4 bg rounded-xl p-4">
            <div class="grid auto-rows-min gap-10 px-0 lg:px-10 md:grid-cols-3">
                <CardBox v-for="registration in registrations" :key="registration.id" :registration="registration"/>
            </div>
        </div>
    </AppLayout>
</template>
