<script lang="ts" setup>
import { Placeholder as ttPlaceholder } from '@tiptap/extension-placeholder';
import { Subscript as ttSubscript } from '@tiptap/extension-subscript';
import { Superscript as ttSuperScript } from '@tiptap/extension-superscript';
import { TaskItem as ttTaskItem } from '@tiptap/extension-task-item';
import { TaskList as ttTaskList } from '@tiptap/extension-task-list';
import { TextAlign as ttTextAlign } from '@tiptap/extension-text-align';
import { Typography as ttTypography } from '@tiptap/extension-typography';
import { Underline as ttUnderline } from '@tiptap/extension-underline';
import { StarterKit } from '@tiptap/starter-kit';
import { EditorContent, useEditor } from '@tiptap/vue-3';

const props = defineProps(['value']);

const tiptapEditor = useEditor({
    content: JSON.parse(props.value),
    editable: false,
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
            class: 'prose text-foreground/70 dark:prose-invert prose-sm sm:prose-sm lg:prose-md focus:outline-none',
        },
    },
});
</script>

<template>
    <div>
        <EditorContent :editor="tiptapEditor" />
    </div>
</template>
