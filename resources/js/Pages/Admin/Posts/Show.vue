<template>
    <AuthenticatedLayout>
        <Head title="Post Details" />

        <div class="py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Post Details</h1>
                        <p class="text-gray-600 mt-2">Post #{{ post.id }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link :href="`/posts/${post.id}/edit`" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            Edit
                        </Link>
                        <Link href="/posts" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg transition">
                            Back
                        </Link>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Left Column - Post Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Post Card -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6 border-b border-gray-200">
                                <h2 class="text-xl font-bold text-gray-900">Post Content</h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <!-- Status Badge -->
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">Status:</span>
                                    <button :class="['px-4 py-2 rounded-full text-sm font-semibold', getStatusColor(post.status)]">
                                        {{ post.status }}
                                    </button>
                                </div>

                                <!-- Image -->
                                <div v-if="post.image" class="flex items-center justify-center">
                                    <img :src="post.image" alt="Post image" class="max-w-full max-h-80 rounded-lg shadow-md object-contain" />
                                </div>
                                <div v-else class="flex items-center justify-center h-40 bg-gray-100 rounded-lg">
                                    <span class="text-gray-400">No image</span>
                                </div>

                                <!-- Content -->
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">📝 Content:</p>
                                    <div class="text-sm text-gray-700 leading-relaxed prose prose-sm max-h-96 overflow-y-auto" v-html="post.content">
                                    </div>
                                </div>

                                <!-- Platforms -->
                                <div>
                                    <p class="text-sm font-semibold text-gray-700 mb-2">🌐 Platforms:</p>
                                    <div class="flex flex-wrap gap-2">
                                        <span v-for="platform in post.platforms" :key="platform.id" class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            {{ platform.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Card -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6 border-b border-gray-200">
                                <h2 class="text-xl font-bold text-gray-900">Timeline</h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <!-- Created -->
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100">
                                            <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">📅 Created</p>
                                        <p class="text-sm text-gray-600">{{ post.created_at }}</p>
                                    </div>
                                </div>

                                <!-- Scheduled -->
                                <div v-if="post.scheduled_at_formatted" class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-yellow-100">
                                            <svg class="h-4 w-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">📅 Scheduled</p>
                                        <p class="text-sm text-gray-600">{{ post.scheduled_at_formatted }}</p>
                                    </div>
                                </div>

                                <!-- Published -->
                                <div v-if="post.published_at" class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100">
                                            <svg class="h-4 w-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">✅ Published</p>
                                        <p class="text-sm text-gray-600">{{ post.published_at }}</p>
                                    </div>
                                </div>

                                <!-- Approved -->
                                <div v-if="post.approved_at" class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-purple-100">
                                            <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900">👤 Approved by {{ post.approved_by_name }}</p>
                                        <p class="text-sm text-gray-600">{{ post.approved_at }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Summary -->
                    <div class="space-y-6">
                        <!-- Summary Card -->
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="p-6 border-b border-gray-200">
                                <h2 class="text-lg font-bold text-gray-900">Summary</h2>
                            </div>
                            <div class="p-6 space-y-4 text-sm">
                                <div>
                                    <p class="font-semibold text-gray-700">Post ID</p>
                                    <p class="text-gray-600">#{{ post.id }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-700">Status</p>
                                    <p :class="['text-sm font-semibold px-2 py-1 rounded inline-block', getStatusColor(post.status)]">
                                        {{ post.status }}
                                    </p>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-700">Platform Count</p>
                                    <p class="text-gray-600">{{ post.platforms.length }} platform(s)</p>
                                </div>
                                <div v-if="logs.length > 0">
                                    <p class="font-semibold text-gray-700">Log Entries</p>
                                    <p class="text-gray-600">{{ logs.length }} total</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md p-6 border border-blue-200">
                            <h3 class="font-bold text-gray-900 mb-4">📊 Activity Stats</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Total Logs:</span>
                                    <span class="font-bold text-gray-900">{{ logs.length }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Success:</span>
                                    <span class="font-bold text-green-600">{{ successLogs }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700">Errors:</span>
                                    <span class="font-bold text-red-600">{{ errorLogs }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logs Section -->
                <div v-if="logs.length > 0" class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900">📋 Activity Logs ({{ logs.length }})</h2>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div v-for="(log, index) in logs" :key="log.id" class="p-6">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div :class="['flex-shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-white text-xs font-bold', log.status === 'success' ? 'bg-green-500' : 'bg-red-500']">
                                        {{ log.status === 'success' ? '✓' : '✕' }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ log.action }}</p>
                                        <p class="text-xs text-gray-500">Log #{{ index + 1 }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p :class="['text-sm font-semibold px-2 py-1 rounded inline-block', log.status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                                        {{ log.status }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">{{ log.created_at }}</p>
                                </div>
                            </div>

                            <!-- Message -->
                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-200 mb-3">
                                <p class="text-sm text-gray-700">{{ log.message }}</p>
                            </div>

                            <!-- Response Data -->
                            <div v-if="log.response_data && Object.keys(log.response_data).length > 0" class="bg-gray-900 rounded-lg p-3 text-white font-mono text-xs overflow-x-auto">
                                <button 
                                    @click="toggleLogDetails(log.id)"
                                    class="text-blue-300 hover:text-blue-100 mb-2 text-xs font-semibold"
                                >
                                    {{ expandedLogs[log.id] ? '▼ Hide Response Data' : '▶ Show Response Data' }}
                                </button>
                                <div v-if="expandedLogs[log.id]" class="mt-2 max-h-48 overflow-y-auto">
                                    <pre>{{ JSON.stringify(log.response_data, null, 2) }}</pre>
                                </div>
                            </div>

                            <!-- Platform Info -->
                            <div v-if="log.platform_name" class="mt-3">
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    📱 {{ log.platform_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Logs State -->
                <div v-else class="mt-8 bg-white rounded-lg shadow-md p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No activity logs yet</h3>
                    <p class="mt-1 text-sm text-gray-500">Activity logs will appear here when actions are performed on this post.</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    post: Object,
    logs: {
        type: Array,
        default: () => []
    },
    statuses: Array,
});

const expandedLogs = ref({});

const getStatusColor = (status) => {
    const colors = {
        pending_approval: 'bg-yellow-100 text-yellow-800',
        queued: 'bg-gray-100 text-gray-800',
        rejected: 'bg-red-100 text-red-800',
        published: 'bg-green-100 text-green-800',
        failed: 'bg-orange-100 text-orange-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const successLogs = computed(() => {
    return props.logs.filter(log => log.status === 'success').length;
});

const errorLogs = computed(() => {
    return props.logs.filter(log => log.status === 'error').length;
});

const toggleLogDetails = (logId) => {
    expandedLogs.value[logId] = !expandedLogs.value[logId];
};
</script>
