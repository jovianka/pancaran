<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const activeTab = ref<'info' | 'jobdesk'>('info');

const toggleTab = (tab: 'info' | 'jobdesk') => {
    activeTab.value = tab;
};

const props = defineProps<{
    event: any;
    info: {
        title: string;
        type: string;
        poster: string;
        startDate: string;
        endDate: string;
        quota: number;
        eventStart: string;
        eventEnd: string;
        subCommittees: string[];
        description: string;
        requirements: string;
        jobDescription: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Event Detail', href: '/explore' }];
</script>

<template>
    <Head title="Event Detail" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-6">
            <div class="mb-2 flex w-full items-center justify-between">
                <button @click="router.visit('/explore')" class="text-sm text-blue-500 hover:underline">← Back</button>
            </div>

            <div class="bg-background flex flex-col gap-6 rounded-xl border p-6 shadow-md md:flex-row">
                <!-- Left (Poster & Enroll Button) -->
                <div class="flex w-full flex-col items-center gap-6 md:w-1/3">
                    <img :src="props.info.poster" alt="Poster" class="aspect-square w-full rounded-lg border object-cover shadow-md" />
                    <Link
                        :href="''"
                        class="w-full rounded-xl bg-blue-500 py-2 text-center font-semibold text-white transition-all hover:bg-blue-600 active:scale-[0.98]"
                    >
                        Enroll
                    </Link>
                </div>

                <!-- Right (Content) -->
                <div class="flex flex-1 flex-col gap-4">
                    <div>
                        <h2 class="text-2xl font-bold">{{ props.info.title }}</h2>
                        <p class="text-foreground/60 text-sm capitalize">Type: {{ props.info.type }}</p>
                    </div>

                    <!-- Tabs -->
                    <div class="flex gap-4 border-b">
                        <button
                            :class="['pb-2', activeTab === 'info' ? 'border-b-2 border-blue-500 font-semibold' : 'text-foreground/60']"
                            @click="toggleTab('info')"
                        >
                            Information
                        </button>
                        <button
                            :class="['pb-2', activeTab === 'jobdesk' ? 'border-b-2 border-blue-500 font-semibold' : 'text-foreground/60']"
                            @click="toggleTab('jobdesk')"
                        >
                            Jobdesc
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="mt-2 flex-1 overflow-auto">
                        <div v-if="activeTab === 'info'" class="space-y-4 text-sm">
                            <div>
                                <strong>Enrollment date</strong><br />{{ props.info.startDate }} <strong>until</strong> {{ props.info.endDate }}
                            </div>
                            <div><strong>Enrollment Quota</strong><br />{{ props.info.quota }} Persons</div>
                            <div><strong>Event schedule</strong><br />{{ props.info.eventStart }} until {{ props.info.eventEnd }}</div>
                            <div><strong>Sub committee</strong><br />{{ props.info.subCommittees.join(', ') }}</div>
                            <div>
                                <strong>Description</strong>
                                <p class="text-foreground/60">{{ props.info.description }}</p>
                            </div>
                            <div v-if="props.info.requirements !== null">
                                <strong>Requirements</strong>
                                <p class="text-foreground/60">{{ props.info.requirements }}</p>
                            </div>
                        </div>

                        <div v-else class="space-y-2 text-sm">
                            <a
                                :href="route('event.downloadJobDescription', { event_id: props.event.id, filename: props.event.jobDescription })"
                                target="_blank"
                                class="text-blue-500 hover:underline"
                            >
                                Click here to download the jobdesc document
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
