<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage<SharedData>();
const quote = page.props.quote;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col justify-center items-center bg-zinc-900 p-10 text-white dark:border-r lg:flex">
            <Link :href="route('home')" class="relative z-20 flex text-lg font-medium">
                <AppLogoIcon class="mr-2 size-8 fill-current" />
            </Link>
            <h1 class="text-5xl text-transparent font-black bg-gradient-to-r from-sky-400 to-sky-200 bg-clip-text">PANCARAN</h1>
            <div v-if="quote" class="relative z-20 mt-4">
                <blockquote class="space-y-2 text-center">
                    <p class="text-lg">&ldquo;{{ quote.message }}&rdquo;</p>
                    <footer class="text-sm text-neutral-300">{{ quote.author }}</footer>
                </blockquote>
            </div>
        </div>
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">{{ title }}</h1>
                    <p class="text-sm text-muted-foreground" v-if="description">{{ description }}</p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
