<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import TiptapEditor from '@/components/TiptapEditor.vue';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar/';
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
import { DateFormatter, DateValue, getLocalTimeZone, today } from '@internationalized/date';
import { useDebounceFn, useFetch } from '@vueuse/core';
import { CalendarIcon, ChevronsUpDown, LoaderCircle, PencilIcon } from 'lucide-vue-next';
import { useFilter } from 'reka-ui';
import { computed, onMounted, ref, useTemplateRef, watch } from 'vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Activity',
        href: '/activity',
    },
    {
        title: 'Create Event',
        href: '/event/create',
    },
];

const props = defineProps(['faculties', 'majors']);

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
        return majors.find((faculty: any) => faculty.name == selectedMajorName.value);
    } else {
        return {};
    }
});

const eventLevels = ['international', 'regional', 'national', 'university', 'faculty', 'major'];
const jobDescriptionInput = useTemplateRef<HTMLInputElement | null>('jobDescription');
const posterInput = useTemplateRef<HTMLInputElement | null>('poster');
const posterImageURL = computed(() => {
    if (form.poster) {
        return URL.createObjectURL(form.poster);
    }
    return '';
});
const resetPosterInput = () => {
    if (posterInput.value) {
        form.poster = null;
        posterInput.value.value = '';
    }
};
const resetJobDescriptionInput = () => {
    if (jobDescriptionInput.value) {
        form.job_description = null;
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

const searchTerm = ref('');
const tags = ref<any[] | null>(null);
const loadingTags = ref(false);

onMounted(async () => {
    const { data } = (await useFetch(route('tag.search', { term: '' }))
        .get()
        .json()) as any;
    tags.value = data.value.data;
});

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
        const options = tags.value.filter((i) => !form.tags.includes(i.name));
        return searchTerm.value ? options.filter((option) => startsWith(option.name, searchTerm.value)) : options;
    }
    return [''];
});

const form = useForm({
    name: '',
    description: '',
    poster: null,
    event_level: '',
    start_date: '',
    end_date: '',
    job_description: null,
    requirements: {},
    tags: [] as string[],
    faculty_id: selectedFaculty.value.id,
    major_id: selectedMajor.value.id,
});

const submit = () => {
    form.faculty_id = selectedFaculty.value.id;
    form.major_id = selectedMajor.value.id;
    form.requirements = JSON.stringify(form.requirements);
    form.post(route('event.store'), {
        preserveScroll: true,
    });
};

watch(selectedFacultyName, async () => {
    selectedMajorName.value = '';
});

watch(startDate, () => {
    if (!startDate.value) {
        endDate.value = undefined;
        form.start_date = '';
    } else {
        form.start_date = startDate.value.toString();
    }
});

watch(endDate, () => {
    if (endDate.value) {
        form.end_date = endDate.value.toString();
    } else {
        form.end_date = '';
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Create an Event" />
        <DefaultPageLayout>
            <Heading title="Create an Event" />
            <form @submit.prevent="submit" class="grid max-w-xl gap-6 space-y-2">
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
                            <img v-else src="https://placehold.co/700x875" alt="Poster Placeholder" class="aspect-[4/5] w-3xs object-fill" /></div
                    ></Label>
                    <!-- @vue-ignore copying styles from the component because the component's broken for some reason. Couldn't reset its value -->
                    <input
                        id="poster"
                        ref="poster"
                        type="file"
                        accept="image/png, image/jpeg, image/jpg"
                        @input="form.poster = $event.target.files[0]"
                        :class="
                            cn(
                                'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 max-w-64 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                                'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
                                'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
                            )
                        "
                    />

                    <Button
                        v-show="form.poster"
                        variant="link"
                        type="button"
                        class="h-fit w-fit cursor-pointer px-1 py-0 text-left text-sm text-red-300"
                        @click="resetPosterInput"
                        >Remove Poster</Button
                    >
                    <InputError :message="form.errors.poster" />
                </div>
                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus autocomplete="name" v-model="form.name" placeholder="Event name/title" />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="grid gap-2" v-if="tags">
                    <Label for="tagsInput">Tags</Label>
                    <TagsInput v-model="form.tags">
                        <div class="flex min-h-5 w-full flex-row flex-wrap gap-2">
                            Added Tags:
                            <TagsInputItem v-for="item in form.tags" :key="item" :value="item" class="w-fit">
                                <TagsInputItemText />
                                <TagsInputItemDelete />
                            </TagsInputItem>
                        </div>
                        <Separator />
                        <TagsInputInput
                            placeholder="Add tags"
                            @input.prevent="
                                (event: any) => {
                                    searchTerm = event.target.value;
                                    loadingTags = true;
                                    searchTag(searchTerm);
                                }
                            "
                            @keydown.enter.prevent="
                                (event: any) => {
                                    if (!form.tags.includes(event.target.value) && event.target.value !== '')
                                        form.tags.push(event.target.value.trim().toLowerCase());
                                    event.target.value = '';
                                    searchTerm = '';
                                    loadingTags = true;
                                    searchTag(searchTerm);
                                }
                            "
                        />
                    </TagsInput>
                    <div class="relative flex flex-row flex-wrap items-center gap-2">
                        <span class="text-xs">Suggestions: </span>
                        <Button
                            type="button"
                            variant="outline"
                            @click.prevent="form.tags.push(tag.name)"
                            class="h-3 w-fit cursor-pointer p-3 text-xs"
                            v-for="(tag, index) of filteredTags"
                            :key="index"
                        >
                            {{ `${tag.name} ${tag.events_count}` }}
                        </Button>
                        <LoaderCircle v-show="loadingTags" class="absolute right-3 animate-spin opacity-50" />
                    </div>
                    <InputError :message="form.errors.tags" />
                </div>
                <div class="grid gap-2">
                    <Label for="description">Description</Label>
                    <Textarea id="description" required autofocus v-model="form.description" placeholder="Description" />
                    <InputError :message="form.errors.description" />
                </div>
                <div class="grid gap-2">
                    <Label for="eventLevel">Event Level</Label>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline" type="button" class="w-fit text-center" id="eventLevel">
                                {{ form.event_level == '' ? 'Event Level' : form.event_level.charAt(0).toUpperCase() + form.event_level.slice(1) }}
                                <ChevronsUpDown />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent class="w-56" align="start">
                            <ScrollArea class="max-h-80">
                                <DropdownMenuRadioGroup v-model="form.event_level">
                                    <DropdownMenuRadioItem v-for="(eventLevel, index) of eventLevels" :key="index" :value="eventLevel">
                                        {{ eventLevel.charAt(0).toUpperCase() + eventLevel.slice(1) }}
                                    </DropdownMenuRadioItem>
                                </DropdownMenuRadioGroup>
                            </ScrollArea>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <InputError v-if="form.errors.event_level" message="Please choose the event level" />
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
                    <InputError v-if="form.errors.faculty_id" message="Please choose your faculty" />
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
                    <InputError v-if="form.errors.major_id" message="Please choose your major" />
                </div>
                <div class="grid gap-2">
                    <Label for="endDate">Date</Label>
                    <Popover>
                        <PopoverTrigger as-child id="endDate">
                            <Button
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
                    <InputError :message="form.errors.start_date" />
                    <InputError :message="form.errors.end_date" />
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
                        required
                        accept=".pdf"
                        @input="form.job_description = $event.target.files[0]"
                        :class="
                            cn(
                                'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                                'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
                                'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
                            )
                        "
                    />
                    <Button
                        v-show="form.job_description"
                        variant="link"
                        type="button"
                        class="h-fit w-fit cursor-pointer px-1 py-0 text-left text-sm text-red-300"
                        @click="resetJobDescriptionInput"
                        >Remove File</Button
                    >
                    <InputError :message="form.errors.job_description" />
                </div>
                <div class="grid gap-2">
                    <div class="flex flex-row gap-2 text-xs">
                        <Label for="requirements">Requirements</Label>
                        <span class="opacity-50">(optional)</span>
                    </div>
                    <TiptapEditor v-model="form.requirements" placeholder="" />
                    <InputError :message="form.errors.requirements" />
                </div>
                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing" class="cursor-pointer bg-blue-500 text-white transition-all hover:bg-blue-600">Create</Button>
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
        </DefaultPageLayout>
    </AppLayout>
</template>
