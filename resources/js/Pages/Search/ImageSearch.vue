<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import AppLayout from '@/Layouts/AppLayout.vue';
import ImageSearchComponent from "@/Components/Search/ImageSearchComponent.vue";
import LatestSearch from "@/Components/Search/LatestSearch.vue";

defineOptions({layout: AppLayout})
defineProps({
    searches: Array,
});

</script>

<template>

    <div>
        <Head title="Image Search"/>

        <div class="max-w-7xl py-6 mx-auto ">
            <div class="w-full sm:px-6 lg:px-4">
                <ImageSearchComponent/>
            </div>
            <div class="mw-full sm:px-6 lg:px-4 mt-5">
                <LatestSearch :latest-searches="searches"
                              :per-page="10"
                              title="My Latest Image Searches"/>
            </div>
        </div>
    </div>

</template>
