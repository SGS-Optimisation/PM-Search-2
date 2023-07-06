<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Results from "@/Components/Results/Results.vue";
import ProgressSpinner from "primevue/progressspinner";
import {onMounted, ref} from "vue";
import axios from "axios";
import route from "ziggy-js";
import {trackPage} from "@/Components/Utility/track";

const props = defineProps({
    collection_id: {type: Number, required: true},
    collection_name: {type: String, required: true},
    mode: {type: String, required: true},
    search_data: {type: Object, required: false},
    thumb: {type: String, required: false},
    filename: {type: String, required: false},
    report: {type: Array, required: false},
    image_path: {type: String, required: false},
    working_data: {type: Object, required: false},
    fields: {type: Object, required: false},
    fields_config: {type: Object, required: false},
    meta: {type: Object, required: false},
    savedFilters: {type: Object, required: false},
})

defineOptions({layout: AppLayout})

const backgroundLoading = ref(false);

onMounted(() => {
    backgroundLoading.value = true;
    checkFreshness();
    trackPage('Collection: ' + props.collection_name);
});

const latestReport = ref(props.report);

function checkFreshness() {
    axios.get(route('api.collections.fresh', {collection: props.collection_id}))
        .then(response => {
            backgroundLoading.value = false;

            if (response.data.already_fresh) {
                console.log('il est frais mon poisson il est frais !');
            } else {
                console.log('poisson pas frais');
                latestReport.value = response.data.report;
            }
        })
}

</script>

<template>
    <Head :title="'Collection: ' + collection_name"/>

    <div class="px-6 text-center bg-green-600 text-white">
        Collection: {{ collection_name }}
        <ProgressSpinner v-if="backgroundLoading"
                         style="width: 12px; height: 12px" strokeWidth="8" />
    </div>

    <Results :mode="mode"
             :search_data="search_data"
             :thumb="thumb"
             :filename="filename"
             :report="latestReport"
             :image_path="image_path"
             :working_data="working_data"
             :fields="fields"
             :fields_config="fields_config"
             :meta="meta"
             :collection-mode="true"
             :saved-filters="savedFilters"
             :collection-id="collection_id"
    />


</template>

<style lang="scss" scoped>

</style>
