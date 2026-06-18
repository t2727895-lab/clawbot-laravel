<template>
    <div class="border border-gray-300 rounded-lg overflow-hidden focus:outline-none focus:border-gray-300">
        <!-- Toolbar -->
        <div class="bg-gray-50 border-b border-gray-300 p-2 flex flex-wrap gap-1">
            <!-- Bold -->
            <button
                @click="editor?.chain().focus().toggleBold().run()"
                :class="['toolbar-btn', editor?.isActive('bold') && 'active']"
                type="button"
                title="Bold (Ctrl+B)"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2h6a1 1 0 011 1v2H7V5a1 1 0 010-1zm6 8H7v2h6v-2z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Italic -->
            <button
                @click="editor?.chain().focus().toggleItalic().run()"
                :class="['toolbar-btn', editor?.isActive('italic') && 'active']"
                type="button"
                title="Italic (Ctrl+I)"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5.951-1.429 5.951 1.429a1 1 0 001.169-1.409l-7-14z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Underline -->
            <button
                @click="editor?.chain().focus().toggleUnderline().run()"
                :class="['toolbar-btn', editor?.isActive('underline') && 'active']"
                type="button"
                title="Underline (Ctrl+U)"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 11-2 0V5H5v10h5a1 1 0 110 2H4a1 1 0 01-1-1V4z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Strike -->
            <button
                @click="editor?.chain().focus().toggleStrike().run()"
                :class="['toolbar-btn', editor?.isActive('strike') && 'active']"
                type="button"
                title="Strikethrough"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.5 2a1 1 0 00-.707.293l-1 1a1 1 0 000 1.414l1.707 1.707a1 1 0 001.414-1.414L4.914 3.5H6a2 2 0 012 2v2h2V4a2 2 0 012-2h1.086l-1.707-1.707a1 1 0 00-1.414 1.414l1 1A1 1 0 004.5 2zm11 8H4a1 1 0 000 2h11.5a1 1 0 000-2z" clip-rule="evenodd" />
                </svg>
            </button>

            <div class="w-px bg-gray-300"></div>

            <!-- Bullet List -->
            <button
                @click="editor?.chain().focus().toggleBulletList().run()"
                :class="['toolbar-btn', editor?.isActive('bulletList') && 'active']"
                type="button"
                title="Bullet List"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Ordered List -->
            <button
                @click="editor?.chain().focus().toggleOrderedList().run()"
                :class="['toolbar-btn', editor?.isActive('orderedList') && 'active']"
                type="button"
                title="Ordered List"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M2 5a1 1 0 011-1h1V3a1 1 0 000 2v1h1a1 1 0 000 2H4v1a1 1 0 11-2 0V8H1a1 1 0 01-1-1V5zm0 8a1 1 0 011-1h1v-1a1 1 0 112 0v1h1a1 1 0 110 2H4v1a1 1 0 11-2 0v-1H1a1 1 0 01-1-1v-1zm1.414-4.414a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414L2 9.586l-.586-.586z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Link -->
            <button
                @click="showLinkModal = true"
                :class="['toolbar-btn', editor?.isActive('link') && 'active']"
                type="button"
                title="Add Link"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM9.172 9.172a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm6.414-6.414a4 4 0 00-5.656 0L9.172 3.515a1 1 0 001.414 1.414l.707-.707a2 2 0 112.828 2.828l-.707.707a1 1 0 001.414 1.414l2.121-2.121a4 4 0 000-5.656z" clip-rule="evenodd" />
                </svg>
            </button>

            <!-- Emoji Picker -->
            <button
                @click="showEmojiPicker = !showEmojiPicker"
                type="button"
                title="Add Emoji"
                class="toolbar-btn"
            >
                😊
            </button>

            <div class="w-px bg-gray-300"></div>

            <!-- Clear Formatting -->
            <button
                @click="editor?.chain().focus().clearNodes().unsetAllMarks().run()"
                type="button"
                title="Clear Formatting"
                class="toolbar-btn"
            >
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Emoji Picker -->
        <div v-if="showEmojiPicker" class="bg-gray-100 border-b border-gray-300 p-3 grid grid-cols-8 gap-1 max-h-48 overflow-y-auto">
            <button
                v-for="emoji in emojis"
                :key="emoji"
                @click="insertEmoji(emoji)"
                class="text-xl hover:bg-gray-200 rounded p-1 transition"
                type="button"
            >
                {{ emoji }}
            </button>
        </div>

        <!-- Link Modal -->
        <div v-if="showLinkModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-medium mb-4">Add Link</h3>
                <input
                    v-model="linkUrl"
                    type="url"
                    placeholder="https://example.com"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-4 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
                <div class="flex gap-2">
                    <button
                        @click="addLink"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg transition"
                        type="button"
                    >
                        Add
                    </button>
                    <button
                        @click="showLinkModal = false"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 rounded-lg transition"
                        type="button"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Editor -->
        <EditorContent :editor="editor" class="prose prose-sm max-w-none editor-content" />
    </div>
</template>

<script setup>
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import { ref } from 'vue';

const props = defineProps({
    modelValue: String,
});

const emit = defineEmits(['update:modelValue']);

const showLinkModal = ref(false);
const showEmojiPicker = ref(false);
const linkUrl = ref('');

const emojis = ['😊', '😂', '❤️', '👍', '🎉', '🚀', '💯', '✨', '🔥', '💡', '📚', '🌟', '⭐', '👏', '🙌', '💪', '👌', '😍', '😎', '🤔', '👋', '🙏', '💼', '📱', '💻', '📊', '📈', '🎯', '🎁', '🏆'];

const editor = useEditor({
    content: props.modelValue || '',
    extensions: [
        StarterKit,
        Underline,
        Link.configure({
            openOnClick: false,
        }),
    ],
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

const addLink = () => {
    if (linkUrl.value && editor.value) {
        editor.value.chain().focus().extendMarkRange('link').setLink({ href: linkUrl.value }).run();
        linkUrl.value = '';
        showLinkModal.value = false;
    }
};

const insertEmoji = (emoji) => {
    editor.value?.chain().focus().insertContent(emoji).run();
};
</script>

<style scoped>
.toolbar-btn {
    @apply p-2 hover:bg-gray-200 rounded transition text-gray-700 flex items-center justify-center;
}

.toolbar-btn.active {
    @apply bg-blue-100 text-blue-600;
}

:deep(.editor-content) {
    @apply p-4 min-h-64 focus:outline-none;
}

:deep(.editor-content.ProseMirror:focus) {
    outline: none;
}

:deep(.editor-content.ProseMirror) {
    outline: none;
}

:deep(.ProseMirror:focus) {
    outline: none;
}

:deep(.editor-content h1),
:deep(.editor-content h2),
:deep(.editor-content h3) {
    @apply font-bold mt-4 mb-2;
}

:deep(.editor-content h1) {
    @apply text-2xl;
}

:deep(.editor-content h2) {
    @apply text-xl;
}

:deep(.editor-content h3) {
    @apply text-lg;
}

:deep(.editor-content ul),
:deep(.editor-content ol) {
    @apply ml-4 mb-2;
}

:deep(.editor-content li) {
    @apply mb-1;
}

:deep(.editor-content a) {
    @apply text-blue-600 underline hover:text-blue-800;
}

:deep(.editor-content strong) {
    @apply font-bold;
}

:deep(.editor-content em) {
    @apply italic;
}

:deep(.editor-content u) {
    @apply underline;
}

:deep(.editor-content s) {
    @apply line-through;
}

:deep(.editor-content p) {
    @apply mb-3;
}
</style>
