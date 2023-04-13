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

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-4 gap-4">
                <div class="col-span-3">
                    <TextSearchComponent/>

                    <div class="mt-8">
                        <ImageSearchComponent/>
                    </div>
                </div>
                <LatestSearch :latest-searches="latestSearches"/>
            </div>

        </div>
    </div>
</template>
