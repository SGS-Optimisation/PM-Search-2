<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {defineComponent, watch, ref, reactive, onMounted, onBeforeMount} from "vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed} from "@vue/reactivity";
import AutoComplete from "primevue/autocomplete";
import Slider from 'primevue/slider';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import DataView from "primevue/dataview";
import DataViewLayoutOptions from "primevue/dataviewlayoutoptions";
import InputSwitch from "primevue/inputswitch";
import SelectButton from 'primevue/selectbutton';
import {router} from '@inertiajs/vue3'
import route from "ziggy-js";
import ReportItem from "@/Components/Search/ReportItem.vue";
import FullModal from "@/Components/Utility/FullModal.vue";
import ViewSearchEntry from "@/Components/Search/ViewSearchEntry.vue";
import {userPreferencesStore} from "@/stores/userPreferencesStore";

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


const userPreferences = userPreferencesStore();

const gridSize = ref(3);
const imageSize = ref('sml');
const backgroundMode = ref('cover');
const perPage = ref(40);

const isOpen = ref(false);
const currentEntry = ref();


onMounted(() => {
    items.value = props.report;
});

function completePerPage(e) {
    perPage.value = parseInt(e.query);
}

function openEntryModal(item) {
    console.log('click event', item);
    isOpen.value = true;
    currentEntry.value = item;
}

function closeEntryModal() {
    isOpen.value = false;
    setTimeout(() => {
        currentEntry.value = null;
    }, 300);
}

const layout = ref('grid');

</script>

<template>

    <Head title="Search Result"/>

    <div class="card">
        <DataView :value="items" :layout="layout" paginator :rows="userPreferences.perPage">
            <template #header>
                <div class="flex justify-between">
                    <div class="flex pt-2">

                        <div class="mx-2">
                            <span class="p-float-label">
                                <AutoComplete v-model="userPreferences.perPage"
                                              :dropdown="true" :suggestions="[20, 40, 100, 1000]"
                                              @complete="completePerPage"
                                              :inputStyle="{width: '5em'}"
                                />
                                <label for="ac">Per Page</label>
                            </span>
                        </div>
                        <div class="mx-2">
                            <label>Grid Size</label>
                            <Slider class="w-14rem" v-model="userPreferences.gridSize" :step="1" :min="1" :max="4"/>
                        </div>
                        <div class="mx-2 flex flex-col">
                            <SelectButton v-model="userPreferences.backgroundMode"
                                          :options="[{value: 'cover', 'label': 'Zoom'}, {value: 'contain', 'label': 'Fit'}]"
                                          optionLabel="label" option-value="value" aria-labelledby="basic" />

                        </div>
                        <div class="mx-2 flex flex-col">
                            <SelectButton v-model="userPreferences.imageSize"
                                          :options="[{value: 'sml', 'label': 'Optimized'}, {value: 'lrg', 'label': 'Large'}]"
                                          optionLabel="label" option-value="value" aria-labelledby="basic" />
<!--                            <InputSwitch v-model="imageSize"  true-value="sml" false-value="lrg"/>-->
                        </div>
                    </div>

                    <DataViewLayoutOptions v-model="layout"/>
                </div>
            </template>
            <template #grid="slotProps">
                <ReportItem
                    :item="slotProps.data"
                    :grid-size="userPreferences.gridSize"
                    :image-size="userPreferences.imageSize"
                    :background-mode="userPreferences.backgroundMode"
                    @on-click-view="openEntryModal"
                />
            </template>

        </DataView>
    </div>

    <FullModal :visible.sync="isOpen" position="full" @keydown.esc="closeEntryModal">
        <template #header>
            <template v-if="currentEntry">
                <p class="font-bold mr-3">
                    Viewing
                    <a class="text-blue-500 hover:text-blue-700"
                       target="_blank"
                       :href="'https://pm.mysgs.sgsco.com/Job/' + currentEntry.formatted_job_number"
                    >
                        {{ currentEntry.formatted_job_number }}
                    </a>
                </p>
            </template>
        </template>
        <ViewSearchEntry :entry="currentEntry" :config="fields_config"
                         @exit="closeEntryModal"
        />
    </FullModal>


</template>
