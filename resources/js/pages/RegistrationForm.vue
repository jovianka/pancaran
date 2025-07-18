<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import Toaster from '@/components/ui/sonner/Sonner.vue';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { onMounted, reactive, ref } from 'vue';
import { toast } from 'vue-sonner';

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
    question_id: number;
    question: string;
    type: string;
    options?: string[] | null;
    required: boolean;
    answer: string | string[];
}

const props = defineProps<{
    form_question: FormQuestion;
}>();

const formData: Record<string, any> = reactive({});
const errors: Record<string, string> = reactive({});
const isSubmitting = ref<boolean>(false);
const isSubmitted = ref<boolean>(false);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Explore',
        href: '/explore',
    },
    {
        title: 'Registration',
        href: `/registration/${props.form_question.event_registration_id}`,
    },
    {
        title: 'Form',
        href: '',
    },
];

onMounted(() => {
    for (const question of props.form_question.questions) {
        if (question.type === 'checkbox') {
            formData[question.id] = [];
        } else {
            formData[question.id] = '';
        }
    }
});

const updateCheckbox = (questionId: number, option: string) => {
    if (!formData[questionId]) {
        formData[questionId] = [];
    }

    let currentValues = [...formData[questionId]];
    if (!currentValues.includes(option)) {
        currentValues.push(option);
    } else {
        currentValues = currentValues.filter((value: string) => value !== option);
    }

    formData[questionId] = currentValues;
};

const handleFileUpload = (questionId: number, event: Event) => {
    const target = event.target as HTMLInputElement;
    const files = target.files ? Array.from(target.files) : [];
    formData[questionId] = files;
};

const getFileNames = (questionId: number): string => {
    const files = formData[questionId] as File[];
    if (!files || files.length === 0) return 'Tidak ada file dipilih';
    return files.map((file) => file.name).join(', ');
};

const validateForm = (): boolean => {
    Object.keys(errors).forEach((key) => delete errors[key]);
    const newErrors: Record<string, string> = {};

    props.form_question.questions.forEach((question) => {
        const value = formData[question.id];
        if (question.required && (!value || (typeof value === 'string' && value.trim() === '') || (Array.isArray(value) && value.length === 0))) {
            newErrors[question.id] = 'Bagian ini wajib diisi';
        }
    });

    Object.assign(errors, newErrors);
    return Object.keys(newErrors).length === 0;
};

const formatAnswersForDatabase = (): FormAnswer[] => {
    const answers: FormAnswer[] = [];

    props.form_question.questions.forEach((question) => {
        const value = formData[question.id];
        let formattedAnswer: string | string[];

        if (question.type === 'checkbox') {
            formattedAnswer = value || [];
        } else if (question.type === 'file_upload') {
            // For file uploads, you might want to store file paths or names
            if (value && Array.isArray(value)) {
                formattedAnswer = value.map((file: File) => file.name);
            } else {
                formattedAnswer = '';
            }
        } else {
            formattedAnswer = value || '';
        }

        answers.push({
            question_id: question.id,
            question: question?.question,
            type: question?.type,
            required: question?.required,
            options: question?.options,
            answer: formattedAnswer,
        });
    });

    return answers;
};

const submitForm = async () => {
    if (!validateForm()) {
        toast.error('Mohon lengkapi semua field yang wajib diisi');
        return;
    }

    isSubmitting.value = true;

    try {
        const formattedAnswers: FormAnswer[] = formatAnswersForDatabase();

        const submitData: Record<string, any> = {
            questions: props.form_question.questions,
            registration_id: props.form_question.event_registration_id,
            answers: formattedAnswers,
        };

        router.post(`/registration/${props.form_question.id}/submit`, submitData, {
            preserveScroll: true,
            onSuccess: () => {
                isSubmitted.value = true;
                toast.success('Formulir berhasil dikirim!', {
                    description: 'Terima kasih atas respon Anda',
                    duration: 4000,
                });
            },
            onError: (inertiaErrors) => {
                console.error('Submission failed:', inertiaErrors);
                Object.assign(errors, inertiaErrors);
                toast.error('Gagal mengirim formulir!', {
                    description: 'Terjadi kesalahan saat menyimpan. Silakan coba lagi.',
                    duration: 4000,
                });
            },
            onFinish: () => {
                isSubmitting.value = false;
            },
        });
    } catch (error) {
        const errorMessage = error instanceof Error ? error.message : 'Terjadi kesalahan tidak terduga';
        toast.error('Terjadi kesalahan', {
            description: errorMessage,
            duration: 4000,
        });
        console.error('Submission error:', error);
        isSubmitting.value = false;
    }
};
</script>

<template>
    <Head title="Registration Form" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <Toaster position="top-center" :rich-colors="true" :close-button="true" />
        <div class="mx-auto max-w-4xl p-6">
            <div class="mb-8 text-center">
                <h1 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">{{ form_question.title }}</h1>
                <p class="text-gray-600 dark:text-gray-200">{{ form_question.description }}</p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Pertanyaan dengan simbol <span class="text-red-500">*</span> wajib diisi</CardTitle>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Generate Questions -->
                        <div v-for="question in form_question.questions" :key="question.id" class="space-y-3">
                            <div class="border-b pb-4">
                                <Label :for="`question-${question.id}`" class="mb-3 block text-base font-medium">
                                    {{ question.question }}
                                    <span v-if="question.required" class="ml-1 text-red-500">*</span>
                                </Label>

                                <!-- Text -->
                                <div v-if="question.type === 'text'">
                                    <Input
                                        :id="`question-${question.id}`"
                                        v-model="formData[question.id]"
                                        placeholder="Berikan Jawaban Anda"
                                        :required="question.required"
                                        autocomplete="off"
                                        class="w-full py-3"
                                    />
                                </div>

                                <!-- Paragraph -->
                                <div v-if="question.type === 'paragraph'">
                                    <Textarea
                                        :id="`question-${question.id}`"
                                        v-model="formData[question.id]"
                                        placeholder="Berikan Jawaban Anda"
                                        :required="question.required"
                                        :rows="4"
                                        class="w-full"
                                    />
                                </div>

                                <!-- Multiple Choice -->
                                <div v-if="question.type === 'multiple_choice'">
                                    <RadioGroup
                                        :model-value="formData[question.id]"
                                        @update:model-value="(value) => (formData[question.id] = value)"
                                        class="space-y-2"
                                    >
                                        <div v-for="option in question.options" :key="option" class="flex items-center space-x-2">
                                            <RadioGroupItem :value="option" :id="`${question.id}-${option}`" />
                                            <Label :for="`${question.id}-${option}`" class="text-sm font-normal">{{ option }}</Label>
                                        </div>
                                    </RadioGroup>
                                </div>

                                <!-- Checkbox -->
                                <div v-if="question.type === 'checkbox'">
                                    <div class="space-y-2">
                                        <div v-for="option in question.options" :key="option" class="flex items-center space-x-2">
                                            <Checkbox :id="`${question.id}-${option}`" @click="() => updateCheckbox(question.id, option)" />
                                            <Label :for="`${question.id}-${option}`" class="text-sm font-normal">{{ option }}</Label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown -->
                                <div v-if="question.type === 'dropdown'">
                                    <Select :model-value="formData[question.id]" @update:model-value="(value) => (formData[question.id] = value)">
                                        <SelectTrigger class="w-full">
                                            <SelectValue placeholder="Pilih Opsi" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="option in question.options" :key="option" :value="option">
                                                {{ option }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <!-- File Upload -->
                                <div v-if="question.type === 'file_upload'">
                                    <Input
                                        :id="`question-${question.id}`"
                                        type="file"
                                        @change="handleFileUpload(question.id, $event)"
                                        :required="question.required"
                                        class="w-full"
                                    />
                                    <p class="mt-2 text-sm text-gray-500" v-if="formData[question.id]">
                                        Selected files: {{ getFileNames(question.id) }}
                                    </p>
                                </div>

                                <!-- Validation Error -->
                                <div v-if="errors[question.id]" class="mt-2 text-sm text-red-500">
                                    {{ errors[question.id] }}
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex pt-6">
                            <Button type="submit" :disabled="isSubmitting" class="px-8">
                                <span v-if="isSubmitting">Submitting...</span>
                                <span v-else>Submit Form</span>
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
        <!-- Toaster placed at the end of AppLayout for proper positioning -->
    </AppLayout>
</template>
