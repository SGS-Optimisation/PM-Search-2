<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import AppLayout from '@/Layouts/AppLayout.vue';
import LatestSearch from "@/Components/Search/LatestSearch.vue";
import TextSearchComponent from "@/Components/Search/TextSearchComponent.vue";
import ImageSearchComponent from "@/Components/Search/ImageSearchComponent.vue";
import CollectionList from "@/Components/Collections/CollectionList.vue";
import {onMounted, provide} from "vue";
import {trackPage} from "@/Components/Utility/track";

defineOptions({layout: AppLayout})

defineProps({
    latestSearches: Array,
    collections: Array,
});

onMounted(() => {
    trackPage('Dashboard');
});

provide('filters', {});

</script>

<template>

    <Head title="Dashboard"/>

    <div class="py-6 mx-auto ">
        <div class="w-full sm:px-6 lg:px-4">
            <div class="grid">
                <div class="col-9">
                    <div class="p6">
                        <TextSearchComponent/>
                    </div>

                    <div class="mt-8">
                        <ImageSearchComponent/>
                    </div>
                </div>
                <div class="col-3">
                    <CollectionList :collections="collections"/>
                    <div class="mt-2">
                        <LatestSearch :latest-searches="latestSearches"/>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
