<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { pdf2img } from '@pdfme/converter';
import { onMounted, ref } from 'vue';

interface Certificate {
    id: number;
    file: string;
    event_name?: string;
    event?: {
        eventUsers?: EventUser[];
    };
    event_level: string;
    role: string;
    category: string;
    date: string;
    skp: number;
}

interface EventUser {
    role?: {
        name?: string;
    };
    user?: {
        name?: string;
        avatar?: string;
    };
}

const props = defineProps<{
    certificate: Certificate;
}>();

const close = () => {
    router.visit('/certificate');
};

const breadcrumbs = [
    {
        title: 'Certificate / Certificate Detail',
        href: '/certificate/' + props.certificate.id,
    },
];

const convertPdfToImageURL = async (filename: string) => {
    const response = await fetch(route('event.getCertificateFile', { filename: filename }));
    const arrayBuffer = await response.arrayBuffer();
    const images = await pdf2img(arrayBuffer, {
        imageType: 'png',
        scale: 1,
        range: { start: 0, end: 1 },
    });

    const blob = new Blob([images[0]], { type: 'image/png' });
    return URL.createObjectURL(blob);
};

const certImageURL = ref('');

onMounted(async () => {
    certImageURL.value = await convertPdfToImageURL(props.certificate.file);
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-6">
            <div class="mb-4 flex items-center justify-between">
                <button @click="close" class="text-foreground/60 flex items-center gap-1 text-sm transition-colors hover:text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="18" height="18" viewBox="0 0 200 200">
                        <path
                            d="M114,100l49-49a9.9,9.9,0,0,0-14-14L100,86,51,37A9.9,9.9,0,0,0,37,51l49,49L37,149a9.9,9.9,0,0,0,14,14l49-49,49,49a9.9,9.9,0,0,0,14-14Z"
                        />
                    </svg>
                    Close
                </button>

                <a
                    :href="route('event.downloadCertificateFile', { filename: certificate.file })"
                    class="flex items-center gap-1 text-sm text-blue-500 hover:underline"
                    target="_blank"
                    rel="noopener"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M3 15C3 17.8284 3 19.2426 3.87868 20.1213C4.75736 21 6.17157 21 9 21H15C17.8284 21 19.2426 21 20.1213 20.1213C21 19.2426 21 17.8284 21 15"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M12 3V16M12 16L16 11.625M12 16L8 11.625"
                            stroke="currentColor"
                            stroke-width="1.5"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                    Download
                </a>
            </div>

            <div class="mb-6 w-fit overflow-hidden rounded-xl border p-4 shadow-lg">
                <img :src="certImageURL" alt="Certificate Image" class="h-full max-h-96 w-fit object-cover object-center" />
            </div>

            <div class="mb-6">
                <h1 class="text-xl font-semibold">{{ props.certificate.event_name }}</h1>
                <p class="text-foreground/60">
                    Held by
                    <span>
                        {{ props.certificate.event?.eventUsers?.find((eu) => eu.role?.name === 'admin')?.user?.name ?? 'Tidak ada admin' }}
                    </span>
                </p>
            </div>

            <div class="space-y-2 text-sm">
                <p><span class="font-semibold">Event Scope</span> : {{ props.certificate.event_level }}</p>
                <p><span class="font-semibold">Position</span> : {{ props.certificate.role }}</p>
                <p><span class="font-semibold">Category</span> : {{ props.certificate.category }}</p>
                <p><span class="font-semibold">Date</span> : {{ props.certificate.date }}</p>
                <p><span class="font-semibold">SKP</span> : {{ props.certificate.skp }}</p>
            </div>
        </div>
    </AppLayout>
</template>
