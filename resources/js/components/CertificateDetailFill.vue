<script setup>
defineProps({
  certificate: Object
})
</script>

<template>
  <div class="rounded-xl border p-4 bg-white shadow-md">
    <img :src="certificate.image_url" alt="Certificate Image" class="rounded-md border mb-4" />

    <div class="text-center font-bold text-lg">
      {{ certificate.user_name }}
    </div>

    <div class="text-center mb-4 text-sm text-gray-600">
      Sebagai : {{ certificate.role }}
    </div>

    <div class="font-semibold text-md text-center mb-2">
      {{ certificate.title }}
    </div>

    <div class="text-center text-sm text-gray-500">
      Held by {{ certificate.organizer }}
    </div>

    <div class="grid grid-cols-2 gap-2 mt-4 text-sm text-gray-700">
      <div><strong>Event Scope :</strong> {{ certificate.event_level }}</div>
      <div><strong>Position :</strong> {{ certificate.role }}</div>
      <div><strong>Date :</strong> {{ certificate.date }}</div>
      <div><strong>SKP :</strong> {{ certificate.skp }}</div>
    </div>
  </div>
</template>
