<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import DefaultPageLayout from '@/layouts/DefaultPageLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRight } from 'lucide-vue-next';
import { PaginationEllipsis, PaginationList, PaginationListItem, PaginationNext, PaginationPrev, PaginationRoot } from 'reka-ui';

const props = defineProps(['skpDetails', 'categoryFilter', 'eventLevelFilter']);

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Database SKP',
        href: '/database-skp',
    },
];

const categories = ['Bidang Penalaran/Ilmiah', 'Bidang Minat dan Bakat', 'Bidang Organisasi dan Kepanitiaan', 'Bidang Pengabdian Kepada Masyarakat'];
const eventLevels = ['international', 'regional', 'university', 'faculty', 'major'];

const setCategoryFilter = (category: string) => {
    if (props.categoryFilter !== category) {
        router.get(route('databaseSkp.view'), {
            page: 1,
            category: category,
            event_level: props.eventLevelFilter,
        });
    } else {
        router.get(route('databaseSkp.view'), {
            page: 1,
            category: null,
            event_level: props.eventLevelFilter,
        });
    }
};

const setEventLevelFilter = (eventLevel: string) => {
    if (props.eventLevelFilter !== eventLevel) {
        router.get(route('databaseSkp.view'), {
            page: 1,
            category: props.categoryFilter,
            event_level: eventLevel,
        });
    } else {
        router.get(route('databaseSkp.view'), {
            page: 1,
            category: props.categoryFilter,
            event_level: null,
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Database SKP" />
        <DefaultPageLayout>
            <Heading title="Database SKP" description="View SKP Details" />
            <div class="my-4 flex flex-row flex-wrap items-center gap-4">
                <span>Filter by Category:</span>
                <Button
                    v-for="(category, index) of categories"
                    :variant="props.categoryFilter == category ? 'default' : 'outline'"
                    :key="index"
                    @click="setCategoryFilter(category)"
                    >{{ category }}</Button
                >
            </div>
            <div class="my-4 flex flex-row flex-wrap items-center gap-4">
                <span>Filter by Event Level:</span>
                <Button
                    v-for="(eventLevel, index) of eventLevels"
                    :variant="props.eventLevelFilter == eventLevel ? 'default' : 'outline'"
                    :key="index"
                    @click="setEventLevelFilter(eventLevel)"
                    >{{ eventLevel.toUpperCase() }}</Button
                >
            </div>
            <PaginationRoot
                class="my-4"
                :total="props.skpDetails.total"
                :items-per-page="props.skpDetails.per_page"
                :sibling-count="1"
                :default-page="1"
                :page="props.skpDetails.current_page"
                show-edges
            >
                <PaginationList v-slot="{ items }" class="flex flex-row items-center justify-end gap-1">
                    <PaginationPrev>
                        <Button variant="ghost" @click="router.get(props.skpDetails.prev_page_url)" :disabled="props.skpDetails.current_page === 1">
                            <ChevronLeftIcon />
                        </Button>
                    </PaginationPrev>
                    <template v-for="(page, index) in items">
                        <PaginationListItem v-if="page.type === 'page'" :key="index" :value="page.value">
                            <Button
                                @click="
                                    router.get(route('databaseSkp.view'), {
                                        page: page.value,
                                        category: props.categoryFilter,
                                        event_level: props.eventLevelFilter,
                                    })
                                "
                                :variant="props.skpDetails.current_page === page.value ? 'outline' : 'ghost'"
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
                            @click="router.get(props.skpDetails.next_page_url)"
                            :disabled="props.skpDetails.current_page === props.skpDetails.last_page"
                        >
                            <ChevronRight />
                        </Button>
                    </PaginationNext>
                </PaginationList>
            </PaginationRoot>
            <Table v-if="props.skpDetails.data.length > 0">
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-12">Nomor</TableHead>
                        <TableHead>Category</TableHead>
                        <TableHead>Description</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead>Event Level</TableHead>
                        <TableHead class="text-center">SKP</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(skpDetail, index) of props.skpDetails.data" :key="skpDetail.id">
                        <TableCell class="text-center">{{ index + 1 + (props.skpDetails.current_page - 1) * props.skpDetails.per_page }}</TableCell>
                        <TableCell>{{ skpDetail.category }}</TableCell>
                        <TableCell>{{ skpDetail.description }}</TableCell>
                        <TableCell>{{ skpDetail.role }}</TableCell>
                        <TableCell>{{ skpDetail.event_level.toUpperCase() }}</TableCell>
                        <TableCell class="text-center">{{ skpDetail.skp }}</TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <!-- No shadcn here, straight from reka-ui -->
            <PaginationRoot
                class="my-4"
                :total="props.skpDetails.total"
                :items-per-page="props.skpDetails.per_page"
                :sibling-count="1"
                :default-page="1"
                :page="props.skpDetails.current_page"
                show-edges
            >
                <PaginationList v-slot="{ items }" class="flex flex-row items-center justify-end gap-1">
                    <PaginationPrev>
                        <Button variant="ghost" @click="router.get(props.skpDetails.prev_page_url)" :disabled="props.skpDetails.current_page === 1">
                            <ChevronLeftIcon />
                        </Button>
                    </PaginationPrev>
                    <template v-for="(page, index) in items">
                        <PaginationListItem v-if="page.type === 'page'" :key="index" :value="page.value">
                            <Button
                                @click="router.get(route('databaseSkp.view'), { page: page.value })"
                                :variant="props.skpDetails.current_page === page.value ? 'outline' : 'ghost'"
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
                            @click="router.get(props.skpDetails.next_page_url)"
                            :disabled="props.skpDetails.current_page === props.skpDetails.last_page"
                        >
                            <ChevronRight />
                        </Button>
                    </PaginationNext>
                </PaginationList>
            </PaginationRoot>
        </DefaultPageLayout>
    </AppLayout>
</template>
