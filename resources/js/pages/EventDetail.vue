<script setup lang="ts">
import TiptapViewer from '@/components/TiptapViewer.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { PencilLineIcon } from 'lucide-vue-next';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { ref } from 'vue';

const activeTab = ref<'info' | 'jobdesk'>('info');

const toggleTab = (tab: 'info' | 'jobdesk') => {
    activeTab.value = tab;
};

const props = defineProps<{
    auth: any;
    eventRegistration: any;
    responses: any;
    faculties: any;
    majors: any;
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
        title: `Registration: ${props.eventRegistration.event.name}`,
        href: `/registration/${props.eventRegistration.id}`,
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
                            props.eventRegistration.poster
                                ? route('registration.getPoster', { id: props.eventRegistration.id })
                                : `https://placehold.co/700x875?text=${encodeURI(props.eventRegistration.event.name)}`
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
                                <strong>Registration time range</strong>
                                <br />
                                <Badge variant="outline">
                                    {{ props.eventRegistration.start_date }}
                                </Badge>
                                until
                                <Badge variant="outline">
                                    {{ props.eventRegistration.end_date }}
                                </Badge>
                            </div>
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
                                v-if="props.eventRegistration.event.job_description"
                                :href="
                                    route('event.downloadJobDescription', {
                                        event_id: props.eventRegistration.id,
                                        filename: props.eventRegistration.event.job_description,
                                    })
                                "
                                target="_blank"
                                class="text-blue-500 hover:underline"
                            >
                                Click here to download the jobdesc document
                            </a>
                        </div>
                    </div>

                    <div class="flex w-full flex-wrap gap-2">
                        <Link :href="route('registration_show_form', { id: eventRegistration.id })" v-if="auth.user.type === 'student'">
                            <Button variant="default" class="lg:text-md w-36 p-5"> Registration Form </Button>
                        </Link>
                        <Link :href="route('edit_registration', { id: eventRegistration.id })" v-if="auth.user.type === 'organization'">
                            <Button variant="outline" class="lg:text-md w-32 p-5"> Edit Registration </Button>
                        </Link>
                        <Link href="" v-if="auth.user.type === 'organization'">
                            <Button variant="destructive" class="lg:text-md w-32 p-5"> Delete </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <div v-if="auth.user.type === 'organization'">
                <h3 class="mt-4 text-xl font-bold">Responses</h3>
                <Table v-if="props.responses.length > 0">
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-12">Nomor</TableHead>
                            <TableHead>Nama</TableHead>
                            <TableHead>NIM</TableHead>
                            <TableHead>Fakultas</TableHead>
                            <TableHead>Prodi</TableHead>
                            <TableHead class="text-center">View Response</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(response, index) of props.responses" :key="response.id">
                            <TableCell class="text-center">{{ index + 1 }}</TableCell>
                            <TableCell>{{ response.user.name }}</TableCell>
                            <TableCell v-if="response.user.nim">{{ response.user.nim }}</TableCell>
                            <TableCell v-else>Organization</TableCell>
                            <TableCell>{{ faculties.find((faculty: any) => faculty.id === response.user.faculty_id).name }}</TableCell>
                            <TableCell>{{ majors.find((major: any) => major.id === response.user.major_id).name }}</TableCell>
                            <TableCell class="text-center">
                                <Link :href="route('registration_show_response', {registration_id: eventRegistration.id, user_id: response.user.id})">
                                    <Button variant="outline" class="w-fit">
                                        <PencilLineIcon />
                                    </Button>
                                </Link>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
                <span v-else>No responses found</span>
            </div>
        </div>
    </AppLayout>
</template>
