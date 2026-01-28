<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'" class="flex flex-col items-center">
        <div class="flex items-center justify-center bg-blue-600 rounded-full shadow-2xl transition-all hover:scale-110" 
             style="width: 100px; height: 100px;"> <i class="fas fa-pills text-white" 
               style="font-size: 60px;"></i> </div>

        <span class="mt-4 text-3xl font-black text-gray-800 uppercase tracking-widest">
            Pharma<span class="text-blue-600">Sys</span>
        </span>
    </Link>
</template>
