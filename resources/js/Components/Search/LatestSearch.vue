<script lang="ts" setup>
import DataView from "primevue/dataview";
import {Link} from "@inertiajs/vue3";

defineProps({
    latestSearches: Array,
})
</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-2 pb-12 px-2">

        <DataView :value="latestSearches">
            <template #header>
                <h4 class="h4 font-bold text-lg">My latest searches</h4>
            </template>

            <template #list="slotProps">
                <div class="col-12">
                    <div class="flex flex-column xl:flex-row xl:align-items-start pb-1 gap-4 border-1 border-b">
                        <span>
                            <i class="pi"  :class="{
                                'pi-language': slotProps.data.search_mode === 'text',
                                'pi-image': slotProps.data.search_mode === 'image',
                            }"
                               :title="slotProps.data.search_mode === 'text' ? 'Text search' : 'Image search'"
                            ></i>
                        </span>

                        <Link class="text-blue-500 hover:text-blue-700 underline"
                           :href="route('search.show', [slotProps.data.id])">

                            <span v-if="slotProps.data.search_mode === 'text'">
                                {{slotProps.data.search_data.search_string.join(' ' + slotProps.data.search_data.operator.toUpperCase() + ' ') }}
                            </span>
                            <span v-else>
                                {{ slotProps.data.working_data.original_filename }}
                            </span>
                        </Link>
                    </div>
                </div>
            </template>


        </DataView>
    </div>
</template>
