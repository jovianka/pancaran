<template>
    <div class="relative h-full flex-1 border rounded-xl shadow-md space-y-4 p-4">
      <div class="flex flex-1 items-center justify-between">
        <strong class="text-xl md:text-2xl">Profile</strong>
      </div>
      <div class="space-y-3">
        <div v-for="(value, key) in profileData" :key="key">
          <p class="text-gray-400 font-bold">{{ key }}</p>
          <p>{{ value }}</p>
        </div>
      </div>
    </div>
  </template>
  
  <script setup lang="ts">
  defineProps<{
    profileData: Record<string, string>
  }>();
  </script>
  