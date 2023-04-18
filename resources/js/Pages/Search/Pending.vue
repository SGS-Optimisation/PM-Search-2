<script lang="ts" setup>
import ProgressSpinner from 'primevue/progressspinner';
import axios from "axios";
import route from "ziggy-js";
import {Head, useForm, usePage} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { onMounted } from "vue";

defineOptions({layout: AppLayout})

const props = defineProps({
    filename: String,
    search: {type: Object, required: true},
});

onMounted(() => {
    axios.get(route('search.status', props.search.search.id))
        .then(({data}) => {
            console.log(data + "got till here");
            if (data.processed) {
                //this.$inertia.visit(
                axios.get(
                    route('search.show', props.search.search.id),
                    {method: 'GET'}
                    //{replace: true}
                );
            } else if(data.error){
                props.search.error = data.error;
            }
        })
});

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
                            <em>{{search.search_data.search_string.join(' ' + search.search_data.operator.toUpperCase() + ' ')}}</em>
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
                    <ProgressSpinner style="width: 50px; height: 50px;" />
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>

