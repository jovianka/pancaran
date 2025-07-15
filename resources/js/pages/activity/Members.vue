<script setup lang="ts">
import CreateInvitationDialog from '@/components/CreateInvitationDialog.vue';
import EditInvitationDialog from '@/components/EditInvitationDialog.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import DefaultPageLayout from '@/layouts/DefaultPageLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ChevronLeftIcon, ChevronRight, PencilLineIcon, SearchXIcon } from 'lucide-vue-next';
import { PaginationEllipsis, PaginationList, PaginationListItem, PaginationNext, PaginationPrev, PaginationRoot } from 'reka-ui';

const props = defineProps(['faculties', 'majors', 'event', 'eventRoles', 'invitations', 'auth', 'eventUsers', 'roleFilter']);

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Activity',
        href: '/activity',
    },
    {
        title: `Manage Members (${props.event.name})`,
        href: '/activity',
    },
];

const setRoleFilter = (roleId: any) => {
    if (props.roleFilter != roleId) {
        router.get(route('members.view', { id: props.event.id }), {
            page: props.eventUsers.current_page,
            role_filter: roleId,
        });
    } else {
        router.get(route('members.view', { id: props.event.id }), {
            page: props.eventUsers.current_page,
            role_filter: null,
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Activity" />
        <DefaultPageLayout>
            <Heading title="Event members" description="Manage your committee members and participants" />
            <h3 class="mt-8 font-bold">Active Members</h3>
            <div class="my-4 flex flex-row flex-wrap items-center gap-4">
                <span>Filter Role:</span>
                <Button
                    v-for="role of eventRoles"
                    :variant="props.roleFilter == role.id ? 'default' : 'outline'"
                    :key="role.id"
                    @click="setRoleFilter(role.id)"
                    >{{ role.name.toUpperCase() }}</Button
                >
            </div>
            <Table v-if="props.eventUsers.data.length > 0">
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-12">Nomor</TableHead>
                        <TableHead>Nama</TableHead>
                        <TableHead>NIM</TableHead>
                        <TableHead>Fakultas</TableHead>
                        <TableHead>Prodi</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead class="text-center">Controls</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(user, index) of props.eventUsers.data" :key="user.id">
                        <TableCell class="text-center">{{ index + 1 }}</TableCell>
                        <TableCell>{{ user.name }}</TableCell>
                        <TableCell v-if="user.nim">{{ user.nim }}</TableCell>
                        <TableCell v-else>Organization</TableCell>
                        <TableCell>{{ faculties.find((faculty: any) => faculty.id === user.faculty_id).name }}</TableCell>
                        <TableCell>{{ majors.find((major: any) => major.id === user.major_id).name }}</TableCell>
                        <TableCell>{{ eventRoles.find((role: any) => role.id === user.pivot.event_role_id).name.toUpperCase() }}</TableCell>
                        <TableCell
                            v-if="
                                eventRoles.find((role: any) => role.id === user.pivot.event_role_id).name.toUpperCase() !== 'ADMIN' &&
                                user.id !== auth.user.id
                            "
                            class="text-center"
                        >
                            <Button variant="outline" class="w-fit">
                                <PencilLineIcon />
                            </Button>
                        </TableCell>
                        <TableCell v-else></TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <div v-else class="text-muted-foreground mt-8 flex justify-center gap-2 text-center">
                <SearchXIcon class="w-4" />
                <p>No event users found</p>
            </div>
            <!-- No shadcn here, straight from reka-ui -->
            <PaginationRoot
                class="mt-4"
                :total="props.eventUsers.total"
                :items-per-page="props.eventUsers.per_page"
                :sibling-count="1"
                :default-page="1"
                :page="props.eventUsers.current_page"
                show-edges
            >
                <PaginationList v-slot="{ items }" class="flex flex-row items-center justify-end gap-1">
                    <PaginationPrev>
                        <Button variant="ghost" @click="router.get(props.eventUsers.prev_page_url)" :disabled="props.eventUsers.current_page === 1">
                            <ChevronLeftIcon />
                        </Button>
                    </PaginationPrev>
                    <template v-for="(page, index) in items">
                        <PaginationListItem v-if="page.type === 'page'" :key="index" :value="page.value">
                            <Button
                                @click="router.get(route('members.view', { id: props.event.id }), { page: page.value })"
                                :variant="props.eventUsers.current_page === page.value ? 'outline' : 'ghost'"
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
                            @click="router.get(props.eventUsers.next_page_url)"
                            :disabled="props.eventUsers.current_page === props.eventUsers.last_page"
                        >
                            <ChevronRight />
                        </Button>
                    </PaginationNext>
                </PaginationList>
            </PaginationRoot>

            <h3 class="mt-8 font-bold">Invitations</h3>
            <CreateInvitationDialog :event-roles="props.eventRoles" :event-id="props.event.id" button-class="my-4" />
            <Table v-if="props.invitations.length > 0">
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-12">Nomor</TableHead>
                        <TableHead>Nama</TableHead>
                        <TableHead>NIM</TableHead>
                        <TableHead>Fakultas</TableHead>
                        <TableHead>Prodi</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead class="text-center">Controls</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(invitation, index) of props.invitations" :key="invitation.id">
                        <TableCell class="text-center">{{ index + 1 }}</TableCell>
                        <TableCell>{{ invitation.recipient.name }}</TableCell>
                        <TableCell v-if="invitation.recipient.nim">{{ invitation.recipient.nim }}</TableCell>
                        <TableCell v-else>Organization</TableCell>
                        <TableCell>{{ faculties.find((faculty: any) => faculty.id === invitation.recipient.faculty_id).name }}</TableCell>
                        <TableCell>{{ majors.find((major: any) => major.id === invitation.recipient.major_id).name }}</TableCell>
                        <TableCell>{{ eventRoles.find((role: any) => role.id === invitation.event_role_id).name.toUpperCase() }}</TableCell>
                        <TableCell>{{ invitation.status.toUpperCase() }}</TableCell>
                        <TableCell
                            v-if="eventRoles.find((role: any) => role.id === invitation.event_role_id).name.toUpperCase() !== 'ADMIN'"
                            class="text-center"
                        >
                            <EditInvitationDialog :event-roles="props.eventRoles" :invitation="invitation" />
                        </TableCell>
                        <TableCell v-else></TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            <div v-else class="text-muted-foreground mt-8 flex justify-center gap-2 text-center">
                <SearchXIcon class="w-4" />
                <p>No event users found</p>
            </div>
        </DefaultPageLayout>
    </AppLayout>
</template>
