<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Avatar, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuRadioGroup, DropdownMenuRadioItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area';
import { useInitials } from '@/composables/useInitials';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { cn } from '@/lib/utils';
import { type BreadcrumbItem, type SharedData, type User } from '@/types';
import { ChevronsUpDown, Pencil } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    faculties: any;
    majors: any;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage<SharedData>();
const user = computed(() => page.props.auth.user as User);

const selectedFacultyName = ref('');
const selectedMajorName = ref('');

const initialFaculty = props.faculties.find((faculty: any) => faculty.id === user.value.faculty_id);
const initialMajor = props.majors.find((major: any) => major.id === user.value.major_id);

selectedFacultyName.value = initialFaculty.name;
selectedMajorName.value = initialMajor.name;

const selectedFaculty = computed(() => {
    if (selectedFacultyName.value) {
        const faculties = props.faculties;
        return faculties.find((faculty: any) => faculty.name == selectedFacultyName.value);
    } else {
        return {};
    }
});

const facultyMajors = computed(() => {
    if (selectedFaculty.value) {
        const faculty = selectedFaculty.value;
        return props.majors.filter((major: any) => major.faculty_id === faculty.id);
    } else {
        return [];
    }
});

const selectedMajor = computed(() => {
    if (selectedMajorName.value) {
        const majors = props.majors;
        return majors.find((faculty: any) => faculty.name == selectedMajorName.value);
    } else {
        return {};
    }
});

const form = useForm({
    avatar: null,
    nim: user.value.nim,
    name: user.value.name,
    email: user.value.email,
    faculty_id: user.value.faculty_id,
    major_id: user.value.major_id,
});

watch(selectedFacultyName, async () => {
    selectedMajorName.value = '';
});

// Compute whether we should show the avatar image
const { getInitials } = useInitials();
const newAvatar = computed(() => {
    if (form.avatar) {
        return URL.createObjectURL(form.avatar);
    }
    return '';
});

const submit = () => {
    form.faculty_id = selectedFaculty.value.id;
    form.major_id = selectedMajor.value.id;
    form.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset('avatar'),
    });
};

const removeAvatar = () => {
    router.delete(route('profile.removeAvatar'), {
        preserveScroll: true,
        onSuccess: () => form.reset('avatar'),
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <div class="flex flex-row items-center">
                            <Label for="avatar">
                                <Avatar class="group relative size-18 overflow-hidden rounded-lg">
                                    <div class="absolute h-full w-full bg-black/40 opacity-0 transition-all group-hover:opacity-100"></div>
                                    <Pencil class="absolute inset-1/2 -translate-1/2 text-white opacity-0 transition-all group-hover:opacity-100" />
                                    <AvatarImage v-if="newAvatar == '' && user.avatar" :src="`/storage/${user.avatar}`" :alt="user.name" />
                                    <AvatarImage v-if="newAvatar" :src="newAvatar" :alt="user.name" />

                                    <!-- Custom avatar fallback -->
                                    <div
                                        v-show="!newAvatar && !user.avatar"
                                        :class="cn('bg-muted flex size-full items-center justify-center rounded-full')"
                                    >
                                        {{ getInitials(user.name) }}
                                    </div>
                                </Avatar>
                            </Label>
                            <div class="ml-4 flex flex-col gap-2">
                                <Label for="avatar" class="w-fit">Choose Avatar</Label>
                                <!-- @vue-ignore copying styles from the component because the component's broken for some reason. Couldn't reset its value -->
                                <input
                                    id="avatar"
                                    type="file"
                                    @input="form.avatar = $event.target.files[0]"
                                    :class="
                                        cn(
                                            'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                                            'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
                                            'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
                                        )
                                    "
                                />
                                <AlertDialog>
                                    <AlertDialogTrigger v-show="user.avatar || form.avatar" class="text-left text-sm text-red-300"
                                        >Delete Photo</AlertDialogTrigger
                                    >
                                    <AlertDialogContent>
                                        <AlertDialogHeader>
                                            <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                                            <AlertDialogDescription>
                                                This action cannot be undone. This will permanently delete your account and remove your data from our
                                                servers.
                                            </AlertDialogDescription>
                                        </AlertDialogHeader>
                                        <AlertDialogFooter>
                                            <AlertDialogCancel>Cancel</AlertDialogCancel>
                                            <AlertDialogAction @click="removeAvatar" type="button" class="h-fit w-fit justify-start">
                                                Delete Photo
                                            </AlertDialogAction>
                                        </AlertDialogFooter>
                                    </AlertDialogContent>
                                </AlertDialog>
                            </div>
                        </div>
                        <InputError class="mt-2" :message="form.errors.avatar" />
                    </div>

                    <div v-if="user.type == 'student'" class="grid gap-2">
                        <Label for="nim" class="w-fit">NIM</Label>
                        <Input id="nim" class="mt-1 block w-full" v-model="form.nim" placeholder="Nomor Induk Mahasiswa" />
                        <InputError class="mt-2" :message="form.errors.nim" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="name">Name</Label>
                        <Input id="name" class="mt-1 block w-full" v-model="form.name" placeholder="Full name" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified_at">
                        <p class="text-muted-foreground -mt-4 text-sm">
                            Your email address is unverified.
                            <Link
                                :href="route('verification.send')"
                                method="post"
                                as="button"
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="faculty">Faculty</Label>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline" class="w-fit text-center" id="faculty">
                                    {{ selectedFacultyName == '' ? 'Choose Faculty' : selectedFacultyName }}
                                    <ChevronsUpDown />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-56">
                                <ScrollArea class="max-h-80">
                                    <DropdownMenuRadioGroup v-model="selectedFacultyName">
                                        <DropdownMenuRadioItem v-for="faculty in props.faculties" :key="faculty.id" :value="faculty.name">
                                            {{ faculty.name }}
                                        </DropdownMenuRadioItem>
                                    </DropdownMenuRadioGroup>
                                </ScrollArea>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <InputError v-if="form.errors.faculty_id" message="Please choose your faculty" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="major">Major</Label>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child :disabled="selectedFacultyName == '' ? true : false">
                                <Button variant="outline" class="w-fit text-center" id="major">
                                    {{ selectedMajorName == '' ? 'Choose Major' : selectedMajorName }}
                                    <ChevronsUpDown />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent class="w-56">
                                <ScrollArea class="max-h-80">
                                    <DropdownMenuRadioGroup v-model="selectedMajorName">
                                        <DropdownMenuRadioItem v-if="selectedFacultyName != 'Any' && user.type == 'organization'" value="Any">
                                            Any
                                        </DropdownMenuRadioItem>
                                        <DropdownMenuRadioItem v-for="major in facultyMajors" :key="major.id" :value="major.name">
                                            {{ major.name }}
                                        </DropdownMenuRadioItem>
                                    </DropdownMenuRadioGroup>
                                </ScrollArea>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <InputError v-if="form.errors.major_id" message="Please choose your major" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing" class="cursor-pointer bg-blue-500 text-white transition-all hover:bg-blue-600"
                            >Save</Button
                        >

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
                </form>
            </div>

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
