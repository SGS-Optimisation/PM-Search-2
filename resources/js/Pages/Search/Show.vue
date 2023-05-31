<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Results from "@/Components/Results/Results.vue";
import {userPreferencesStore} from "@/stores/userPreferencesStore";
import useHotkey from "vue3-hotkey";
import {ref} from "vue";
import PreviewCard from "@/Components/Results/PreviewCard.vue";
import PreviewRow from "@/Components/Results/PreviewRow.vue";

const props = defineProps({
    search_id: {type: Number, required: true},
    parent_search_id: {type: Number, required: false},
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

const userPreferences = userPreferencesStore();
const previewCards = ref<PreviewCard[]>([]);
const compareSelection = ref<Object[]>([]);
const comparing = ref(false);
const previewRows = ref<PreviewRow[]>([]);

function openComparisonView() {
    if (!(compareSelection.value.length < 2 || compareSelection.value.length > 4)) {
        comparing.value = true;
    }
}


function clearSelection() {
    compareSelection.value = [];
    for (const card of previewCards.value) {
        card.clearSelection();
    }
    for (const card of previewRows.value) {
        card.clearSelection();
    }
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
const stop = useHotkey(hotkeys.value)

</script>

<template>
    <Head title="Search results"/>

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
    />


</template>

<style lang="scss" scoped>

</style>
