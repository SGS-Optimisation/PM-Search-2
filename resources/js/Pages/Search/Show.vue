<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {defineComponent, watch, ref, reactive, onMounted, onBeforeMount, provide} from "vue";
import AutoComplete from "primevue/autocomplete";
import Slider from 'primevue/slider';
import DataView from "primevue/dataview";
import DataViewLayoutOptions from "primevue/dataviewlayoutoptions";
import Dropdown from "primevue/dropdown";
import SelectButton from 'primevue/selectbutton';
import Sidebar from "primevue/sidebar";
import PreviewCard from "@/Components/Results/PreviewCard.vue";
import PreviewRow from "@/Components/Results/PreviewRow.vue";
import FullModal from "@/Components/Utility/FullModal.vue";
import ViewSearchEntry from "@/Components/Results/ViewSearchEntry.vue";
import {userPreferencesStore} from "@/stores/userPreferencesStore";
import moment from "moment/moment";
import _ from "lodash";
import ResultsSidebar from "@/Components/Results/ResultsSidebar.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import QuickViewSearchEntry from "@/Components/Results/QuickViewSearchEntry.vue";
import TextSearchComponent from "@/Components/Search/TextSearchComponent.vue";

const props = defineProps({
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

const items = ref();
const userPreferences = userPreferencesStore();
const layout = ref('grid');
const perPage = ref(40);
const sortOptions = ref([{label: 'Score', value: 'score'}, {label: 'Booked Date', value: 'booked_date_fmt'}]);
const isOpen = ref(false);
const currentEntry = ref();
const quickViewEntry = ref();
const filters = ref<Object>({});
const filteredSearchData = ref<Object[]>([]);

const filterText = ref<String>('');
const visibleQuickDetails = ref(false);

const backgroundModes = ref([
    {value: 'cover', 'label': 'Cover'},
    {value: 'contain', 'label': 'Fit'},
    {value: 'zoom', 'label': 'Zoom'},
]);

provide('filters', {filters, filteredSearchData, filterText});
provide('fields', {fields: props.fields, fields_config: props.fields_config});
provide('report', props.report);

watch(
    () => [filters, filterText],
    (newValue, oldValue) => {
        filteredSearchData.value = getSearchData();
    },
    {deep: true}
)

const titleCase = (str) => window.titleCase(str);

onMounted(() => {
    items.value = props.report;

    items.value.map((item) => {
        item.booked_date_fmt = moment(item.booked_date).format('YYYY-MM-DD');
    })

    userPreferences.selectedTaxonomy.forEach((item) => {
        filters.value[item] = [];
    })

    filteredSearchData.value = getSearchData();
});

function completePerPage(e) {
    perPage.value = parseInt(e.query);
}

function openEntryModal(item) {
    visibleQuickDetails.value = false;
    isOpen.value = true;
    currentEntry.value = item;
}

function openQuickView(item) {
    visibleQuickDetails.value = true;
    quickViewEntry.value = item;
}

function closeEntryModal() {
    isOpen.value = false;
    setTimeout(() => {
        currentEntry.value = null;
    }, 300);
}

function nextEntry() {
    let index = filteredSearchData.value.indexOf(currentEntry.value);
    if (index < filteredSearchData.value.length - 1) {
        currentEntry.value = filteredSearchData.value[index + 1];
    }
}

function prevEntry() {
    let index = filteredSearchData.value.indexOf(currentEntry.value);
    if (index > 0) {
        currentEntry.value = filteredSearchData.value[index - 1];
    }
}


function getSearchData() {

    let data = _.filter(props.report, (entry: Object) => {
        if (!filterText.value || filterText.value === '') {
            return true;
        }
        return Object.values(entry).some((val) => val.toString().toLowerCase().indexOf(filterText.value.toLowerCase()) !== -1);
    })

    for (const field in filters.value) {
        let terms = filters.value[field];
        if (terms.length > 0) {
            data = data.filter((entry: Object) => {

                return entry.hasOwnProperty(field)
                    && terms.includes(entry[field])
            });
        }
    }

    return data;
}


</script>

<template>
    <Head title="Search results"/>

    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto pt-3 pb-1 px-4 overflow-hidden sm:rounded-md">
            <TextSearchComponent :initial-values="search_data" :compact-mode="true"/>
        </div>
    </div>

    <div class="md:grid md:grid-cols-4 lg:grid-cols-5">

        <ResultsSidebar/>

        <div class="results-content md:col-span-3 lg:col-span-4 mt-2">
            <DataView :value="filteredSearchData" :layout="userPreferences.layout" paginator paginatorPosition="bottom"
                      :rows="userPreferences.perPage"
                      :sortOrder="userPreferences.sortOrder" :sortField="userPreferences.sortField">
                <template #header>

                    <div class="grid-options flex pt-2 justify-between ">
                        <div class="flex pt-2">

                            <div class="mx-2">
                            <span class="p-float-label">
                                <AutoComplete v-model="userPreferences.perPage"
                                              dropdown :suggestions="[20, 40, 100, 1000]"
                                              @complete="completePerPage"
                                              :inputStyle="{width: '5em'}"
                                />
                                <label for="ac">Per Page</label>
                            </span>
                            </div>
                            <template v-if="userPreferences.layout === 'grid'">
                                <div class="mx-2">
                                    <label>Grid Size</label>
                                    <Slider class="w-14rem" v-model="userPreferences.gridSize" :step="1" :min="1"
                                            :max="5"/>
                                </div>
                                <div class="mx-2 flex">
                                    <SelectButton v-model="userPreferences.backgroundMode" id="imageMode"
                                                  :options="backgroundModes"
                                                  optionLabel="label" option-value="value" aria-labelledby="basic"/>

                                </div>
                                <div class="mx-2 flex flex-col">
                                    <SelectButton v-model="userPreferences.imageSize"
                                                  :options="[{value: 'sml', 'label': 'Optimized'}, {value: 'lrg', 'label': 'Large'}]"
                                                  optionLabel="label" option-value="value" aria-labelledby="basic"/>
                                    <!--                            <InputSwitch v-model="imageSize"  true-value="sml" false-value="lrg"/>-->
                                </div>
                            </template>
                        </div>

                        <div class="flex  pt-2">
                            <div class="flex mx-4">
                                <div class="flex flex-row">

                                <span class="p-float-label">
                                    <Dropdown v-model="userPreferences.sortField"
                                              panelClass="text-xs"
                                              :options="sortOptions"
                                              option-label="label"
                                              option-value="value"
                                              placeholder="Sort by…"/>
                                    <label for="sort">Sorting</label>
                                </span>

                                    <div class="flex flex-col ml-2">
                                        <a class="cursor-pointer" @click="userPreferences.sortOrder = 1"
                                           :class="{'text-blue-800': userPreferences.sortOrder === 1,
                                                    'text-gray-200': userPreferences.sortOrder === -1,
                                                    }"
                                        >
                                            <i class="pi pi-chevron-up"></i>

                                        </a>
                                        <a class="cursor-pointer" @click="userPreferences.sortOrder = -1"
                                           :class="{'text-blue-800': userPreferences.sortOrder === -1,
                                           'text-gray-200': userPreferences.sortOrder === 1
                                        }">
                                            <i class="pi pi-chevron-down"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="justify-items-end">
                                <DataViewLayoutOptions v-model="userPreferences.layout"/>
                            </div>
                        </div>
                    </div>


                </template>

                <template #grid="slotProps">
                    <PreviewCard
                        :item="slotProps.data"
                        :grid-size="userPreferences.gridSize"
                        :extra-class="visibleQuickDetails && quickViewEntry.formatted_job_number !== slotProps.data.formatted_job_number ? 'blur-sm': ''"
                        :image-size="userPreferences.imageSize"
                        :background-mode="userPreferences.backgroundMode"
                        @on-click-view="openEntryModal"
                        @on-click-quick-view="openQuickView"
                    />
                </template>
                <template #list="slotProps">
                    <PreviewRow
                        :item="slotProps.data"
                        :config="fields_config"
                        :grid-size="userPreferences.gridSize"
                        :extra-class="visibleQuickDetails && quickViewEntry.formatted_job_number !== slotProps.data.formatted_job_number ? 'blur-sm': ''"
                        :image-size="userPreferences.imageSize"
                        :background-mode="userPreferences.backgroundMode"
                        @on-click-view="openEntryModal"
                        @on-click-quick-view="openQuickView"
                    />
                </template>

            </DataView>
        </div>

    </div>

    <Sidebar v-model:visible="visibleQuickDetails" position="left" class="w-full md:w-20rem lg:w-30rem">
        <QuickViewSearchEntry :entry="quickViewEntry" :config="fields_config" @request-full-view="openEntryModal"/>

    </Sidebar>
    <FullModal v-model:visible="isOpen" position="full" id="report-modal">
        <template #header>
            <template v-if="currentEntry">
                <div class="flex flex-row items-center">
                    <span class="font-bold mr-3">
                        Viewing
                        <a class="text-blue-500 hover:text-blue-700"
                           target="_blank"
                           title="Open in MySGS"
                           :href="'https://pm.mysgs.sgsco.com/Job/' + currentEntry.formatted_job_number"
                        >
                            {{ currentEntry.formatted_job_number }} <i class="pi pi-external-link"></i>
                        </a>
                    </span>

                    <a class="p-button p-button-info p-button-outlined p-button-sm mx-2" href="#job-image">Image</a>
                    <a class="p-button p-button-info p-button-outlined p-button-sm mx-2" href="#job-meta">Metada</a>
                </div>
            </template>
        </template>
        <ViewSearchEntry :entry="currentEntry" :config="fields_config"
                         @exit="closeEntryModal"
                         @next="nextEntry"
                         @prev="prevEntry"
        />
    </FullModal>


</template>

<style>

.grid-options {
    @apply text-xs;
}

.p-float-label .p-inputwrapper-filled ~ label {
    top: -0.3rem;
}

.p-dataview .p-dataview-header {
    padding: 0.5rem 1rem;
}

.results-sidebar, .p-dataview .p-dataview-header, .p-paginator {
    @apply bg-indigo-50;
}

.p-dataview .p-dataview-content {
    @apply bg-neutral-200 p-2;
}

#taxonomySelector {
}
</style>
