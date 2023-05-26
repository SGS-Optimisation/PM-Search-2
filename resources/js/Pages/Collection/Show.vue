<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Results from "@/Components/Results/Results.vue";

const props = defineProps({
    collection_name: {type: String, required: true},
    search_id: {type: Number, required: true},
    parent_search_id: {type: Number, required: false,},
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
})

defineOptions({layout: AppLayout})


</script>

<template>
    <Head :title="'Collection: ' + collection_name"/>

    <div class="px-6 text-center bg-green-600 text-white">
        Collection: {{ collection_name }}
    </div>

    <Results :search_id="search_id"
             :parent_search_id="parent_search_id"
             :mode="mode"
             :search_data="search_data"
             :thumb="thumb"
             :filename="filename"
             :report="report"
             :image_path="image_path"
             :working_data="working_data"
             :fields="fields"
             :fields_config="fields_config"
             :meta="meta"
             :collection-mode="true"
    />


</template>

<style lang="scss" scoped>

</style>
