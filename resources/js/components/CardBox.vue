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
    tag: {
        id: number;
        name: string;
    }
}

// Terima props event bertipe Event
const props = defineProps<{
  event: Event
}>();

</script>


<template>
    <article class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
        <div class="flex justify-between items-center mb-5 text-gray-500">
            <Link href="#" class="bg-blue-100 text-primary-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-primary-200 dark:text-primary-800 hover:underline">
                <svg class="mr-1 w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                    <path d="m6 10.5237-2.27075.6386C3.29797 11.2836 3 11.677 3 12.125V20c0 .5523.44772 1 1 1h2V10.5237Zm12 0 2.2707.6386c.4313.1213.7293.5147.7293.9627V20c0 .5523-.4477 1-1 1h-2V10.5237Z"/>
                    <path fill-rule="evenodd" d="M12.5547 3.16795c-.3359-.22393-.7735-.22393-1.1094 0l-6.00002 4c-.45952.30635-.5837.92722-.27735 1.38675.30636.45953.92723.5837 1.38675.27735L8 7.86853V21h8V7.86853l1.4453.96352c.0143.00957.0289.01873.0435.02746.1597.09514.3364.14076.5112.1406.3228-.0003.6395-.15664.832-.44541.3064-.45953.1822-1.0804-.2773-1.38675l-6-4ZM10 12c0-.5523.4477-1 1-1h2c.5523 0 1 .4477 1 1s-.4477 1-1 1h-2c-.5523 0-1-.4477-1-1Zm1-4c-.5523 0-1 .44772-1 1s.4477 1 1 1h2c.5523 0 1-.44772 1-1s-.4477-1-1-1h-2Z" clip-rule="evenodd"/>
                </svg>
                {{ event.tag.name ?? 'Tidak ada tag' }}
            </Link>
            <span class="text-sm">{{dayjs(event.created_at).fromNow()}}</span>
        </div>
        <div class="w-full aspect-video overflow-hidden rounded-lg my-5">
            <img class="w-full h-auto" src="https://images.unsplash.com/photo-1584448141569-69f342da535c" alt="poster">
        </div>
        <h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><Link class="hover:underline" href="#"> {{ event.name ?? 'Tidak ada nama' }}</Link></h2>
        <p class="max-h-[20vh] line-clamp-3 mb-5 font-light text-gray-500 dark:text-gray-400">{{ event.description ?? '' }}</p>
        <div class="flex justify-between items-center">
            <Link href="#" class="flex items-center space-x-4 hover:underline">
                <img class="w-7 h-7 rounded-full" src="https://d3t3ozftmdmh3i.cloudfront.net/production/podcast_uploaded_nologo400/2042763/2042763-1625923912928-d2e76fac0ba79.jpg" alt="Admin Profile" />
                <span class="font-medium dark:text-white" >
                    {{ event.event_users?.find(eu => eu.role?.name === 'admin')?.user?.name ?? 'Tidak ada admin' }}
                </span>
            </Link>
            <Link href="#" class="inline-flex items-center font-medium text-black dark:text-white dark:text-primary-500 hover:underline">
                Read more
                <svg class="ml-2 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
            </Link>
        </div>
    </article>
</template>
