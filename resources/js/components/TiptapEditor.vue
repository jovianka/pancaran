<script setup lang="ts">
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Placeholder as ttPlaceholder } from '@tiptap/extension-placeholder';
import { Subscript as ttSubscript } from '@tiptap/extension-subscript';
import { Superscript as ttSuperScript } from '@tiptap/extension-superscript';
import { TaskItem as ttTaskItem } from '@tiptap/extension-task-item';
import { TaskList as ttTaskList } from '@tiptap/extension-task-list';
import { TextAlign as ttTextAlign } from '@tiptap/extension-text-align';
import { Typography as ttTypography } from '@tiptap/extension-typography';
import { Underline as ttUnderline } from '@tiptap/extension-underline';
import { StarterKit } from '@tiptap/starter-kit';
import { Content, EditorContent, useEditor } from '@tiptap/vue-3';
import {
    AlignCenter,
    AlignJustify,
    AlignLeft,
    AlignRight,
    Bold,
    Code,
    Italic,
    List,
    ListChecks,
    ListOrdered,
    Strikethrough,
    Subscript,
    Superscript,
    TextQuote,
    Underline,
} from 'lucide-vue-next';
import Button from './ui/button/Button.vue';
import Toggle from './ui/toggle/Toggle.vue';

interface Props {
    editable?: boolean;
}

const model = defineModel();
const props = withDefaults(defineProps<Props>(), {
    editable: true,
});

const tiptapEditor = useEditor({
    content: model.value as Content,
    editable: props.editable,
    extensions: [
        StarterKit,
        ttUnderline,
        ttTaskItem.configure({
            nested: true,
        }),
        ttTaskList,
        ttSubscript,
        ttSuperScript,
        ttTextAlign.configure({
            types: ['heading', 'paragraph'],
            alignments: ['left', 'center', 'right', 'justify'],
        }),
        ttTypography,
        ttPlaceholder.configure({
            placeholder: 'Write something',
        }),
    ],
    editorProps: {
        attributes: {
            class: 'prose prose-slate dark:prose-invert prose-sm sm:prose-base lg:prose-md focus:outline-none mt-2',
        },
    },
    onUpdate: ({ editor }) => {
        model.value = editor.getJSON();
    },
});
</script>

<template>
    <div class="mt-3 w-fit rounded-md p-3 outline outline-black/30 dark:outline-white/30">
        <div class="flex flex-row flex-wrap items-center gap-2" v-if="tiptapEditor">
            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('bold')"
                @click="tiptapEditor.chain().focus().toggleBold().run()"
            >
                <Bold />
            </Toggle>
            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('italic')"
                @click="tiptapEditor.chain().focus().toggleItalic().run()"
            >
                <Italic />
            </Toggle>
            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('underline')"
                @click="tiptapEditor.chain().focus().toggleUnderline().run()"
            >
                <Underline />
            </Toggle>
            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('strike')"
                @click="tiptapEditor.chain().focus().toggleStrike().run()"
            >
                <Strikethrough />
            </Toggle>

            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('blockquote')"
                @click="tiptapEditor.chain().focus().toggleBlockquote().run()"
            >
                <TextQuote />
            </Toggle>
            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('codeBlock')"
                @click="tiptapEditor.chain().focus().toggleCodeBlock().run()"
            >
                <Code />
            </Toggle>

            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('orderedList')"
                @click="tiptapEditor.chain().focus().toggleOrderedList().run()"
            >
                <ListOrdered />
            </Toggle>
            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('bulletList')"
                @click="tiptapEditor.chain().focus().toggleBulletList().run()"
            >
                <List />
            </Toggle>
            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('taskList')"
                @click="tiptapEditor.chain().focus().toggleTaskList().run()"
            >
                <ListChecks />
            </Toggle>

            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('subscript')"
                @click="tiptapEditor.chain().focus().unsetSuperscript().toggleSubscript().run()"
            >
                <Subscript />
            </Toggle>
            <Toggle
                class="hover:cursor-pointer"
                :model-value="tiptapEditor?.isActive('superscript')"
                @click="tiptapEditor.chain().focus().unsetSubscript().toggleSuperscript().run()"
            >
                <Superscript />
            </Toggle>

            <DropdownMenu>
                <DropdownMenuTrigger>
                    <Button variant="ghost" class="cursor-pointer">
                        <AlignLeft
                            v-show="
                                tiptapEditor.isActive('paragraph', { textAlign: 'left' }) ||
                                tiptapEditor.isActive('paragraph', { textAlign: null }) ||
                                tiptapEditor.isActive('codeBlock', { textAlign: undefined })
                            "
                        />
                        <AlignCenter v-show="tiptapEditor.isActive('paragraph', { textAlign: 'center' })" />
                        <AlignRight v-show="tiptapEditor.isActive('paragraph', { textAlign: 'right' })" />
                        <AlignJustify v-show="tiptapEditor.isActive('paragraph', { textAlign: 'justify' })" />
                    </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-fit min-w-min">
                    <DropdownMenuItem class="cursor-pointer" @click="tiptapEditor.chain().focus().setTextAlign('left').run()"
                        ><AlignLeft />
                    </DropdownMenuItem>
                    <DropdownMenuItem class="cursor-pointer" @click="tiptapEditor.chain().focus().setTextAlign('center').run()"
                        ><AlignCenter
                    /></DropdownMenuItem>
                    <DropdownMenuItem class="cursor-pointer" @click="tiptapEditor.chain().focus().setTextAlign('right').run()"
                        ><AlignRight
                    /></DropdownMenuItem>
                    <DropdownMenuItem class="cursor-pointer" @click="tiptapEditor.chain().focus().setTextAlign('justify').run()"
                        ><AlignJustify
                    /></DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
        <EditorContent :editor="tiptapEditor" v-model="model" />
    </div>
</template>

<style lang="scss">
.tiptap ul[data-type='taskList'] {
    padding: 0.25rem;

    li {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.5rem;
    }
}
</style>
