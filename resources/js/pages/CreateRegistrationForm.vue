<script setup lang="ts">
import { ref, reactive, computed, watchEffect } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group'
import { Checkbox } from '@/components/ui/checkbox'
import { Badge } from '@/components/ui/badge'
import { Switch } from '@/components/ui/switch'
import { Calendar } from '@/components/ui/calendar'
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Plus, Trash2, FileText, Upload, Image, Calendar as CalendarIcon, Settings, AlertCircle, CheckCircle } from 'lucide-vue-next'
import { type DateValue, fromDate, getLocalTimeZone, today } from '@internationalized/date'
import { addDays, format } from 'date-fns'
import { Head, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import Toaster from '@/components/ui/sonner/Sonner.vue';
import { toast } from 'vue-sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Activity',
        href: '/activity',
    },
    {
        title: 'Event',
        href: '/activity/${event.id}',
    },
    {
        title: 'Create Registration',
        href: '',
    },
];


interface Question {
    id: number
    question: string
    type: string
    required: boolean
    options?: string[] | null
}

interface InlineQuestion {
    text: string
    type: string
    options: string[]
}

interface FormDetails {
    title: string
    description: string
}

interface RegistrationDetails {
    poster: File | null
    type: string | null
    status: 'open'|'closed'
    start_date: string | null
    end_date: string | null
}

const formDetails = reactive<FormDetails>({
    title: 'Untitled Form',
    description: 'Please fill out this registration form'
})

const registrationDetails = reactive<RegistrationDetails>({
    poster: null,
    type: null,
    status: 'open',
    start_date: null,
    end_date: null,
})

const props = withDefaults(defineProps<{
    event_id: number;
}>(),{
    event_id: 1
});


const validateRegistrationDetails = () =>{
    if (registrationDetails.type === null){
        alert('Please choose registration details.')
        return false
    }else if(registrationDetails.start_date === null){
        alert('Please choose the start date')
        return false
    }else if(registrationDetails.end_date === null){
        alert('Please choose the closed date')
        return false
    }

    return true
}

const registrationStatus = ref<'open' | 'closed'>(registrationDetails.status)

const updateRegistrationStatus = (status: 'open' | 'closed') => {
    registrationStatus.value = status
    registrationDetails.status = status
}

// Date handling
const startDate = ref<Date | undefined>()
const endDate = ref<Date | undefined>()

const toDate = (dateValue: DateValue): Date => {
    return new Date(dateValue.year, dateValue.month - 1, dateValue.day)
}

// Update dates when changed
const updateStartDate = (date: DateValue | undefined) => {
    if (!date){
        startDate.value = undefined
        registrationDetails.start_date = null
        return
    }

    const jsDate = toDate(date)
    if (jsDate >= currentDate){
        startDate.value = jsDate
        registrationDetails.start_date = jsDate.toISOString().split('T')[0]
    }
}


const updateEndDate = (date: DateValue | undefined) => {
    if (!date) {
        endDate.value = undefined
        registrationDetails.end_date = null
        return
    }

    const jsDate = toDate(date)
    if (jsDate >= (startDate.value ?? new Date())){
        endDate.value = jsDate
        registrationDetails.end_date = jsDate.toISOString().split('T')[0]
    }
}

// Current date for comparison (only date part)
const today_date = new Date()
const currentDate = new Date(today_date.getFullYear(), today_date.getMonth(), today_date.getDate())

// Registration status logic
const isRegistrationOpen = computed(() => {
    // If manually closed, return false
    if (registrationDetails.status === 'closed') return false

    const start = startDate.value
    const end = endDate.value

    if (!start || !end) return false

    // Compare only dates (without time)
    const startDateOnly = new Date(start.getFullYear(), start.getMonth(), start.getDate())
    const endDateOnly = new Date(end.getFullYear(), end.getMonth(), end.getDate())

    return startDateOnly <= currentDate && currentDate <= endDateOnly
})

// Warning when registration is open but past end date
const showDateWarning = computed(() => {
    if (registrationDetails.status === 'closed') return false

    const end = endDate.value
    if (!end) return false

    const endDateOnly = new Date(end.getFullYear(), end.getMonth(), end.getDate())
    return currentDate > endDateOnly
})

// Warning when registration is manually closed but within date range
const showManualCloseWarning = computed(() => {
    if (!registrationDetails.status===closed) return false

    const start = startDate.value
    const end = endDate.value

    if (!start || !end) return false

    const startDateOnly = new Date(start.getFullYear(), start.getMonth(), start.getDate())
    const endDateOnly = new Date(end.getFullYear(), end.getMonth(), end.getDate())

    return startDateOnly <= currentDate && currentDate <= endDateOnly
})

const questions = ref<Question[]>([{
    id: 1,
    question: '',
    type: '',
    required: false,
    options: ['']
}])

const firstEmpty = ref<Question | undefined>()

watchEffect(() => {
    firstEmpty.value = questions.value[0]
})

const checkOneQuestion = computed(() => {
    const question = firstEmpty.value
    if (question?.question === '' || question?.type === ''){
        return true
    }else if(['multiple_choice', 'checkbox', 'dropdown'].includes(question?.type ?? '') && question?.options?.[0] === ''){
        return true
    }else return false
})

const nextQuestionId = ref(2)
const showAddForm = ref<number | null>(null)

const inlineQuestion = reactive<InlineQuestion>({
    text: '',
    type: '',
    options: ['']
})

// File poster handling
const posterPreview = ref<string | null>(null)
const isDragOver = ref(false)

const handlePosterUpload = (event: Event) => {
    const target = event.target as HTMLInputElement
    const file = target.files?.[0]
    if (file) {
        processFile(file)
    }
}

const handleDrop = (event: DragEvent) => {
    event.preventDefault()
    isDragOver.value = false
    const file = event.dataTransfer?.files?.[0]
    if (file) {
        processFile(file)
    }
}

const processFile = (file: File) => {
    // Validasi file (hanya image)
    if (file.type !== 'image/jpeg' && file.type !== 'image/png') {
        toast.error('Kesalan upload!', {
            description: 'Tolong unggah dokumen foto dengan format .jpeg atau .png',
            duration: 4000,
        })
        return
    }

    registrationDetails.poster = file

    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
        posterPreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
}

const removePoster = () => {
    registrationDetails.poster = null
    posterPreview.value = null
    // Reset input file
    const fileInput = document.getElementById('poster-upload') as HTMLInputElement
    if (fileInput) {
        fileInput.value = ''
    }
}

const addQuestion = (afterIndex: number) => {
     if (!inlineQuestion.text || !inlineQuestion.type) return

    const question: Question = {
        id: nextQuestionId.value++,
        question: inlineQuestion.text,
        type: inlineQuestion.type,
        required: false,
        options: ['multiple_choice', 'checkbox', 'dropdown'].includes(inlineQuestion.type)
        ? inlineQuestion.options.filter(opt => opt.trim()) || ['']
        : ['']
    }

    questions.value.splice(afterIndex + 1, 0, question)

    inlineQuestion.text = ''
    inlineQuestion.type = ''
    inlineQuestion.options = ['']
    showAddForm.value = null
}

const removeQuestion = (index: number) => {
    if (questions.value.length > 1) {
        questions.value.splice(index, 1)
    }
}

const addQuestionOption = (questionId: number) => {
    const question = questions.value.find(q => q.id === questionId)
    if (question) {
        if (!question.options) {
            question.options = ['']
        } else {
            question.options.push('')
        }
    }
}

const removeQuestionOption = (questionId: number, optionIndex: number) => {
    const question = questions.value.find(q => q.id === questionId)
    if (question && question.options && question.options.length > 1) {
        question.options.splice(optionIndex, 1)
    }
}

const updateQuestionType = (questionId: number, newType: string | null) => {
    if (!newType) return
    const question = questions.value.find(q => q.id === questionId)
    if (question) {
        question.type = newType
        if (['multiple_choice', 'checkbox', 'dropdown'].includes(newType)) {
        if (!question.options || question.options.length === 0) {
            question.options = ['']
        }
        } else {
        question.options = ['']
        }
    }
}

const addInlineOption = () => {
    inlineQuestion.options.push('')
}

const removeInlineOption = (index: number) => {
    if (inlineQuestion.options.length > 1) {
        inlineQuestion.options.splice(index, 1)
    }
}

const showAddQuestionForm = (index: number) => {
    showAddForm.value = index
    inlineQuestion.text = ''
    inlineQuestion.type = ''
    inlineQuestion.options = ['']
}

const isFormValid = (): boolean => {
    return !!formDetails.title && questions.value.some(q => q.question && q.type)
}

const cancelAddQuestion = () => {
    showAddForm.value = null
    inlineQuestion.text = ''
    inlineQuestion.type = ''
    inlineQuestion.options = ['']
}



// Fungsi untuk memformat questions menjadi array yang terstruktur
const formatQuestionsForDatabase = (): Question[] => {
    const formattedQuestions: Question[] = []

    questions.value.forEach(question => {
        // Skip questions yang tidak memiliki question text atau type
        if (!question.question?.trim() || !question.type) return

        let formattedOptions: string[] | null = null

        // Hanya set options untuk type yang membutuhkan pilihan
        if (['multiple_choice', 'checkbox', 'dropdown'].includes(question.type)) {
            // Filter options yang tidak kosong
            const validOptions = (question.options || []).filter(opt => opt.trim())
            formattedOptions = validOptions.length > 0 ? validOptions : null
        }

        formattedQuestions.push({
            id: question.id,
            question: question.question.trim(),
            type: question.type,
            required: Boolean(question.required),
            options: formattedOptions
        })
    })

    return formattedQuestions
}

const submitForm = () => {
    if (!validateRegistrationDetails()) return
    if (!isFormValid()) {
        toast.error('Formulir tidak lengkap!', {
            description: 'Tolong isi judul dan minimal satu pertanyaan',
            duration: 4000,
        })
        return
    }

    const formattedQuestions = formatQuestionsForDatabase()

    const formData = new FormData()

    // Append basic form data
    formData.append('event_id', props.event_id.toString())
    formData.append('title', formDetails.title)
    formData.append('description', formDetails.description || '')
    formData.append('type', registrationDetails.type || '')
    formData.append('status', registrationDetails.status)
    formData.append('start_date', registrationDetails.start_date || '')
    formData.append('end_date', registrationDetails.end_date || '')

    // Append poster if exists
    if (registrationDetails.poster) {
        formData.append('poster', registrationDetails.poster)
    }

    // Append questions sebagai JSON string
    formData.append('questions', JSON.stringify(formattedQuestions))


    router.post(`/registration/${props.event_id}/form/submit`, formData, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Formulir berhasil dibuat!', {
                    description: 'Silahkan cek activity Anda',
                    duration: 4000,
            })
        },
        onError: (errors) => {
            toast.error('Formulir gagal dibuat!', {
                description: 'Terdapat kesalahan dalam form',
                duration: 4000,
            })
            console.error('Validation errors:', errors)
        }
    })
}

</script>

<template>
<Head title="create registration"></Head>
<AppLayout :breadcrumbs="breadcrumbs">
    <Toaster
        position="top-center"
        :rich-colors="true"
        :close-button="true"
    />
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Registration</h1>
            <p class="text-gray-600">Create and customize your registration form</p>
        </div>

        <!-- Enrollemnt Details -->
        <Card class="mb-6">
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <Settings class="w-5 h-5" />
                    Enrollment Details
                </CardTitle>
            </CardHeader>
            <CardContent class="space-y-6">
                <!-- Enrollment Type -->
                <div class="space-y-2 w-full">
                    <Label for="enrollment-type">Enrollment Type</Label>
                    <Select id="enrollment-type" v-model="registrationDetails.type" class="w-full">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select type" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="registration">Registration</SelectItem>
                            <SelectItem value="recruitment">Recruitment</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Poster Upload -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <Image class="w-4 h-4" />
                        <Label for="poster-upload">Event Poster</Label>
                    </div>

                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-all duration-200"
                        :class="{ 'border-blue-400 bg-blue-50': isDragOver }" @dragover.prevent="isDragOver = true"
                        @dragleave.prevent="isDragOver = false" @drop.prevent="handleDrop">
                        <Upload class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                        <div class="space-y-2">
                            <p class="text-sm font-medium text-gray-700">
                                <label for="poster-upload"
                                    class="text-blue-600 hover:text-blue-500 cursor-pointer underline">
                                    Click to upload
                                </label>
                                or drag and drop
                            </p>
                            <p class="text-xs text-gray-500">PNG, JPG</p>
                        </div>
                        <input id="poster-upload" type="file" accept=".jpg,.jpeg,.png" class="hidden"
                            @change="handlePosterUpload" />
                    </div>

                    <div v-if="registrationDetails.poster" class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg w-full">
                            <div class="flex items-center gap-3 w-full w-min-0">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <Image class="w-5 h-5 text-blue-600" />
                                </div>
                                <div class="w-full min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{
                                        registrationDetails.poster?.name }}</p>
                                    <p class="text-xs text-gray-500">{{ registrationDetails.poster ?
                                        Math.round(registrationDetails.poster.size / 1024) : 0 }} KB</p>
                                </div>
                                <Badge variant="secondary" class="bg-green-100 text-green-700">
                                    <CheckCircle class="w-3 h-3 mr-1" />
                                    Uploaded
                                </Badge>
                            </div>
                        </div>
                        <div>
                            <Button @click="removePoster()" class="px-8">
                                Cancel
                            </Button>
                        </div>
                    </div>
                </div>

                <!-- Registration Status and Dates -->
                <Card>
                    <CardContent>
                        <div class="space-y-6">
                            <!-- Registration Status Toggle -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="space-y-1">
                                        <Label class="text-base font-medium">Registration Status</Label>
                                        <p class="text-sm text-gray-500">
                                            {{ registrationDetails.status === 'closed' ? 'Manually closed' : 'Open basedon dates' }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <Badge :variant="isRegistrationOpen ? 'default' : 'secondary'"
                                            :class="isRegistrationOpen ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                            {{ isRegistrationOpen ? 'Open' : 'Closed' }}
                                        </Badge>
                                        <Switch
                                            @update:model-value="updateRegistrationStatus($event ? 'closed' : 'open')" />
                                    </div>
                                </div>

                                <!-- Warning Messages -->
                                <div v-if="showDateWarning" class="mt-4">
                                    <Alert class="border-amber-200 bg-amber-50">
                                        <AlertCircle class="h-4 w-4 text-amber-600" />
                                        <AlertDescription class="text-amber-800">
                                            Registration is currently open but past the end date. It will automatically
                                            close and become invisible to users.
                                        </AlertDescription>
                                    </Alert>
                                </div>

                                <div v-if="showManualCloseWarning" class="mt-4">
                                    <Alert class="border-blue-200 bg-blue-50">
                                        <AlertCircle class="h-4 w-4 text-blue-600" />
                                        <AlertDescription class="text-blue-800">
                                            Registration is manually closed but within the date range. It will remain
                                            closed until you toggle it back on.
                                        </AlertDescription>
                                    </Alert>
                                </div>
                            </div>

                            <!-- Date Range -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-2">
                                    <CalendarIcon class="w-4 h-4" />
                                    <Label class="text-base font-medium">Registration Period</Label>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Start Date -->
                                    <div class="space-y-2">
                                        <Label class="text-sm font-medium">Start Date</Label>
                                        <Popover>
                                            <PopoverTrigger asChild>
                                                <Button variant="outline"
                                                    :class="['w-full justify-start text-left font-normal', !startDate && 'text-muted-foreground']">
                                                    <CalendarIcon class="mr-2 h-4 w-4" />
                                                    {{ startDate ? format(startDate, 'PPP') : 'Pick a date' }}
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="w-auto p-0" align="start">
                                                <Calendar
                                                    @update:model-value="updateStartDate($event)" :v-model="startDate" :min-value="today(getLocalTimeZone())">
                                                </Calendar>
                                            </PopoverContent>
                                        </Popover>
                                    </div>

                                    <!-- End Date -->
                                    <div class="space-y-2">
                                        <Label class="text-sm font-medium">End Date</Label>
                                        <Popover>
                                            <PopoverTrigger asChild>
                                                <Button variant="outline"
                                                    :class="['w-full justify-start text-left font-normal', !endDate && 'text-muted-foreground']">
                                                    <CalendarIcon class="mr-2 h-4 w-4" />
                                                    {{ endDate ? format(endDate, 'PPP') : 'Pick a date' }}
                                                </Button>
                                            </PopoverTrigger>
                                            <PopoverContent class="w-auto p-0" align="start">
                                                <Calendar
                                                    @update:model-value="updateEndDate($event)"
                                                    :v-model="endDate"
                                                    :disabled="!startDate"
                                                    :min-value="startDate ? fromDate(addDays(startDate, 1), getLocalTimeZone()) : today(getLocalTimeZone())"
                                                />
                                            </PopoverContent>
                                        </Popover>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </CardContent>
        </Card>

        <!-- Form Title and Description -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <Settings class="w-5 h-5" />
                    Form Details
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <div>
                        <Label for="form-title" class="text-sm font-medium">Form Title</Label>
                        <Input id="form-title" v-model="formDetails.title" placeholder="Enter form title"
                            class="mt-1" />
                    </div>
                    <div>
                        <Label for="form-description" class="text-sm font-medium">Form Description</Label>
                        <Textarea id="form-description" v-model="formDetails.description"
                            placeholder="Enter form description" rows="3" class="mt-1" />
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Form Builder -->
        <Card>
            <CardHeader>
                <CardTitle>Form Questions</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="space-y-4">
                    <!-- Dynamic Questions -->
                    <div v-for="(question, index) in questions" :key="question.id"
                        class="border rounded-lg p-4 relative">
                        <!-- Question Editor -->
                        <div class="space-y-4 mb-4">
                            <div class="flex gap-4 items-end">
                                <div class="flex-1">
                                    <Label :for="`edit-question-${question.id}`" class="mb-2">Question Text</Label>
                                    <Input :id="`edit-question-${question.id}`" v-model="question.question"
                                        placeholder="Enter your question" />
                                </div>
                                <div class="w-48">
                                    <Label :for="`edit-type-${question.id}`" class="mb-2">Question Type</Label>
                                    <Select v-model="question.type"
                                        @update:model-value="updateQuestionType(question.id, $event as string)">
                                        <SelectTrigger>
                                            <SelectValue placeholder="Select type" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="text">Short Answer</SelectItem>
                                            <SelectItem value="paragraph">Long Text</SelectItem>
                                            <SelectItem value="multiple_choice">Multiple Choice</SelectItem>
                                            <SelectItem value="checkbox">Checkbox</SelectItem>
                                            <SelectItem value="dropdown">Dropdown</SelectItem>
                                            <SelectItem value="file_upload">File Upload</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center space-x-2">
                                        <Label :for="`required-${question.id}`" class="text-sm">Required</Label>
                                        <Checkbox :id="`required-${question.id}`" :checked="question.required"
                                             v-model:="question.required" />
                                    </div>
                                    <Button variant="ghost" size="sm" @click="removeQuestion(index)"
                                        class="text-red-500 hover:text-red-700" :disabled="questions.length <= 1">
                                        <Trash2 class="w-4 h-4" />
                                    </Button>
                                </div>
                            </div>

                            <!-- Options for Multiple Choice, Checkbox, and Dropdown -->
                            <div v-if="['multiple_choice', 'checkbox', 'dropdown'].includes(question.type)" class="mt-4">
                                <Label class="mb-2">Options</Label>
                                <div class="space-y-2">
                                    <div v-for="(option, optIndex) in question.options ?? []" :key="optIndex" class="flex gap-2">
                                        <Input v-model="(question.options as string[])[optIndex]" :placeholder="`Option ${optIndex + 1}`" />
                                        <Button variant="outline" size="sm"
                                                @click="removeQuestionOption(question.id, optIndex)"
                                                :disabled="(question.options?.length ?? 0) <= 1">
                                            <Trash2 class="w-4 h-4" />
                                        </Button>
                                    </div>
                                    <Button variant="outline" size="sm" @click="addQuestionOption(question.id)">
                                        <Plus class="w-4 h-4 mr-2" />
                                        Add Option
                                    </Button>
                                </div>
                            </div>
                        </div>

                        <!-- Question Preview -->
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-700 mb-2">Preview:</p>
                            <div v-if="question.question && question.type">
                                <Label class="text-base font-medium mb-2 block">
                                    {{ question.question }}
                                    <span v-if="question.required" class="text-red-500 ml-1">*</span>
                                </Label>

                                <!-- Short Answer -->
                                <div v-if="question.type === 'text'">
                                    <Input placeholder="Text" disabled />
                                </div>

                                <!-- Paragraph -->
                                <div v-if="question.type === 'paragraph'">
                                    <Textarea placeholder="Paragraph" rows="4" disabled />
                                </div>

                                <!-- Multiple Choice -->
                                <div v-if="question.type === 'multiple_choice' && question.options">
                                    <RadioGroup disabled>
                                        <div v-for="option in question.options" :key="option"
                                            class="flex items-center space-x-2">
                                            <RadioGroupItem :value="option" disabled />
                                            <Label>{{ option }}</Label>
                                        </div>
                                    </RadioGroup>
                                </div>

                                <!-- Checkbox -->
                                <div v-if="question.type === 'checkbox' && question.options">
                                    <div v-for="option in question.options" :key="option"
                                        class="flex items-center space-x-2 mb-2">
                                        <Checkbox disabled />
                                        <Label>{{ option }}</Label>
                                    </div>
                                </div>

                                <!-- Dropdown -->
                                <div v-if="question.type === 'dropdown' && question.options">
                                    <Select disabled>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Choose an option" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem v-for="option in question.options" :key="option"
                                                :value="option">
                                                {{ option }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <!-- File Upload -->
                                <div v-if="question.type === 'file_upload'">
                                    <Input type="file" disabled />
                                    <p class="text-sm text-gray-500 mt-1">File upload field</p>
                                </div>
                            </div>
                            <div v-else class="text-gray-500 text-sm">
                                Please enter question text and select a type to see preview
                            </div>
                        </div>

                        <!-- Add Question Button Below Each Question -->
                        <div class="border-t pt-4">
                            <Button variant="outline" size="sm" @click="showAddQuestionForm(index)" class="w-full">
                                <Plus class="w-4 h-4 mr-2" />
                                Add Question Below
                            </Button>
                        </div>

                        <!-- Inline Add Question Form -->
                        <div v-if="showAddForm === index" class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="space-y-4">
                                <div class="flex gap-4 items-end">
                                    <div class="flex-1">
                                        <Label for="inline-question-text" class="mb-2">Question Text</Label>
                                        <Input id="inline-question-text" v-model="inlineQuestion.text"
                                            placeholder="Enter your question" />
                                    </div>
                                    <div class="w-48">
                                        <Label for="inline-question-type" class="mb-2">Question Type</Label>
                                        <Select v-model="inlineQuestion.type">
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select type" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="text">Short Answer</SelectItem>
                                                <SelectItem value="paragraph">Long Text</SelectItem>
                                                <SelectItem value="multiple_choice">Multiple Choice</SelectItem>
                                                <SelectItem value="checkbox">Checkbox</SelectItem>
                                                <SelectItem value="dropdown">Dropdown</SelectItem>
                                                <SelectItem value="file_upload">File Upload</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>

                                <!-- Options for Multiple Choice, Checkbox, and Dropdown -->
                                <div v-if="['multiple_choice', 'checkbox', 'dropdown'].includes(inlineQuestion.type)"
                                    class="mt-4">
                                    <Label class="mb-2">Options</Label>
                                    <div class="space-y-2">
                                        <div v-for="(option, optIndex) in inlineQuestion.options" :key="optIndex"
                                            class="flex gap-2">
                                            <Input v-model="inlineQuestion.options[optIndex]"
                                                :placeholder="`Option ${optIndex + 1}`" />
                                            <Button variant="outline" size="sm" @click="removeInlineOption(optIndex)"
                                                :disabled="inlineQuestion.options.length <= 1">
                                                <Trash2 class="w-4 h-4" />
                                            </Button>
                                        </div>
                                        <Button variant="outline" size="sm" @click="addInlineOption">
                                            <Plus class="w-4 h-4 mr-2" />
                                            Add Option
                                        </Button>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <Button @click="addQuestion(index)"
                                        :disabled="!inlineQuestion.text || !inlineQuestion.type">
                                        <Plus class="w-4 h-4 mr-2" />
                                        Add Question
                                    </Button>
                                    <Button variant="outline" @click="cancelAddQuestion">
                                        Cancel
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="questions.length === 0" class="text-center py-8 text-gray-500">
                        <FileText class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                        <p>No questions available.</p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <Button @click="submitForm" class="px-8" :disabled="!formDetails.title || checkOneQuestion || registrationDetails.type === null || registrationDetails.start_date === null || registrationDetails.end_date === null">
                Save Registration Form
            </Button>
        </div>
    </div>
</AppLayout>

</template>
