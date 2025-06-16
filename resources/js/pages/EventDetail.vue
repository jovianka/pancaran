<template>
    <div class="flex h-screen">
      <!-- Sidebar -->
      
  
      <!-- Main Content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <!-- Back button -->
        <button class="text-gray-500 hover:text-gray-700 mb-4">← Back</button>
  
        <!-- Content grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Poster -->
          <div class="rounded overflow-hidden shadow">
            <img src="/images/event_poster.webp" alt="Event Poster" class="w-full rounded" />
          </div>
  
          <!-- Event Details -->
          <div>
            <h2 class="text-2xl font-bold">PANITIA PORSENI 2088</h2>
            <p class="text-sm text-gray-500 mb-4">Committee | BEM FMIPA</p>
  
            <!-- Tabs -->
            <div class="flex border-b border-gray-300 mb-4">
              <button
                @click="activeTab = 'info'"
                :class="activeTab === 'info' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'"
                class="px-4 py-2 text-sm font-semibold focus:outline-none"
              >
                Information
              </button>
              <button
                @click="activeTab = 'jobdesc'"
                :class="activeTab === 'jobdesc' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'"
                class="px-4 py-2 text-sm font-semibold focus:outline-none"
              >
                Jobdesc
              </button>
            </div>
  
            <!-- Tab Content -->
            <div v-if="activeTab === 'info'" class="space-y-2 text-sm text-gray-700">
              <p><strong>Enrollment date:</strong> March 1, 2088 [12:00pm] until March 15, 2088 [12:00pm]</p>
              <p><strong>Enrollment Quota:</strong> 50 Persons</p>
              <p><strong>Event schedule:</strong> May 2, 2088 until May 15, 2088</p>
              <p><strong>Sub committee:</strong> Executive, 3D, Logistic, Event Organizer, Public Relation</p>
              <p><strong>Description:</strong> Lorem Ipsum is simply dummy text of the printing and typesetting industry...</p>
              <p><strong>Requirements:</strong> Lorem Ipsum is simply dummy text of the printing and typesetting industry...</p>
              <p><strong>Contact:</strong></p>
              <div class="flex space-x-2">
                <button class="bg-gray-200 text-sm px-3 py-1 rounded">Rina</button>
                <button class="bg-gray-200 text-sm px-3 py-1 rounded">Juliana</button>
              </div>
            </div>
  
            <div v-else class="text-sm text-gray-700">
              <p>Job description content goes here...</p>
            </div>
          </div>
        </div>
  
        <!-- Footer buttons -->
        <div class="mt-6 flex gap-4">
          <button class="bg-gray-800 text-white px-4 py-2 rounded">Invite</button>
          <button class="bg-gray-800 text-white px-4 py-2 rounded">Peserta</button>
          <button class="bg-gray-800 text-white px-4 py-2 rounded">Committee</button>
        </div>
      </main>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  const activeTab = ref('info');
  </script>
  
  <style scoped>
  body {
    font-family: 'Inter', sans-serif;
  }
  </style>
  