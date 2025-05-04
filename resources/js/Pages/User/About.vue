<script setup>
import { Head } from "@inertiajs/vue3";
import App from "@/Layouts/App.vue";
</script>

<template>
    <App>
        <Head title="Tentang Kami"></Head>
        <main class="pt-8 pb-16 lg:pt-16 lg:pb-24 bg-white dark:bg-gray-900 antialiased">
            <div class="flex justify-between px-4 mx-auto max-w-screen-xl">
                <article class="mx-auto w-full max-w-2xl format format-sm sm:format-base lg:format-lg format-blue dark:format-invert">
                    <!-- Logo Section -->
                    <div class="flex justify-center mb-8">
                        <img src="/images/compro2.png" alt="Cakayu Logo" class="w-100 h-auto">
                    </div>
                    <!-- End Logo Section -->
                    <p class="lead">
                        <strong>Cakayu</strong> adalah supplier di bidang konveksi dan percetakan yang menyediakan seragam, pakaian, dan berbagai macam merchandise. Kami berkomitmen untuk memberikan produk yang dapat disesuaikan dengan desain, material, atau spesifikasi lainnya sesuai kebutuhan badan usaha atau instansi untuk mendukung performa profesionalitas.
                    </p>
                    <h2 class="mt-8 text-2xl font-bold text-gray-900 dark:text-white">Mengapa Memilih Kami</h2>
                    <ul class="list-disc pl-5 mt-4 text-gray-700 dark:text-gray-300">
                        <li><strong>Kualitas Tertinggi:</strong> Kami menggunakan material dengan kualitas terbaik pada setiap produk.</li>
                        <li><strong>Konsultasi Desain:</strong> Menyediakan desain grafis khusus sehingga hasil produk sesuai dengan keinginan.</li>
                        <li><strong>Kompetitif:</strong> Harga yang terjangkau.</li>
                        <li><strong>Tanpa Minimal Order:</strong> Jumlah dapat disesuaikan dengan keinginan.</li>
                    </ul>
                    <h2 class="mt-8 text-2xl font-bold text-gray-900 dark:text-white">Visi</h2>
                    <p class="mt-4">
                        Menjadi supplier yang senantiasa berkembang dan mampu bersaing dalam industri global, serta memberikan hasil terbaik dan kepuasan untuk setiap pelanggan.
                    </p>
                    <h2 class="mt-8 text-2xl font-bold text-gray-900 dark:text-white">Misi</h2>
                    <ul class="list-disc pl-5 mt-4 text-gray-700 dark:text-gray-300">
                        <li>Memberikan pelayanan terbaik (Service Excellence).</li>
                        <li>Memastikan setiap produk berkualitas.</li>
                        <li>Berkomitmen terhadap hasil yang sesuai dengan keinginan pelanggan.</li>
                    </ul>
                    <h2 class="mt-8 text-2xl font-bold text-gray-900 dark:text-white">Kontak Kami</h2>
                    <p class="mt-4">
                        Jika Anda memiliki pertanyaan atau ingin berkonsultasi, jangan ragu untuk menghubungi kami:
                    </p>
                    <p class="mt-2">
                        <strong>Alamat:</strong> Kp. Lubang Buaya RT 003/001, Kec Setu, Kab. Bekasi, 17322<br>
                        <strong>Email:</strong> cakayu25@gmail.com<br>
                        <strong>Telepon:</strong> 0838 1126 6373
                    </p>
                </article>
            </div>
        </main>
    </App>
</template>
