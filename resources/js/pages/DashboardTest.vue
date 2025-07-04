<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import { Chart, BarController, BarElement, CategoryScale, LinearScale } from 'chart.js'
import ProfileSection from '@/components/ProfileSection.vue'

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

Chart.register(BarController, BarElement, CategoryScale, LinearScale)

const chartCanvas = ref()

onMounted(() => {
  new Chart(chartCanvas.value, {
    type: 'bar',
    data: {
      labels: ['Kepemimpinan', 'Minat dan Bakat', 'Pengabdian', 'Penalaran Ilmiah'],
      datasets: [{
        label: 'Aktivitas Organisasi',
        data: [10, 18, 7, 12],
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
  })
})

const profileData = {
  Name: 'Badan Eksekutif Mahasiswa Fakultas Kedokteran',
  'Jenis Organisasi': 'Eksekutif Mahasiswa',
  Scope: 'Fakultas Kedokteran',
  'Tahun berdiri': '22 January 1999',
  Status: 'Active',
}
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- screen -->
    <div class="flex flex-col h-dvh gap-4 p-4">
      <!-- top -->
      <div class="relative flex-1 h-full flex flex-col md:flex-row gap-4">
        <!-- left -->
        <div class="relative h-full flex flex-col space-y-4">
          <div class="relative w-full h-[40vh] md:h-full border rounded-xl shadow-md flex items-center justify-center">
            <p class="text-6xl">🧑‍🎓</p>
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
        <!-- left -->
        <div class="relative w-[200px] h-full flex flex-col gap-4">
          <div>
              <strong class="text-xl md:text-2xl">Insight</strong>
          </div>
          <div>
            <p class="text-gray-500">Activity Created</p>
            <p class="font-bold">25</p>
          </div>
          <div>
            <p class="text-gray-500">On-Going Activity</p>
            <p class="font-bold">3</p>
          </div>
        </div>
        <!-- right -->
        <div class="relative flex-1">
          <canvas ref="chartCanvas" class="w-fill h-fill" />
        </div>
      </div>
    </div>

  </AppLayout>
</template>
