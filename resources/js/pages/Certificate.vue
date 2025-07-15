<script setup lang="ts">
import CertificateCard from '@/components/CertificateCard.vue';
import SearchBox from '@/components/SearchBoxCertificate.vue';
import skpTotal from '@/components/skpTotal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Certificate',
        href: '/certificate',
    },
];

interface CertificateType {
    id: number;
    file?: string;
    event?: {
        id: number;
        name?: string;
        eventUsers?: {
            role?: {
                name?: string;
            };
            user?: {
                name?: string;
            };
        }[];
    };
    detailSkp?: {
        skp?: number;
    };
}

const props = defineProps<{
    certificates: CertificateType[];
}>();

const search = ref('');

const filteredCertificates = computed(() => {
    if (!search.value) return props.certificates;

    const keyword = search.value.toLowerCase();

    return props.certificates.filter((c) => {
        const eventName = c.event?.name?.toLowerCase() ?? '';

        const adminName = c.event?.eventUsers?.find((eu) => eu.role?.name === 'admin')?.user?.name?.toLowerCase() ?? '';

        return eventName.includes(keyword) || adminName.includes(keyword);
    });
});
</script>

<template>
    <Head title="Certificate" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 pt-6 sm:px-8">
            <h2 class="text-xl font-semibold tracking-tight">Certificate</h2>
        </div>
        <div class="mb-4 flex w-full flex-col gap-4 pr-4 md:flex-row md:items-center md:justify-between">
            <SearchBox v-model="search" />
            <skpTotal :certificate="filteredCertificates" />
        </div>

        <!-- Certificate Grid -->
        <div class="grid grid-cols-1 gap-6 px-4 py-6 sm:grid-cols-2 sm:px-8 md:grid-cols-3 xl:grid-cols-4">
            <CertificateCard v-for="(certificate, index) in filteredCertificates" :key="index" :certificate="certificate" />
        </div>
    </AppLayout>
</template>
