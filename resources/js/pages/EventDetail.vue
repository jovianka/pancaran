<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { type BreadcrumbItem } from '@/types';

const activeTab = ref<'info' | 'jobdesk'>('info')

const toggleTab = (tab: 'info' | 'jobdesk') => {
  activeTab.value = tab
}

const props = defineProps<{
  info: {
    title: string;
    type: string;
    poster: string;
    startDate: string;
    endDate: string;
    quota: number;
    eventStart: string;
    eventEnd: string;
    subCommittees: string[];
    description: string;
    requirements: string;
    jobDescription: string;
  }
}>();

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Event Detail', href: '/explore' },
];
</script>

<template>
  <Head title="Event Detail" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-6 flex flex-col gap-4">
      <div class="w-full flex justify-between items-center mb-2">
        <button @click="router.visit('/explore')" class="text-blue-500 text-sm hover:underline">← Back</button>
      </div>

      <div class="flex flex-col md:flex-row gap-6 border rounded-xl p-6 shadow-md bg-white">
        <!-- Left (Poster & Enroll Button) -->
        <div class="w-full md:w-1/3 flex flex-col items-center gap-6">
          <img :src="props.info.poster" alt="Poster" class="rounded-lg shadow-md w-full aspect-square object-cover border" />
          <button
            @click="router.visit('/enroll')"
            class="w-full bg-blue-500 text-white font-semibold py-2 rounded-xl hover:bg-blue-600 active:scale-[0.98] transition-all"
          >
            Enroll
          </button>
        </div>

        <!-- Right (Content) -->
        <div class="flex-1 flex flex-col gap-4">
          <div>
            <h2 class="text-2xl font-bold">{{ props.info.title }}</h2>
            <p class="text-sm text-gray-500 capitalize">Type: {{ props.info.type }}</p>
          </div>

          <!-- Tabs -->
          <div class="flex gap-4 border-b">
            <button :class="['pb-2', activeTab === 'info' ? 'border-b-2 border-blue-500 font-semibold' : 'text-gray-500']" @click="toggleTab('info')">Information</button>
            <button :class="['pb-2', activeTab === 'jobdesk' ? 'border-b-2 border-blue-500 font-semibold' : 'text-gray-500']" @click="toggleTab('jobdesk')">Jobdesk</button>
          </div>

          <!-- Content -->
          <div class="flex-1 overflow-auto mt-2">
            <div v-if="activeTab === 'info'" class="space-y-4 text-sm">
              <div><strong>Enrollment date</strong><br />{{ props.info.startDate }} <strong>until</strong> {{ props.info.endDate }}</div>
              <div><strong>Enrollment Quota</strong><br />{{ props.info.quota }} Persons</div>
              <div><strong>Event schedule</strong><br />{{ props.info.eventStart }} until {{ props.info.eventEnd }}</div>
              <div><strong>Sub committee</strong><br />{{ props.info.subCommittees.join(', ') }}</div>
              <div><strong>Description</strong><p class="text-gray-600">{{ props.info.description }}</p></div>
              <div><strong>Requirements</strong><p class="text-gray-600">{{ props.info.requirements }}</p></div>
            </div>

            <div v-else class="space-y-2 text-sm">
              <a :href="props.info.jobDescription" target="_blank" class="text-blue-500 hover:underline">
                Click here to see jobdesk document
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
