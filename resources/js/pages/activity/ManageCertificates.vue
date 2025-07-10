<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuRadioGroup,
    DropdownMenuRadioItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import DefaultPageLayout from '@/layouts/DefaultPageLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import type { Font } from '@pdfme/common';
import { generate } from '@pdfme/generator';
import { barcodes, image, text } from '@pdfme/schemas';
import { Designer } from '@pdfme/ui';
import { useFetch } from '@vueuse/core';
import { ChevronsUpDownIcon, EllipsisIcon, XIcon } from 'lucide-vue-next';
import { computed, onMounted, ref, useTemplateRef, watch } from 'vue';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Activity',
        href: '/activity',
    },
    {
        title: 'Manage Certificates',
        href: '/activity',
    },
];

const props = defineProps(['defaultCertificateTemplate', 'eventRoles', 'event', 'certificates']);

const selectedRoleId = ref<string>('');
const selectedRole = computed(() => {
    if (selectedRoleId.value !== '') {
        return props.eventRoles.find((role: any) => role.id === parseInt(selectedRoleId.value));
    } else {
        return null;
    }
});

const font: Font = {
    Serif: {
        data: 'https://raw.githubusercontent.com/google/fonts/refs/heads/main/ofl/notoserif/NotoSerif%5Bwdth%2Cwght%5D.ttf',
        fallback: true,
    },
    SansSerif: {
        data: 'https://raw.githubusercontent.com/google/fonts/refs/heads/main/ofl/cantarell/Cantarell-Regular.ttf',
    },
};
const template = JSON.parse(props.defaultCertificateTemplate);
const options = {
    zoomLevel: 1,
    sidebarOpen: true,
    font: font,
};

const plugins = {
    text: text,
    image: image,
    qrcode: barcodes.qrcode,
};
let pdfDesignerInstance: Designer;

const pdfDesignerElement = useTemplateRef<HTMLElement>('pdfDesigner');
const nomorSuratInput = ref('');

const getSelectedRoleBasePdf = async () => {
    if (selectedRole.value.certificate_basepdf) {
        const { data } = (await useFetch(
            route('event.getCertificateBasePdf', { event_id: props.event.id, filename: selectedRole.value.certificate_basepdf }),
        ).get()) as any;
        return data.value;
    }
};

const generateCertificates = () => {
    if (nomorSuratInput.value === '' || selectedRole.value === null) {
        alert('FIll nomor surat input and pick a role!');
        return;
    }
    pdfDesignerInstance.saveTemplate();

    const promises = selectedRole.value.users.map(async (user: any) => {
        const inputs = [{ nama: user.name }];
        const currentTemplate = pdfDesignerInstance.getTemplate();
        const pdf = await generate({ template: currentTemplate, inputs });
        const blob = new Blob([pdf], { type: 'application/pdf' });

        return {
            file: blob,
            nomor_surat: nomorSuratInput.value,
            detail_skp_id: selectedRole.value.detail_skp_id,
            event_role_id: selectedRole.value.id,
            event_id: props.event.id,
            user_id: user.id,
        };
    });

    Promise.all(promises).then((certificates) => {
        const formData = new FormData();
        certificates.forEach((cert, index) => {
            formData.append(`certificates[${index}][file]`, cert.file, `cert-${cert.user_id}.pdf`);
            formData.append(`certificates[${index}][nomor_surat]`, cert.nomor_surat);
            formData.append(`certificates[${index}][detail_skp_id]`, cert.detail_skp_id);
            formData.append(`certificates[${index}][event_role_id]`, cert.event_role_id);
            formData.append(`certificates[${index}][event_id]`, cert.event_id);
            formData.append(`certificates[${index}][user_id]`, cert.user_id);
        });

        router.post(
            route('certificates.generate', {
                event_id: props.event.id,
                role_id: selectedRole.value.id,
            }),
            formData,
            {
                forceFormData: true,
            },
        );
    });
};

onMounted(() => {
    if (pdfDesignerElement.value) {
        pdfDesignerInstance = new Designer({
            domContainer: pdfDesignerElement.value!,
            template,
            options,
            plugins,
        });
    }

    pdfDesignerInstance.onSaveTemplate((template) => {
        if (selectedRole.value) {
            router.post(route('certificates.saveTemplate', { event_id: props.event.id, role_id: selectedRole.value.id }), {
                certificate_schema: JSON.stringify(template.schemas),
                certificate_basepdf: String(template.basePdf),
            });
        }
    });
});

watch(selectedRole, async () => {
    if (selectedRole.value && selectedRole.value.certificate_basepdf && selectedRole.value.certificate_schema) {
        const newTemplate = {
            basePdf: await getSelectedRoleBasePdf(),
            schemas: JSON.parse(selectedRole.value.certificate_schema),
        };
        pdfDesignerInstance.updateTemplate(newTemplate);
    } else {
        pdfDesignerInstance.updateTemplate(template);
    }
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head title="Activity" />
        <DefaultPageLayout>
            <Heading title="Manage Certificates" description="Create Templates and Generate Certificate PDFs for each role" />

            <div class="mb-3 flex flex-row items-center gap-3">
                <h3 class="font-bold">Create Template</h3>
                <DropdownMenu>
                    <Button variant="secondary" as-child>
                        <DropdownMenuTrigger class="uppercase hover:cursor-pointer">
                            {{ selectedRole ? selectedRole.name : 'Select Role' }}
                            <ChevronsUpDownIcon />
                        </DropdownMenuTrigger>
                    </Button>
                    <DropdownMenuContent>
                        <DropdownMenuRadioGroup v-model="selectedRoleId">
                            <DropdownMenuRadioItem v-for="role of props.eventRoles" :key="role.id" class="uppercase" :value="String(role.id)">
                                {{ role.name }}
                            </DropdownMenuRadioItem>
                        </DropdownMenuRadioGroup>
                    </DropdownMenuContent>
                </DropdownMenu>
                <Input type="text" v-model="nomorSuratInput" class="max-w-sm" placeholder="Nomor Surat" />
                <Button variant="default" @click="pdfDesignerInstance?.saveTemplate()">Save Template</Button>
                <Button variant="default" @click="generateCertificates">Generate & Send Certificates</Button>
            </div>
            <div ref="pdfDesigner" class="max-h-[500px] max-w-6xl"></div>

            <h3 class="mt-8 font-bold">Certificates Sent</h3>
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-12">Nomor</TableHead>
                        <TableHead>Recipient Name</TableHead>
                        <TableHead>Recipient NIM</TableHead>
                        <TableHead>No. Surat</TableHead>
                        <TableHead>Recipient Role</TableHead>
                        <TableHead>File</TableHead>
                        <TableHead>Controls</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(certificate, index) of props.certificates" :key="certificate.id">
                        <TableCell class="text-center">{{ index + 1 }}</TableCell>
                        <TableCell>{{ certificate.user.name }}</TableCell>
                        <TableCell v-if="certificate.user.nim">{{ certificate.user.nim }}</TableCell>
                        <TableCell v-else>Organization</TableCell>
                        <TableCell>{{ certificate.nomor_surat }}</TableCell>
                        <TableCell>{{ certificate.role.name.toUpperCase() }}</TableCell>
                        <TableCell>
                            <a
                                :href="route('event.getCertificateFile', { event_id: props.event.id, filename: certificate.file })"
                                target="_blank"
                                rel="noopener"
                                as-child
                            >
                                <Button variant="link" class="p-0 text-blue-200">Download File</Button>
                            </a>
                        </TableCell>
                        <TableCell>
                            <DropdownMenu>
                                <Button variant="outline" as-child>
                                    <DropdownMenuTrigger class="hover:cursor-pointer">
                                        <EllipsisIcon />
                                    </DropdownMenuTrigger>
                                </Button>
                                <DropdownMenuContent>
                                    <DropdownMenuItem
                                        @click="
                                            router.delete(`/event/${props.event.id}/delete-certificate/${certificate.id}`, { preserveScroll: true })
                                        "
                                    >
                                        <XIcon /> Remove
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </DefaultPageLayout>
    </AppLayout>
</template>
