<template>
    <AuthenticatedLayout>
        <Head title="Platforms Management" />

        <div class="py-12">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8 flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Platforms Management</h1>
                        <p class="text-gray-600 mt-2">Manage social media platforms for post distribution</p>
                    </div>
                    <Link href="/platforms/create" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        + New Platform
                    </Link>
                </div>

                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                    ✅ {{ $page.props.flash.success }}
                </div>

                <!-- Error Message -->
                <div v-if="$page.props.flash?.error" class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                    ❌ {{ $page.props.flash.error }}
                </div>

                <!-- Platforms Table -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Platform</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Slug</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="platform in platforms" :key="platform.id" class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ platform.name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <code class="bg-gray-100 px-2 py-1 rounded">{{ platform.slug }}</code>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ platform.description || '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button
                                        @click="toggleActive(platform)"
                                        :class="[
                                            'px-3 py-1 rounded-full text-sm font-semibold transition',
                                            platform.is_active
                                                ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                                : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                                        ]"
                                    >
                                        {{ platform.is_active ? '✅ Active' : '⭕ Inactive' }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-2">
                                    <Link :href="`/platforms/${platform.id}/edit`" class="text-blue-600 hover:text-blue-900 font-medium">
                                        Edit
                                    </Link>
                                    <button
                                        @click="deletePlatform(platform)"
                                        class="text-red-600 hover:text-red-900 font-medium"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="platforms.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No platforms found. <Link href="/platforms/create" class="text-blue-600 hover:text-blue-900">Create one</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    platforms: Array,
});

const form = useForm({});

const toggleActive = (platform) => {
    form.post(`/platforms/${platform.id}/toggle`);
};

const deletePlatform = (platform) => {
    if (confirm(`Are you sure you want to delete "${platform.name}"?`)) {
        form.delete(`/platforms/${platform.id}`);
    }
};
</script>
