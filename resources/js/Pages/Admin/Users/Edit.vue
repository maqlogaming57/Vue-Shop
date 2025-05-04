<script setup>
import { Head } from "@inertiajs/vue3";

defineProps({
    user: Object,
});
</script>

<template>
    <div>
        <Head title="Edit User" />
        <h1 class="text-2xl font-bold mb-4">Edit User</h1>
        <form :action="`/admin/users/${user.id}`" method="POST">
            <input type="hidden" name="_method" value="PUT" />
            <div>
                <label for="name">Name</label>
                <input type="text" id="name" name="name" :value="user.name" required />
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" id="email" name="email" :value="user.email" required />
            </div>
            <div>
                <label for="isAdmin">Role</label>
                <select id="isAdmin" name="isAdmin">
                    <option :selected="!user.isAdmin" value="0">User</option>
                    <option :selected="user.isAdmin" value="1">Admin</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
        </form>
    </div>
</template>