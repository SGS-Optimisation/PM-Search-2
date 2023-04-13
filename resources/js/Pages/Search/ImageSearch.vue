<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {defineComponent, watch, ref, reactive} from "vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed} from "@vue/reactivity";
import VuePictureCropper, {cropper} from 'vue-picture-cropper'
import FileUpload from 'primevue/fileupload';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import {router} from '@inertiajs/vue3'
import route from "ziggy-js";
import ImageSearchComponent from "@/Components/Search/ImageSearchComponent.vue";

defineOptions({layout: AppLayout})


</script>

<template>

    <div>
        <Head title="Image Search"/>
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Image Search
                </h2>
            </div>
        </header>

        <div class="py-12">
            <ImageSearchComponent/>
        </div>
    </div>

</template>
