<script setup>
import { ref } from "vue";
import { Head, router, useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ElNotification } from "element-plus";

// Gunakan useForm dari Inertia untuk menangani form dengan lebih baik
const form = useForm({
    name: '',
    email: '',
    password: '',
    isAdmin: 0
});

// Fungsi reset untuk form
const resetForm = () => {
    form.reset();
};

// Fungsi submit yang diperbarui menggunakan form Inertia
const submitForm = () => {
    form.post(route("admin.users.store"), {
        onSuccess: (page) => {
            ElNotification({
                title: "Success",
                message: page.props.flash?.success || "User berhasil ditambahkan.",
                type: "success",
            });
            resetForm();
        },
        onError: (errors) => {
            ElNotification({
                title: "Error",
                message: "Gagal menambahkan user. Periksa kembali form.",
                type: "error",
            });
        },
    });
};
</script>

<template>
    <AdminLayout>
        <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
            <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
                <Head title="Add User" />
                <h1 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Add User</h1>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            id="name"
                            required
                            class="form-input"
                        />
                        <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input
                            v-model="form.email"
                            type="email"
                            id="email"
                            required
                            class="form-input"
                        />
                        <div v-if="form.errors.email" class="text-red-500 text-sm mt-1">{{ form.errors.email }}</div>
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                        <input
                            v-model="form.password"
                            type="password"
                            id="password"
                            required
                            class="form-input"
                        />
                        <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">{{ form.errors.password }}</div>
                    </div>
                    <div>
                        <label for="isAdmin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
                        <select
                            v-model="form.isAdmin"
                            id="isAdmin"
                            class="form-input"
                        >
                            <option value="0">User</option>
                            <option value="1">Admin</option>
                        </select>
                        <div v-if="form.errors.isAdmin" class="text-red-500 text-sm mt-1">{{ form.errors.isAdmin }}</div>
                    </div>
                    <div class="flex justify-end gap-4">
                        <button
                            type="button"
                            @click="resetForm"
                            class="btn-secondary"
                        >
                            Reset
                        </button>
                        <button
                            type="submit"
                            class="btn-primary"
                            :disabled="form.processing"
                        >
                            {{ form.processing ? 'Submitting...' : 'Submit' }}
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </AdminLayout>
</template>

<style scoped>
.form-input {
    @apply mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white;
}
.btn-primary {
    @apply text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 disabled:opacity-50;
}
.btn-secondary {
    @apply text-white bg-gray-500 hover:bg-gray-600 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-700 dark:hover:bg-gray-800 dark:focus:ring-gray-800;
}
</style>