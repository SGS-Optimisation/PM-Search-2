<script lang="ts" setup>
import DataView from "primevue/dataview";
import {Link} from "@inertiajs/vue3";
import Tag from "primevue/tag";
import {configStore} from "@/stores/config-store";

const props = defineProps({
    latestSearches: Array,
})

function formatAdvancedSearchField(field, value) {
    if( !configStore.getAdvancedSearchFields().hasOwnProperty(field)) {
        return field + ':' + value;
    }
    const config = configStore.getAdvancedSearchFields()[field];

    if(configStore.getAdvancedSearchFields()[field].type === 'date') {
        value = JSON.parse(value);
        value = (new Date(value[0])).toLocaleDateString() + ' - ' + (new Date(value[1])).toLocaleDateString();
    }

    return config.key + ':' + value;
}

</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-2 pb-12 px-2">

        <DataView :value="latestSearches" paginator :rows="5">
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
                                <template v-if="slotProps.data.search_data.hasOwnProperty('search_string')">
                                    {{slotProps.data.search_data.search_string.join(' ' + slotProps.data.search_data.operator.toUpperCase() + ' ') }}
                                </template>
                                <template v-else>
                                    {{slotProps.data.search_data.textsearchstrings.join(' ' + slotProps.data.search_data.textsearchoperator.toUpperCase() + ' ') }}

                                    <template v-if="slotProps.data.search_data.hasOwnProperty('fields')">
                                        <Tag v-for="(value, field) in slotProps.data.search_data.fields" :key="field" class="ml-1 mb-1"
                                             severity=""
                                             :value="formatAdvancedSearchField(field, value)" rounded></Tag>
                                    </template>
                                </template>
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
