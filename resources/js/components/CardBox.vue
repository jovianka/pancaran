<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import { EventUser, Event, Registration, Tag, TagPivot  } from '@/pages/Explore.vue';

dayjs.extend(relativeTime);

// Terima props event bertipe Event
defineProps<{
  registration: Registration
}>();

const emit = defineEmits (['tag', 'by'])

function sendTag(data:string){
    const buttonData = "tag:" + data;
    emit('tag', buttonData)

}

function sendOrganization(data:string){
    const buttonData = "by:" + data;
    // console.log(buttonData)
    emit('by', buttonData)

}

</script>


<template>
    <article class="p-6 bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-800 dark:border-gray-700">
        <div class="w-full aspect-video overflow-hidden rounded-lg mb-5 bg-gray-500">
            <img class="w-full h-auto" :src="registration?.poster ?? 'https://images.unsplash.com/photo-1584448141569-69f342da535c'" alt="poster">
            <!-- <div v-if="error" class="placeholder">
            </div> -->
        </div>
        <div class="space-y-2">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white"><Link class="hover:underline" href="#"> {{ registration.event.name ?? 'Tidak ada nama' }}</Link></h2>
            <button @click="sendOrganization(registration.event.event_users?.find(eu => eu.role?.name === 'admin')?.user?.name ?? '') " class="flex items-center space-x-2">
                <img class="w-7 h-7 rounded-full" :src="registration?.event?.event_users?.find(eu => eu.role?.name === 'admin')?.user?.avatar ?? 'https://d3t3ozftmdmh3i.cloudfront.net/production/podcast_uploaded_nologo400/2042763/2042763-1625923912928-d2e76fac0ba79.jpg'" alt="Admin Profile" />
                <span class="font-medium dark:text-white hover:underline" >
                    {{ registration.event.event_users?.find(eu => eu.role?.name === 'admin')?.user?.name ?? 'Tidak ada admin' }}
                </span>
            </button>
            <p class="max-h-[20vh] line-clamp-3 font-light text-gray-500 dark:text-gray-400">{{ registration.event.description ?? '' }}</p>
            <div class="flex flex-wrap gap-2 w-full">
                <button @click="sendTag(tag.name)" v-for="tag in registration.event.tags.slice(0, 3)" :key="tag.id" class="bg-blue-100 text-gray-500 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-primary-200 dark:text-primary-800 hover:underline">
                    {{ tag.name ?? 'Tidak ada tag' }}
                </button>
            </div>
        </div>
        <div class="flex justify-between items-center mt-5">
            <button class="bg-blue-500 px-4 py-1 rounded-md">
                <Link :href="`/explore/${registration.event.id}`" class="inline-flex items-center font-medium text-sm text-white dark:text-primary-500 hover:underline">
                    Read more
                </Link>
            </button>
            <span class="w-fit text-sm text-gray-500">{{dayjs(registration.event.created_at).fromNow()}}</span>
        </div>
    </article>
</template>
