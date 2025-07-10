<script setup lang="ts">
import { Pagination, Registration } from '@/pages/Explore.vue'
import { Link, router} from '@inertiajs/vue3'
import { ChevronLeft, ChevronRight } from 'lucide-vue-next'

defineProps<{
  pagination: Pagination<Registration>
}>()

function goToPage(url: string | null) {
  if (!url) return
  let filters: Record<string, string[]> = {
    by: [],
    scope: [],
    tag: [],
    title: []
  }

  // Ambil chips dari sessionStorage
  const savedChips = sessionStorage.getItem('search_chips')
  if (savedChips) {
    try {
      const chips = JSON.parse(savedChips) as { key: string; value: string }[]
      for (const chip of chips) {
        if (chip.key === 'by:') filters.by.push(chip.value)
        if (chip.key === 'scope:') filters.scope.push(chip.value)
        if (chip.key === 'tag:') filters.tag.push(chip.value)
      }
    } catch (e) {
      console.error("Failed to parse chips from sessionStorage", e)
    }
  }

  router.get(url, filters, {
    preserveState: true,
    preserveScroll: true
  })
}

</script>

<template>
    <nav v-if="pagination.links.length > 1" class="flex space-x-2 w-full justify-center">
        <template v-for="(link, i) in pagination.links" :key="i">
            <component
                :is="link.url ? Link : 'span'"
                :href="link.url || undefined"
                @click.prevent.left="link.url && goToPage(link.url)"
                class="px-3 py-1 border rounded-md text-sm flex items-center justify-center gap-1"
                :class="{
                'bg-blue-500 text-white': link.active,
                'text-gray-400 cursor-not-allowed': !link.url,
                'hover:bg-blue-100': !link.active && link.url
                }"
            >
                <template v-if="link.label.includes('Previous') || link.label.includes('&laquo;')">
                    <ChevronLeft class="w-4 h-4" />
                </template>
                <template v-else-if="link.label.includes('Next') || link.label.includes('&raquo;')">
                    <ChevronRight class="w-4 h-4" />
                </template>
                <template v-else>
                    <span v-html="link.label" />
                </template>
            </component>
        </template>
    </nav>
</template>
