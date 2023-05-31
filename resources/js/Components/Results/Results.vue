<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {defineComponent, watch, ref, reactive, onMounted, onBeforeMount, provide, computed} from "vue";
import AutoComplete from "primevue/autocomplete";
import Button from "primevue/button";
import Slider from 'primevue/slider';
import DataView from "primevue/dataview";
import DataViewLayoutOptions from "primevue/dataviewlayoutoptions";
import Divider from 'primevue/divider';
import Dropdown from "primevue/dropdown";
import Inplace from "primevue/inplace";
import OverlayPanel from 'primevue/overlaypanel';
import PreviewCard from "@/Components/Results/PreviewCard.vue";
import PreviewRow from "@/Components/Results/PreviewRow.vue";
import SelectButton from 'primevue/selectbutton';
import Sidebar from "primevue/sidebar";
import FullModal from "@/Components/Utility/FullModal.vue";
import ViewSearchEntry from "@/Components/Results/ViewSearchEntry.vue";
import {userPreferencesStore} from "@/stores/userPreferencesStore";
import moment from "moment/moment";
import _ from "lodash";
import ResultsSidebar from "@/Components/Results/ResultsSidebar.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import QuickViewSearchEntry from "@/Components/Results/QuickViewSearchEntry.vue";
import TextSearchComponent from "@/Components/Search/TextSearchComponent.vue";
import ImageSearchComponent from "@/Components/Search/ImageSearchComponent.vue";
import {snakeCase} from "lodash";

const props = defineProps({
    collectionMode: {type: Boolean, default: false},
    search_id: {type: Number, required: false},
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

const items = ref();
const userPreferences = userPreferencesStore();
const layout = ref('grid');
const perPage = ref(25);
const sortOptions = ref([{label: 'Score', value: 'score'}, {label: 'Booked Date', value: 'booked_date_fmt'}]);
const isOpen = ref(false);
const comparing = ref(false);
const currentEntry = ref();
const quickViewEntry = ref();
const compareSelection = ref<Object[]>([]);
const filters = ref<Object>({});
const filteredSearchData = ref<Object[]>([]);

const filterText = ref<String>('');
const visibleQuickDetails = ref(false);

const backgroundModes = ref([
    {value: 'cover', 'label': '<u>C</u>over'},
    {value: 'contain', 'label': '<u>F</u>it'},
    {value: 'zoom', 'label': '<u>Z</u>oom'},
]);

provide('filters', {filters, filteredSearchData, filterText});
provide('fields', {fields: props.fields, fields_config: props.fields_config});
provide('report', props.report);

watch(
    () => [filters, filterText, props.report],
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
    userPreferences.perPage = parseInt(e.query);
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


function getSearchData(): Object {

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

const groupedSortedConfig = computed(() => {
    var sortedKeys = Object.keys(props.fields_config).sort((a, b) => {
        return props.fields_config[a].position - props.fields_config[b].position;
    });

    var sortedObj = {};
    for (const key in sortedKeys) {
        sortedObj[sortedKeys[key]] = props.fields_config[sortedKeys[key]];
    }

    var groupedObj = {};
    for (const key in sortedObj) {
        if (sortedObj[key].section) {
            if (!groupedObj[sortedObj[key].section]) {
                groupedObj[sortedObj[key].section] = {};
            }
            groupedObj[sortedObj[key].section][key] = sortedObj[key];
        }
    }

    return groupedObj;
});

function updateComparisonSelection(item) {
    if (compareSelection.value.includes(item)) {
        compareSelection.value = compareSelection.value.filter((entry) => entry !== item);
    } else {
        compareSelection.value.push(item);
    }
}

function openComparisonView() {
    if (!(compareSelection.value.length < 2 || compareSelection.value.length > 4)) {
        comparing.value = true;
    }
}

const previewCards = ref<PreviewCard[]>([]);
const previewRows = ref<PreviewRow[]>([]);

function clearSelection() {
    compareSelection.value = [];
    for (const card of previewCards.value) {
        card.clearSelection();
    }
    for (const card of previewRows.value) {
        card.clearSelection();
    }
}

function closeComparisonView() {
    comparing.value = false;
}

const gridConfigOverlay = ref();
const toggleGridConfig = (event) => {
    gridConfigOverlay.value.toggle(event);
}

const hotkeys = ref([
    {
        keys: ['Alt+f'],
        preventDefault: true,
        handler() {
            userPreferences.backgroundMode = 'contain';
        }
    },
    {
        keys: ['Alt+c'],
        preventDefault: true,
        handler() {
            userPreferences.backgroundMode = 'cover';
        }
    },
    {
        keys: ['Alt+z'],
        preventDefault: true,
        handler() {
            userPreferences.backgroundMode = 'zoom';
        }
    },
    {
        keys: ['Alt+s'],
        preventDefault: true,
        handler() {
            openComparisonView();
        }
    },
    {
        keys: ['Alt+l'],
        preventDefault: true,
        handler() {
            clearSelection();
        }
    }
])

</script>

<template>

    <template v-if="mode === 'text'">
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto pt-3 pb-1 px-4 overflow-hidden sm:rounded-md">

                <TextSearchComponent :initial-values="search_data"
                                     :compact-mode="true"
                                     :allow-save="!collectionMode"
                                     :search-id="search_id"
                />
            </div>
        </div>
    </template>
    <template v-else>
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto pt-3 pb-1 px-4 overflow-hidden sm:rounded-md flex justify-center">
                <ImageSearchComponent :initial-values="{filename, thumb, image_path }" :compact-mode="true"/>
            </div>
        </div>
    </template>

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
                                    <Dropdown v-model="userPreferences.perPage"
                                              :options="[{label: '25', value: 25}, {label: '50', value: 50}, {label: '100', value: 100}]"
                                              option-label="label"
                                              option-value="value"
                                              class="md:w-7rem"
                                    />
                                    <label for="ac">Per Page</label>
                                </span>
                            </div>
                            <div class="flex" v-if="userPreferences.layout === 'grid'">
                                <div class="mx-2">
                                    <Inplace v-if="false" :closable="true">
                                        <template #display>
                                            <span class="p-button hover:p-button-secondary">
                                                Grid Size
                                            </span>
                                        </template>
                                        <template #content>
                                            <label class="block text-xs mb-2">Grid Size</label>
                                            <Slider class="w-10rem" v-model="userPreferences.gridSize" :step="1"
                                                    :min="1"
                                                    :max="5"/>
                                        </template>
                                    </Inplace>
                                    <template v-else>
                                        <label class="block text-xs mb-2">Grid Size</label>
                                        <Slider class="w-10rem" v-model="userPreferences.gridSize" :step="1"
                                                :min="1"
                                                :max="5"/>
                                    </template>


                                </div>
                                <div class="mx-2 flex">
                                    <SelectButton v-model="userPreferences.backgroundMode" id="imageMode"
                                                  :options="backgroundModes"
                                                  optionLabel="label" option-value="value" aria-labelledby="basic">
                                        <template #option="slotProps">
                                            <span class="p-button-label" v-html="slotProps.option.label"></span>
                                        </template>
                                    </SelectButton>


                                </div>
                            </div>
                            <Button type="button" icon="pi pi-caret-down" @click="toggleGridConfig"/>

                            <OverlayPanel ref="gridConfigOverlay">
                                <div class="mx-2 flex flex-col gap-3">
                                    <div class="flex" v-if="userPreferences.layout === 'grid'">
                                        <label class="align-self-center mr-2 text-xs">Thumbnail resolution</label>
                                        <SelectButton v-model="userPreferences.imageSize"
                                                      :options="[{value: 'sml', 'label': 'Optimized'}, {value: 'lrg', 'label': 'Large'}]"
                                                      optionLabel="label" option-value="value"
                                                      aria-labelledby="basic"/>
                                    </div>
                                    <div class="flex">
                                        <Button type="button" icon="pi pi-columns"
                                                class="w-full"
                                                v-tooltip="compareSelection.length < 2 || compareSelection.length > 4 ? 'Select 2-4 images to compare' : ''"
                                                :severity="compareSelection.length < 2 || compareSelection.length > 4 ? 'secondary' : 'primary'"
                                                @click="openComparisonView">
                                            <u>S</u>election Comparison
                                        </Button>
                                    </div>
                                    <div class="flex">
                                        <Button type="button" icon="pi pi-columns"
                                                class="w-full"
                                                :severity="compareSelection.length === 0 ? 'secondary' : 'primary'"
                                                @click="clearSelection">
                                            C<u>l</u>ear Selection
                                        </Button>
                                    </div>
                                </div>
                            </OverlayPanel>
                        </div>

                        <div class="flex pt-2">
                            <div class="flex mx-1">
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
                                            <i class="pi pi-chevron-up" style="font-size: 0.8rem"></i>

                                        </a>
                                        <a class="cursor-pointer" @click="userPreferences.sortOrder = -1"
                                           :class="{'text-blue-800': userPreferences.sortOrder === -1,
                                           'text-gray-200': userPreferences.sortOrder === 1
                                        }">
                                            <i class="pi pi-chevron-down" style="font-size: 0.8rem"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <Divider layout="vertical"/>
                            <div class="justify-items-end">
                                <DataViewLayoutOptions v-model="userPreferences.layout"
                                                       @update:model-value="clearSelection"/>
                            </div>
                        </div>
                    </div>


                </template>

                <template #grid="slotProps">
                    <PreviewCard :ref="el => previewCards.push(el)"
                                 :item="slotProps.data"
                                 :grid-size="userPreferences.gridSize"
                                 :extra-class="visibleQuickDetails && quickViewEntry.formatted_job_number !== slotProps.data.formatted_job_number ? 'blur-sm': ''"
                                 :image-size="userPreferences.imageSize"
                                 :background-mode="userPreferences.backgroundMode"
                                 @on-click-view="openEntryModal"
                                 @on-click-quick-view="openQuickView"
                                 @selection-changed="updateComparisonSelection"
                    />
                </template>
                <template #list="slotProps">
                    <PreviewRow :ref="el => previewRows.push(el)"
                                :item="slotProps.data"
                                :config="fields_config"
                                :grid-size="userPreferences.gridSize"
                                :extra-class="visibleQuickDetails && quickViewEntry.formatted_job_number !== slotProps.data.formatted_job_number ? 'blur-sm': ''"
                                :image-size="userPreferences.imageSize"
                                :background-mode="userPreferences.backgroundMode"
                                @on-click-view="openEntryModal"
                                @on-click-quick-view="openQuickView"
                                @selection-changed="updateComparisonSelection"
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
                    <template v-for="(field, section) in groupedSortedConfig">
                        <a class="p-button p-button-info p-button-outlined p-button-sm mx-2"
                           :href="'#' + snakeCase(section)">
                            {{ section }}
                        </a>
                    </template>
                </div>
            </template>
        </template>
        <ViewSearchEntry :entry="currentEntry" :grouped-sorted-config="groupedSortedConfig"
                         @exit="closeEntryModal"
                         @next="nextEntry"
                         @prev="prevEntry"
        />
    </FullModal>

    <FullModal v-model:visible="comparing" position="full">
        <template #header>

        </template>
        <QuickViewSearchEntry :entries="compareSelection" :config="fields_config"
                              :compareMode="true"
                              @request-full-view="openEntryModal"/>
    </FullModal>


</template>

<style lang="scss" scoped>

:deep(*) {
    .p-dataview-header {
        @apply sticky top-0 z-30;
        padding: 0.5rem 1rem;
    }

    .p-dataview-content, article {
        @apply z-20;
    }

    .p-inplace .p-inplace-display {
        padding: 0;
        border-radius: 0;
    }

    .p-button.p-autocomplete-dropdown {
        padding: 0;
    }

    .p-autocomplete-input.p-inputtext {
        padding: 5px;
    }

    .grid-options,
    .grid-options .p-button,
    .grid-options .p-button-label,
    .grid-options .p-float-label label,
    .grid-options .p-dropdown-label {
        font-size: 11px;

    }

    .p-float-label .p-inputwrapper-filled ~ label {
        top: -0.3rem;
    }

    .p-dataview .p-dataview-header, .p-paginator {
        @apply bg-indigo-50;
    }

    .p-dataview .p-dataview-content {
        @apply bg-neutral-200 p-2;
    }
}

</style>
