<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface EventUser {
  role?: {
    name?: string;
  };
  user?: {
    name?: string;
  };
}

defineProps<{
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
</script>

<template>
  <!-- Bungkus seluruh card dengan Link -->
  <Link
  v-if="certificate?.id" :href="`/certificate/${certificate.id}`"
    class="bg-white rounded-xl shadow p-3 flex flex-col dark:bg-gray-800 dark:border-gray-700 hover:ring-2 ring-blue-400 transition-all"
  >
    <!-- Gambar Sertifikat -->
    <img
      :src="certificate?.file ?? 'https://via.placeholder.com/300x200'"
      alt="Certificate Preview"
      class="rounded-lg aspect-video object-cover"
    />

    <!-- Detail Sertifikat -->
    <div class="mt-2 px-1 text-sm">
      <!-- Nama Event -->
      <p class="font-semibold text-gray-900 truncate dark:text-white">
        {{ certificate?.event?.name ?? 'Event name' }}
      </p>

      <!-- Admin / Held By -->
      <p class="text-gray-500 dark:text-gray-400 text-xs">
        Held by {{
          certificate?.event?.eventUsers?.find(eu => eu?.role?.name === 'admin')?.user?.name ?? 'Tidak ada admin'
        }}
      </p>

      <!-- SKP -->
      <p class="text-gray-500 text-right font-medium text-xs mt-1 dark:text-gray-400">
        SKP:
        <span class="text-gray-900 font-medium text-xs dark:text-white">
          {{ certificate?.detailSkp?.skp ?? 'N/A' }}
        </span>
      </p>
    </div>
  </Link>
</template>
