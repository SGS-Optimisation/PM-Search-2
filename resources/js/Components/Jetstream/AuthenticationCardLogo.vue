<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'">
        <svg class="mx-auto w-16 h-16" viewBox="0 0 77 73" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M259,457.89v-73h77v73Zm65.83-23.09V391.14H281.17v16.75h26.05a.86.86,0,0,1,.43.15c.14.09.25.22.52.45v26.3Zm-28.92-14.75H264c0,10.46,0,20.76,0,31.05a.89.89,0,0,0,.15.44c.08.13.22.24.17.19h31.58Z"
                  transform="translate(-259 -384.89)" fill="#fefdfd"/>
            <path d="M324.83,434.8H308.17V408.5c-.27-.23-.38-.36-.52-.45a.86.86,0,0,0-.43-.15H281.17V391.14h43.66Z"
                  transform="translate(-259 -384.89)" fill="#e60e07"/>
            <path d="M295.91,420.05v31.68H264.33c.05,0-.09-.06-.17-.19a.89.89,0,0,1-.15-.44c0-10.29,0-20.59,0-31.05Z"
                  transform="translate(-259 -384.89)" fill="#302e29"/>
        </svg>
        <p class="text-red-700 text-xl text-center cursor-pointer">
            AI Search v2
        </p>
    </Link>
</template>
