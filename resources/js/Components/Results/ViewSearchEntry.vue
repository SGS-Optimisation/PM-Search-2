<script lang="ts" setup>
import Fieldset from 'primevue/fieldset';
import Image from 'primevue/image';
import ProgressSpinner from 'primevue/progressspinner';
import VueLoadImage from 'vue-load-image'
import {configStore} from "@/stores/config-store";
import {ref, onMounted, computed, onUpdated} from "vue";
import {snakeCase} from "lodash";
import GenericField from "@/Components/Search/GenericField.vue";
import job_api from "@/Api/Photon/job_api";
import _ from "lodash";

const emit = defineEmits(['next', 'prev', 'exit']);

const props = defineProps({
    entry: {
        type: Object,
        required: true
    },
    groupedSortedConfig: {
        type: Object,
        required: true
    },
})

const jobColours = ref([]);


const modal = ref();

onMounted(() => {
    modal.value.focus()
    getJobColours();
})

onUpdated(() => {
    jobColours.value = [];
    getJobColours();
})

function getJobColours() {
    job_api.jobColour(props.entry['formatted_job_number']).then((response) => {
        console.log(response.data);
        jobColours.value = _.sortBy(response.data, 'seq');
    })
}

const fields_config = computed(() => {
    return configStore.getFields();
})

const bridge_fields = computed(() => {
    return configStore.getBridgeFields();
})

const grouped_fields = computed(() => {
    return configStore.getGroupedFields();
})

const table_fields = computed(() => {
    return configStore.getTableFields();
})

const thumb = computed(() => {
    return props.entry['image_sml'];
})

const highres = computed(() => {
    return props.entry['image_lrg'];
})

const backgroundStyle = computed(() => {
    return {
        'background-size': 'contain',
        'background-repeat': 'no-repeat',
        'background-position': 'center center',
        'background-image': `url(${props.entry['image_sml']})`,
    }
})

const titleCase = (str) => window.titleCase(str);
const snakeToTitle = (str) => window.snakeToTitle(str);

function isEcode(str) {
    return str.match(/^[0-9]{6}[eE]$/);
}

const ecode = computed(() => {
    return parseInt(props.entry.pcm_type_profile_name.toString().toLowerCase().replace('e', ''));
})

function exit() {
    emit('exit');
}

function prev() {
    console.log('prev');
    emit('prev');
}

function next() {
    emit('next');
    console.log('next');
}
</script>

<template>
    <div v-if="entry" @keydown.esc="exit" tabindex="0" autofocus ref="modal"
         @keyup.left="prev"
         @keyup.right="next"
    >

        <div class="modal-body">
            <div class="mx-auto max-w-[90%] h-full max-h-full" id="job-image">
                <vue-load-image>
                    <template v-slot:image>
                        <div :data-src="highres" class="text-center">
                            <!--                        <img :src="highres"/>-->
                            <Image :src="highres" alt="" imageClass="max-h-screen" preview/>
                        </div>
                    </template>
                    <template v-slot:preloader>
                        <div class="flex flex-col h-full justify-items-center items-center" :style="backgroundStyle">

                            <ProgressSpinner/>
                        </div>
                    </template>
                    <template v-slot:error>Failed to load image</template>
                </vue-load-image>
                <!--                <Image :src="highres" alt="" preview/>-->
                <!--                <inner-image-zoom
                                    :src="highres"
                                    className="max-h-screen"
                                    zoomType="click"
                                />-->

            </div>

            <template v-for="(sortedConfig, section) in groupedSortedConfig">
                <Fieldset :legend="section" :id="snakeCase(section)" class="mt-3">
                    <template #legend>
                        <div class="flex align-items-center text-gray-50">
                            <span class="pi pi-user mr-2"></span>
                            <span class="font-bold">{{ section }}</span>
                        </div>
                    </template>

                    <div class="text-sm md:grid md:grid-flow-row-dense md:grid-cols-6">
                        <template v-for="(params, field) in sortedConfig">
                            <template v-if="params.display && !bridge_fields.includes(field)">
                                <div class="p-2 border border-bluegray-50 even:bg-neutral-50 even:border-white">
                                    <p class="font-bold">
                                        {{ params.hasOwnProperty('title') ? params.title : titleCase(field) }}</p>
                                    <div class="break-all">
                                        <GenericField :field="field" :value="entry[field]"/>
                                    </div>
                                </div>

                            </template>
                        </template>
                    </div>

                    <template v-for="table in table_fields">
                        <template v-if="table.section === section">
                            <div class="flex mt-3 justify-content-around">
                                <table class="border-collapse table-auto w-50 text-sm" :id="snakeCase(table.name)">
                                    <thead>
                                    <tr>
                                        <th colspan="8">Colours From File</th>
                                    </tr>
                                    <tr>
                                        <template v-for="field in table.fields">
                                            <th class="border-b dark:border-slate-600 font-medium px-4 pl-3 pt-0 pb-1 text-slate-400 text-left">
                                                {{
                                                    fields_config[field].hasOwnProperty('title') ? fields_config[field].title : titleCase(field)
                                                }}
                                            </th>
                                        </template>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <template v-for="(row, index) in entry[table.fields[0]]">
                                        <tr>
                                            <template v-for="field in table.fields">
                                                <td class="border-b border-slate-300 p-1 pl-3 text-slate-500">
                                                    <div class="flex flex-row align-middle">
                                                        <template v-if="field === 'hex_colors'">
                                                            <div class="h-5 w-5 min-w-9 mr-1"
                                                                 :style="'background-color:' + entry[field][index]">

                                                            </div>
                                                        </template>
                                                        {{
                                                            entry[field][index] !== undefined ? entry[field][index] : ''
                                                        }}
                                                    </div>
                                                </td>
                                            </template>
                                        </tr>
                                    </template>
                                    </tbody>
                                </table>

                                <template v-if="section === 'Colour Details' && jobColours.length">
                                    <table class="border-collapse table-auto ml-20 text-sm max-w-[50%]">
                                        <thead>
                                        <tr>
                                            <th colspan="8">Colours From MySGS</th>
                                        </tr>
                                        <tr>
                                            <template v-for="field in Object.keys(jobColours[0])">
                                                <th class="border-b dark:border-slate-600 font-medium px-4 pl-3 pt-0 pb-1 text-slate-400 text-left">
                                                    {{ snakeToTitle(field) }}
                                                </th>
                                            </template>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="row of jobColours" :data-row="JSON.stringify(row)">
                                            <td v-for="field in row" class="border-b border-slate-300 p-1 pl-3 text-slate-500" >
                                                <span class="align-middle" :data-field="field">
                                                    {{ field }}
                                                </span>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </template>
                            </div>
                        </template>
                    </template>
                </Fieldset>
            </template>


            <Fieldset v-for="group in grouped_fields" :id="snakeCase(group.name)">
                <template #legend>
                    <div class="flex align-items-center text-gray-50">
                        <span class="pi pi-user mr-2"></span>
                        <span class="font-bold">{{ group.name }}</span>
                    </div>
                </template>

                <div class="md:grid md:grid-cols-6">
                    <div v-for="field in group.fields">
                        <div class="px-4">
                            <p class="font-bold">
                                {{
                                    fields_config[field].hasOwnProperty('title') ?
                                        fields_config[field].hasOwnProperty('title')
                                        : titleCase(field)
                                }}
                            </p>
                            <ul v-if="fields_config[field].response_type === 'list'">
                                <template v-for="(row) in entry[field]">
                                    <li>{{ row }}</li>
                                </template>
                            </ul>
                            <p v-else>
                                {{ entry[bconf] }}
                            </p>
                        </div>
                    </div>
                </div>

            </Fieldset>

        </div>

    </div>
</template>

<style>

fieldset.p-fieldset legend.p-fieldset-legend {
    @apply bg-blue-500;
}

.p-image-toolbar {
    z-index: 9999;
}

.p-image-preview-container {
    @apply overflow-hidden;
}

.p-image-preview-container:hover > .p-image-preview-indicator {
    background: none;
}

</style>
