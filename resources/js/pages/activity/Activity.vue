<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';

import Heading from '@/components/Heading.vue';
import { type BreadcrumbItem } from '@/types';

import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Combobox, ComboboxAnchor, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxItemIndicator, ComboboxList } from '@/components/ui/combobox';
import { Dialog, DialogContent, DialogDescription, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip';
import AppLayout from '@/layouts/AppLayout.vue';
import DefaultPageLayout from '@/layouts/DefaultPageLayout.vue';
import { cn } from '@/lib/utils';
import { useDebounceFn, useFetch } from '@vueuse/core';
import { Award, Check, ChevronLeftIcon, ChevronRightIcon, Eye, PlusIcon, ScanEye, Search, UserPen, Users } from 'lucide-vue-next';
import { PaginationEllipsis, PaginationList, PaginationListItem, PaginationNext, PaginationPrev, PaginationRoot, useFilter } from 'reka-ui';
import { onMounted, ref } from 'vue';

const props = defineProps(['faculties', 'majors', 'events', 'searchQuery', 'filter']);
const searchBarInputValue = ref('');
const { contains } = useFilter({ sensitivity: 'base' });

const searchedEvents = ref<any[] | null>(null);

const searchEvents: any = useDebounceFn(async (term: string) => {
    const { data } = (await useFetch(route('activity.search', { _query: { term: term } }))
        .get()
        .json()) as any;
    searchedEvents.value = data.value.data;
}, 500);

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Activity',
        href: '/activity',
    },
];

onMounted(() => {
    if (props.searchQuery) {
        searchBarInputValue.value = props.searchQuery;
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Activity" />
        <DefaultPageLayout v-if="props.events">
            <Heading title="Activity" description="Manage your events" />
            <div class="my-5 flex flex-row items-center justify-between">
                <Combobox class="w-full max-w-2xl rounded-lg border" v-model="searchedEvents" :ignore-filter="true">
                    <ComboboxAnchor class="w-full">
                        <div class="relative w-full items-center">
                            <ComboboxInput
                                v-model="searchBarInputValue"
                                class="pl-9"
                                placeholder="Search for event(s) you're in"
                                @input="searchEvents(searchBarInputValue)"
                            />
                            <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                                <Search class="text-muted-foreground size-4" />
                            </span>
                        </div>
                    </ComboboxAnchor>
                    <ComboboxList align="start">
                        <ComboboxGroup>
                            <ComboboxItem
                                value="search"
                                class="w-full"
                                @select.prevent="
                                    (ev) => {
                                        router.get(route('activity'), { search: searchBarInputValue.trim(), page: 1, filter: filter });
                                    }
                                "
                                >Search for {{ searchBarInputValue.trim() ? `"${searchBarInputValue}"` : 'all event(s)' }}
                            </ComboboxItem>
                            <ComboboxItem
                                v-for="event in searchedEvents"
                                :key="event.id"
                                :value="event.name"
                                @select.prevent="
                                    (ev) => {
                                        router.visit(route('event.view', { id: event.id }));
                                    }
                                "
                            >
                                <div class="flex w-full items-center gap-2">
                                    <img
                                        :src="
                                            event.poster
                                                ? route('event.getPoster', { event_id: event.id, filename: event.poster })
                                                : `https://placehold.co/700x875?text=${encodeURI(event.name)}`
                                        "
                                        :alt="`Poster kegiatan ${event.name}`"
                                        class="w-8 overflow-hidden rounded-t-md"
                                    />
                                    {{ event.name }}
                                </div>
                                <ComboboxItemIndicator>
                                    <Check :class="cn('ml-auto h-4 w-4')" />
                                </ComboboxItemIndicator>
                            </ComboboxItem>
                        </ComboboxGroup>
                    </ComboboxList>
                </Combobox>
                <!-- No shadcn here, straight from reka-ui -->
                <PaginationRoot
                    :total="props.events.total"
                    :items-per-page="props.events.per_page"
                    :sibling-count="1"
                    :default-page="1"
                    :page="props.events.current_page"
                    show-edges
                >
                    <PaginationList v-slot="{ items }" class="flex flex-row items-center justify-end gap-1">
                        <PaginationPrev>
                            <Button
                                variant="ghost"
                                @click="router.get(props.events.prev_page_url, { search: searchQuery ?? null, filter: filter })"
                                :disabled="props.events.current_page === 1"
                            >
                                <ChevronLeftIcon />
                            </Button>
                        </PaginationPrev>
                        <template v-for="(page, index) in items">
                            <PaginationListItem v-if="page.type === 'page'" :key="index" :value="page.value">
                                <Button
                                    @click="router.get('activity', { page: page.value, search: searchQuery ?? null, filter: filter })"
                                    :variant="props.events.current_page === page.value ? 'outline' : 'ghost'"
                                    >{{ page.value }}</Button
                                >
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="page.type" :index="index">
                                <Button variant="ghost" disabled> &#8230; </Button>
                            </PaginationEllipsis>
                        </template>
                        <PaginationNext>
                            <Button
                                variant="ghost"
                                @click="router.get(props.events.next_page_url, { search: searchQuery ?? null, filter: filter })"
                                :disabled="props.events.current_page === props.events.last_page"
                            >
                                <ChevronRightIcon />
                            </Button>
                        </PaginationNext>
                    </PaginationList>
                </PaginationRoot>
            </div>
            <div class="my-5 flex flex-row items-center justify-between">
                <div class="flex flex-row items-center gap-3">
                    <span class="">Filter: </span>
                    <Button
                        :variant="filter === 'ongoing' ? 'default' : 'outline'"
                        @click="
                            filter === 'ongoing'
                                ? router.get('activity', { search: searchQuery ?? null, filter: '' })
                                : router.get('activity', { search: searchQuery ?? null, filter: 'ongoing' })
                        "
                        >Ongoing</Button
                    >
                    <Button
                        :variant="filter === 'finished' ? 'default' : 'outline'"
                        @click="
                            filter === 'finished'
                                ? router.get('activity', { search: searchQuery ?? null, filter: '' })
                                : router.get('activity', { search: searchQuery ?? null, filter: 'finished' })
                        "
                        >Finished</Button
                    >
                </div>
                <div>
                    <Button variant="outline" as-child>
                        <Link :href="route('event.create')">
                            Create an Event
                            <PlusIcon />
                        </Link>
                    </Button>
                </div>
            </div>
            <div class="grid grid-cols-[repeat(auto-fill,_minmax(16rem,_1fr))] gap-5">
                <Card
                    v-for="event in props.events.data.filter((ev: any) => contains(ev.status, filter ?? ''))"
                    :key="event.id"
                    class="group/card relative w-auto gap-0 rounded-md p-0 transition-all duration-300 hover:scale-105 focus:scale-105"
                >
                    <CardHeader class="w-full gap-0 p-0">
                        <Link href="">
                            <img
                                :src="
                                    event.poster
                                        ? route('event.getPoster', { event_id: event.id, filename: event.poster })
                                        : `https://placehold.co/700x875?text=${encodeURI(event.name)}`
                                "
                                :alt="`Poster kegiatan ${event.name}`"
                                class="overflow-hidden rounded-t-md"
                            />
                        </Link>
                        <Dialog>
                            <DialogTrigger as-child>
                                <button
                                    class="group/view-poster-icon bg-background/70 hover:bg-foreground/50 absolute top-2 right-2 cursor-pointer rounded-xl p-1.5 transition-all duration-300"
                                >
                                    <ScanEye class="size-5 text-white transition-all duration-300 group-hover/view-poster-icon:text-black" />
                                </button>
                            </DialogTrigger>
                            <DialogContent class="overflow-hidden rounded-md p-0" :show-close-button="false">
                                <DialogTitle class="hidden">{{ 'Poster ' + event.name }}</DialogTitle>
                                <DialogDescription class="hidden">{{ 'Poster ' + event.name }}</DialogDescription>
                                <img
                                    :src="
                                        event.poster
                                            ? route('event.getPoster', { event_id: event.id, filename: event.poster })
                                            : `https://placehold.co/700x875?text=${encodeURI(event.name)}`
                                    "
                                    :alt="`Poster kegiatan ${event.name}`"
                                />
                            </DialogContent>
                        </Dialog>
                    </CardHeader>
                    <CardContent class="bg-background relative z-20 flex w-full rounded-b-md px-0 py-0 text-left">
                        <CardTitle
                            class="bg-background/80 pointer-events-none absolute top-0 left-0 z-10 h-fit w-full origin-bottom -translate-y-full p-3 transition-all group-hover/card:-translate-y-full xl:translate-y-0"
                            >{{ event.name }}</CardTitle
                        >
                        <div class="bg-background z-20 flex w-full flex-row justify-end gap-3 rounded-b-md px-4 py-3 text-left">
                            <Tooltip :delay-duration="200">
                                <TooltipTrigger as-child>
                                    <Link href="" class="hover:bg-foreground/60 block size-fit rounded-full bg-white p-2 transition-all duration-300"
                                        ><Award class="size-4 text-black"
                                    /></Link>
                                </TooltipTrigger>
                                <TooltipContent>Manage Certificates</TooltipContent>
                            </Tooltip>
                            <Tooltip :delay-duration="200">
                                <TooltipTrigger as-child>
                                    <Link href="" class="hover:bg-foreground/60 block size-fit rounded-full bg-white p-2 transition-all duration-300"
                                        ><UserPen class="size-4 text-black"
                                    /></Link>
                                </TooltipTrigger>
                                <TooltipContent>Manage Committee</TooltipContent>
                            </Tooltip>
                            <Tooltip :delay-duration="200">
                                <TooltipTrigger as-child>
                                    <Link
                                        href=""
                                        class="bg-foreground hover:bg-foreground/60 block size-fit rounded-full p-2 transition-all duration-300"
                                        ><Users class="size-4 text-black"
                                    /></Link>
                                </TooltipTrigger>
                                <TooltipContent>Manage Participants</TooltipContent>
                            </Tooltip>
                            <Tooltip :delay-duration="200">
                                <TooltipTrigger as-child>
                                    <Link
                                        href=""
                                        class="bg-foreground hover:bg-foreground/60 block size-fit rounded-full p-2 transition-all duration-300"
                                        ><Eye class="size-4 text-black"
                                    /></Link>
                                </TooltipTrigger>
                                <TooltipContent>Event Details</TooltipContent>
                            </Tooltip>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </DefaultPageLayout>
        <DefaultPageLayout v-else> ga nemu </DefaultPageLayout>
    </AppLayout>
</template>
