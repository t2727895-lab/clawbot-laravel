<template>
    <AuthenticatedLayout>
        <Head title="Edit Post" />

        <div class="py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Edit Post</h1>
                    <p class="text-gray-600 mt-2">Update your post content and platform settings</p>
                </div>

                <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content Column -->
                    <div class="lg:col-span-2">
                        <!-- Content Field -->
                        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Post Content</label>
                            <RichTextEditor v-model="form.content" />
                            <div class="mt-2 text-sm text-gray-500">
                                Character count: {{ stripHtml(form.content).length }}
                            </div>
                            <p v-if="form.errors.content" class="text-red-600 text-sm mt-2">{{ form.errors.content }}</p>
                        </div>

                        <!-- Image Field -->
                        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image (Optional)</label>
                            <div class="space-y-4">
                                <!-- File Input -->
                                <div>
                                    <label for="image_file" class="flex items-center justify-center w-full px-4 py-8 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-sm font-medium text-gray-600">Click to upload or drag and drop</span>
                                            <p class="text-xs text-gray-500 mt-1">JPG, JPEG, PNG up to 5MB</p>
                                        </div>
                                    </label>
                                    <input
                                        id="image_file"
                                        type="file"
                                        accept=".jpg,.jpeg,.png"
                                        @change="handleImageUpload"
                                        class="hidden"
                                    />
                                </div>

                                <!-- URL Input -->
                                <div>
                                    <label for="image" class="block text-xs font-medium text-gray-600 mb-1">Or paste image URL</label>
                                    <input
                                        id="image"
                                        v-model="form.image"
                                        type="url"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="https://example.com/image.jpg"
                                    />
                                </div>

                                <p v-if="form.errors.image" class="text-red-600 text-sm font-medium">⚠️ {{ form.errors.image }}</p>
                                <p v-if="uploadProgress > 0 && uploadProgress < 100" class="text-blue-600 text-sm font-medium">📤 Uploading: {{ uploadProgress }}%</p>
                            </div>
                        </div>

                        <!-- Content Preview -->
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-sm font-medium text-gray-700 mb-4">📱 Content Preview</h3>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 prose prose-sm max-w-none">
                                <div v-if="form.image" class="mb-3">
                                    <img :src="form.image" alt="Post preview" class="w-full max-h-64 object-cover rounded" @error="() => form.image = ''" />
                                </div>
                                <div v-if="form.content" v-html="form.content" />
                                <p v-else class="text-gray-500">Your content preview will appear here...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <!-- Platforms Section -->
                        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-4">🌐 Select Platforms</label>
                            
                            <div class="space-y-3">
                                <div v-for="platform in platforms" :key="platform.id" class="flex items-center">
                                    <input
                                        :id="`platform-${platform.id}`"
                                        type="checkbox"
                                        :value="platform.id"
                                        v-model="form.platforms"
                                        class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                                    />
                                    <label :for="`platform-${platform.id}`" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer capitalize">
                                        {{ platform.name }}
                                    </label>
                                </div>
                            </div>

                            <p v-if="form.errors.platforms" class="text-red-600 text-sm mt-3">{{ form.errors.platforms }}</p>

                            <div class="mt-4 p-3 bg-blue-50 rounded text-sm text-blue-800">
                                ✅ Selected: {{ form.platforms.length > 0 ? form.platforms.map(id => platforms.find(p => p.id === id)?.name).join(', ') : 'None' }}
                            </div>
                        </div>

                        <!-- Status Section - Read Only -->
                        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Status</label>
                            <div class="px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 flex items-center justify-between">
                                <span :class="['px-3 py-1 rounded-full text-sm font-semibold', getStatusColor(form.status)]">
                                    {{ form.status }}
                                </span>
                            </div>
                            <p class="text-gray-500 text-xs mt-2">💡 To change status, use the status button on the Posts list page</p>
                        </div>

                        <!-- Schedule Section (shows for queued status) -->
                        <div v-if="form.status === 'queued'" class="bg-blue-50 border border-blue-200 rounded-lg shadow-sm p-6 mb-6">
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">📅 Schedule Date & Time</label>
                            <input
                                id="scheduled_at"
                                v-model="form.scheduled_at"
                                type="datetime-local"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p v-if="form.errors.scheduled_at" class="text-red-600 text-sm mt-2">{{ form.errors.scheduled_at }}</p>
                            <p class="text-gray-600 text-sm mt-2">📌 Leave empty to use 1 hour from now</p>
                        </div>

                        <!-- Approval Notice (shows for pending_approval status) -->
                        <div v-if="form.status === 'pending_approval'" class="bg-yellow-50 border border-yellow-200 rounded-lg shadow-sm p-6 mb-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-yellow-800">⏳ Awaiting Approval</p>
                                    <p class="text-xs text-yellow-700 mt-1">This post will need admin approval before queuing for publication.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Published Status Info -->
                        <div v-if="form.status === 'published'" class="bg-green-50 border border-green-200 rounded-lg shadow-sm p-6 mb-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-green-800">✅ Direct Publish</p>
                                    <p class="text-xs text-green-700 mt-1">This post will be published immediately with webhook callback.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Rejected Status Info -->
                        <div v-if="form.status === 'rejected'" class="bg-red-50 border border-red-200 rounded-lg shadow-sm p-6 mb-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-red-800">❌ Rejected</p>
                                    <p class="text-xs text-red-700 mt-1">This post will not be published and requires revision.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Failed Status Info -->
                        <div v-if="form.status === 'failed'" class="bg-orange-50 border border-orange-200 rounded-lg shadow-sm p-6 mb-6">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 110.5 7.338A6.002 6.002 0 0113.477 14.89z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-orange-800">⚠️ Failed</p>
                                    <p class="text-xs text-orange-700 mt-1">Publishing failed. Please review and requeue.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="space-y-2">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-2 px-6 rounded-lg transition flex items-center justify-center gap-2"
                            >
                                <svg v-if="!form.processing" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span v-if="form.processing">Saving...</span>
                                <span v-else>Save Changes</span>
                            </button>
                            <Link href="/posts" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition flex items-center justify-center">
                                Cancel
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RichTextEditor from '@/Components/RichTextEditor.vue';

const props = defineProps({
    post: Object,
    platforms: Array,
    statuses: Array,
});

const uploadProgress = ref(0);

const form = useForm({
    content: props.post?.content || '',
    image: props.post?.image || '',
    status: props.post?.status || 'queued',
    platforms: props.post?.platforms || [],
    scheduled_at: props.post?.scheduled_at || '',
});

const stripHtml = (html) => {
    const tmp = document.createElement('DIV');
    tmp.innerHTML = html;
    return tmp.textContent || tmp.innerText || '';
};

const getStatusColor = (status) => {
    const colors = {
        pending_approval: 'bg-yellow-100 text-yellow-800',
        queued: 'bg-gray-100 text-gray-800',
        rejected: 'bg-red-100 text-red-800',
        published: 'bg-blue-100 text-blue-800',
        failed: 'bg-orange-100 text-orange-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const handleImageError = () => {
    form.image = '';
    alert('Failed to load image. Please check the URL or try uploading again.');
};

const handleImageUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please upload JPG, JPEG, or PNG files only.');
        event.target.value = '';
        return;
    }

    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('File size exceeds 5MB limit. Please choose a smaller image.');
        event.target.value = '';
        return;
    }

    const formData = new FormData();
    formData.append('image', file);

    try {
        uploadProgress.value = 20;
        
        // Get CSRF token from meta tag or form
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value;

        if (!token) {
            throw new Error('CSRF token not found. Please refresh the page.');
        }

        const response = await fetch('/upload-image', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
            },
        });

        uploadProgress.value = 80;

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.message || 'Upload failed');
        }

        const data = await response.json();
        form.image = data.url;
        uploadProgress.value = 100;
        
        setTimeout(() => {
            uploadProgress.value = 0;
        }, 1000);

        event.target.value = '';
    } catch (error) {
        console.error('Upload error:', error);
        alert('Error uploading image: ' + error.message);
        uploadProgress.value = 0;
        event.target.value = '';
    }
};

const submit = () => {
    // Convert datetime-local format (YYYY-MM-DDTHH:mm) to required format (YYYY-MM-DD HH:mm)
    if (form.scheduled_at) {
        form.scheduled_at = form.scheduled_at.replace('T', ' ');
    }
    form.patch(`/posts/${props.post?.id}`);
};
</script>
