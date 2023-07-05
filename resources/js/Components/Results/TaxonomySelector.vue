<script lang="ts" setup>

import {computed, onMounted, ref, watch} from "vue";
import MultiSelect from "primevue/multiselect";

const props = defineProps({
    taxonomyName: String,
    allTerms: Array,
    filteredTerms: Array,
    modelValue: Array,
})

const emit = defineEmits(['termSelected', 'update:modelValue']);

const selection = ref<String[]>([]);

watch(() => props.modelValue, (value) => {
    selection.value = value;
});

onMounted(() => {
    selection.value = props.modelValue;
});

const sortedTerms = computed(() => {

    if (props.allTerms === undefined || props.filteredTerms === undefined) {
        return [];
    }

    var groupedTerms: Object[] = [];
    var allOptions: Object[] = [];
    var filteredMatches: Object[] = [];

    if (props.filteredTerms.length !== props.allTerms.length) {

        filteredMatches = props.filteredTerms.sort().map(term => {
            return {label: term, value: term}
        });

        if (props.filteredTerms.length > 0) {
            groupedTerms.push({
                label: 'Matches',
                items: filteredMatches
            });
        }

        allOptions = props.allTerms.filter(term => !props.filteredTerms.includes(term))
            .sort().map(term => {
                return {label: term, value: term}
            });

        groupedTerms.push({
            label: 'Other Options',
            items: allOptions
        });

    } else {
        allOptions = props.allTerms.sort().map(term => {
            return {label: term, value: term}
        });

        groupedTerms.push({
            label: 'All Options',
            items: allOptions
        });
    }



    return groupedTerms;
});

function clearSelected() {
    selection.value = [];
}

const titleCase = (str) => window.titleCase(str);

function setSelected(value) {
    emit('termSelected', props.taxonomyName, value);
}

</script>


<template>
    <div>
        <span class="p-float-label">

            <MultiSelect v-model="selection"
                         filter :resetFilterOnHide="true" :autoFilterFocus="true"
                         @change="$emit('update:modelValue', $event.value)"
                         :options="sortedTerms"
                         display="chip"
                         optionGroupLabel="label" optionGroupChildren="items"
                         option-value="value" option-label="label"
                         placeholder="Select"
                         filter-placeholder="Browse"
                         class="w-full"/>
            <label class="block text-xs font-medium text-gray-700 capitalize">
                {{taxonomyName}}
            </label>
        </span>
    </div>
</template>
