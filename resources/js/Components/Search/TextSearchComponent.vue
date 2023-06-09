<script lang="ts" setup>
import {defineComponent, watch, ref, reactive, onMounted, onUpdated, computed, defineAsyncComponent, inject} from "vue";
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
import {useToast} from "primevue/usetoast";
import moment from "moment";

const AddCollectionForm = defineAsyncComponent(() => import('@/Components/Collections/Form.vue'));

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
    },
    allowSave: {
        type: Boolean,
        default: false
    },
    searchId: {
        type: Number,
        required: false,
    }
})

const {filters} = inject('filters');

const dialog = useDialog();
const toast = useToast();

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
            search: props.searchId,
            filters,
        },
        templates: {
            //footer: markRaw(FooterDemo)
        },
        onClose: (options) => {
            if (options.data) {
                toast.add({
                    severity: 'info',
                    summary: 'Collection created',
                    life: 3000});
            }
        }
    });
}

const form = useForm({
    search_string: Array,
    operator: <String>'and',
})

const tags = ref<Array<String>>([])
const resetOnSuccess = ref<boolean>(false)
const operatorOptions = ref([
    {name: 'And', value: 'and'},
    {name: 'Or', value: 'or'},
]);

function handleChangeTag(event) {
    let tooltip = document.getElementsByClassName('p-tooltip-right')
    if (tooltip.length > 0) {
        for (let i = 0; i < tooltip.length; i++) {
            tooltip[i].style.display = "none"
        }
    }
    tags.value = event;

    for (const tag of tags.value) {
        if (tag.indexOf(':') > 0) {
            const parts = tag.split(':');
            var field: string = '';
            var type = null;

            for (const key in configStore.getAdvancedSearchFields()) {
                if (configStore.getAdvancedSearchFields()[key].key === parts[0]) {
                    field = key;
                    type = configStore.getAdvancedSearchFields()[key].type
                    break;
                }
            }

            if (field) {
                if (type !== 'date') advancedSearchField.value[field] = parts[1];
            } else
                parseAdvancedFields();
        }
    }
}

const canSearch = computed(() => {
    return tags.value.length > 0;
})

function doSearch(this: any) {

    form.transform((data) => {
        var query = tags.value;

        console.log('checking advanced search field', advancedSearchField.value);
        for (const field in advancedSearchField.value) {
            const fieldConfig = configStore.getAdvancedSearchFields()[field];

            console.log('checking field ', field);
            if (fieldConfig && fieldConfig.hasOwnProperty('standalone') && fieldConfig.standalone) {
                query.push(fieldConfig.key + ':' + advancedSearchField.value[field]);
            }
        }

        return {
            ...data,
            search_string: query,
        }
    })
        .post(route('search.store'), {
            preserveScroll: true
        })
}

const simplified_group_name = ref('')

const advancedSearchField = ref({})
const advancedSearchOverlay = ref();
const inputTextField = ref();
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
    var keepFields: String[] = [];

    for (const search of tags.value) {
        if (search.indexOf(':') < 0) {
            keepFields.push(search);
        }
    }

    for (const key in advancedSearchField.value) {
        if (advancedSearchField.value[key]) {
            let fieldConfig = configStore.getAdvancedSearchFields()[key];

            let tagKey = fieldConfig.key;

            if (configStore.getAdvancedSearchFields()[key].type === 'date') {

                if (!checkValidRange(key))
                    continue;

                var d1 = moment(new Date(advancedSearchField.value[key][0])).format("yyyy-MM-DD")
                var d2 = moment(new Date(advancedSearchField.value[key][1])).format("yyyy-MM-DD")
                keepFields.push(tagKey + ':' + d1 + '>' + d2);
            } else if (!fieldConfig.hasOwnProperty('standalone') || !fieldConfig.standalone) {
                keepFields.push(tagKey + ':' + advancedSearchField.value[key]);
            }
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
    parseInitialValues();
});

onUpdated(() => {
    //parseInitialValues();
})


function parseInitialValues() {
    if (props.initialValues !== null && props.initialValues.value !== null) {
        if (props.initialValues.textsearchstrings != null) {
            for (const tag in props.initialValues.textsearchstrings) {
                tags.value.push(props.initialValues.textsearchstrings[tag]);
            }
        }
        if (props.initialValues.fields != null) {
            for (const field in props.initialValues.fields) {
                var fieldVal = props.initialValues.fields[field];

                if (configStore.getAdvancedSearchFields().hasOwnProperty(field) ) {
                    const fieldConfig = configStore.getAdvancedSearchFields()[field];

                    if (fieldConfig.hasOwnProperty('standalone') && fieldConfig.standalone) {
                        advancedSearchField.value[field] = fieldVal;
                        continue;
                    }

                    if (fieldConfig.type === 'date') {
                        const dates_array = JSON.parse(fieldVal)
                        var d1 = new Date(dates_array[0]);
                        var d2 = new Date(dates_array[1]);
                        advancedSearchField.value[field] = [d1, d2];

                        let tagKey = fieldConfig.key;
                        var d1_str = d1.toISOString().split('T')[0];
                        var d2_str = d2.toISOString().split('T')[0];
                        tags.value.push(tagKey + ':' + d1_str + '>' + d2_str);

                    } else {
                        tags.value.push(fieldConfig.key + ': ' + fieldVal);
                    }
                }
            }
        }
        handleChangeTag(tags.value);
    }
    //advancedSearchFieldSuggestions.value['simplified_group_name'] = [];
};

const toggleAdvanced = (event) => {
    advancedSearchOverlay.value.toggle(event);
};

function resetFilters() {
    tags.value = ""
    form.reset();
    advancedSearchField.value = ""
    inputTextField.value=""
}

const titleCase = (str) => window.titleCase(str);

</script>


<template>
    <div class="flex">
        <Button v-if="allowSave" icon="pi pi-save" size="small" class="mr-3" @click="openAddCollectionDialog"/>
        <jet-form-section :compact-mode="compactMode" class="grow">
            <template #title>
                Text Search
            </template>
            <template #description v-if="!compactMode">
                <p class="text-[13px]">Search for text from the artwork pdf consisting of 700K plus files collected
                    since July 2018 along with
                    other parameters such as brand, variety, print process, printer, dieline and other data points from
                    over 3 million MySGS jobs.
                    <br>
                    Unavailable thumbnails are a result of the job not launching/employing the standard workflow which
                    support the capture of thumbnail files.
                </p>
            </template>
            <template #form>

                <template v-for="(config, field) in configStore.getAdvancedSearchFields()" :key="field">
                    <template v-if="config.hasOwnProperty('standalone') && config.standalone === true">

                        <div class="flex flex-row items-center"
                            :class="{
                                'md:col-span-1': compactMode,
                                'md:col-span-6  w-full ': !compactMode
                            }"
                        >
                            <span class="p-float-label">

                                <template v-if="config.type === 'text'">
                                    <InputText ref="inputTextField" :id="field" v-model="advancedSearchField[field]"/>
                                </template>
                                <template v-else-if="config.type === 'autocomplete'">
                                    <AutoComplete :id="field" v-model="advancedSearchField[field]"
                                                  class="v3ti "
                                                  :suggestions="advancedSearchFieldSuggestions[field]"
                                                  @complete="autocompleteSearch($event, field)"/>
                                </template>
                                <label :for="field">{{
                                        titleCase(config.hasOwnProperty('title') ? config.title : field)
                                    }}</label>
                            </span>
                        </div>
                    </template>
                </template>

                <div class="flex flex-row items-center grow"
                     :class="{
                                'md:col-span-4': compactMode,
                                'md:col-span-6 w-full ': !compactMode
                            }">
                    <div class="grow pr-2">
                        <div class="flex flex-row items-between relative">
                            <vue3-tags-input
                                id="main-search"
                                class="grow pl-4"
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

                            <span class="absolute left-1 top-2 cursor-pointer"
                                  @click="resetFilters">
                                <i class="text-gray-300 pi pi-times-circle"></i>
                            </span>
                        </div>

                    </div>

                    <div class="text-right">
                        <SelectButton v-model="form.operator" :options="operatorOptions"
                                      :unselectable=true
                                      optionLabel="name" optionValue="value"/>
                    </div>
                    <div class="ml-2">
                        <Button type="submit" @click="doSearch" :disabled="!canSearch"
                                label="Search" severity="success"/>
                    </div>

                </div>
            </template>

        </jet-form-section>

        <OverlayPanel ref="advancedSearchOverlay" appendTo="body" @hide="parseAdvancedFields" show-close-icon>
            <div class="md:grid md:grid-cols-4 gap-x-6 gap-y-3 justify-content-center align-items-center p-2">
                <template v-for="(config, field) in configStore.getAdvancedSearchFields()" :key="field">
                    <template v-if="!config.hasOwnProperty('standalone') || config.standalone !== true">
                        <label class="block text-xs font-medium text-gray-700 capitalize text-right" :title="field">
                            {{ titleCase(config.hasOwnProperty('title') ? config.title : field) }}
                        </label>

                        <template v-if="config.type === 'text'">
                            <InputText v-model="advancedSearchField[field]"/>
                        </template>
                        <template v-else-if="config.type === 'autocomplete'">
                            <AutoComplete v-model="advancedSearchField[field]"
                                          :suggestions="advancedSearchFieldSuggestions[field]"
                                          :dropdown="config.dropdown"
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
                </template>
            </div>
        </OverlayPanel>
    </div>
</template>

<style scoped>
:deep(.p-selectbutton) {
    @apply w-max;
}

:deep(.p-inputtext) {
    padding: 0.25rem 0.5rem;
}
</style>
