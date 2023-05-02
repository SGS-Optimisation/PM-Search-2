<script lang="ts" setup>
import {defineComponent, watch, ref, reactive, onMounted} from "vue";
import JetFormSection from '@/Components/Jetstream/FormSection.vue';
import route from "ziggy-js";
import AutoComplete from 'primevue/autocomplete';
import Button from 'primevue/button';
import Calendar from 'primevue/calendar';
import SelectButton from 'primevue/selectbutton';
import Vue3TagsInput from 'vue3-tags-input';
import {useForm} from "@inertiajs/vue3";
import OverlayPanel from "primevue/overlaypanel";
import {useDialog} from 'primevue/usedialog';
import InputText from "primevue/inputtext";
import {configStore} from "@/stores/config-store";
import axios from "axios";

const props = defineProps({
    initialValues: {
        type: Object,
        required: false,
        default: null
    },
    compactMode: {
        type: Boolean,
        required: false,
        default: false,
    }
})

const form = useForm({
    search_string: Array,
    operator: <String>'and',
})
const dialog = useDialog();
const tags = ref<Array<String>>([])
const resetOnSuccess = ref<boolean>(false)
const operatorOptions = ref([
    {name: 'And', value: 'and'},
    {name: 'Or', value: 'or'},
]);

function handleChangeTag(event) {
    tags.value = event;

    for(const tag of tags.value) {
        if(tag.indexOf(':') > 0) {
            const parts = tag.split(':');
            var field = false;

            for(const key in configStore.getAdvancedSearchFields()) {
                if(configStore.getAdvancedSearchFields()[key].key === parts[0]) {
                    field = key;
                    break;
                }
            }

            if(field)
                advancedSearchField.value[field] = parts[1];
            else
                parseAdvancedFields();
        }
    }
}

function doSearch(this: any) {

    form.transform(data => ({
        ...data,
        search_string: tags.value,
    })).post(route('search.store'), {
        preserveScroll: true
    })
}

const advancedSearchOverlay = ref();
const advancedSearchField = ref({})
const advancedSearchFieldSuggestions = ref({})

function autocompleteSearch(event, field) {
    console.log('autocompleteSearch', event, field);
    //return;

    //TODO: fix this
    axios.post(configStore.getAutocompleteSuggester(), {
        suggester: field + '_suggester',
        query: event.query
    }).then((response) => {
        advancedSearchFieldSuggestions.value[field] = response.data.suggestions;
    })
}

function parseAdvancedFields() {
    var keepFields = [];

    for (const search of tags.value) {
        if (search.indexOf(':') < 0) {
            keepFields.push(search);
        }
    }

    for (const key in advancedSearchField.value) {
        if (advancedSearchField.value[key]) {
            let tagKey = configStore.getAdvancedSearchFields()[key].key;

            if (configStore.getAdvancedSearchFields()[key].type === 'date') {

                if (!checkValidRange(key))
                    continue;

                var d1 = advancedSearchField.value[key][0].toISOString().split('T')[0];
                var d2 = advancedSearchField.value[key][1].toISOString().split('T')[0];
                keepFields.push(tagKey + ':' + d1 + '>' + d2);
            } else
                keepFields.push(tagKey + ':' + advancedSearchField.value[key]);
        }
    }

    tags.value = keepFields;
}

function checkValidRange(field) {
    if (advancedSearchField.value.hasOwnProperty(field)
        && advancedSearchField.value[field] !== null
        && advancedSearchField.value[field].length === 2
        && advancedSearchField.value[field][0] !== null
        && advancedSearchField.value[field][1] !== null) {
        return true;
    }
    advancedSearchField[field] = [];
    return false;
}

onMounted(() => {
    if (props.initialValues !== null && props.initialValues.value !== null) {
        if (props.initialValues.textsearchstrings != null) {
            for (const tag in props.initialValues.textsearchstrings) {
                tags.value.push(props.initialValues.textsearchstrings[tag]);
            }
        }
        if (props.initialValues.fields != null) {
            for (const field in props.initialValues.fields) {
                if (configStore.getAdvancedSearchFields().hasOwnProperty(field)) {
                    if (configStore.getAdvancedSearchFields()[field].type === 'date') {
                        const dates_array = JSON.parse(props.initialValues.fields[field])
                        var d1 = new Date(dates_array[0]).toLocaleDateString().split('T')[0];
                        var d2 = new Date(dates_array[1]).toLocaleDateString().split('T')[0];
                        var value = d1 + ' - ' + d2;
                        tags.value.push(configStore.getAdvancedSearchFields()[field].key + ': ' + value);
                    } else {
                        tags.value.push(configStore.getAdvancedSearchFields()[field].key + ': ' + props.initialValues.fields[field]);
                    }
                }
            }
        }
        handleChangeTag(tags.value);
    }

});

const toggleAdvanced = (event) => {
    advancedSearchOverlay.value.toggle(event);
};

const titleCase = (str) => window.titleCase(str);

</script>


<template>
    <div>
        <jet-form-section @submitted="doSearch" class="mb-4" :compact-mode="compactMode">
            <template #title>
                Text Search
            </template>
            <template #description v-if="!compactMode">
                Search for text from the artwork pdf consisting of 700K plus files collected since July 2018 along with
                other parameters such as brand, variety, promotion, substrate, dieline and other data points from over 3 million Mysgs jobs.
            </template>
            <template #form>
                <div class="col-span-6 flex flex-row w-full items-center">
                    <div class="grow pr-2">
                        <div class="flex flex-row items-between relative">
                            <vue3-tags-input
                                id="main-search"
                                class="grow"
                                v-tooltip="'Enter up to 8 terms, separated by a comma. Avoid using long search terms.'"
                                :tags="tags"
                                :add-tag-on-keys="[13, 188]"
                                :limit=8
                                placeholder="Search"
                                @on-tags-changed="handleChangeTag"
                            />
                            <span class="absolute right-1 top-2 cursor-pointer"
                                  @click="toggleAdvanced">
                                <i class="text-gray-300 pi pi-filter"></i>
                            </span>
                        </div>

                    </div>

                    <div class="text-right">
                        <SelectButton v-model="form.operator" :options="operatorOptions"
                                      :unselectable=false
                                      optionLabel="name" optionValue="value"/>
                    </div>
                    <div class="ml-2">
                        <Button type="submit" @click="doSearch"
                                label="Search" severity="success"/>
                    </div>

                </div>
            </template>

        </jet-form-section>

        <OverlayPanel ref="advancedSearchOverlay" appendTo="body" @hide="parseAdvancedFields" show-close-icon>
            <div class="md:grid md:grid-cols-4 gap-x-6 gap-y-3 justify-content-center align-items-center p-2">
                <template v-for="(config, field) in configStore.getAdvancedSearchFields()" :key="field">
                    <label class="block text-xs font-medium text-gray-700 capitalize text-right">
                        {{ titleCase(field) }}
                    </label>

                    <template v-if="config.type === 'text'">
                        <InputText v-model="advancedSearchField[field]"/>
                    </template>
                    <template v-else-if="config.type === 'autocomplete'">
                        <AutoComplete v-model="advancedSearchField[field]"
                                      :suggestions="advancedSearchFieldSuggestions[field]"
                                      @complete="autocompleteSearch($event, field)"/>
                    </template>
                    <template v-else-if="config.type === 'date'">
                        <Calendar v-model="advancedSearchField[field]" selectionMode="range" :manualInput="false"
                                  view="month" placeholder="Date Range"
                                  @hide="checkValidRange(field)"
                                  show-button-bar
                        />
                    </template>
                </template>
            </div>
        </OverlayPanel>
    </div>
</template>

<style>
.p-selectbutton {
    @apply w-max;
}
</style>
