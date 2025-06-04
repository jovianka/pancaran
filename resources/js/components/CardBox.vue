<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime);

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


// Terima props event bertipe Event
const props = defineProps<{
  registration: Registration
}>();

</script>


<template>
    <article class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full aspect-video overflow-hidden rounded-lg mb-5">
            <img class="w-full h-auto" src="https://images.unsplash.com/photo-1584448141569-69f342da535c" alt="poster">
        </div>
        <div class="space-y-2">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><Link class="hover:underline" href="#"> {{ registration.event.name ?? 'Tidak ada nama' }}</Link></h2>
            <Link href="#" class="flex items-center space-x-2 hover:underline">
                <img class="w-7 h-7 rounded-full" :src="registration?.event?.event_users?.find(eu => eu.role?.name === 'admin')?.user?.avatar ?? 'https://d3t3ozftmdmh3i.cloudfront.net/production/podcast_uploaded_nologo400/2042763/2042763-1625923912928-d2e76fac0ba79.jpg'" alt="Admin Profile" />
                <span class="font-medium dark:text-white" >
                    {{ registration.event.event_users?.find(eu => eu.role?.name === 'admin')?.user?.name ?? 'Tidak ada admin' }}
                </span>
            </Link>
            <p class="max-h-[20vh] line-clamp-3 font-light text-gray-500 dark:text-gray-400">{{ registration.event.description ?? '' }}</p>
            <div class="flex gap-2 w-max">
                <Link v-for="tag in registration.event.tags" :key="tag.id" href="#" class="bg-blue-100 text-gray-500 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-primary-200 dark:text-primary-800 hover:underline">
                    {{ tag.name ?? 'Tidak ada tag' }}
                </Link>
            </div>
        </div>
        <div class="flex justify-between items-center mt-5">
            <button class="bg-blue-500 px-4 py-1 rounded-md">
                <Link href="#" class="inline-flex items-center font-medium text-sm text-white dark:text-primary-500 hover:underline">
                    Read more
                    <!-- <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg> -->
                </Link>
            </button>
            <span class="w-fit text-sm text-gray-500">{{dayjs(registration.event.created_at).fromNow()}}</span>
        </div>
    </article>
</template>
