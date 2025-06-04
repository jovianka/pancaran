<template>
<div class="border rounded-md px-4 py-2 mx-4 text-sm text-gray-700 dark:text-gray-200 dark:border-gray-600 min-w-fit w-fit self-start md:self-auto">
    <span class="font-medium">SKP Total :</span>
    <span class="ml-1 font-semibold"><!--{{ totalSkp }}--> 100</span>
  </div>

  </template>
  <!--
  <script setup lang="ts">
  import { computed } from 'vue'
  
  interface Certificate {
    id: number
    skp: number
  }
  
  const props = defineProps<{
    certificates: Certificate[]
  }>()
  
  const totalSkp = computed(() => {
    return props.certificates.reduce((sum, cert) => sum + cert.skp, 0)
  })
  </script>


-->

