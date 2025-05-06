<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuRadioGroup, DropdownMenuRadioItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ScrollArea } from '@/components/ui/scroll-area/';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ChevronDown, LoaderCircle } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

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

const form = useForm({
    nim: '',
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    faculty_id: selectedFaculty.value.id,
    major_id: selectedMajor.value.id,
});

watch(selectedFacultyName, async () => {
    selectedMajorName.value = '';
});

const submit = () => {
    form.faculty_id = selectedFaculty.value.id;
    form.major_id = selectedMajor.value.id;

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">NIM</Label>
                    <Input id="name" type="text" required autofocus :tabindex="0" v-model="form.nim" placeholder="Nomor Induk Mahasiswa" />
                    <InputError :message="form.errors.nim" />
                </div>

                <div class="grid gap-2">
                    <Label for="name">Name</Label>
                    <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
                    <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        v-model="form.password"
                        placeholder="Password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>

                <div class="grid gap-2">
                    <Label for="faculty">Faculty</Label>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline" class="w-fit" id="faculty">
                                {{ selectedFacultyName == '' ? 'Choose Faculty' : selectedFacultyName }}
                                <ChevronDown />
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
                            <Button variant="outline" class="w-fit" id="major">
                                {{ selectedMajorName == '' ? 'Choose Major' : selectedMajorName }}
                                <ChevronDown />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent class="w-56">
                            <ScrollArea class="max-h-80">
                                <DropdownMenuRadioGroup v-model="selectedMajorName">
                                    <DropdownMenuRadioItem v-for="major in facultyMajors" :key="major.id" :value="major.name">
                                        {{ major.name }}
                                    </DropdownMenuRadioItem>
                                </DropdownMenuRadioGroup>
                            </ScrollArea>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <InputError v-if="form.errors.major_id" message="Please choose your major" />
                </div>
                <Button type="submit" class="mt-2 w-full hover:cursor-pointer" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-muted-foreground text-center text-sm">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
