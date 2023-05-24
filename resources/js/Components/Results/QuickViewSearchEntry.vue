<script lang="ts" setup>
import Fieldset from "primevue/fieldset";
import Button from "primevue/button";
import {computed} from "vue";
import {configStore} from "@/stores/config-store";

const props = defineProps({
    entry: {
        type: Object,
        required: false
    },
    config: {
        type: Object,
        required: true
    },
    compareMode: {
        type: Boolean,
        default: false,
    },
    entries: {
        type: Array,
        required: false,
    },
})

const displayedEntries = computed(() => {
    return props.compareMode ? props.entries : [props.entry];
})

const emit = defineEmits(['request-full-view']);

function openFullView() {
    emit('request-full-view', props.entry);
}

const sortedConfig = computed(() => {
    var sortedKeys = Object.keys(props.config).sort((a, b) => {
        return props.config[a].position - props.config[b].position;
    });

    var sortedObj = {};
    for (const key in sortedKeys) {
        sortedObj[sortedKeys[key]] = props.config[sortedKeys[key]];
    }

    return sortedObj;

});

const bridge_fields = computed(() => {
    return configStore.getBridgeFields();
})

function isEcode(str) {
    return str.match(/^[0-9]{6}[eE]$/);
}

function parseEcode(entry){
    return parseInt(entry.pcm_type_profile_name.toString().toLowerCase().replace('e', ''));
}

const titleCase = (str) => window.titleCase(str);

</script>

<template>
    <div class="">
        <Button v-if="!compareMode" @click="openFullView" label="Full View" class="mb-4 w-full"/>
        <table class="table table-responsive border text-sm">
            <tr v-if="compareMode">
                <td></td>
                <td v-for="item in displayedEntries">
                    <Button @click="openFullView" label="Full View" class="mb-4"/>
                </td>
            </tr>
            <template v-for="(params, field) in sortedConfig">
                <template v-if="params.display && !bridge_fields.includes(field)">

                    <tr class="border even:bg-blue-100">
                        <td class="font-bold text-xs whitespace-nowrap"
                            :class="{'pr-3': compareMode}"
                        >
                            {{ params.hasOwnProperty('title') ? params.title : titleCase(field) }}
                        </td>

                        <td v-for="item in displayedEntries"
                            :class="{
                                'md:grow' : !compareMode,
                                'pr-3' : compareMode,
                            }">
                            <template v-if="field==='formatted_job_number'">
                                <a class="text-blue-500 hover:text-blue-700" target="_blank"
                                   :href="'https://pm.mysgs.sgsco.com/Job/' + item[field]"
                                >
                                    {{ item[field] }} <i class="text-xs pi pi-external-link"></i>
                                </a>
                            </template>
                            <template v-else-if="field==='pcm_type_profile_name' && isEcode(item[field])">
                                <a class="text-blue-500 hover:text-blue-700" target="_blank"
                                   :href="'https://cmf.sgsco.com/color-profile/' + parseEcode(item)"
                                   v-tooltip="'Open color profile in CMF'"
                                >
                                    {{ item[field] }} <i class="text-xs pi pi-external-link"></i>
                                </a>
                            </template>
                            <template v-else>
                                {{ item[field] && typeof item[field].toString === 'function' ? item[field].toString() : item[field] }}

                                <span v-if="!item[field]">&nbsp;</span>
                            </template>
                        </td>
                    </tr>
                </template>
            </template>
        </table>


    </div>
</template>

