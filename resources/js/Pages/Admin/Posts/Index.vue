<template>
    <AuthenticatedLayout>
        <Head title="Posts Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Posts Management</h1>
                        <p class="text-gray-600 mt-2">Create and manage social media posts</p>
                    </div>
                    <Link href="/posts/create" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg flex items-center gap-2 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Create Post
                    </Link>
                </div>

                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                    ✅ {{ $page.props.flash.success }}
                </div>

                <!-- Posts Table -->
                <div v-if="posts.data.length > 0" class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Image</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Content</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Platforms</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Published</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="post in posts.data" :key="post.id" class="hover:bg-gray-50 transition">
                                <!-- Image -->
                                <td class="px-6 py-4">
                                    <button 
                                        v-if="post.image" 
                                        @click="openImageModal(post.image, post.content)"
                                        class="inline-block relative"
                                    >
                                        <img :src="post.image" :alt="post.content.substring(0, 50)" class="w-12 h-12 rounded object-cover hover:opacity-75 transition cursor-pointer" />
                                    </button>
                                    <span v-else class="text-gray-400 text-sm">No image</span>
                                </td>

                                <!-- Content -->
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <button 
                                        @click="openImageModal(post.image, post.content)"
                                        class="line-clamp-2 max-w-xs text-left hover:text-blue-600 hover:underline transition cursor-pointer"
                                    >
                                        {{ stripHtml(post.content) }}
                                    </button>
                                </td>

                                <!-- Platforms -->
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-wrap gap-1">
                                        <span v-for="platform in post.platforms" :key="platform" class="inline-flex px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ platform }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 text-center">
                                    <button 
                                        @click="openStatusModal(post)"
                                        :class="['px-3 py-1 rounded-full text-sm font-semibold cursor-pointer hover:opacity-75 transition', getStatusColor(post.status)]"
                                    >
                                        {{ post.status }}
                                    </button>
                                </td>

                                <!-- Published Date -->
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <div v-if="post.published_at" class="flex items-center gap-1">
                                        <span class="text-green-600">✅</span>
                                        {{ post.published_at }}
                                    </div>
                                    <div v-else-if="post.scheduled_at_formatted" class="flex items-center gap-1">
                                        <span class="text-blue-600">📅</span>
                                        {{ post.scheduled_at_formatted }}
                                    </div>
                                    <div v-else class="text-gray-400">-</div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right text-sm space-x-2">
                                    <Link :href="`/posts/${post.id}`" class="text-green-600 hover:text-green-900 font-medium transition">
                                        View
                                    </Link>
                                    <Link :href="`/posts/${post.id}/edit`" class="text-blue-600 hover:text-blue-900 font-medium transition">
                                        Edit
                                    </Link>
                                    <button
                                        @click="deletePost(post.id)"
                                        class="text-red-600 hover:text-red-900 font-medium transition"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-lg shadow-sm p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No posts yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating your first post.</p>
                </div>

                <!-- Pagination -->
                <div v-if="posts.links && posts.links.length > 0" class="mt-8 flex items-center justify-center gap-2">
                    <Link v-for="link in posts.links" :key="link.url || link.label" :href="link.url || '#'" :class="[
                        'px-3 py-1 rounded text-sm',
                        link.active ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
                        !link.url ? 'cursor-not-allowed opacity-50' : ''
                    ]" v-html="link.label" />
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div v-if="showImageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto flex flex-col">
                <div class="flex items-center justify-between p-4 border-b border-gray-200 sticky top-0 bg-white">
                    <h3 class="text-lg font-semibold text-gray-900">Post Preview</h3>
                    <button 
                        @click="closeImageModal"
                        class="text-gray-500 hover:text-gray-700 transition"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 p-6 space-y-4">
                    <!-- Image -->
                    <div class="flex items-center justify-center">
                        <img :src="modalImageUrl" alt="Preview" class="max-w-full max-h-80 object-contain rounded-lg shadow-md" />
                    </div>
                    
                    <!-- Content -->
                    <div v-if="modalContent" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-sm font-semibold text-gray-700 mb-2">📝 Post Content:</p>
                        <div class="text-sm text-gray-700 leading-relaxed prose prose-sm max-h-48 overflow-y-auto" v-html="modalContent">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Update Modal -->
        <div v-if="showStatusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg max-w-md w-full">
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Update Post Status</h3>
                    <button 
                        @click="closeStatusModal"
                        class="text-gray-500 hover:text-gray-700 transition"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Status Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                        <select 
                            v-model="statusFormData.newStatus"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Select a status</option>
                            <option value="pending_approval">Pending Approval</option>
                            <option value="queued">Queued</option>
                            <option value="rejected">Rejected</option>
                            <option value="published">Published</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>

                    <!-- Schedule DateTime (shows for queued status) -->
                    <div v-if="statusFormData.newStatus === 'queued'" class="p-3 bg-blue-50 rounded-lg border border-blue-200 space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">📅 Schedule Date & Time</label>
                            <input 
                                v-model="statusFormData.scheduledAt"
                                type="datetime-local"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            />
                            <p class="text-xs text-gray-500 mt-2">Leave empty to use 1 hour from now</p>
                        </div>
                    </div>

                    <!-- Webhook Checkbox (shows for published status) -->
                    <div v-else-if="statusFormData.newStatus === 'published'" class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input 
                                v-model="statusFormData.callWebhook"
                                type="checkbox"
                                class="w-4 h-4 text-purple-600 rounded focus:ring-2 focus:ring-purple-500"
                            />
                            <span class="text-sm font-medium text-gray-700">Call agent webhook</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-2">Webhook will be triggered to publish this post</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-6">
                        <button
                            @click="closeStatusModal"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg transition"
                        >
                            Cancel
                        </button>
                        <button
                            @click="updatePostStatus"
                            :disabled="!statusFormData.newStatus || statusFormLoading"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-2 px-4 rounded-lg transition"
                        >
                            <span v-if="statusFormLoading">Updating...</span>
                            <span v-else>Update Status</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    posts: Object,
    platforms: Array,
    statuses: Array,
});

const showImageModal = ref(false);
const modalImageUrl = ref('');
const modalContent = ref('');

const showStatusModal = ref(false);
const statusFormLoading = ref(false);
const statusFormData = ref({
    postId: null,
    currentStatus: null,
    newStatus: '',
    callWebhook: true,
    scheduledAt: '',
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

const openImageModal = (imageUrl, content) => {
    modalImageUrl.value = imageUrl;
    modalContent.value = content;
    showImageModal.value = true;
};

const closeImageModal = () => {
    showImageModal.value = false;
    modalImageUrl.value = '';
    modalContent.value = '';
};

const openStatusModal = (post) => {
    // Convert scheduled_at to datetime-local format if it exists
    let scheduledAtValue = '';
    if (post.scheduled_at) {
        // Post object has scheduled_at as ISO string or formatted string
        const dateObj = new Date(post.scheduled_at);
        if (!isNaN(dateObj.getTime())) {
            // Format as YYYY-MM-DDTHH:mm for datetime-local input
            const year = dateObj.getFullYear();
            const month = String(dateObj.getMonth() + 1).padStart(2, '0');
            const day = String(dateObj.getDate()).padStart(2, '0');
            const hours = String(dateObj.getHours()).padStart(2, '0');
            const minutes = String(dateObj.getMinutes()).padStart(2, '0');
            scheduledAtValue = `${year}-${month}-${day}T${hours}:${minutes}`;
        }
    }

    statusFormData.value = {
        postId: post.id,
        currentStatus: post.status,
        newStatus: post.status,
        callWebhook: true,
        scheduledAt: scheduledAtValue,
    };
    showStatusModal.value = true;
};

const closeStatusModal = () => {
    showStatusModal.value = false;
    statusFormData.value = {
        postId: null,
        currentStatus: null,
        newStatus: '',
        callWebhook: true,
        scheduledAt: '',
    };
};

const updatePostStatus = async () => {
    if (!statusFormData.value.newStatus) return;

    statusFormLoading.value = true;

    try {
        const payload = {
            status: statusFormData.value.newStatus,
            call_webhook: statusFormData.value.callWebhook,
        };

        // Add scheduled_at if queued status is selected
        if (statusFormData.value.newStatus === 'queued') {
            // Use provided datetime or default to 1 hour from now
            if (statusFormData.value.scheduledAt) {
                const dateObj = new Date(statusFormData.value.scheduledAt);
                payload.scheduled_at = dateObj.toISOString();
            } else {
                const oneHourFromNow = new Date();
                oneHourFromNow.setHours(oneHourFromNow.getHours() + 1);
                payload.scheduled_at = oneHourFromNow.toISOString();
            }
        }

        // Use direct fetch with PATCH method
        const response = await fetch(`/posts/${statusFormData.value.postId}/status`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        closeStatusModal();
        // Refresh the page to see the updated status
        window.location.reload();
    } catch (error) {
        console.error('Error updating status:', error);
        alert('Error updating post status: ' + error.message);
    } finally {
        statusFormLoading.value = false;
    }
};

const approvePost = () => {
    updatePostStatus();
};

const deletePost = (postId) => {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(`/posts/${postId}`);
    }
};
</script>
