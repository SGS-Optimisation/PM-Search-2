<script lang="ts" setup>
import Fieldset from "primevue/fieldset";
import Button from "primevue/button";
import {computed} from "vue";
import {configStore} from "@/stores/config-store";

const props = defineProps({
    entry: {
        type: Object,
        required: true
    },
    config: {
        type: Object,
        required: true
    }
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

const ecode = computed(() => {
    return parseInt(props.entry.pcm_type_profile_name.toString().toLowerCase().replace('e', ''));
})

const titleCase = (str) => window.titleCase(str);

</script>

<template>
    <div>
        <Button @click="openFullView" label="Full View" class="mb-4 w-full"/>
        <table class="table table-responsive border text-sm">
            <template v-for="(params, field) in sortedConfig">
                <template v-if="params.display && !bridge_fields.includes(field)">
                    <tr class="border even:bg-blue-100">
                        <td class="font-bold text-xs whitespace-nowrap">{{ titleCase(field) }}</td>
                        <td class="md:grow">
                            <template v-if="field==='formatted_job_number'">
                                <a class="text-blue-500 hover:text-blue-700" target="_blank"
                                   :href="'https://pm.mysgs.sgsco.com/Job/' + entry[field]"
                                >
                                    {{ entry[field] }}
                                </a>
                            </template>
                            <template v-else-if="field==='pcm_type_profile_name' && isEcode(entry[field])">
                                <a class="text-blue-500 hover:text-blue-700" target="_blank"
                                   :href="'https://cmf.sgsco.com/color-profile/' + ecode"
                                   v-tooltip="'Open color profile in CMF'"
                                >
                                    {{ entry[field] }} <i class="text-xs pi pi-external-link"></i>
                                </a>
                            </template>
                            <template v-else>
                                {{ entry[field].toString() }}
                            </template>
                        </td>
                    </tr>
                </template>
            </template>
        </table>


    </div>
</template>

