<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import CardBox from '@/components/CardBox.vue';
import {shallowRef, toRefs, watch, ref} from 'vue';
import { useScroll } from '@vueuse/core'
import ChipSearch from '@/components/ChipSearch.vue';
import Pagination from '@/components/ExplorePagination.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Explore',
        href: '/explore',
    },
];

export interface User{
    id: number;
    nim: string;
    name: string;
    email: string;
    avatar: string;
    type: string;
}

export interface EventUser{
    id: number;
    status: string;
    user: User;
    event_id: number;
    role:{
        id: number;
        name: string;
        quota: number;
        event_id: number;
        skp_detail_id: number;
    }
}

export interface Event {
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

export interface Tag{
    id: number;
    name: string;
    created_at: Date;
    updated_at: Date;
    pivot?:TagPivot;
}

export interface TagPivot{
    id: number;
    created_at: Date;
    updated_at: Date;
    tag_id: number;
    event_id: number;
}

export interface Registration{
    id: number;
    poster: string;
    type: string;
    status: string;
    created_at: Date;
    updated_at: Date;
    event: Event;
}


export interface Pagination<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
    links: PaginationLink[];
}

export interface PaginationLink {
  url: string | null;
  label: string;
  active: boolean;
}

defineProps<{
    registrations: Pagination<Registration>;
    tags: Tag[];
    users: User[];
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

const filterFromCard = ref('');

function handleFilterFromCard(data: string) {
    filterFromCard.value = data
    console.log(filterFromCard.value)
}

function resetFilterFromCard(data:string){
    filterFromCard.value = data
}

</script>

<template>
    <Head title="Explore" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <nav :class="['sticky max-md:top-16 top-12 bg-white dark:bg-gray-800 max-h-min flex flex-col justify-center items-center flex-1 rounded-b-xl border-b border-sidebar-border/70 dark:border-sidebar-border md:min-h-min', showNav ? 'opacity-100 visible' : 'opacity-0 invisible']">
            <ChipSearch :tags="tags" :users="users" :filterFromCard="filterFromCard" @apply-filter="resetFilterFromCard"/>
        </nav>
        <div class="flex my-4 w-full px-3 justify-end">
            <Pagination :pagination="registrations"/>
        </div>
        <div class="flex h-full flex-1 flex-col gap-4 bg rounded-xl px-4">
            <div class="grid auto-rows-min gap-10 px-0 lg:px-10 md:grid-cols-2 xl:grid-cols-3">
                <CardBox v-for="registration in registrations.data" :key="registration.id" :registration="registration" @tag="handleFilterFromCard" @by="handleFilterFromCard"/>
            </div>
        </div>
        <div class="flex my-4 w-full px-3 justify-end">
            <Pagination :pagination="registrations"/>
        </div>
    </AppLayout>
</template>
