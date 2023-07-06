<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {defineComponent, watch, ref, reactive, provide, onMounted} from "vue";
import route from "ziggy-js";
import AppLayout from "@/Layouts/AppLayout.vue";
import TextSearchComponent from "@/Components/Search/TextSearchComponent.vue";
import LatestSearch from "@/Components/Search/LatestSearch.vue";
import {trackPage} from "@/Components/Utility/track";

defineOptions({layout: AppLayout})
defineProps({
    searches: Array,
});
provide('filters', {});

onMounted(() => {
    trackPage('Text Search');
});

</script>

<template>

    <div>
        <Head title="Text Search"/>
        <div class="max-w-7xl py-6 mx-auto ">
            <div class="w-full sm:px-6 lg:px-4">
                <TextSearchComponent/>
            </div>
            <div class="mw-full sm:px-6 lg:px-4 mt-5">
                <LatestSearch :latest-searches="searches"
                              :per-page="10"
                              title="My Latest Text Searches"/>
            </div>

        </div>
    </div>
</template>

<style scoped>

</style>
