<script setup lang="ts">
import ProfileSection from '@/components/ProfileSection.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { BarController, BarElement, CategoryScale, Chart, LinearScale } from 'chart.js';
import { onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: '/dashboard' }];

const props = defineProps<{
    profileData: Record<string, string>;
    userType: string;
    insight: {
        skpTotal?: number;
        certificatesTotal?: number;
        eventsJoined?: number;
        ongoingActivity: number;
        activityCreated?: number;
        chartLabels: string[];
        chartValues: number[];
    };
}>();

Chart.register(BarController, BarElement, CategoryScale, LinearScale);

const chartCanvas = ref();

onMounted(() => {
    new Chart(chartCanvas.value, {
        type: 'bar',
        data: {
            labels: props.insight.chartLabels,
            datasets: [
                {
                    label: 'Insight',
                    data: props.insight.chartValues,
                    backgroundColor: '#3B82F6',
                    maxBarThickness: 60,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } },
        },
    });
});
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-dvh flex-col gap-4 p-4">
            <!-- top -->
            <div class="relative flex h-full flex-1 flex-col gap-4 md:flex-row">
                <!-- left -->
                <div class="relative flex h-full flex-col space-y-4">
                    <div class="relative flex h-[40vh] w-full items-center justify-center rounded-xl border shadow-md md:h-full">
                        <p class="text-6xl">
                            {{ userType === 'organization' ? '🏢' : '🧑‍🎓' }}
                        </p>
                    </div>
                    <Button class="relative w-full rounded-xl bg-blue-500 p-2 shadow-md hover:cursor-pointer hover:bg-blue-700 md:w-[250px]" as-child>
                        <Link :href="route('profile.edit')" class="text-lg font-bold text-white">Edit Profile</Link>
                    </Button>
                </div>

                <!-- right -->
                <ProfileSection :profileData="profileData" />
            </div>

            <!-- bottom -->
            <div class="relative flex h-full flex-1 flex-col gap-8 rounded-xl border p-4 shadow-md md:flex-row">
                <!-- left (insight text) -->
                <div class="relative flex w-[250px] flex-col gap-4">
                    <div>
                        <strong class="text-xl md:text-2xl">Insight</strong>
                    </div>

                    <template v-if="userType === 'student'">
                        <div>
                            <p class="text-gray-500">SKP Total</p>
                            <p class="font-bold">{{ insight.skpTotal }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Certificates Total</p>
                            <p class="font-bold">{{ insight.certificatesTotal }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Events Joined</p>
                            <p class="font-bold">{{ insight.eventsJoined }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">On-Going Activity</p>
                            <p class="font-bold">{{ insight.ongoingActivity }}</p>
                        </div>
                    </template>

                    <template v-else>
                        <div>
                            <p class="text-gray-500">Activity Created</p>
                            <p class="font-bold">{{ insight.activityCreated }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">On-Going Activity</p>
                            <p class="font-bold">{{ insight.ongoingActivity }}</p>
                        </div>
                    </template>
                </div>

                <!-- right (chart) -->
                <div class="relative h-[300px] flex-1">
                    <canvas ref="chartCanvas" class="h-full w-full" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
