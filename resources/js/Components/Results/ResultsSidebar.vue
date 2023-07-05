<script lang="ts" setup>
import {userPreferencesStore} from "@/stores/userPreferencesStore";
import TaxonomySelector from "@/Components/Results/TaxonomySelector.vue";
import {computed} from "@vue/reactivity";
import {defineAsyncComponent, inject, onMounted, ref, watch} from "vue";
import Button from 'primevue/button';
import MultiSelect from "primevue/multiselect";
import InputText from "primevue/inputtext";
import axios from "axios";
import route from "ziggy-js";
import {useToast} from "primevue/usetoast";
import {useDialog} from 'primevue/usedialog';
import _ from "lodash";

const props = defineProps({
    collectionId: {type: Number, required: false},
    collectionMode: {type: Boolean, default: false},
    savedFilters: {type: [Object, Boolean], default: false}
});

const {filters, filteredSearchData, filterText} = inject('filters');
const report: Object[] = inject('report');
const {fields, fields_config} = inject('fields');
const userPreferences = userPreferencesStore();

const allSearchOptions = ref<Object>({});
const filteredSearchOptions = ref<Object>({});

const titleCase = (str) => window.titleCase(str);

const arr = [];

Object.keys(fields).map((key) => {
    if (fields_config.hasOwnProperty(key))
        arr.push({
            "value": key,
            "label": titleCase(fields_config[key].hasOwnProperty('title') ? fields_config[key].title : key)
        })
    //value: key, label: titleCase(fields_config[key].hasOwnProperty('title') ? fields_config[key].title : key)}
})

const availableFields = computed(() => {
    return arr.sort((a, b) => a.label.localeCompare(b.label))
})

const selectedTaxonomies = ref<String[]>([]);

onMounted(() => {
    console.log('mount generated all search and filtered search options');
    filteredSearchOptions.value = getFilterOptions();
    allSearchOptions.value = filteredSearchOptions.value

    if (props.collectionMode && props.savedFilters) {
        filters.value = props.savedFilters
        selectedTaxonomies.value = Object.keys(props.savedFilters);
    } else {
        selectedTaxonomies.value = userPreferences.selectedTaxonomy;
    }
});

watch(
    () => filters,
    (newValue, oldValue) => {
        console.log('generating filtered search options because of filters watcher');
        filteredSearchOptions.value = getFilterOptions();

        if (!allSearchOptions.value || Object.keys(allSearchOptions.value).length === 0) {
            allSearchOptions.value = filteredSearchOptions.value;
        }
    },
    {deep: true}
)

const totalResults = computed(() => {
    return report.length;
})

const filteredResults = computed(() => {
    return filteredSearchData.value.length;
})

const displayedTaxonomies = computed(() => {
    if (props.collectionMode && props.savedFilters) {
        return selectedTaxonomies.value;
    }
    return userPreferences.selectedTaxonomy;
})

function clearSelectedFields() {
    clearFilters()
    userPreferences.selectedTaxonomy = [];
}

function clearFilters() {
    for (const field in filters.value) {
        filters.value[field] = [];
    }
}

function updateSelectedTaxonomies(taxonomies) {
    console.log('updating selected taxonomies', taxonomies);

    if(props.collectionMode){
        for(const taxonomy of Object.keys(filters.value)){
            if(!taxonomies.includes(taxonomy)){
                delete filters.value[taxonomy];
            }
        }
    }
}

function getFilterOptions() {
    console.log('filtering options');
    var options = {};

    for (const i in filteredSearchData.value) {
        let entry = filteredSearchData.value[i];
        for (const j in Object.keys(entry)) {
            let field = Object.keys(entry)[j];

            if (!Object.keys(fields).includes(field)) {
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

const dialog = useDialog();
const toast = useToast();
const AddCollectionForm = defineAsyncComponent(() => import('@/Components/Collections/Form.vue'));
const openAddCollectionDialog = () => {
    console.log('saving to collection');
    const addDialogRef = dialog.open(AddCollectionForm, {
        props: {
            header: 'Create Collection',
            style: {
                width: '50vw',
            },
            breakpoints: {
                '960px': '75vw',
                '640px': '90vw'
            },
            modal: true
        },
        data: {
            collection_id: props.collectionId,
            filters: filters.value,
        },
        templates: {
            //footer: markRaw(FooterDemo)
        },
        onClose: (options) => {
            if (options.data) {
                toast.add({
                    severity: 'info',
                    summary: 'Collection created',
                    life: 3000
                });
            }
        }
    });
}

function updateCollectionFilters() {
    axios.post(
        route('api.collections.update-filters', {collection: props.collectionId}),
        {
            filters: filters.value
        }
    ).then(response => {
            //backgroundLoading.value = false;

            if (response) {
                console.log("saved")
            } else {
                console.log("error")
            }
        })
}

</script>

<template>
    <div class="results-sidebar md:col-span-1 lg:col-span-1 h-screen sticky top-0 mt-2 shadow-lg">
        <div class="flex my-3 pt-20">
            <div class="max-w-7xl mx-auto sm:px-2">
                <div class="bg-white shadow sm:rounded-lg px-2">
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
        <div class="filter-options flex flex-col mb-12 mt-12 ">

            <div class="flex w-full mb-5 justify-center">
                <span class="p-float-label">
                    <InputText type="text" v-model="filterText"
                               v-tooltip="'Search across all the fields from the current results'"/>
                    <label for="sort">Filter results</label>
                </span>
            </div>

            <div class="flex flex-col border-t-2 border-b-2 border-gray-200 px-2 pt-3 pb-4 shadow-lg">
                <p class="pb-4">Advanced Filtering</p>
                <div class="pb-4">
                        <span class="p-float-label grow">
                            <template v-if="collectionMode && savedFilters">
                               <MultiSelect v-model="selectedTaxonomies" id="taxonomySelector"
                                            filter reset-filter-on-hide auto-filter-focus
                                            @update:modelValue="updateSelectedTaxonomies"
                                            :options="availableFields"
                                            option-value="value" option-label="label"
                                            display="chip"
                                            placeholder="Searchable Fields"
                                            filter-placeholder="Browse"
                                            scroll-height="21rem"
                                            class="w-full"/>
                                 </template>
                            <template v-else>
                                <MultiSelect v-model="userPreferences.selectedTaxonomy" id="taxonomySelector"
                                             filter reset-filter-on-hide auto-filter-focus
                                             @update:modelValue="updateSelectedTaxonomies"
                                             :options="availableFields"
                                             option-value="value" option-label="label"
                                             display="chip"
                                             placeholder="Searchable Fields"
                                             filter-placeholder="Browse"
                                             scroll-height="21rem"
                                             class="w-full"/>
                            </template>

                            <label for="taxonomySelector">Select Additional Search Filters</label>
                        </span>
                    <template v-if="userPreferences.selectedTaxonomy.length">
                        <Button class="px-3" size="small" label="Remove Filter Fields" icon="pi pi-times"
                                @click="clearSelectedFields"/>
                    </template>
                </div>

                <div class="">
                    <div class="px-3 py-3" v-for="field in displayedTaxonomies" :key="field">

                        <TaxonomySelector :taxonomy-name="field"
                                          :filtered-terms="filteredSearchOptions[field]"
                                          :all-terms="allSearchOptions[field]"
                                          v-model="filters[field]"
                        />
                    </div>

                    <div class="mx-2 flex flex-col gap-3">
                        <template v-if="displayedTaxonomies.length">
                            <Button class="px-3" size="small" label="Clear Filters" icon="pi pi-times"
                                    @click="clearFilters"/>
                        </template>
                        <template v-if="collectionMode">
                            <div class="flex flex-row justify-content-around">
                            <Button class="px-3" size="small" title="Save New Collection" icon="pi pi-save"
                                    @click="openAddCollectionDialog"/>
                            <Button class="px-3" size="small" title="Update Collection Filters" icon="pi pi-refresh"
                                    @click="updateCollectionFilters"/>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped>
.results-sidebar {
    @apply bg-indigo-50 z-40;
}
</style>
