<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import CertificateDetailFill from '@/components/CertificateDetailFill.vue'

interface CertificateProps {
  id: number
  title: string
  organizer: string
  date: string
  image_url: string
  user_name: string
  role: string
  event_level: string
  skp: number
}

const props = defineProps<{
  certificate: CertificateProps
}>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Certificate Detail',
        href: '/certificate/${certificate.id}',
    },
];



defineProps({
  certificate: Object
})

const close = () => {
  router.visit('/certificate') // arahkan ke halaman utama
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
  <div class="p-4">
    <div class="flex justify-between items-center mb-4">
      <button @click="close" class="text-sm text-gray-500">✖ Close</button>
      <a 
  :href="certificate.image_url" 
  download 
  class="text-sm text-blue-500 hover:underline"
>
  ⬇ Download
</a>

    </div>

    <CertificateDetailFill :certificate="certificate" />
  </div>
    </AppLayout>
</template>
