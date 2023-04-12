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
import Dropdown from "primevue/dropdown";
import InputSwitch from "primevue/inputswitch";
import MultiSelect from "primevue/multiselect";
import SelectButton from 'primevue/selectbutton';
import {router} from '@inertiajs/vue3'
import route from "ziggy-js";
import ReportItem from "@/Components/Search/ReportItem.vue";
import FullModal from "@/Components/Utility/FullModal.vue";
import ViewSearchEntry from "@/Components/Search/ViewSearchEntry.vue";
import {userPreferencesStore} from "@/stores/userPreferencesStore";
import moment from "moment/moment";
import TaxonomySelector from "@/Components/Search/TaxonomySelector.vue";
import _ from "lodash";
import 'primeflex/primeflex.css';

defineOptions({layout: AppLayout})

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

const availableFields = computed(() => {
    return Object.keys(props.fields).map((key) => {
        return {value: key, label: titleCase(key)}
    });
})

const items = ref();
const userPreferences = userPreferencesStore();
const layout = ref('grid');
const perPage = ref(40);
const sortOptions = ref([{label: 'Score', value: 'score'}, {label: 'Booked Date', value: 'booked_date_fmt'}]);
const isOpen = ref(false);
const currentEntry = ref();
const filters = ref<Object>({});

watch(
    () => filters,
    (newValue, oldValue) => {
        filteredSearchData.value = getSearchData();
        filteredSearchOptions.value = getFilterOptions();
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
    allSearchOptions.value = getFilterOptions();
    filteredSearchOptions.value = getFilterOptions();
});

function updateSelectedTaxonomy(taxonomy) {
    filters.value[taxonomy] = [];
}

function clearSelectedFields() {
    clearFilters()
    userPreferences.selectedTaxonomy = [];
}

function clearFilters() {
    for (const field in filters.value) {
        filters.value[field] = [];
    }
}

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

const filteredSearchData = ref<Object[]>([]);
const allSearchOptions = ref<Object>({});
const filteredSearchOptions = ref<Object>({});
const filterText = ref<String>('');

function getSearchData() {

    let data = _.filter(props.report, (entry: Object) => {
        if (!filterText.value || filterText.value === '') {
            return true;
        }
        return Object.values(entry).some((val) => val.toLowerCase().indexOf(filterText.value.toLowerCase()) !== -1);
    })

    for (const field in filters.value) {
        let terms = filters.value[field];
        if (terms.length > 0) {
            console.log('filtering', field, terms);
            data = data.filter((entry: Object) => {
                if (terms.includes(entry[field])) {
                    console.log('FOUND');
                }

                return entry.hasOwnProperty(field)
                    && terms.includes(entry[field])
            });
        }
    }

    return data;
}


function getFilterOptions() {
    console.log('filtering options');
    var options = {};

    for (const i in filteredSearchData.value) {
        let entry = filteredSearchData.value[i];
        for (const j in Object.keys(entry)) {
            let field = Object.keys(entry)[j];

            if (!Object.keys(props.fields).includes(field)) {
                continue;
            }

            if (!options.hasOwnProperty(field)) {
                options[field] = [];
            }

            let values = entry[field];
            if (!Array.isArray(values)) {
                values = [values];
            }

            for (var value of values) {
                if (!options[field].includes(value)) {
                    options[field].push(value);
                }
            }
        }
    }

    return options;
}

const totalResults = computed(() => {
    return props.report.length;
})


const filteredResults = computed(() => {
    return filteredSearchData.value.length;
})

</script>

<template>

    <Head title="Search Result"/>

    <div class="card">
        <DataView :value="filteredSearchData" :layout="layout" paginator :rows="userPreferences.perPage"
                  :sortOrder="userPreferences.sortOrder" :sortField="userPreferences.sortField">
            <template #header>
                <div class="flex flex-col mb-12">
                    <div class="flex flex-row mb-5 mt-4">
                        <span class="p-float-label grow">
                            <MultiSelect v-model="userPreferences.selectedTaxonomy" id="taxonomySelector"
                                         filter :resetFilterOnHide="true"
                                         @update:modelValue="updateSelectedTaxonomy"
                                         :options="availableFields"
                                         option-value="value" option-label="label"
                                         display="chip"
                                         placeholder="Searchable Fields"
                                         class="w-full"/>
                            <label for="taxonomySelector">Select Search Fields</label>
                        </span>
                        <template v-if="userPreferences.selectedTaxonomy.length">
                            <Button title="Clear fields" class="" icon="pi pi-times"
                                    @click="clearSelectedFields"/>
                        </template>
                    </div>
                    <div class="grid">
                        <div class="col-2" v-for="field in userPreferences.selectedTaxonomy" :key="field">

                            <TaxonomySelector :taxonomy-name="field"
                                              :filtered-terms="filteredSearchOptions[field]"
                                              :all-terms="allSearchOptions[field]"
                                              v-model="filters[field]"
                            />
                        </div>

                        <template v-if="userPreferences.selectedTaxonomy.length">
                            <Button class="" size="small" label="Clear Filters" icon="pi pi-times" @click="clearFilters"/>
                        </template>
                    </div>
                </div>

                <div class="flex mb-3 justify-between">
                    <div class="flex pt-2">

                        <div class="mx-2">
                            <span class="p-float-label">
                                <AutoComplete v-model="userPreferences.perPage"
                                              type="number"
                                              dropdown :suggestions="[20, 40, 100, 1000]"
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
                        <div class="mx-2 flex">
                            <SelectButton v-model="userPreferences.backgroundMode" id="imageMode"
                                          :options="[{value: 'cover', 'label': 'Cover'}, {value: 'contain', 'label': 'Fit'}]"
                                          optionLabel="label" option-value="value" aria-labelledby="basic"/>

                        </div>
                        <div class="mx-2 flex flex-col">
                            <SelectButton v-model="userPreferences.imageSize"
                                          :options="[{value: 'sml', 'label': 'Optimized'}, {value: 'lrg', 'label': 'Large'}]"
                                          optionLabel="label" option-value="value" aria-labelledby="basic"/>
                            <!--                            <InputSwitch v-model="imageSize"  true-value="sml" false-value="lrg"/>-->
                        </div>
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
                                       :class="{'text-blue-500': userPreferences.sortOrder === 1}">
                                        <i class="pi pi-chevron-up"></i>

                                    </a>
                                    <a class="cursor-pointer" @click="userPreferences.sortOrder = -1"
                                       :class="{'text-blue-500': userPreferences.sortOrder === -1}">
                                        <i class="pi pi-chevron-down"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="justify-items-end">
                            <DataViewLayoutOptions v-model="layout"/>
                        </div>
                    </div>
                </div>

                <div class="flex mb-3">
                    <div class="max-w-7xl mx-auto sm:px-2">
                        <div class="bg-white overflow-hidden shadow sm:rounded-lg px-2">
                            <p>
                                <template v-if="filteredResults !== totalResults">
                                    {{ filteredResults }} {{ filteredResults === 1 ? 'match' : 'matches' }}
                                    filtered from {{ totalResults }}
                                </template>
                                <template v-else>
                                    Found {{ totalResults }} {{ totalResults === 1 ? 'match' : 'matches' }}
                                </template>
                            </p>
                        </div>
                    </div>
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

</style>
