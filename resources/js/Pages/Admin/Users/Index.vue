<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { ElMessageBox, ElNotification } from "element-plus";

defineProps({
    users: Array,
    flash: Object,
});

// Fungsi untuk konfirmasi delete user
const confirmDelete = (userId) => {
    ElMessageBox.confirm(
        'Apakah Anda yakin ingin menghapus user ini?',
        'Konfirmasi',
        {
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal',
            type: 'warning',
        }
    ).then(() => {
        router.delete(route('admin.users.destroy', userId), {
            onSuccess: (page) => {
                ElNotification({
                    title: 'Success',
                    message: page.props.flash?.success || 'User berhasil dihapus.',
                    type: 'success',
                });
            },
            onError: (error) => {
                ElNotification({
                    title: 'Error',
                    message: 'Gagal menghapus user.',
                    type: 'error',
                });
            },
        });
    }).catch(() => {
        // User canceled the delete action
    });
};
</script>

<template>
    <AdminLayout>
        <section class="bg-gray-50 dark:bg-gray-900 p-3 sm:p-5">
            <div class="mx-auto max-w-screen-xl px-4 lg:px-12">
                <Head title="Users" />
                
                <!-- Header dengan tombol Add User -->
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manage Users</h1>
                    <Link 
                        :href="route('admin.users.create')" 
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                    >
                        Add User
                    </Link>
                </div>
                
                <!-- Flash Message -->
                <div v-if="flash?.success" class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ flash.success }}
                </div>
                <div v-if="flash?.error" class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
                    {{ flash.error }}
                </div>
                
                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">#</th>
                                <th scope="col" class="px-6 py-3">Name</th>
                                <th scope="col" class="px-6 py-3">Email</th>
                                <th scope="col" class="px-6 py-3">Role</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(user, index) in users" :key="user.id" class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4">{{ index + 1 }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ user.name }}</td>
                                <td class="px-6 py-4">{{ user.email }}</td>
                                <td class="px-6 py-4">
                                    <span 
                                        v-if="user.isAdmin" 
                                        class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300"
                                    >
                                        Admin
                                    </span>
                                    <span 
                                        v-else 
                                        class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300"
                                    >
                                        User
                                    </span>
                                </td>
                                <td class="px-6 py-4 flex gap-2">
                                    <Link 
                                        :href="route('admin.users.edit', user.id)" 
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                                    >
                                        Edit
                                    </Link>
                                    <button 
                                        @click="confirmDelete(user.id)" 
                                        class="font-medium text-red-600 dark:text-red-500 hover:underline"
                                    >
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center">No users found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>