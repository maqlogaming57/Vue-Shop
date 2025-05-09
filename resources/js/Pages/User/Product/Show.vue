<script setup>
import {Head, Link, router, usePage} from "@inertiajs/vue3";
import App from "@/Layouts/App.vue";
import {ElNotification} from "element-plus";
import {Splide, SplideSlide} from "@splidejs/vue-splide";
import { ref, watch } from 'vue';

defineProps({
    product: Object,
});

const auth = usePage().props.auth;
const selectedColor = ref('white');
const selectedSize = ref('M');

// Tambahkan watch untuk debug
watch(selectedColor, (newColor) => {
    console.log("Selected color changed to:", newColor);
});

watch(selectedSize, (newSize) => {
    console.log("Selected size changed to:", newSize);
});

const colors = [
    { name: 'White', class: 'bg-white', selectedClass: 'ring-gray-400', value: 'white' },
    { name: 'Black', class: 'bg-black', selectedClass: 'ring-gray-900', value: 'black' },
    { name: 'Red', class: 'bg-red-500', selectedClass: 'ring-red-500', value: 'red' },
    { name: 'Blue', class: 'bg-blue-500', selectedClass: 'ring-blue-500', value: 'blue' },
    { name: 'Green', class: 'bg-green-500', selectedClass: 'ring-green-500', value: 'green' },
    { name: 'Yellow', class: 'bg-yellow-500', selectedClass: 'ring-yellow-500', value: 'yellow' },
];

const sizes = [
    { name: 'S', value: 'S' },
    { name: 'M', value: 'M' },
    { name: 'L', value: 'L' },
    { name: 'XL', value: 'XL' },
    { name: 'XXL', value: 'XXL' },
    { name: '3L', value: '3L' },
    { name: '4L', value: '4L' },
    { name: '5L', value: '5L' },
];

const addToCart = (product) => {
    // Log data sebelum mengirim untuk debugging
    console.log("Adding to cart:", {
        product_id: product.id,
        color: selectedColor.value,
        size: selectedSize.value
    });
    
    router.post(route('cart.store', product), {
        color: selectedColor.value,
        size: selectedSize.value,
    }, {
        preserveScroll: true, // Tambahkan ini untuk mencegah scroll ke atas setelah proses
        onBefore: () => {
            // Validasi sebelum mengirim
            if (!selectedColor.value || !selectedSize.value) {
                ElNotification({
                    title: 'Warning',
                    message: 'Please select color and size',
                    type: 'warning',
                });
                return false;
            }
        },
        onSuccess: (page) => {
            if (page.props.flash.success) {
                ElNotification({
                    title: 'Success',
                    message: page.props.flash.success,
                    type: 'success',
                });
            }
        },
        onError: (errors) => {
            console.error("Errors:", errors);
            ElNotification({
                title: 'Error',
                message: 'Failed to add product to cart',
                type: 'error',
            });
        }
    });
};

const sampleImage = [
    {
        id: 1,
        name: 'Sample 1',
        href: '#',
        imageSrc: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRqEWgS0uxxEYJ0PsOb2OgwyWvC0Gjp8NUdPw&usqp=CAU',
    },{
        id: 2,
        name: 'Sample 2',
        href: '#',
        imageSrc: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRqEWgS0uxxEYJ0PsOb2OgwyWvC0Gjp8NUdPw&usqp=CAU',
    },{
        id: 3,
        name: 'Sample 3',
        href: '#',
        imageSrc: 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRqEWgS0uxxEYJ0PsOb2OgwyWvC0Gjp8NUdPw&usqp=CAU',
    }
];

</script>

<template>
    <App>
        <Head :title="product.title" />
        <div class="mt-4 mb-6">
        <section class="text-gray-600 body-font overflow-y-auto h-full">
            <div class="container px-5 py-24 mx-auto ">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 rounded-lg bg-white dark:bg-gray-800 px-10 py-12">
                    <div class="w-full">
                        <Splide :options="{ type : 'loop', gap: '1rem', autoplay: true}" aria-label="My Favorite Images">
                            <SplideSlide v-if="product.product_images && product.product_images.length" v-for="item in product.product_images" :key="item.id">
                                <div class="h-52 max-h-52 lg:h-[500px] lg:max-h-[500px]">
                                    <img class="w-full h-52 lg:h-[500px] object-cover object-center rounded" :src="`/${item.image}`">
                                </div>
                            </SplideSlide>
                            <SplideSlide v-else v-for="(simg, key) in sampleImage" :key="key">
                                <img class="w-full lg:h-auto h-64 object-cover object-center rounded" :src="simg.imageSrc">
                            </SplideSlide>
                        </Splide>
                    </div>
                    <div class="lg:pl-10 lg:py-6 mt-6 lg:mt-0">
                        <h2 class="text-sm title-font text-gray-500 tracking-widest dark:text-gray-100">{{ product.brand ? product.brand.name : 'Brand' }}</h2>
                        <h1 class="text-gray-900 text-3xl title-font font-medium mb-1 dark:text-gray-100">{{ product.title }}</h1>
                        <div class="flex mb-4">
                              <span class="flex items-center">
                                <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-green-500" viewBox="0 0 24 24">
                                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                                <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-green-500" viewBox="0 0 24 24">
                                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                                <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-green-500" viewBox="0 0 24 24">
                                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                                <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-green-500" viewBox="0 0 24 24">
                                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                                <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-4 h-4 text-green-500" viewBox="0 0 24 24">
                                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                </svg>
                                <span class="text-gray-600 ml-3 dark:text-gray-100">4 Reviews</span>
                              </span>
                              <span class="flex ml-3 pl-3 py-2 border-l-2 border-gray-200 space-x-2s">
                                <a class="text-gray-500">
                                  <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                    <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                                  </svg>
                                </a>
                                <a class="text-gray-500">
                                  <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                    <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                                  </svg>
                                </a>
                                <a class="text-gray-500">
                                  <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                    <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                                  </svg>
                                </a>
                            </span>
                        </div>
                        <p class="leading-relaxed dark:text-gray-100">{{ product.description }}</p>
                        <div class="flex mt-6 items-center pb-5 border-b-2 border-gray-100 mb-5">
                            <div class="flex flex-col gap-4">
                                <!-- Product stock -->
                                <div class="flex items-center">
                                    <p class="text-gray-900 dark:text-gray-100 mr-6">Stock : {{product.available_stock}}</p>
                                </div>
                                
                                <!-- Color selector -->
                                <div class="flex items-center space-x-3">
                                    <span class="text-gray-900 dark:text-gray-100">Color :</span>
                                    <div class="flex items-center space-x-2">
                                        <template v-for="color in colors" :key="color.name">
                                            <label
                                                :class="[
                                                    'relative -m-0.5 flex cursor-pointer items-center justify-center rounded-full p-0.5 focus:outline-none',
                                                    selectedColor === color.value ? 'ring ring-offset-1' : '',
                                                    color.selectedClass
                                                ]"
                                            >
                                                <input 
                                                    type="radio" 
                                                    name="color-choice" 
                                                    :value="color.value" 
                                                    v-model="selectedColor"
                                                    class="sr-only"
                                                    @change="() => console.log('Color selected:', color.value)"
                                                >
                                                <span
                                                    :class="[
                                                        'h-8 w-8 rounded-full border border-black border-opacity-10',
                                                        color.class
                                                    ]"
                                                    :aria-label="color.name"
                                                />
                                            </label>
                                        </template>
                                    </div>
                                </div>

                                <!-- Size selector -->
                                <div class="flex items-center space-x-3">
                                    <span class="text-gray-900 dark:text-gray-100">Size :</span>
                                    <div class="flex items-center space-x-2 flex-wrap gap-y-2">
                                        <template v-for="size in sizes" :key="size.value">
                                            <label
                                                :class="[
                                                    'relative flex cursor-pointer items-center justify-center rounded-md border py-2 px-4 text-sm font-medium uppercase',
                                                    selectedSize === size.value 
                                                        ? 'border-gray-600 bg-gray-100 dark:bg-gray-700' 
                                                        : 'border-gray-200 dark:border-gray-600',
                                                    'cursor-pointer bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100'
                                                ]"
                                            >
                                                <input 
                                                    type="radio" 
                                                    name="size-choice" 
                                                    :value="size.value" 
                                                    v-model="selectedSize"
                                                    class="sr-only"
                                                    @change="() => console.log('Size selected:', size.value)"
                                                >
                                                {{ size.name }}
                                            </label>
                                        </template>
                                    </div>
                                </div>
                                
                                <!-- Display selected options for debugging -->
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Selected options: {{ selectedColor }} / {{ selectedSize }}
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-between">
                            <div class="mt-1">
                                <span class="title-font font-medium text-2xl text-gray-900 dark:text-gray-100">Rp. {{ Number(product.price).toLocaleString() }}</span>
                            </div>
                            <div class="flex">
                                <div class="flex bg-blue-600 px-3 rounded-full items-center transition duration-200 hover:scale-110 hover:bg-blue-800">
                                    <a v-if="auth.user" @click="addToCart(product)" class="cursor-pointer inline-flex py-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                        </svg>

                                        <span class="text-white ml-2">Add to cart</span>
                                    </a>
                                    <Link v-else :href="route('login')" class="cursor-pointer inline-flex py-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                        </svg>
                                        <span class="text-white ml-2">Add to cart</span>
                                    </Link>
                                </div>
                                <button class="rounded-full w-10 h-10 bg-gray-200 p-0 border-0 inline-flex items-center justify-center text-gray-500 ml-4">
                                    <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                                        <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        </div>
    </App>
</template>