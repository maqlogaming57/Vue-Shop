<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

// Ambil data sales dari props yang dikirim oleh backend
const sales = computed(() => usePage().props.sales);
</script>

<template>
    <div class="p-6 bg-white dark:bg-gray-800">
        <h1 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">Report Penjualan</h1>
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Tanggal</th>
                    <th scope="col" class="px-6 py-3">Order ID</th>
                    <th scope="col" class="px-6 py-3">Jumlah</th>
                    <th scope="col" class="px-6 py-3">Harga</th>
                    <th scope="col" class="px-6 py-3">Kurir</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="sale in sales"
                    :key="sale.id"
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700"
                >
                    <td class="px-6 py-4">{{ new Date(sale.created_at).toLocaleDateString() }}</td>
                    <td class="px-6 py-4">{{ sale.order_id }}</td>
                    <td class="px-6 py-4">
                        <!-- Ambil quantity dari items -->
                        <span v-for="item in sale.items" :key="item.id">
                            {{ item.quantity }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ sale.gross_amount }}</td>
                    <td class="px-6 py-4">{{ sale.courir }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>