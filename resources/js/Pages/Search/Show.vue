<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {defineComponent, watch, ref, reactive, onMounted} from "vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed} from "@vue/reactivity";
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import DataView from "primevue/dataview";
import DataViewLayoutOptions from "primevue/dataviewlayoutoptions";
import {router} from '@inertiajs/vue3'
import route from "ziggy-js";
import ReportItem from "@/Components/Search/ReportItem.vue";

defineOptions({layout: AppLayout})

const props = defineProps({
    search_id: {
        type: Number,
        required: true
    },
    parent_search_id: {
        type: Number,
        required: false,
    },
    mode: {
        type: String,
        required: true
    },
    search_data: {
        type: Object,
        required: false
    },
    thumb: {
        type: String,
        required: false
    },
    filename: {
        type: String,
        required: false
    },
    report: {
        type: Array,
        required: false
    },
    image_path: {
        type: String,
        required: false
    },
    working_data: {
        type: Object,
        required: false
    },
    fields: {
        type: Object,
        required: false
    },
    fields_config: {
        type: Object,
        required: false
    },
    meta: {
        type: Object,
        required: false
    },

})

const items = ref();

onMounted(() => {
    //ProductService.getProducts().then((data) => (products.value = data.slice(0, 12)));
    items.value = props.report;
});

function openEntryModal(e: Event) {
    console.log('click event', e);
}

const layout = ref('grid');

</script>

<template>

    <Head title="Search Result"/>

    <div class="card">
        <DataView :value="items" :layout="layout" paginator :rows="40">

            <template #grid="slotProps">
                <ReportItem :item="slotProps.data"></ReportItem>
            </template>

        </DataView>
    </div>


</template>
