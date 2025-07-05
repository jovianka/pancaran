<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import CertificateCard from '@/components/CertificateCard.vue';
import SearchBox from '@/components/SearchBoxCertificate.vue';
import { ref, computed } from 'vue'
import skpTotal from '@/components/skpTotal.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Certificate',
        href: '/certificate',
    },
];

interface CertificateType {
  id: number; // ✅ Tambahkan ini
  file?: string;
  event?: {
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
}>()

const search = ref('')

// 🔍 Filter berdasarkan judul event atau penyelenggara
const filteredCertificates = computed(() => {
  if (!search.value) return props.certificates;

  const keyword = search.value.toLowerCase();

  return props.certificates.filter(c => {
    const eventName = c.event?.name?.toLowerCase() ?? '';

    // ✅ Gunakan eventUsers, bukan event_users
    const adminName = c.event?.eventUsers?.find(
      eu => eu.role?.name === 'admin'
    )?.user?.name?.toLowerCase() ?? '';

    return eventName.includes(keyword) || adminName.includes(keyword);
  });
});

console.log(JSON.stringify(props.certificates, null, 2))


</script>

<template>
    <Head title="Certificate" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="px-4 sm:px-8 pt-6">
            <h2 class="text-xl font-semibold tracking-tight">Certificate</h2>
        </div>
        <div class="flex flex-col md:flex-row md:justify-between md:items-center w-full gap-4 mb-4 pr-4">
            <SearchBox v-model="search" />
            <skpTotal :certificate="filteredCertificates"/>
        </div>
        
        <!-- Certificate Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6 px-4 sm:px-8 py-6">
            <CertificateCard
                v-for="(certificate, index) in filteredCertificates"
                :key="index"
                :certificate="certificate"
            />
        </div>

    </AppLayout>
</template>