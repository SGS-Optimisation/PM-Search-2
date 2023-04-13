<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Jetstream/Welcome.vue';
import LatestSearch from "@/Components/Search/LatestSearch.vue";
import TextSearchComponent from "@/Components/Search/TextSearchComponent.vue";
import ImageSearchComponent from "@/Components/Search/ImageSearchComponent.vue";

defineOptions({layout: AppLayout})

defineProps({
    latestSearches: Array,
    soz: String,
});


</script>

<template>

    <Head title="Dashboard"/>

    <div class="py-6 mx-auto ">
        <div class="w-full sm:px-6 lg:px-8">
            <div class="grid">
                <div class="col-8">
                    <TextSearchComponent/>

                    <div class="mt-8">
                        <ImageSearchComponent/>
                    </div>
                </div>
                <div class="col-4">
                    <LatestSearch :latest-searches="latestSearches"/>
                </div>
            </div>

        </div>
    </div>
</template>
