<script lang="ts" setup>
import {userPreferencesStore} from "@/stores/userPreferencesStore";
import TaxonomySelector from "@/Components/Results/TaxonomySelector.vue";
import {computed} from "@vue/reactivity";
import {inject, onMounted, ref, watch} from "vue";
import Button from 'primevue/button';
import MultiSelect from "primevue/multiselect";
import InputText from "primevue/inputtext";

const {filters, filteredSearchData, filterText} = inject('filters');
const report: Object[] = inject('report');
const {fields, fields_config} = inject('fields');
const userPreferences = userPreferencesStore();

const allSearchOptions = ref<Object>({});
const filteredSearchOptions = ref<Object>({});

const availableFields = computed(() => {
    return Object.keys(fields).map((key) => {
        return {value: key, label: titleCase(key)}
    });
})

onMounted(() => {
    console.log('mount generated all search and filtered search options');
    filteredSearchOptions.value = getFilterOptions();
    allSearchOptions.value = filteredSearchOptions.value
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

const titleCase = (str) => window.titleCase(str);

const totalResults = computed(() => {
    return report.length;
})

const filteredResults = computed(() => {
    return filteredSearchData.value.length;
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

function updateSelectedTaxonomy(taxonomy) {
    filters.value[taxonomy] = [];
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
                    <InputText type="text" v-model="filterText" v-tooltip="'Search across all the fields from the current results'"/>
                    <label for="sort">Filter results</label>
                </span>
            </div>

            <div class="flex flex-col border-t-2 border-b-2 border-gray-200 px-2 pt-3 pb-4 shadow-lg">
                <p class="pb-4">Advanced filtering</p>
                <div class="pb-4">
                        <span class="p-float-label grow">
                            <MultiSelect v-model="userPreferences.selectedTaxonomy" id="taxonomySelector"
                                         filter reset-filter-on-hide auto-filter-focus
                                         @update:modelValue="updateSelectedTaxonomy"
                                         :options="availableFields"
                                         option-value="value" option-label="label"
                                         display="chip"
                                         placeholder="Searchable Fields"
                                         class="w-full"/>
                            <label for="taxonomySelector">Select Search Fields</label>
                        </span>
                    <template v-if="userPreferences.selectedTaxonomy.length">
                        <Button class="px-3" size="small" label="Remove Filter Fields" icon="pi pi-times"
                                @click="clearSelectedFields"/>
                    </template>
                </div>

                <div class="">
                    <div class="px-3 py-3" v-for="field in userPreferences.selectedTaxonomy" :key="field">

                        <TaxonomySelector :taxonomy-name="field"
                                          :filtered-terms="filteredSearchOptions[field]"
                                          :all-terms="allSearchOptions[field]"
                                          v-model="filters[field]"
                        />
                    </div>

                    <template v-if="userPreferences.selectedTaxonomy.length">
                        <Button class="px-3" size="small" label="Clear Filters" icon="pi pi-times"
                                @click="clearFilters"/>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
