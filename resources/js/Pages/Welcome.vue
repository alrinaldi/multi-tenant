<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    laravelVersion: String,
    phpVersion: String
});
</script>

<template>
    <div class="container">
        <h1>Laravel {{ laravelVersion }} + Inertia 2.0 🚀</h1>
        <p>PHP Version: {{ phpVersion }}</p>

        <div style="margin-top: 20px;">
            <Link href="/test" prefetch cache-for="10s" class="btn">
                Tes Halaman Kedua (Prefetch Mode)
            </Link>
        </div>
    </div>
</template>

<style scoped>
.container { text-align: center; margin-top: 100px; font-family: sans-serif; }
.btn { background: #000; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
.btn:hover { background: #333; }
</style>