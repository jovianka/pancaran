<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import { Chart, BarController, BarElement, CategoryScale, LinearScale } from 'chart.js'
import ProfileSection from '@/components/ProfileSection.vue'

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
];

const props = defineProps<{
  profileData: Record<string, string>;
  userType: string;
  insight: {
    skpTotal?: number;
    certificatesTotal?: number;
    eventsJoined?: number;
    ongoingActivity: number;
    activityCreated?: number;
    chartLabels: string[];
    chartValues: number[];
  };
}>();

Chart.register(BarController, BarElement, CategoryScale, LinearScale)

const chartCanvas = ref()

onMounted(() => {
  new Chart(chartCanvas.value, {
    type: 'bar',
    data: {
      labels: props.insight.chartLabels,
      datasets: [{
        label: 'Insight',
        data: props.insight.chartValues,
        backgroundColor: '#3B82F6',
        maxBarThickness: 60,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: { y: { beginAtZero: true } },
      plugins: { legend: { display: false } }
    }
  });
});
</script>


<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex flex-col h-dvh gap-4 p-4">
      <!-- top -->
      <div class="relative flex-1 h-full flex flex-col md:flex-row gap-4">
        <!-- left -->
        <div class="relative h-full flex flex-col space-y-4">
          <div class="relative w-full h-[40vh] md:h-full border rounded-xl shadow-md flex items-center justify-center">
            <p class="text-6xl">
              {{userType === 'organization' ? '🏢' : '🧑‍🎓' }}
            </p>
          </div>
          <button class="relative w-full md:w-[250px] rounded-xl shadow-md bg-blue-500 p-2">
            <p class="text-lg text-white font-bold">Edit Profile</p>
          </button>
        </div>

        <!-- right -->
        <ProfileSection :profileData="profileData" />
      </div>

      <!-- bottom -->
      <div class="relative flex-1 h-full flex flex-col md:flex-row gap-8 p-4 border rounded-xl shadow-md">
        <!-- left (insight text) -->
        <div class="relative w-[250px] flex flex-col gap-4">
          <div>
            <strong class="text-xl md:text-2xl">Insight</strong>
          </div>

          <template v-if="userType === 'student'">
            <div>
              <p class="text-gray-500">SKP Total</p>
              <p class="font-bold">{{ insight.skpTotal }}</p>
            </div>
            <div>
              <p class="text-gray-500">Certificates Total</p>
              <p class="font-bold">{{ insight.certificatesTotal }}</p>
            </div>
            <div>
              <p class="text-gray-500">Events Joined</p>
              <p class="font-bold">{{ insight.eventsJoined }}</p>
            </div>
            <div>
              <p class="text-gray-500">On-Going Activity</p>
              <p class="font-bold">{{ insight.ongoingActivity }}</p>
            </div>
          </template>

          <template v-else>
            <div>
              <p class="text-gray-500">Activity Created</p>
              <p class="font-bold">{{ insight.activityCreated }}</p>
            </div>
            <div>
              <p class="text-gray-500">On-Going Activity</p>
              <p class="font-bold">{{ insight.ongoingActivity }}</p>
            </div>
          </template>
        </div>

        <!-- right (chart) -->
        <div class="relative flex-1 h-[300px]">
          <canvas ref="chartCanvas" class="w-full h-full" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>