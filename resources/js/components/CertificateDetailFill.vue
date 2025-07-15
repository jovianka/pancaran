<script setup lang="ts">
import { pdf2img } from '@pdfme/converter';
import { onMounted, ref } from 'vue';


const props = defineProps(['certificate']);

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
    <div class="rounded-xl border bg-white p-4 shadow-md">
        <img :src="certImageURL" alt="Certificate Image" class="mb-4 rounded-md border" />

        <div class="text-center text-lg font-bold">
            {{ certificate.user_name }}
        </div>

        <div class="mb-4 text-center text-sm text-gray-600">Sebagai : {{ certificate.role }}</div>

        <div class="text-md mb-2 text-center font-semibold">
            {{ certificate.title }}
        </div>

        <div class="text-center text-sm text-gray-500">Held by {{ certificate.organizer }}</div>

        <div class="mt-4 grid grid-cols-2 gap-2 text-sm text-gray-700">
            <div><strong>Event Scope :</strong> {{ certificate.event_level }}</div>
            <div><strong>Position :</strong> {{ certificate.role }}</div>
            <div><strong>Date :</strong> {{ certificate.date }}</div>
            <div><strong>SKP :</strong> {{ certificate.skp }}</div>
        </div>
    </div>
</template>
