<script setup lang="ts">
import { cn } from '@/lib/utils';
import { onMounted, ref } from 'vue';

import { Combobox, ComboboxAnchor, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxList, ComboboxEmpty } from '@/components/ui/combobox';
import { useDebounceFn, useFetch } from '@vueuse/core';
import { CheckIcon, SearchIcon } from 'lucide-vue-next';
import { ScrollArea } from './ui/scroll-area';

const searchedSkp = ref<any[] | null>(null);
const searchBarInputValue = ref('');
const loadingSkp = ref(false);
const selectedSkp = ref(null);

const searchSkp = useDebounceFn(async (term: string) => {
    const { data } = (await useFetch(route('skp.search', { term: term }))
        .get()
        .json()) as any;
    searchedSkp.value = data.value.data;
    loadingSkp.value = false;
}, 500);

onMounted(async () => {
    const { data } = (await useFetch(route('skp.search', { term: '' }))
        .get()
        .json()) as any;
    searchedSkp.value = data.value.data;
});

const model = defineModel();
</script>

<template>
    <Combobox v-model="selectedSkp" class="w-full min-w-0 rounded-lg border" :ignore-filter="true">
        <ComboboxAnchor class="w-full">
            <div class="relative w-full items-center">
                <ComboboxInput
                    v-model="searchBarInputValue"
                    :display-value="
                        (val) => (val ? `${val.category} -- ${val.description} -- ${val.role} -- ${val.event_level.toUpperCase()} (${val.skp})` : '')
                    "
                    class="file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive col-span-3 flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 pl-9 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                    placeholder="Search for SKP detail"
                    @input="searchSkp(searchBarInputValue)"
                />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                    <SearchIcon class="text-muted-foreground size-4" />
                </span>
            </div>
        </ComboboxAnchor>
        <ComboboxList align="center">
            <ComboboxEmpty class="p-3">
                No SKP Detail Found
            </ComboboxEmpty>
            <ScrollArea class="h-[200px] rounded-md border p-2" v-if="searchedSkp?.length !== 0">
                <ComboboxGroup>
                    <ComboboxItem
                        v-for="skp of searchedSkp"
                        :key="skp.id"
                        :value="skp"
                        @select="
                            (ev: any) => {
                                model = skp.id;
                            }
                        "
                    >
                        {{ `${skp.category} -- ${skp.description} -- ${skp.role} -- ${skp.event_level.toUpperCase()} (${skp.skp})` }}
                        <ComboboxItemIndicator>
                            <CheckIcon :class="cn('ml-auto h-4 w-4')" />
                        </ComboboxItemIndicator>
                    </ComboboxItem>
                </ComboboxGroup>
            </ScrollArea>
        </ComboboxList>
    </Combobox>
</template>
