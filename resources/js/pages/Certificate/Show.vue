<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

// ✅ Definisikan tipe untuk Certificate
interface Certificate {
  id: number
  file: string
  event_name?: string
  event?: {
    eventUsers?: EventUser[];
  };
  event_level: string
  role: string
  category: string
  date: string
  skp: number
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
  certificate: Certificate
}>()

const close = () => {
  router.visit('/certificate')
}

const breadcrumbs = [
  {
    title: 'Certificate / Certificate Detail',
    href: '/certificate/' + props.certificate.id,
  },
]

async function downloadFile() {
  try {
    const response = await fetch(props.certificate.file, {
      mode: 'cors'
    });

    if (!response.ok) throw new Error('File tidak ditemukan');

    const blob = await response.blob();
    const contentType = blob.type; // ← Deteksi tipe file
    const extension = getExtensionFromType(contentType);

    const filename = 'certificate' + extension;

    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    a.remove();
    window.URL.revokeObjectURL(url);
  } catch (err) {
    console.error('Download error:', err);
    alert('Download gagal. File tidak tersedia atau format tidak dikenali.');
  }
}

function getExtensionFromType(type: string): string {
  const map: Record<string, string> = {
    'application/pdf': '.pdf',
    'image/jpeg': '.jpg',
    'image/png': '.png',
    'image/webp': '.webp',
    'image/svg+xml': '.svg',
    'image/gif': '.gif',
  };
  return map[type] ?? '';
}

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6">
      <div class="flex justify-between items-center mb-4">
  <button @click="close" class="flex items-center gap-1 text-sm text-gray-700 hover:text-red-600 transition-colors">
    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" width="18" height="18" viewBox="0 0 200 200">
      <path d="M114,100l49-49a9.9,9.9,0,0,0-14-14L100,86,51,37A9.9,9.9,0,0,0,37,51l49,49L37,149a9.9,9.9,0,0,0,14,14l49-49,49,49a9.9,9.9,0,0,0,14-14Z"/>
    </svg>
    Close
  </button>

  <a @click.prevent="downloadFile" href="#" class="flex items-center gap-1 text-sm text-blue-500 hover:underline">
  <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
    <path d="M3 15C3 17.8284 3 19.2426 3.87868 20.1213C4.75736 21 6.17157 21 9 21H15C17.8284 21 19.2426 21 20.1213 20.1213C21 19.2426 21 17.8284 21 15"
      stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
    <path d="M12 3V16M12 16L16 11.625M12 16L8 11.625"
      stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>
  Download
</a>

</div>

      <div class="border rounded-xl overflow-hidden shadow-lg p-4 mb-6">
        <img :src="props.certificate.file" alt="Certificate Image" class="w-full h-full max-h-fit object-cover object-center" />
      </div>

      <div class="mb-6">
        <h1 class="text-xl font-semibold">{{ props.certificate.event_name }}</h1>
        <p class="text-gray-500">Held by <span>
          {{ props.certificate.event?.eventUsers?.find(eu => eu.role?.name === 'admin')?.user?.name ?? 'Tidak ada admin' }}

</span>
</p>
      </div>

      <div class="text-sm space-y-2">
        <p><span class="font-semibold">Event Scope</span> : {{ props.certificate.event_level }}</p>
        <p><span class="font-semibold">Position</span> : {{ props.certificate.role }}</p>
        <p><span class="font-semibold">Category</span> : {{ props.certificate.category }}</p>
        <p><span class="font-semibold">Date</span> : {{ props.certificate.date }}</p>
        <p><span class="font-semibold">SKP</span> : {{ props.certificate.skp }}</p>
      </div>
    </div>
  </AppLayout>
</template>
