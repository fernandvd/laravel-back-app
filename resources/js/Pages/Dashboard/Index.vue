<template>
    <div>
        <Head title="Dashboard" />
        <h1 class="mb-8 text-3xl font-bold">Dashboard</h1>
        <p class="mb-8 leading-normal">
            Hey there! Welcome to app
        </p>
        <p class="mb-8 leading-normal">
            This is a fork of demo of Inertia.js
        </p>
        <p class="mb-8 leading-normal">
            Hey there! Welcome, a demo app designed to help illustrate how
            <a class="text-indigo-500 hover:text-orange-600 underline" href="https://inertiajs.com" target="_blank">
                Inertia.js
            </a>
            works.
        </p>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import Layout from '@/Shared/Layout.vue';

export default {
    components: {
        Head,
    },
    layout: Layout,
}
</script>
