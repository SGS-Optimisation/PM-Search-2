<script lang="ts" setup>
import moment from 'moment';
import {computed} from "@vue/reactivity";
import {configStore} from "@/stores/config-store";
import _ from 'lodash';
import {ref} from "vue";
import Checkbox from 'primevue/checkbox';

const emit = defineEmits(['on-click-view', 'on-click-quick-view', 'selection-changed']);

const props = defineProps({
    item: {type: Object, required: true},
    backgroundMode: {type: String, default: 'cover'},
    imageSize: {type: String, default: 'sml'},
    gridSize: {type: Number, default: 3},
    extraClass: {type: String, default: ''},
    config: {
        type: Object,
        required: true
    }
});

const thumb = computed(() => {
    return props.item['image_sml'];
})

const highres = computed(() => {
    return props.item['image_lrg'];
})

const imageAvailable = ref(true);

const zoomSrc = computed(() => {
    return props.gridSize === 1 ? highres.value : thumb.value;
})

const imageStyle = computed(() => {
    if (props.backgroundMode === 'contain') {
        return "height: fit-content; margin: auto"
    }
    if (props.backgroundMode === 'cover') {
        return "height: auto; object-fit: cover"
    }
})

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

const splittedConfig = computed(() => {
    var chunks = _.chunk(configStore.getResultsRowFields(), Math.ceil(configStore.getResultsRowFields().length / 2));

    var result = [];
    for (const chunk in chunks) {
        var obj = {};
        for (const key in chunks[chunk]) {
            obj[chunks[chunk][key]] = props.config[chunks[chunk][key]];
        }
        result.push(obj);
    }

    return result;

});

const bridge_fields = computed(() => {
    return configStore.getBridgeFields();
})

const bookedDate = computed(() => {
    return moment(props.item.booked_date).format('YYYY-MM-DD');
})

function isEcode(str) {
    return str.match(/^[0-9]{6}[eE]$/);
}

const ecode = computed(() => {
    return parseInt(props.item.pcm_type_profile_name.toString().toLowerCase().replace('e', ''));
})

const titleCase = (str) => window.titleCase(str);

const checked = ref(false);

function clearSelection() {
    checked.value = false;
}

defineExpose({
    clearSelection
})

</script>

<template>

    <div class="col-12">
        <div class="bg-white flex flex-column xl:flex-row xl:align-items-start p-4 gap-4 mb-2">
            <Checkbox v-model="checked" :binary="true" @click="$emit('selection-changed', item)"/>
            <template v-if="imageAvailable">
                <div class="w-3">
                <img @click.prevent="$emit('on-click-view', item)"
                     class="responsive  max-h-32"
                     @error="imageAvailable = false"
                     v-tooltip.top="'Open fullscreen image'" :src="thumb" loading="lazy"
                />
                </div>
            </template>
            <template v-else>
                <p class="w-3 text-center align-middle my-auto text-gray-200">
                    🚫<br>
                    The Thumbnail for this job was not captured as part of the standard workflow
                </p>
            </template>
            <div v-for="(configCol, index) in splittedConfig"
                 class="flex flex-column sm:flex-row justify-content-between align-items-center xl:align-items-start flex-1 gap-4">
                <div class="flex flex-column align-items-center sm:align-items-start gap-3">
                    <div class="flex flex-col text-xs">
                        <div v-for="(params, field) in configCol">
                            <template v-if="params.display && !bridge_fields.includes(field)">
                                <span class="font-bold">{{ params.hasOwnProperty('title') ? params.title : titleCase(field) }}</span>:
                                <template v-if="field==='formatted_job_number'">
                                    <a class="text-blue-500 hover:text-blue-700" target="_blank"
                                       :href="'https://pm.mysgs.sgsco.com/Job/' + item[field]"
                                    >
                                        {{ item[field] }} <i class="text-xs pi pi-external-link"></i>
                                    </a>
                                </template>
                                <template v-else-if="field==='pcm_type_profile_name' && isEcode(item[field])">
                                    <a class="text-blue-500 hover:text-blue-700" target="_blank"
                                       :href="'https://cmf.sgsco.com/color-profile/' + ecode"
                                       v-tooltip="'Open color profile in CMF'"
                                    >
                                        {{ item[field] }} <i class="text-xs pi pi-external-link"></i>
                                    </a>
                                </template>
                                <template v-else-if="item[field] && field==='printer_spec_url'">
                                    <a class="text-blue-500 hover:text-blue-700" target="_blank"
                                       :href="'https://automation.sgsco.com/prepress/details?id=' + item[field]"
                                       v-tooltip="'Open color profile in Printer Specs'"
                                    >
                                        {{ item[field] }} <i class="text-xs pi pi-external-link"></i>
                                    </a>
                                </template>
                                <template v-else>
                                    {{ item[field] }}
                                </template>
                            </template>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>
