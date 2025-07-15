<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import DefaultPageLayout from '@/layouts/DefaultPageLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';

const props = defineProps(['invitations']);

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Invitations',
        href: '/invitations',
    },
];

const rejectInvitation = (invitationId: number) => {
    router.patch(route('invitations.reject', { id: invitationId }));
};

const acceptInvitation = (invitationId: number) => {
    router.post(route('invitations.accept', { id: invitationId }));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Invitations" />
        <DefaultPageLayout>
            <Heading title="Invitations" description="View and accept/reject invitations to an event" />
            <Card v-for="(invitation, index) of props.invitations" :key="index" class="w-sm">
                <CardHeader>
                    <CardTitle>{{ invitation.event.name }}</CardTitle>
                    <CardDescription class="text-muted-foreground">Role: {{ invitation.role.name }}</CardDescription>
                </CardHeader>
                <CardContent>
                    <form>
                        <div class="grid w-full items-center gap-4">
                            <div class="flex flex-col space-y-1.5">
                                <h3 class="mb-0">Title: {{ invitation.title }}</h3>
                                <p class="text-muted-foreground">{{ invitation.body }}</p>
                            </div>
                        </div>
                    </form>
                </CardContent>
                <CardFooter class="flex justify-end gap-3 px-6">
                    <Button variant="destructive" @click="rejectInvitation(invitation.id)">Reject</Button>
                    <Button variant="default" class="bg-blue-600 text-white hover:bg-blue-700" @click="acceptInvitation(invitation.id)">Accept</Button>
                </CardFooter>
            </Card>
        </DefaultPageLayout>
    </AppLayout>
</template>
