<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import SearchBox from '@/components/SearchBox.vue';
import CardBox from '@/components/CardBox.vue';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import {Link} from '@inertiajs/vue3';

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

interface eventData {
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
    tag: {
        id: number;
        name: string;
    }
}

defineProps<{
  events: eventData[]
}>();

const showNav= ref(true)
let lastScrollTop = 0

const handleScroll = () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop

    if (scrollTop > lastScrollTop + 10) {
        showNav.value = false
    } else if (scrollTop < lastScrollTop) {
        showNav.value = true
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
})

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
    <Head title="Explore" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <nav :class="['sticky max-md:top-16 top-12 bg-white dark:bg-gray-800 max-h-min flex flex-col justify-center items-center flex-1 rounded-b-xl border-b border-sidebar-border/70 dark:border-sidebar-border md:min-h-min', showNav ? 'opacity-100 visible' : 'opacity-0 invisible']">
            <SearchBox />
            <div class="flex flex-1 gap-4 min-w-min p-2 w-full justify-center mb-2 lg:mb-0 text-xs lg:text-base border-t-1 border-gray-150 dark:border-gray-700">
                <span  class="hidden sm:block">Show only:</span>
                <div class="flex justify-center gap-2">
                    <input type="radio" id="all" name="tag" value="all" checked class="focus:ring-blue-600 focus:fill-white">
                    <label for="all">All</label>
                </div>
                <div class="flex justify-center gap-2">
                    <input type="radio" id="committee" name="tag" value="committee">
                    <label for="committee">Committee</label>
                </div>
                <div class="flex justify-center gap-2">
                    <input type="radio" id="competition" name="tag" value="competition">
                    <label for="competition">Competition</label>
                </div>
                <div class="flex justify-center gap-2">
                    <input type="radio" id="volunteer" name="tag" value="volunteer">
                    <label for="volunteer">Volunteer</label>
                </div>
            </div>
        </nav>
        <div class="flex h-full flex-1 flex-col gap-4 bg rounded-xl p-4">
            <div class="grid auto-rows-min gap-10 px-0 lg:px-10 md:grid-cols-3">
                <CardBox v-for="event in events" :key="event.id" :event="event"/>
            </div>
        </div>

    </AppLayout>
</template>
