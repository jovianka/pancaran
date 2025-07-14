<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { router } from '@inertiajs/vue3';

const goToPathWithPost = () => {
  router.get('/edit-registration', {
    registration_id: 23
  })
}
</script>
<template>
    <Button @click=goToPathWithPost>Edit Form</Button>
</template>
