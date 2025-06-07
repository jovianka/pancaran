<script setup lang="ts">
import { ref, computed, watch, defineProps, onMounted, onBeforeUnmount } from 'vue'
import { router} from '@inertiajs/vue3'
import {X} from 'lucide-vue-next'
interface TagPivot{
    id: number;
    created_at: Date;
    updated_at: Date;
    tag_id: number;
    event_id: number;
}
interface Tag{
    id: number;
    name: string;
    created_at: Date;
    updated_at: Date;
    pivot?:TagPivot;
}

interface User{
    id: number;
    nim: string;
    name: string;
    email: string;
    avatar: string;
    type: string;
}

const props = defineProps<{
    tags: Tag[];
    users: User[];
    filterFromCard: string;
}>();

const emit = defineEmits(['apply-filter'])

const input = ref("")
const chips = ref<{ key: string, value: string }[]>([])
const suggestions = ref<string[]>([])
const currentPrefix = ref("")
const selectedSuggestion = ref(0)

console.log(chips.value)
const data = {
    users: props.users.map(user => user.name),
    tags: props.tags.map(tag => tag.name),
    scopes: ['major',
            'faculty',
            'university',
            'regional',
            'national',
            'international'],
}


type SourceKey = keyof typeof data

const filters = [
    { prefix: 'by:', suggestionSource: 'users' },
    { prefix: 'tag:', suggestionSource: 'tags' },
    { prefix: 'scope:', suggestionSource: 'scopes' },
] satisfies { prefix: string, suggestionSource: SourceKey }[]

const filteredSuggestions = computed(() => {
    const result: Record<SourceKey, string[]> = {
        users: [],
        tags: [],
        scopes: [],
    };

    (Object.keys(data) as SourceKey[]).forEach((key) => {
        result[key] = data[key].filter((item) => {
            const isUsed = chips.value.some(chip => chip.value === item);
            return !isUsed
        });
    });

    return result
})


function applySuggestion() {
    const val = props.filterFromCard ? props.filterFromCard.trim() : input.value.trim();
    emit('apply-filter', "")
    const match = val.match(/^(\w+):\s*(.*)$/i)
    if (!match) {
        sendSearchRequest(val)
        input.value = ''
        return
    }

    if (chips.value.length >= 6){
        return
    }

    const rawPrefix = match[1].toLowerCase() + ':'
    const query = match[2]

    const alreadyExists = chips.value.some(chip => chip.key === rawPrefix && chip.value.toLowerCase() === query.toLowerCase());
    if (alreadyExists) {
        return;
    }


    const prefixObj = filters.find(f => f.prefix === rawPrefix)
    if (!prefixObj) return


    const selected = suggestions.value[selectedSuggestion.value] || query

    const matchData = data[prefixObj.suggestionSource].filter(element =>
    element.toLowerCase().includes(query.toLowerCase())
    )

    if (!matchData.length){
        return
    }

    if (selected) {
        chips.value.push({ key: prefixObj.prefix, value: selected })
        input.value = ''
    }

    sendSearchRequest()

    suggestions.value = []
    selectedSuggestion.value = 0
}


function navigate(dir: number) {
    selectedSuggestion.value = (selectedSuggestion.value + dir + suggestions.value.length) % suggestions.value.length
}

function getSuggestions(source: SourceKey, query: string): string[] {
    if (!query){
        return filteredSuggestions.value[source].slice(0, 6)
    }

    return filteredSuggestions.value[source].filter(item =>
        item.toLowerCase().includes(query.toLowerCase())
    ).slice(0, 6)
}

function selectSuggestion(item: string) {
    if (chips.value.length >= 6){
        return
    }
    const prefix = currentPrefix.value
    chips.value.push({ key: prefix, value: item })
    input.value = ''
    suggestions.value = []
}

function removeTag(index: number) {
    chips.value.splice(index, 1)
    inputProcess()
    sendSearchRequest()
}

function inputProcess(){
    const inputValue = input.value.trim()

    const match = inputValue.match(/^(\w+):\s*(.*)$/)
    if (!match || chips.value.length >= 6) {
        suggestions.value = []
        currentPrefix.value = ''
        return
    }

    const rawPrefix = match[1].toLowerCase() + ':'
    const query = match[2]

    const prefixObj = filters.find(f => f.prefix === rawPrefix)
    if (!prefixObj) {
        suggestions.value = []
        return
    }

    currentPrefix.value = prefixObj.prefix
    suggestions.value = getSuggestions(prefixObj.suggestionSource, query)
    selectedSuggestion.value = 0
}

function extractParams(title: string = "") {
    const params: Record<string, string[]> = {
        'by': [],
        'scope': [],
        'tag': [],
        'title': []
    }

    chips.value.forEach(chip => {
        if (chip.key === 'by:') params.by.push(chip.value)
        if (chip.key === 'scope:') params.scope.push(chip.value)
        if (chip.key === 'tag:') params.tag.push(chip.value)
    })

    if (title !== ""){
        params.title.push(title)
    }
    return params
}

async function sendSearchRequest(title: string = "") {
    const payload = extractParams(title)
    console.log(chips.value)
    router.get('explore', payload, {
        preserveState: true,
        preserveScroll: true,
        only: [],
        onSuccess: (page) => {
            console.log('Results:', page.props)
        },
        onError: (errors) => {
            console.error('Error:', errors)
        }
    })
}


watch(chips, (newChips)=>{
    sessionStorage.setItem('search_chips', JSON.stringify(newChips))
}, {deep:true})

watch(input, () => {
    inputProcess()
})

watch(() => props.filterFromCard, ()=>{
    applySuggestion()
})

onMounted(() => {
    console.log('Mounted! Trying to load chips from localStorage...')
    const savedChips = sessionStorage.getItem('search_chips')
    if (savedChips) {
        try {
            chips.value = JSON.parse(savedChips)
        } catch (e) {
            console.error('Failed to parse saved chips:', e)
        }
    }
})

onBeforeUnmount(() => {
    sessionStorage.removeItem('search_chips')
})

</script>

<template>
  <div class="searchbox w-full max-w-2xl px-5 lg:px-6 my-4">
    <div class="flex items-center bg-gray-50 dark:bg-gray-900 border border-gray-300 w-full py-3 px-4 rounded-lg gap-3 text-sm focus-within:ring-[1.5px] ring-gray-500">
        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m21 21-3.5-3.5M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/>
        </svg>
        <input
        class="w-full outline-none ring-0 focus:outline-none focus:ring-0 focus:border-transparent text-black dark:text-white "
        v-model="input"
        @keydown.enter.prevent="applySuggestion"
        @keydown.tab.prevent="applySuggestion"
        @keydown.down.prevent="navigate(1)"
        @keydown.up.prevent="navigate(-1)"
        placeholder="Search using filters: by:name, scope:level, tag:topic"
        />
    </div>
    <div class="mt-2 text-sm text-gray-500 min-h-[28px] flex items-center flex-wrap gap-2">
      Filters:
      <span
        v-for="(chip, index) in chips"
        :key="chip.key + chip.value"
        class="inline-flex items-center gap-1 bg-blue-100 px-2 py-1 rounded mr-2 font-medium"
      >
        {{ chip.key }} {{ chip.value }}
        <button class="text-red-500 font-bold" @click="removeTag(index)"><X class="w-3 h-3"></X></button>
      </span>
    </div>

    <ul
      v-if="suggestions.length"
      class="suggestions absolute bg-white shadow w-full max-w-xl rounded-b-lg z-10 dark:text-black"
    >
      <li
        v-for="(item, index) in suggestions"
        :key="item"
        :class="['py-1 px-2 cursor-pointer', index === selectedSuggestion ? 'bg-gray-300' : '']"
        @click="selectSuggestion(item)"
      >
        {{ item }}
      </li>
    </ul>
  </div>
</template>
