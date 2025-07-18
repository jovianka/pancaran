<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { pdf2img } from '@pdfme/converter';
import { onMounted, ref } from 'vue';

interface EventUser {
    role?: {
        name?: string;
    };
    user?: {
        name?: string;
    };
}

const props = defineProps<{
    certificate: {
        id: number;
        file?: string;
        event?: {
            name?: string;
            eventUsers?: EventUser[];
        };
        detailSkp?: {
            skp?: number;
        };
    };
}>();

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

const certImageURL = ref('')

onMounted(async () => {
    certImageURL.value = await convertPdfToImageURL(props.certificate.file!);
});

</script>

<template>
    <!-- Bungkus seluruh card dengan Link -->
    <Link
        v-if="certificate?.id"
        :href="`/certificate/${certificate.id}`"
        class="flex flex-col rounded-xl bg-white p-3 shadow ring-blue-400 transition-all hover:ring-2 dark:border-gray-700 dark:bg-gray-800"
    >
        <!-- Gambar Sertifikat -->
        <img
            :src="certificate?.file ? certImageURL : 'https://placehold.co/300x200'"
            alt="Certificate Preview"
            class="aspect-video rounded-lg object-cover"
        />

        <!-- Detail Sertifikat -->
        <div class="mt-2 px-1 text-sm">
            <!-- Nama Event -->
            <p class="truncate font-semibold text-gray-900 dark:text-white">
                {{ certificate?.event?.name ?? 'Event name' }}
            </p>

            <!-- Admin / Held By -->
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Held by {{ certificate?.event?.eventUsers?.find((eu) => eu.role?.name!.toUpperCase() === 'ADMIN')?.user?.name ?? 'Tidak ada admin' }}
            </p>

            <!-- SKP -->
            <p class="mt-1 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                SKP:
                <span class="text-xs font-medium text-gray-900 dark:text-white">
                    {{ certificate?.detailSkp?.skp ?? 'N/A' }}
                </span>
            </p>
        </div>
    </Link>
</template>
