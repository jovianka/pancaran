<script lang="ts" setup>
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import { ChevronsUpDown, PlusIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import InputError from './InputError.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuRadioGroup, DropdownMenuRadioItem, DropdownMenuTrigger } from './ui/dropdown-menu';
import { ScrollArea } from './ui/scroll-area';

const props = defineProps(['eventRoles', 'buttonClass', 'eventId']);

const form = useForm({
    role_id: 0,
    title: '',
    body_message: '',
    recipient_nim: '',
    event_id: props.eventId,
});

const roleIdString = ref('');
const roleId = computed(() => {
    return Number(roleIdString.value);
});

const inviteDialogState = ref(false);

const selectableEventRoles = computed(() => {
    return props.eventRoles.filter((role: any) => role.name.toUpperCase() !== 'ADMIN');
});

const sendInvitation = () => {
    form.role_id = roleId.value;
    form.post(route('members.sendInvitation'), {
        preserveState: true,
        onSuccess: () => {
            inviteDialogState.value = false;
        },
    });
};
</script>

<template>
    <Dialog v-model:open="inviteDialogState">
        <DialogTrigger as-child>
            <Button variant="outline" :class="props.buttonClass">
                Invite Member
                <PlusIcon />
            </Button>
        </DialogTrigger>
        <DialogContent class="sm:max-w-3xl">
            <form @submit.prevent="sendInvitation()" class="grid">
                <DialogHeader>
                    <DialogTitle>Invite a Member</DialogTitle>
                    <DialogDescription>Invite a member and their role</DialogDescription>
                </DialogHeader>
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-8 items-center gap-4">
                        <Label for="role" class="col-span-2"> Role </Label>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <div class="col-span-6 flex flex-col">
                                    <Button variant="outline" type="button" class="w-fit text-center" id="eventLevel">
                                        {{ roleId == 0 ? 'Pick a Role' : eventRoles.find((role: any) => role.id === roleId).name }}
                                        <ChevronsUpDown />
                                    </Button>
                                    <InputError v-if="form.errors.role_id" message="Please pick a valid role" />
                                </div>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-56" align="start">
                                <ScrollArea class="max-h-80">
                                    <DropdownMenuRadioGroup v-model="roleIdString">
                                        <DropdownMenuRadioItem v-for="(role, index) of selectableEventRoles" :key="index" :value="String(role.id)">
                                            {{ role.name.toUpperCase() }}
                                        </DropdownMenuRadioItem>
                                    </DropdownMenuRadioGroup>
                                </ScrollArea>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                    <div class="grid grid-cols-8 items-center gap-4">
                        <Label for="title" class="col-span-2"> Title </Label>
                        <div class="col-span-6 flex flex-col">
                            <Input required id="title" type="text" v-model="form.title" class="w-full" />
                            <InputError v-if="form.errors.title" message="Please fill invitation title" />
                        </div>
                    </div>
                    <div class="grid grid-cols-8 items-center gap-4">
                        <Label for="body" class="col-span-2"> Body (message) </Label>
                        <div class="col-span-6 flex flex-col">
                            <Input required id="body" type="text" v-model="form.body_message" class="w-full" />
                            <InputError v-if="form.errors.body_message" message="Please fill invitation body" />
                        </div>
                    </div>
                    <div class="grid grid-cols-8 items-center gap-4">
                        <Label for="recipientNIM" class="col-span-2"> Recipient NIM </Label>
                        <div class="col-span-6 flex flex-col">
                            <Input required id="recipientNIM" type="text" v-model="form.recipient_nim" class="w-full" />
                            <InputError v-if="form.errors.recipient_nim" message="User not found" />
                        </div>
                    </div>
                </div>
                <DialogFooter>
                    <Button type="submit" :disabled="form.processing"> Send Invitation </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
