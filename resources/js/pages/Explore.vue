<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import SearchBox from '@/components/SearchBox.vue';
import CardBox from '@/components/CardBox.vue';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import {Link} from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Explore',
        href: '/explore',
    },
];

const showNav= ref(true)
let lastScrollTop = 0

const handleScroll = () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop

    if (scrollTop > lastScrollTop + 10) {
        showNav.value = false
    } else if (scrollTop < lastScrollTop) {
        showNav.value = true
    }

    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
})

onBeforeUnmount(() => {
    window.removeEventListener('scroll', handleScroll)
})
</script>

<template>
    <Head title="Explore" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <nav :class="['sticky max-md:top-16 top-12 bg-white dark:bg-gray-800 max-h-min sm:flex items-center flex-1 rounded-xl border-b border-sidebar-border/70 dark:border-sidebar-border md:min-h-min', showNav ? 'opacity-100 visible' : 'opacity-0 invisible']">
            <SearchBox />
            <div class="flex flex-1 gap-[3vw] min-w-min p-2 w-full justify-center mb-2 lg:mb-0 text-xs lg:text-base">
                <Link href="#">All</Link>
                <Link href="#">Committee</Link>
                <Link href="#">Seminar</Link>
                <Link href="#">Competition</Link>
                <Link href="#">Volunteer</Link>
            </div>
        </nav>
        <div class="flex h-full flex-1 flex-col gap-4 bg rounded-xl p-4">
            <div class="grid auto-rows-min gap-10 px-0 lg:px-10 md:grid-cols-3">
                <CardBox />
                <CardBox />
                <CardBox />
                <CardBox />
                <CardBox />
                <CardBox />
            </div>
        </div>
    </AppLayout>
</template>
