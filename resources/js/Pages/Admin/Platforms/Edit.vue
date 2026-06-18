<template>
    <AuthenticatedLayout>
        <Head title="Edit Platform" />

        <div class="py-12">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Edit Platform</h1>
                    <p class="text-gray-600 mt-2">Update platform details</p>
                </div>

                <form @submit.prevent="submit" class="bg-white rounded-lg shadow-sm p-6">
                    <!-- Name Field -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Platform Name *</label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="e.g., LinkedIn, Facebook, Twitter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        <p v-if="form.errors.name" class="text-red-600 text-sm mt-2">{{ form.errors.name }}</p>
                    </div>

                    <!-- Slug Field -->
                    <div class="mb-6">
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">Slug *</label>
                        <input
                            id="slug"
                            v-model="form.slug"
                            type="text"
                            placeholder="e.g., linkedin, facebook, twitter"
                            @input="form.slug = form.slug.toLowerCase().replace(/[^a-z0-9-]/g, '-')"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        <p class="text-gray-500 text-sm mt-1">URL-friendly identifier (lowercase, hyphens only)</p>
                        <p v-if="form.errors.slug" class="text-red-600 text-sm mt-2">{{ form.errors.slug }}</p>
                    </div>

                    <!-- Description Field -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            placeholder="Enter platform description..."
                            rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        />
                        <p v-if="form.errors.description" class="text-red-600 text-sm mt-2">{{ form.errors.description }}</p>
                    </div>

                    <!-- Active Status -->
                    <div class="mb-6 flex items-center">
                        <input
                            id="is_active"
                            v-model="form.is_active"
                            type="checkbox"
                            class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500"
                        />
                        <label for="is_active" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer">
                            Active (available for posting)
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-2 px-6 rounded-lg transition"
                        >
                            {{ form.processing ? 'Updating...' : 'Update Platform' }}
                        </button>
                        <Link href="/platforms" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition">
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    platform: Object,
});

const form = useForm({
    name: props.platform.name,
    slug: props.platform.slug,
    description: props.platform.description || '',
    is_active: props.platform.is_active,
});

const submit = () => {
    form.patch(`/platforms/${props.platform.id}`);
};
</script>
