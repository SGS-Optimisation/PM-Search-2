<script lang="ts" setup>

import {computed, ref} from "vue";
import MultiSelect from "primevue/multiselect";

const props = defineProps({
    taxonomyName: String,
    allTerms: Array,
    fiteredTerms: Array,
    modelValue: Array,
})

const emit = defineEmits(['termSelected', 'update:modelValue']);

const selection = ref<String[]>([]);
const sortedTerms = computed(() => {

    if (props.allTerms === undefined || props.fiteredTerms === undefined) {
        return [];
    }

    var filteredMatches = <Object>[];

    if (selection.value.length > 0) {
        filteredMatches = props.fiteredTerms.sort().map(term => {
            return {label: term, value: term}
        });
    }

    var otherOptions = <Object>[];

    otherOptions = props.allTerms.sort().map(term => {
        return {label: term, value: term}
    })

    var groupedTerms = [];
    if (filteredMatches.length > 0) {
        groupedTerms.push({
            label: 'Matches',
            items: filteredMatches
        });
    }

    groupedTerms.push({
            label: 'All Options',
            items: otherOptions
        })

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
                         filter @change="$emit('update:modelValue', $event.value)"
                         :options="sortedTerms"
                         display="chip"
                         optionGroupLabel="label" optionGroupChildren="items"
                         option-value="value" option-label="label"
                         placeholder="Select"
                         class="w-full"/>
            <label class="block text-xs font-medium text-gray-700 capitalize">
                {{ titleCase(taxonomyName).replace(/Id$/, '') }}
            </label>
        </span>
    </div>
</template>

