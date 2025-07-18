<!-- resources/js/Pages/RegisteredEventDetail.vue -->
<script setup lang="ts">
import TiptapViewer from '@/components/TiptapViewer.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Tooltip, TooltipTrigger, TooltipContent } from '@/components/ui/tooltip';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { EyeIcon, PencilIcon, PlusIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const activeTab = ref<'info' | 'jobdesk'>('info');

const toggleTab = (tab: 'info' | 'jobdesk') => {
    activeTab.value = tab;
};

const props = defineProps<{
    event: any;
    eventRegistrations: any;
    info: {
        title: string;
        type: string;
        poster: string;
        eventStart: string;
        eventEnd: string;
        subCommittees: string[];
        description: string;
        requirements: string;
        jobDescription: string;
        eventId: number;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activity',
        href: '/activity',
    },
    {
        title: `Event: ${props.event.name}`,
        href: `/activity/${props.event.id}`,
    },
];
</script>

<template>
    <Head title="Activity Detail" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-col gap-4 p-6">
            <div class="mb-2 flex w-full items-center justify-between">
                <button @click="router.visit('/activity')" class="text-sm text-blue-500 hover:underline">← Back</button>
            </div>

            <div class="bg-background flex flex-col gap-6 rounded-xl border p-6 shadow-md lg:flex-row">
                <!-- Left (Poster & Action Buttons) -->
                <div class="flex max-w-sm flex-col items-center gap-6 lg:w-md lg:max-w-md">
                    <img
                        :src="
                            props.event.poster
                                ? route('event.getPoster', { event_id: props.event.id, filename: props.event.poster })
                                : `https://placehold.co/700x875?text=${encodeURI(event.name)}`
                        "
                        alt="Poster"
                        class="aspect-[4/5] w-full rounded-lg border object-cover shadow-md"
                    />
                </div>

                <!-- Right (Details) -->
                <div class="flex flex-1 flex-col gap-4">
                    <div>
                        <h2 class="text-2xl font-bold">{{ props.info.title }}</h2>
                    </div>

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

                    <div class="mt-2 flex-1 overflow-auto">
                        <div v-if="activeTab === 'info'" class="space-y-4 text-sm">
                            <div>
                                <strong>Event schedule</strong>
                                <br />
                                <Badge variant="outline">
                                    {{ props.info.eventStart }}
                                </Badge>
                                until
                                <Badge variant="outline">
                                    {{ props.info.eventEnd }}
                                </Badge>
                            </div>
                            <div>
                                <strong>Sub committee</strong>
                                <br />
                                <p class="text-foreground/70">{{ props.info.subCommittees.join(', ') }}</p>
                            </div>
                            <div>
                                <strong>Description</strong>
                                <p class="text-foreground/70">{{ props.info.description }}</p>
                            </div>
                            <div v-if="props.info.requirements">
                                <strong>Requirements</strong>
                                <TiptapViewer :value="props.info.requirements" />
                            </div>
                        </div>

                        <div v-else class="space-y-2 text-sm">
                            <a
                                v-if="props.event.job_description"
                                :href="route('event.downloadJobDescription', { event_id: props.event.id, filename: props.event.job_description })"
                                target="_blank"
                                class="text-blue-500 hover:underline"
                            >
                                Click here to download the jobdesc document
                            </a>
                        </div>
                    </div>

                    <div class="flex w-full flex-wrap gap-2">
                        <Link :href="route('event.edit', { id: event.id })">
                            <Button variant="outline" class="lg:text-md w-32 p-5"> Edit Event </Button>
                        </Link>
                        <Link :href="route('members.view', { id: event.id })">
                            <Button variant="outline" class="lg:text-md w-32 p-5"> Members </Button>
                        </Link>
                        <Link :href="route('certificates.manage', { id: event.id })">
                            <Button variant="outline" class="lg:text-md w-32 p-5"> Certificates </Button>
                        </Link>
                        <Link href="">
                            <Button variant="destructive" class="lg:text-md w-32 p-5"> Leave </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <div>
                <div class="mb-4 flex flex-row items-center gap-3">
                    <h3 class="text-xl font-bold">Registrations</h3>
                    <Link :href="route('create-registration', { event_id: props.event.id })">
                        <Button variant="outline" class="flex flex-row">
                            <span>Create Registration</span>
                            <PlusIcon />
                        </Button>
                    </Link>
                </div>
                <div class="flex flex-row flex-wrap gap-5">
                    <Card v-for="(registration, index) of props.eventRegistrations" :key="index" class="w-sm">
                        <CardHeader>
                            <CardTitle>{{ `Registration ${index + 1}` }}</CardTitle>
                            <CardDescription class="text-muted-foreground">{{
                                `${registration.start_date} until ${registration.end_date}`
                            }}</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="grid w-full items-center gap-4">
                                <div class="flex flex-row gap-2">
                                    <span>Questions count: {{ registration.questions_count }}</span>
                                </div>
                                <div class="flex flex-row gap-2">
                                    <span>Responses count: {{ registration.responses_count }}</span>
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter class="flex justify-end gap-3 px-6">
                            <Tooltip :delay-duration="200">
                                <TooltipTrigger as-child>
                                    <Link
                                        :href="route('registration.view', { id: registration.id })"
                                        class="bg-foreground hover:bg-foreground/60 block size-fit rounded-full p-2 transition-all duration-300"
                                        ><EyeIcon class="text-background size-4"
                                    /></Link>
                                </TooltipTrigger>
                                <TooltipContent>Event Details</TooltipContent>
                            </Tooltip>
                            <Tooltip :delay-duration="200">
                                <TooltipTrigger as-child>
                                    <Link
                                        :href="route('edit_registration', { id: registration.id })"
                                        class="bg-foreground hover:bg-foreground/60 block size-fit rounded-full p-2 transition-all duration-300"
                                        ><PencilIcon class="text-background size-4"
                                    /></Link>
                                </TooltipTrigger>
                                <TooltipContent>Edit Registration</TooltipContent>
                            </Tooltip>
                        </CardFooter>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
