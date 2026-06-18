<template>
    <AuthenticatedLayout>
        <Head title="Create User" />

        <div class="py-12">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">Create New User</h1>
                    <p class="text-gray-600 mt-2">Add a new user to the system</p>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form @submit.prevent="submit">
                        <!-- Name Field -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="John Doe"
                                required
                            />
                            <p v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
                        </div>

                        <!-- Email Field -->
                        <div class="mb-6">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="john@example.com"
                                required
                            />
                            <p v-if="form.errors.email" class="text-red-600 text-sm mt-1">{{ form.errors.email }}</p>
                        </div>

                        <!-- Password Field -->
                        <div class="mb-6">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="••••••••"
                                autocomplete="new-password"
                                required
                            />
                            <p v-if="form.errors.password" class="text-red-600 text-sm mt-1">{{ form.errors.password }}</p>
                            <p class="text-gray-500 text-sm mt-1">Minimum 8 characters</p>
                        </div>

                        <!-- Role Field -->
                        <div class="mb-6">
                            <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <select
                                id="role"
                                v-model="form.role"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                required
                            >
                                <option value="">Select a role</option>
                                <option v-for="role in roles" :key="role.id" :value="role.name">
                                    {{ role.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.role" class="text-red-600 text-sm mt-1">{{ form.errors.role }}</p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4 pt-6">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-semibold py-2 px-6 rounded-lg transition flex items-center justify-center gap-2"
                            >
                                <svg v-if="!form.processing" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create User</span>
                            </button>
                            <Link href="/users" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition flex items-center justify-center">
                                Cancel
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    roles: Array,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: '',
});

const submit = () => {
    form.post('/users');
};
</script>
