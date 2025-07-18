<script setup lang="ts">
import { Registration } from '@/pages/Explore.vue';
import { Link } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime);

// Terima props event bertipe Event
const props = defineProps<{
    registration: Registration;
}>();

const emit = defineEmits(['tag', 'by']);

function sendTag(data: string) {
    const buttonData = 'tag:' + data;
    emit('tag', buttonData);
}

function sendOrganization(data: string) {
    const buttonData = 'by:' + data;
    // console.log(buttonData)
    emit('by', buttonData);
}
</script>

<template>
    <article class="rounded-lg border border-gray-200 bg-white p-6 shadow-md dark:border-gray-700 dark:bg-gray-800">
        <div class="mb-5 aspect-video w-full overflow-hidden rounded-lg bg-gray-500">
            <img
                class="h-auto w-full"
                :src="registration?.poster ? route('registration.getPoster', { id: props.registration.id }) : ''"
                alt="poster"
            />
            <!-- <div v-if="error" class="placeholder">
            </div> -->
        </div>
        <div class="space-y-2">
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                <Link class="hover:underline" href="#"> {{ registration.event.name ?? 'Tidak ada nama' }}</Link>
            </h2>
            <button
                @click="sendOrganization(registration.event.event_users?.find((eu) => eu.role?.name === 'admin')?.user?.name ?? '')"
                class="flex items-center space-x-2"
            >
                <img
                    class="h-7 w-7 rounded-full"
                    :src="
                        registration?.event?.event_users?.find((eu) => eu.role?.name === 'admin')?.user?.avatar ??
                        'https://d3t3ozftmdmh3i.cloudfront.net/production/podcast_uploaded_nologo400/2042763/2042763-1625923912928-d2e76fac0ba79.jpg'
                    "
                    alt="Admin Profile"
                />
                <span class="font-medium hover:underline dark:text-white">
                    {{ registration.event.event_users?.find((eu) => eu.role?.name.toUpperCase() === 'ADMIN')?.user?.name ?? 'Tidak ada admin' }}
                </span>
            </button>
            <p class="line-clamp-3 max-h-[20vh] font-light text-gray-500 dark:text-gray-400">{{ registration.event.description ?? '' }}</p>
            <div class="flex w-full flex-wrap gap-2">
                <button
                    @click="sendTag(tag.name)"
                    v-for="tag in registration.event.tags.slice(0, 3)"
                    :key="tag.id"
                    class="dark:bg-primary-200 dark:text-primary-800 inline-flex items-center rounded bg-blue-100 px-2.5 py-0.5 text-sm font-medium text-gray-500 hover:underline"
                >
                    {{ tag.name ?? 'Tidak ada tag' }}
                </button>
            </div>
        </div>
        <div class="mt-5 flex items-center justify-between">
            <button class="rounded-md bg-blue-500 px-4 py-1">
                <Link
                    :href="`/registration/${registration.id}`"
                    class="dark:text-primary-500 inline-flex items-center text-sm font-medium text-white hover:underline"
                >
                    Read more
                </Link>
            </button>
            <span class="w-fit text-sm text-gray-500">{{ dayjs(registration.event.created_at).fromNow() }}</span>
        </div>
    </article>
</template>
