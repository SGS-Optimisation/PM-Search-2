<script lang="ts" setup>
import AppLayout from "../../Layouts/AppLayout";
import Loader from "../../Components/Loader";
import axios from "axios";
import route from "ziggy-js";

defineOptions({layout: AppLayout})

/*defineProps({
    filename: String,
    search: String,
});*/
function mounted(this: any) {
    this.waitMode();
 }
        function waitMode(this: any) {
            this.timeOut = setTimeout(() => {
                this.queryProcessed();
            }, 5000);
        }

        function queryProcessed(this: any) {
            axios.get(route('search.status', this.search.id))
                .then(({data}) => {
                    console.log(data);
                    if (data.processed) {
                        this.$inertia.visit(
                            route('search.show', this.search.id),
                            {method: 'GET'},
                            {replace: true}
                        );
                    } else if(data.error){
                        this.error = data.error;
                    }
                    else {
                        this.waitMode();
                    }
                })
        }

</script>

<template>
    <app-layout>
        <template #header>
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
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-2 overflow-hidden shadow-xl sm:rounded-lg">
                    <template v-if="error || search.working_data.hasOwnProperty('error') && search.working_data.error">
                        An error has occured. Please try again later.
                    </template>
                    <template v-else>
                        <loader/>
                    </template>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<style scoped>

</style>
