<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';

import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import SkpInput from '@/components/SkpInput.vue';
import TiptapEditor from '@/components/TiptapEditor.vue';
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
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar/';
import { Card, CardContent, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuRadioGroup, DropdownMenuRadioItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover/';
import { ScrollArea } from '@/components/ui/scroll-area/';
import { Separator } from '@/components/ui/separator';
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import DefaultPageLayout from '@/layouts/DefaultPageLayout.vue';
import { cn } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { DateFormatter, DateValue, getLocalTimeZone, parseDate, today } from '@internationalized/date';
import { useDebounceFn, useFetch } from '@vueuse/core';
import { CalendarIcon, ChevronsUpDown, LoaderCircle, PencilIcon } from 'lucide-vue-next';
import { useFilter } from 'reka-ui';
import { computed, onMounted, ref, useTemplateRef, watch } from 'vue';

const props = defineProps(['faculties', 'majors', 'event', 'eventTags', 'eventRoles']);

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Activity',
        href: '/activity',
    },
    {
        title: `Edit Event "${props.event.name}"`,
        href: `/activity/edit-event/${props.event.id}`,
    },
];

const selectedFacultyName = ref('');
const selectedMajorName = ref('');

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
        return majors.find((major: any) => major.name == selectedMajorName.value);
    } else {
        return {};
    }
});

const eventLevels = ['international', 'regional', 'national', 'university', 'faculty', 'major'];
const jobDescriptionInput = useTemplateRef<HTMLInputElement | null>('jobDescription');
const posterInput = useTemplateRef<HTMLInputElement | null>('poster');
const posterImageURL = computed(() => {
    if (eventEditForm.poster) {
        return URL.createObjectURL(eventEditForm.poster);
    }
    return '';
});
const jobDescriptionURL = computed(() => {
    if (eventEditForm.job_description && eventEditForm.job_description !== props.event.job_description) {
        return URL.createObjectURL(eventEditForm.job_description);
    }
    return null;
});
const resetPosterInput = () => {
    if (posterInput.value) {
        eventEditForm.poster = null;
        posterInput.value.value = '';
    }
};
const resetJobDescriptionInput = () => {
    if (jobDescriptionInput.value) {
        eventEditForm.job_description = props.event.job_description;
        jobDescriptionInput.value.value = '';
    }
};

const df = new DateFormatter('en-US', {
    dateStyle: 'long',
});
const startDate = ref<DateValue>();
const endDate = ref<DateValue>();

const startDateText = computed(() => {
    if (startDate.value) {
        return df.format(startDate.value.toDate(getLocalTimeZone()));
    }
    return null;
});

const endDateText = computed(() => {
    if (endDate.value) {
        return df.format(endDate.value.toDate(getLocalTimeZone()));
    }
    return null;
});

const searchTagTerm = ref('');
const tags = ref<any[] | null>(null);
const loadingTags = ref(false);

const searchTag = useDebounceFn(async (term: string) => {
    const { data } = (await useFetch(route('tag.search', { term: term }))
        .get()
        .json()) as any;
    tags.value = data.value.data;
    loadingTags.value = false;
}, 500);

const { startsWith } = useFilter({ sensitivity: 'base' });
const filteredTags = computed(() => {
    if (tags.value) {
        const options = tags.value.filter((i) => !eventEditForm.tags.includes(i.name));
        return searchTagTerm.value ? options.filter((option) => startsWith(option.name, searchTagTerm.value)) : options;
    }
    return [''];
});

const eventEditForm = useForm({
    name: props.event.name ?? '',
    surat_tugas: props.event.surat_tugas.nomor ?? '',
    description: props.event.description ?? '',
    poster: null,
    event_level: props.event.event_level ?? '',
    start_date: props.event.start_date ?? '',
    end_date: props.event.end_date ?? '',
    job_description: props.event.job_description ?? null,
    requirements: JSON.parse(props.event.requirements) ?? {},
    tags: [] as string[],
    faculty_id: selectedFaculty.value.id,
    major_id: selectedMajor.value.id,
});

const addRoleForm = useForm({
    name: '',
    quota: 0,
    skp_id: null,
    permissions: {
        view_event: true,
        edit_event: false,
        delete_event: false,
        create_registration: false,
        edit_registration: false,
        delete_registration: false,
        approve_registration: false,
        manage_participants: false,
        assign_roles: false,
        download_certificates: false,
    },
});

interface IStringIndex {
    [key: string]: any;
}

const updateRoleForm: IStringIndex = useForm({
    name: '',
    quota: 0,
    skp_id: null,
    permissions: {
        view_event: true,
        edit_event: false,
        delete_event: false,
        create_registration: false,
        edit_registration: false,
        delete_registration: false,
        approve_registration: false,
        manage_participants: false,
        assign_roles: false,
        download_certificates: false,
    },
});

const removePoster = () => {
    router.delete(route('event.removePoster', { id: props.event.id }), {
        preserveScroll: true,
        onSuccess: () => eventEditForm.reset('poster'),
    });
};

const submitEventEdit = () => {
    eventEditForm.faculty_id = selectedFaculty.value.id;
    eventEditForm.major_id = selectedMajor.value.id;
    eventEditForm.requirements = JSON.stringify(eventEditForm.requirements);
    if (eventEditForm.job_description === props.event.job_description) {
        eventEditForm.job_description = null;
    }

    eventEditForm.post(route('event.update', { id: props.event.id }), {
        preserveScroll: true,
        onFinish: () => {
            eventEditForm.requirements = JSON.parse(props.event.requirements);
            eventEditForm.job_description = props.event.job_description ?? null;
        },
    });
};

const submitAddRole = () => {
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    addRoleForm.permissions = JSON.stringify(addRoleForm.permissions);
    addRoleForm.post(route('event.addRole', { id: props.event.id }), {
        preserveScroll: true,
        onFinish: () => {
            addRoleForm.reset();
        },
    });
};

const submitUpdateRole = (roleId: number) => {
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    updateRoleForm.permissions = JSON.stringify(updateRoleForm.permissions);
    updateRoleForm.post(route('event.updateRole', { event_id: props.event.id, role_id: roleId }), {
        preserveScroll: true,
        onFinish: () => {
            updateRoleForm.reset();
        },
    });
};

const submitDeleteRole = (roleId: number) => {
    router.delete(`/event/${props.event.id}/edit/delete-role/${roleId}`, {
        preserveScroll: true,
    });
};

onMounted(async () => {
    startDate.value = parseDate(props.event.start_date);
    endDate.value = parseDate(props.event.end_date);

    for (const tag of props.eventTags) {
        eventEditForm.tags.push(tag.name);
    }

    const faculties = props.faculties;
    const faculty = faculties.find((faculty: any) => faculty.id === props.event.faculty_id);
    selectedFacultyName.value = faculty.name;

    const majors = props.majors;
    const major = majors.find((major: any) => major.id === props.event.major_id);
    selectedMajorName.value = major.name;

    const { data } = (await useFetch(route('tag.search', { term: '' }))
        .get()
        .json()) as any;
    tags.value = data.value.data;
});

watch(selectedFacultyName, (newVal, oldVal) => {
    if (newVal !== oldVal && oldVal !== '') {
        selectedMajorName.value = '';
    }
});

watch(startDate, () => {
    if (!startDate.value) {
        endDate.value = undefined;
        eventEditForm.start_date = '';
    } else {
        eventEditForm.start_date = startDate.value.toString();
    }
});

watch(endDate, () => {
    if (!endDate.value) {
        eventEditForm.end_date = '';
    } else {
        eventEditForm.end_date = endDate.value.toString();
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="`Edit event ${props.event.name}`" />
        <DefaultPageLayout v-if="props.event">
            <Heading :title="`Edit event ${props.event.name}`" />
            <form @submit.prevent="submitEventEdit" class="grid max-w-xl gap-6 space-y-2">
                <div class="grid gap-2">
                    <Label for="poster" class="flex w-fit flex-col items-start">
                        <div class="flex flex-row gap-2 text-xs">
                            Poster
                            <span class="opacity-50">(.jpg, .jpeg, .png)</span>
                        </div>
                        <div class="group relative w-fit cursor-pointer overflow-hidden rounded-lg">
                            <div class="absolute h-full w-full bg-black/40 opacity-0 transition-all group-hover:opacity-100"></div>
                            <PencilIcon class="absolute inset-1/2 -translate-1/2 text-white opacity-0 transition-all group-hover:opacity-100" />
                            <img v-if="posterImageURL" :src="posterImageURL" alt="Poster" class="aspect-[4/5] w-3xs" />
                            <img
                                v-else-if="event.poster"
                                :src="route('event.getPoster', { event_id: event.id, filename: event.poster })"
                                alt="Poster Placeholder"
                                class="aspect-[4/5] w-3xs object-fill"
                            />
                            <img v-else src="https://placehold.co/700x875" alt="Poster Placeholder" class="aspect-[4/5] w-3xs object-fill" /></div
                    ></Label>
                    <!-- @vue-ignore copying styles from the component because the component's broken for some reason. Couldn't reset its value -->
                    <input
                        id="poster"
                        ref="poster"
                        type="file"
                        accept="image/png, image/jpeg, image/jpg"
                        @input="eventEditForm.poster = $event.target.files[0]"
                        :class="
                            cn(
                                'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                                'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
                                'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
                            )
                        "
                    />

                    <div class="flex">
                        <AlertDialog>
                            <Button
                                v-show="props.event.poster"
                                variant="link"
                                type="button"
                                class="h-fit w-fit cursor-pointer px-1 py-0 text-left text-sm text-red-300"
                                as-child
                            >
                                <AlertDialogTrigger>Delete Poster</AlertDialogTrigger>
                            </Button>
                            <AlertDialogContent>
                                <AlertDialogHeader>
                                    <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                                    <AlertDialogDescription> This action cannot be undone. </AlertDialogDescription>
                                </AlertDialogHeader>
                                <AlertDialogFooter>
                                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                                    <AlertDialogAction @click="removePoster" type="button" class="h-fit w-fit justify-start">
                                        Delete Poster
                                    </AlertDialogAction>
                                </AlertDialogFooter>
                            </AlertDialogContent>
                        </AlertDialog>
                        <Button
                            v-show="eventEditForm.poster"
                            variant="link"
                            type="button"
                            class="h-fit w-fit cursor-pointer px-1 py-0 text-left text-sm text-red-300"
                            @click="resetPosterInput"
                            >Reset Poster</Button
                        >
                    </div>
                    <InputError :message="eventEditForm.errors.poster" />
                </div>
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus autocomplete="name" v-model="eventEditForm.name" placeholder="Event name/title" />
                    <InputError :message="eventEditForm.errors.name" />
                </div>
                <div class="grid gap-2">
                    <div class="flex text-xs gap-2">
                        <Label for="suratTugas">Nomor Surat Tugas</Label>
                        <span class="opacity-50">(dibutuhkan sebelum membuat registrasi peserta)</span>
                    </div>
                    <Input id="suratTugas" type="text" required autofocus autocomplete="suratTugas" v-model="eventEditForm.surat_tugas" placeholder="Nomor Surat Tugas" />
                    <InputError :message="eventEditForm.errors.surat_tugas" />
                </div>
                <div class="grid gap-2" v-if="tags">
                    <Label for="tagsInput">Tags</Label>
                    <TagsInput v-model="eventEditForm.tags">
                        <div class="flex min-h-5 w-full flex-row flex-wrap gap-2">
                            Added Tags:
                            <TagsInputItem v-for="item in eventEditForm.tags" :key="item" :value="item" class="w-fit">
                                <TagsInputItemText />
                                <TagsInputItemDelete />
                            </TagsInputItem>
                        </div>
                        <Separator />
                        <TagsInputInput
                            placeholder="Add tags"
                            @input.prevent="
                                (event: any) => {
                                    searchTagTerm = event.target.value;
                                    loadingTags = true;
                                    searchTag(searchTagTerm);
                                }
                            "
                            @keydown.enter.prevent="
                                (event: any) => {
                                    if (!eventEditForm.tags.includes(event.target.value) && event.target.value !== '')
                                        eventEditForm.tags.push(event.target.value.trim().toLowerCase());
                                    event.target.value = '';
                                    searchTagTerm = '';
                                    loadingTags = true;
                                    searchTag(searchTagTerm);
                                }
                            "
                        />
                    </TagsInput>
                    <div class="relative flex flex-row flex-wrap items-center gap-2">
                        <span class="text-xs">Suggestions: </span>
                        <Button
                            type="button"
                            variant="outline"
                            @click.prevent="eventEditForm.tags.push(tag.name)"
                            class="h-3 w-fit cursor-pointer p-3 text-xs"
                            v-for="(tag, index) of filteredTags"
                            :key="index"
                        >
                            {{ `${tag.name} ${tag.events_count}` }}
                        </Button>
                        <LoaderCircle v-show="loadingTags" class="absolute right-3 animate-spin opacity-50" />
                    </div>
                    <InputError :message="eventEditForm.errors.tags" />
                </div>
                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Textarea id="description" required autofocus v-model="eventEditForm.description" placeholder="Description" />
                    <InputError :message="eventEditForm.errors.description" />
                </div>
                <div class="grid gap-2">
                    <Label for="eventLevel">Event Level</Label>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline" type="button" class="w-fit text-center" id="eventLevel">
                                {{
                                    eventEditForm.event_level == ''
                                        ? 'Event Level'
                                        : eventEditForm.event_level.charAt(0).toUpperCase() + eventEditForm.event_level.slice(1)
                                }}
                                <ChevronsUpDown />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent class="w-56" align="start">
                            <ScrollArea class="max-h-80">
                                <DropdownMenuRadioGroup v-model="eventEditForm.event_level">
                                    <DropdownMenuRadioItem v-for="(eventLevel, index) of eventLevels" :key="index" :value="eventLevel">
                                        {{ eventLevel.charAt(0).toUpperCase() + eventLevel.slice(1) }}
                                    </DropdownMenuRadioItem>
                                </DropdownMenuRadioGroup>
                            </ScrollArea>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <InputError v-if="eventEditForm.errors.event_level" message="Please choose the event level" />
                </div>
                <div class="grid gap-2">
                    <Label for="faculty">Faculty</Label>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline" type="button" class="w-fit text-center" id="faculty">
                                {{ selectedFacultyName == '' ? 'Choose Faculty' : selectedFacultyName }}
                                <ChevronsUpDown />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent class="w-56" align="start">
                            <ScrollArea class="max-h-80">
                                <DropdownMenuRadioGroup v-model="selectedFacultyName">
                                    <DropdownMenuRadioItem v-for="faculty in props.faculties" :key="faculty.id" :value="faculty.name">
                                        {{ faculty.name }}
                                    </DropdownMenuRadioItem>
                                </DropdownMenuRadioGroup>
                            </ScrollArea>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <InputError v-if="eventEditForm.errors.faculty_id" message="Please choose your faculty" />
                </div>

                <div class="grid gap-2">
                    <Label for="major">Major</Label>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child :disabled="selectedFacultyName == '' ? true : false">
                            <Button variant="outline" type="button" class="w-fit text-center" id="major">
                                {{ selectedMajorName == '' ? 'Choose Major' : selectedMajorName }}
                                <ChevronsUpDown />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent class="w-56" align="start">
                            <ScrollArea class="max-h-80">
                                <DropdownMenuRadioGroup v-model="selectedMajorName">
                                    <DropdownMenuRadioItem v-if="selectedFacultyName != 'Any'" value="Any"> Any </DropdownMenuRadioItem>
                                    <DropdownMenuRadioItem v-for="major in facultyMajors" :key="major.id" :value="major.name">
                                        {{ major.name }}
                                    </DropdownMenuRadioItem>
                                </DropdownMenuRadioGroup>
                            </ScrollArea>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <InputError v-if="eventEditForm.errors.major_id" message="Please choose your major" />
                </div>
                <div class="grid gap-2">
                    <Label for="endDate">Date</Label>
                    <Popover>
                        <PopoverTrigger as-child>
                            <Button
                                id="endDate"
                                variant="outline"
                                type="button"
                                :class="cn('w-fit justify-start text-left font-normal', (!endDate || !startDate) && 'text-muted-foreground')"
                            >
                                <CalendarIcon class="mr-2 h-4 w-4" />
                                {{ startDate && endDate ? `${startDateText} - ${endDateText}` : 'Pick a date' }}
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="flex w-auto flex-row items-center p-0" align="start">
                            <Calendar v-model="startDate" initial-focus :min-value="today(getLocalTimeZone())" />
                            until
                            <Calendar v-model="endDate" :min-value="startDate" :disabled="!startDate" />
                        </PopoverContent>
                    </Popover>
                    <InputError :message="eventEditForm.errors.start_date" />
                    <InputError :message="eventEditForm.errors.end_date" />
                </div>
                <div class="grid gap-2">
                    <div class="flex flex-row gap-2 text-xs">
                        <Label for="jobDescription">Job Description File</Label>
                        <span class="opacity-50">(.pdf)</span>
                    </div>
                    <!-- @vue-ignore copying styles from the component because the component's broken for some reason. Couldn't reset its value -->
                    <input
                        id="jobDescription"
                        ref="jobDescription"
                        type="file"
                        accept=".pdf"
                        @input="eventEditForm.job_description = $event.target.files[0]"
                        :class="
                            cn(
                                'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                                'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
                                'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
                            )
                        "
                    />
                    <iframe
                        :src="
                            jobDescriptionURL ?? route('event.getJobDescription', { event_id: props.event.id, filename: props.event.job_description })
                        "
                        frameborder="0"
                        class="h-80 w-xl"
                    ></iframe>
                    <Button
                        v-show="eventEditForm.job_description && eventEditForm.job_description !== props.event.job_description"
                        variant="link"
                        type="button"
                        class="h-fit w-fit cursor-pointer px-1 py-0 text-left text-sm text-red-300"
                        @click="resetJobDescriptionInput"
                        >Reset File</Button
                    >
                    <InputError :message="eventEditForm.errors.job_description" />
                </div>
                <div class="grid gap-2">
                    <div class="flex flex-row gap-2 text-xs">
                        <Label for="requirements">Requirements</Label>
                        <span class="opacity-50">(optional)</span>
                    </div>
                    <TiptapEditor v-model="eventEditForm.requirements" />
                    <InputError :message="eventEditForm.errors.requirements" />
                </div>
                <div class="flex items-center gap-4">
                    <Button :disabled="eventEditForm.processing" class="cursor-pointer bg-blue-500 text-white transition-all hover:bg-blue-600"
                        >Save</Button
                    >
                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-show="eventEditForm.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                    </Transition>
                </div>
            </form>

            <!-- Event Role Editor -->
            <div class="mt-12 flex flex-row items-center gap-5">
                <Heading title="Edit Event Roles" class="mb-0!" />
                <Dialog>
                    <DialogTrigger as-child>
                        <Button variant="outline">Add Role</Button>
                    </DialogTrigger>
                    <DialogContent class="sm:max-w-3xl">
                        <form @submit.prevent="submitAddRole" class="grid">
                            <DialogHeader>
                                <DialogTitle>Add role</DialogTitle>
                                <DialogDescription>Add a role and its SKP estimation</DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="grid grid-cols-8 items-center gap-4">
                                    <Label for="addRoleName" class="text-right"> Name </Label>
                                    <Input id="addRoleName" v-model="addRoleForm.name" class="col-span-7" placeholder="Anggota Sie PDD" />
                                </div>
                                <div class="grid grid-cols-8 items-center gap-4">
                                    <Label for="addRoleSkp" class="text-right"> SKP </Label>
                                    <SkpInput id="addRoleSkp" v-model="addRoleForm.skp_id" class="col-span-7" />
                                </div>
                                <div class="grid grid-cols-8 items-center gap-4">
                                    <Label for="addRoleQuota" class="text-right"> Quota </Label>
                                    <Input id="addRoleQuota" type="number" v-model="addRoleForm.quota" class="col-span-7" />
                                </div>
                                <div class="grid grid-cols-8 items-center gap-4">
                                    <Label class="text-right"> Permissions </Label>
                                    <div class="col-span-8 grid grid-cols-3 gap-4">
                                        <div class="col-span-1 flex flex-row items-center gap-2">
                                            <Checkbox id="addRoleViewEventPermission" v-model="addRoleForm.permissions.view_event" />
                                            <Label for="addRoleViewEventPermission" class="text-right"> View Event </Label>
                                        </div>
                                        <div class="col-span-1 flex flex-row items-center gap-2">
                                            <Checkbox id="addRoleEditEventPermission" v-model="addRoleForm.permissions.edit_event" />
                                            <Label for="addRoleEditEventPermission" class="text-right"> Edit Event </Label>
                                        </div>
                                        <div class="col-span-1 flex flex-row items-center gap-2">
                                            <Checkbox id="addRoleDeleteEvent" v-model="addRoleForm.permissions.delete_event" />
                                            <Label for="addRoleDeleteEvent" class="text-right"> Delete Event </Label>
                                        </div>
                                        <div class="col-span-1 flex flex-row items-center gap-2">
                                            <Checkbox
                                                id="addRoleCreateRegistrationPermission"
                                                v-model="addRoleForm.permissions.create_registration"
                                            />
                                            <Label for="addRoleCreateRegistrationPermission" class="text-right"> Create Registration </Label>
                                        </div>
                                        <div class="col-span-1 flex flex-row items-center gap-2">
                                            <Checkbox id="addRoleEditRegistrationPermission" v-model="addRoleForm.permissions.edit_registration" />
                                            <Label for="addRoleEditRegistrationPermission" class="text-right"> Edit Registration </Label>
                                        </div>
                                        <div class="col-span-1 flex flex-row items-center gap-2">
                                            <Checkbox
                                                id="addRoleDeleteRegistrationPermission"
                                                v-model="addRoleForm.permissions.delete_registration"
                                            />
                                            <Label for="addRoleDeleteRegistrationPermission" class="text-right"> Delete Registration </Label>
                                        </div>
                                        <div class="col-span-1 flex flex-row items-center gap-2">
                                            <Checkbox
                                                id="addRoleApproveRegistrationPermission"
                                                v-model="addRoleForm.permissions.approve_registration"
                                            />
                                            <Label for="addRoleApproveRegistrationPermission" class="text-right"> Approve Registration </Label>
                                        </div>
                                        <div class="col-span-1 flex flex-row items-center gap-2">
                                            <Checkbox
                                                id="addRoleManageParticipantsPermission"
                                                v-model="addRoleForm.permissions.manage_participants"
                                            />
                                            <Label for="addRoleManageParticipantsPermission" class="text-right"> Manage Participants </Label>
                                        </div>
                                        <div class="col-span-1 flex flex-row items-center gap-2">
                                            <Checkbox id="addRoleAssignRolesPermission" v-model="addRoleForm.permissions.assign_roles" />
                                            <Label for="addRoleAssignRolesPermission" class="text-right"> Assign Roles </Label>
                                        </div>
                                        <div class="col-span-1 flex flex-row items-center gap-2">
                                            <Checkbox
                                                id="addRoleDownloadCertificatesPermission"
                                                v-model="addRoleForm.permissions.download_certificates"
                                            />
                                            <Label for="addRoleDownloadCertificatesPermission" class="text-right"> Download Certificates </Label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <DialogFooter>
                                <Button type="submit" :disabled="addRoleForm.processing"> Add New Role </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
            </div>

            <!-- Event Role Cards -->
            <div class="mt-5 flex flex-row flex-wrap gap-5">
                <Card class="w-lg" v-for="role of eventRoles" :key="role.id">
                    <CardHeader>
                        <CardTitle>{{ role.name }}</CardTitle>
                    </CardHeader>
                    <CardContent class="text-foreground/70 text-sm">
                        <p v-if="role.detail_skp">Kategori SKP: {{ role.detail_skp.category }}</p>
                        <p v-if="role.detail_skp">Deskripsi SKP: {{ `${role.detail_skp.description} -- ${role.detail_skp.role}` }}</p>
                        <p v-if="role.detail_skp">Nilai SKP: {{ role.detail_skp.skp }}</p>
                        <p>Quota: {{ role.quota }}</p>
                        <div>
                            <p>Permissions:</p>
                            <ul class="list-disc pl-8">
                                <li v-for="permission of role.permissions" :key="permission.id">{{ permission.name }}</li>
                            </ul>
                        </div>
                    </CardContent>
                    <CardFooter class="gap-3">
                        <Dialog>
                            <DialogTrigger as-child>
                                <Button variant="outline">Edit</Button>
                            </DialogTrigger>
                            <DialogContent class="sm:max-w-3xl">
                                <form @submit.prevent="submitUpdateRole(role.id)" class="grid">
                                    <DialogHeader>
                                        <DialogTitle>Update role</DialogTitle>
                                        <DialogDescription>Update a role and its SKP estimation</DialogDescription>
                                    </DialogHeader>
                                    <div class="grid gap-4 py-4">
                                        <div class="grid grid-cols-8 items-center gap-4">
                                            <Label for="updateRoleName" class="text-right"> Name </Label>
                                            <Input id="updateRoleName" v-model="updateRoleForm.name" class="col-span-7" :placeholder="role.name" />
                                        </div>
                                        <div class="grid grid-cols-8 items-center gap-4">
                                            <Label for="updateRoleSkp" class="text-right"> SKP </Label>
                                            <SkpInput id="updateRoleSkp" v-model="updateRoleForm.skp_id" class="col-span-7" />
                                        </div>
                                        <div class="grid grid-cols-8 items-center gap-4">
                                            <Label for="updateRoleQuota" class="text-right"> Quota </Label>
                                            <Input id="updateRoleQuota" type="number" v-model="updateRoleForm.quota" class="col-span-7" />
                                        </div>
                                        <div class="grid grid-cols-8 items-center gap-4">
                                            <Label class="text-right"> Permissions </Label>
                                            <div class="col-span-8 grid grid-cols-3 gap-4">
                                                <div class="col-span-1 flex flex-row items-center gap-2">
                                                    <Checkbox id="updateRoleViewEventPermission" v-model="updateRoleForm.permissions.view_event" />
                                                    <Label for="updateRoleViewEventPermission" class="text-right"> View Event </Label>
                                                </div>
                                                <div class="col-span-1 flex flex-row items-center gap-2">
                                                    <Checkbox id="updateRoleEditEventPermission" v-model="updateRoleForm.permissions.edit_event" />
                                                    <Label for="updateRoleEditEventPermission" class="text-right"> Edit Event </Label>
                                                </div>
                                                <div class="col-span-1 flex flex-row items-center gap-2">
                                                    <Checkbox id="updateRoleDeleteEvent" v-model="updateRoleForm.permissions.delete_event" />
                                                    <Label for="updateRoleDeleteEvent" class="text-right"> Delete Event </Label>
                                                </div>
                                                <div class="col-span-1 flex flex-row items-center gap-2">
                                                    <Checkbox
                                                        id="updateRoleCreateRegistrationPermission"
                                                        v-model="updateRoleForm.permissions.create_registration"
                                                    />
                                                    <Label for="updateRoleCreateRegistrationPermission" class="text-right">
                                                        Create Registration
                                                    </Label>
                                                </div>
                                                <div class="col-span-1 flex flex-row items-center gap-2">
                                                    <Checkbox
                                                        id="updateRoleEditRegistrationPermission"
                                                        v-model="updateRoleForm.permissions.edit_registration"
                                                    />
                                                    <Label for="updateRoleEditRegistrationPermission" class="text-right"> Edit Registration </Label>
                                                </div>
                                                <div class="col-span-1 flex flex-row items-center gap-2">
                                                    <Checkbox
                                                        id="updateRoleDeleteRegistrationPermission"
                                                        v-model="updateRoleForm.permissions.delete_registration"
                                                    />
                                                    <Label for="updateRoleDeleteRegistrationPermission" class="text-right">
                                                        Delete Registration
                                                    </Label>
                                                </div>
                                                <div class="col-span-1 flex flex-row items-center gap-2">
                                                    <Checkbox
                                                        id="updateRoleApproveRegistrationPermission"
                                                        v-model="updateRoleForm.permissions.approve_registration"
                                                    />
                                                    <Label for="updateRoleApproveRegistrationPermission" class="text-right">
                                                        Approve Registration
                                                    </Label>
                                                </div>
                                                <div class="col-span-1 flex flex-row items-center gap-2">
                                                    <Checkbox
                                                        id="updateRoleManageParticipantsPermission"
                                                        v-model="updateRoleForm.permissions.manage_participants"
                                                    />
                                                    <Label for="updateRoleManageParticipantsPermission" class="text-right">
                                                        Manage Participants
                                                    </Label>
                                                </div>
                                                <div class="col-span-1 flex flex-row items-center gap-2">
                                                    <Checkbox
                                                        id="updateRoleAssignRolesPermission"
                                                        v-model="updateRoleForm.permissions.assign_roles"
                                                    />
                                                    <Label for="updateRoleAssignRolesPermission" class="text-right"> Assign Roles </Label>
                                                </div>
                                                <div class="col-span-1 flex flex-row items-center gap-2">
                                                    <Checkbox
                                                        id="updateRoleDownloadCertificatesPermission"
                                                        v-model="updateRoleForm.permissions.download_certificates"
                                                    />
                                                    <Label for="updateRoleDownloadCertificatesPermission" class="text-right">
                                                        Download Certificates
                                                    </Label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <DialogFooter>
                                        <Button type="submit" :disabled="updateRoleForm.processing"> Update Role </Button>
                                    </DialogFooter>
                                </form>
                            </DialogContent>
                        </Dialog>
                        <Button class="text-foreground bg-red-500 hover:bg-red-700" @click.prevent="submitDeleteRole(role.id)">Delete</Button>
                    </CardFooter>
                </Card>
            </div>

        </DefaultPageLayout>
    </AppLayout>
</template>
