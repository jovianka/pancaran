<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuContent, DropdownMenuRadioGroup, DropdownMenuRadioItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Toaster from '@/components/ui/sonner/Sonner.vue';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ChevronsUpDownIcon } from 'lucide-vue-next';
import { computed, reactive, ref } from 'vue';

interface Question {
    id: number;
    question: string;
    type: string;
    required: boolean;
    options?: string[] | null;
}

interface FormQuestion {
    id: number;
    title: string;
    description: string;
    questions: Question[];
    created_at: Date;
    updated_at: Date;
    event_registration_id: number;
}

interface FormAnswer {
    id: number;
    user_id: number;
    date_submitted: Date;
    details: Answer[];
    created_at: Date;
    updated_at: Date;
}

interface Answer {
    id: number;
    question: string;
    type: string;
    options?: string[] | null;
    required: boolean;
    answer: string | string[]; // Aslinya string | string[] (checkbox)
}

const props = defineProps<{
    form_answer: FormAnswer;
    form_question: FormQuestion;
    event_roles: any;
    registration: any;
}>();

const errors: Record<string, string> = reactive({});
const roleIdString = ref('');
const roleId = computed(() => {
    return Number(roleIdString.value);
});

const acceptResponse = (registrationId: number, roleId: number, userId: number) => {
    if (!roleId) {
        alert('Pick a role!');
        return;
    }

    router.post(route('accept_response'), {
        registration_id: registrationId,
        role_id: roleId,
        user_id: userId,
    });
};

const rejectResponse = (registrationId: number, userId: number) => {
    router.post(route('reject_response'), {
        registration_id: registrationId,
        user_id: userId,
    });
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Explore',
        href: '/explore',
    },
    {
        title: 'Response',
        href: `/registration/${props.form_question.event_registration_id}`,
    },
    {
        title: 'Form',
        href: '',
    },
];
</script>

<template>
    <Head title="Registration Form" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster position="top-center" :rich-colors="true" :close-button="true" />
        <div class="mx-auto max-w-4xl p-6">
            <div class="mb-8 text-center">
                <h1 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">{{ props.form_question.title }}</h1>
                <p class="text-gray-600 dark:text-gray-200">{{ props.form_question.description }}</p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Pertanyaan dengan simbol <span class="text-red-500">*</span> wajib diisi</CardTitle>
                </CardHeader>
                <CardContent>
                    <form class="space-y-6">
                        <!-- Generate Questions -->
                        <div v-for="question in props.form_answer.details" :key="question.id" class="space-y-3">
                            <div class="border-b pb-4">
                                <Label :for="`question-${question.id}`" class="mb-3 block text-base font-medium">
                                    {{ question.question }}
                                    <span v-if="question.required" class="ml-1 text-red-500">*</span>
                                </Label>

                                <!-- Text -->
                                <div v-if="question.type === 'text'">
                                    <Input
                                        disabled
                                        :id="`question-${question.id}`"
                                        :default-value="question.answer as string"
                                        placeholder="Berikan Jawaban Anda"
                                        :required="question.required"
                                        autocomplete="off"
                                        class="w-full py-3"
                                    />
                                </div>

                                <!-- Paragraph -->
                                <div v-if="question.type === 'paragraph'">
                                    <Textarea
                                        disabled
                                        :id="`question-${question.id}`"
                                        :default-value="question.answer as string"
                                        placeholder="Berikan Jawaban Anda"
                                        :required="question.required"
                                        :rows="4"
                                        class="w-full"
                                    />
                                </div>

                                <!-- Multiple Choice -->
                                <div v-if="question.type === 'multiple_choice'">
                                    <RadioGroup disabled :default-value="question.answer" class="space-y-2">
                                        <div v-for="option in question.options" :key="option" class="flex items-center space-x-2">
                                            <RadioGroupItem :value="option" :id="`${question.id}-${option}`" />
                                            <Label :for="`${question.id}-${option}`" class="text-sm font-normal">{{ option }}</Label>
                                        </div>
                                    </RadioGroup>
                                </div>

                                <!-- Checkbox -->
                                <div v-if="question.type === 'checkbox'">
                                    <div class="space-y-2">
                                        <div v-for="option of question.options" :key="option" class="flex items-center space-x-2">
                                            <Checkbox disabled :id="`${question.id}-${option}`" :default-value="question.answer?.includes(option)" />
                                            <Label :for="`${question.id}-${option}`" class="text-sm font-normal">{{ option }}</Label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown -->
                                <div v-if="question.type === 'dropdown'">
                                    <Select :default-value="question.answer">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Pilih Opsi" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="option in question.options" :key="option" :value="option" disabled>
                                                {{ option }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <!-- File Upload -->
                                <div v-if="question.type === 'file_upload'">
                                    <p class="mt-2 text-sm text-gray-500" v-for="(filename, index) of question.answer" :key="index">
                                        {{ filename }}
                                    </p>
                                </div>

                                <!-- Validation Error -->
                                <div v-if="errors[question.id]" class="mt-2 text-sm text-red-500">
                                    {{ errors[question.id] }}
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex gap-4 pt-6">
                            <Dialog>
                                <DialogTrigger as-child>
                                    <Button type="button" class="cursor-pointer bg-blue-500 text-white transition-all hover:bg-blue-600">
                                        Accept
                                    </Button>
                                </DialogTrigger>
                                <DialogContent class="sm:max-w-sm">
                                    <DialogHeader>
                                        <DialogTitle>Accept User as Member</DialogTitle>
                                    </DialogHeader>
                                    <div class="grid gap-4 py-4">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <div class="flex flex-col">
                                                    <Button variant="outline" type="button" class="w-fit text-center" id="eventLevel">
                                                        {{
                                                            roleId == 0
                                                                ? 'Pick a Role'
                                                                : props.event_roles.find((role: any) => role.id === roleId).name
                                                        }}
                                                        <ChevronsUpDownIcon />
                                                    </Button>
                                                </div>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent class="w-56" align="start">
                                                <ScrollArea class="max-h-80">
                                                    <DropdownMenuRadioGroup v-model="roleIdString">
                                                        <DropdownMenuRadioItem
                                                            v-for="(role, index) of props.event_roles"
                                                            :key="index"
                                                            :value="String(role.id)"
                                                        >
                                                            {{ role.name.toUpperCase() }}
                                                        </DropdownMenuRadioItem>
                                                    </DropdownMenuRadioGroup>
                                                </ScrollArea>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </div>
                                    <DialogFooter>
                                        <Button
                                            @click="acceptResponse(props.registration.id, roleId, props.form_answer.user_id)"
                                            type="button"
                                            class="cursor-pointer bg-blue-500 text-white transition-all hover:bg-blue-600"
                                        >
                                            Accept
                                        </Button>
                                    </DialogFooter>
                                </DialogContent>
                            </Dialog>
                            <Button @click="rejectResponse(props.registration.id, props.form_answer.user_id)" type="button" variant="destructive">
                                Reject
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
        <!-- Toaster placed at the end of AppLayout for proper positioning -->
    </AppLayout>
</template>
