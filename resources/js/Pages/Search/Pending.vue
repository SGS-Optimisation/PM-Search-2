<script lang="ts" setup>
import ProgressSpinner from 'primevue/progressspinner';
import Tag from "primevue/tag";
import axios from "axios";
import route from "ziggy-js";
import {Head, router, useForm, usePage} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import {onBeforeMount, onMounted, ref} from "vue";
import {configStore} from "@/stores/config-store";

defineOptions({layout: AppLayout})

const props = defineProps({
    filename: String,
    search: {type: Object, required: true},
});

var error = ref<boolean>(false)

onBeforeMount(() => {
    waitMode();
});

function waitMode() {
    setTimeout(() => {
        queryProcessed();
    }, 1000);
}

function queryProcessed() {
    axios.get(route('search.status', props.search.id))
        .then(({data}) => {
            console.log(data);
            if (data.processed) {
                router.visit(
                    route('search.show', {id: props.search.id}),
                    {
                        method: 'get',
                        replace: true
                    });
            } else if (data.error) {
                error = data.error;
            } else {
                waitMode();
            }
        });
}

function formatAdvancedSearchField(field, value) {
    const config = configStore.getAdvancedSearchFields()[field];

    if(configStore.getAdvancedSearchFields()[field].type === 'date') {
        value = JSON.parse(value);
        value = (new Date(value[0])).toLocaleDateString() + ' - ' + (new Date(value[1])).toLocaleDateString();
    }

    return config.key + ':' + value;
}

</script>

<template>
    <div class="py-12">
        <header class="flex flex-col items-center justify-center">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <template v-if="search.search_mode === 'image'">
                        Awaiting result for file {{ filename }}
                    </template>
                    <template v-else>
                        <p>Searching for

                            <template v-if="search.search_data.hasOwnProperty('search_string')">
                                <em>{{ search.search_data.search_string.join(' ' + search.search_data.operator.toUpperCase() + ' ') }}</em>
                            </template>
                            <template v-else>
                                {{search.search_data.textsearchstrings.join(' ' + search.search_data.textsearchoperator.toUpperCase() + ' ') }}

                                <template v-if="search.search_data.hasOwnProperty('fields')">
                                    <Tag v-for="(value, field) in search.search_data.fields" :key="field" class="ml-1 mb-1"
                                         severity=""
                                         :value="formatAdvancedSearchField(field, value)" rounded></Tag>
                                </template>
                            </template>

                        </p>
                    </template>
                </h2>
            </div>
        </header>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col items-center justify-center p-2 overflow-hidden sm:rounded-md">
                <template v-if="error || search.working_data.hasOwnProperty('error') && search.working_data.error">
                    An error has occured. Please try again later.
                </template>
                <template v-else>
                    <ProgressSpinner style="width: 50px; height: 50px;"/>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>

