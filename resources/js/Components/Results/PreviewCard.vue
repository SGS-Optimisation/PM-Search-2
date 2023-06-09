<script lang="ts" setup>
import moment from 'moment';
import {computed} from "@vue/reactivity";
import InnerImageZoom from 'vue-inner-image-zoom';
import {onMounted, ref} from "vue";
import Checkbox from 'primevue/checkbox';
import GenericField from "@/Components/Search/GenericField.vue";

const emit = defineEmits(['on-click-view', 'on-click-quick-view', 'selection-changed']);

const props = defineProps({
    item: {type: Object, required: true},
    backgroundMode: {type: String, default: 'cover'},
    imageSize: {type: String, default: 'sml'},
    gridSize: {type: Number, default: 3},
    extraClass: {type: String, default: ''},
});

const imageAvailable = ref(true);

const backgroundImage = computed(() => {
    return props.item['image_' + props.imageSize];
})

onMounted(() => {
    for (const field in props.item) {
        if (field === 'printer_name' && Array.isArray(props.item[field])) {
            const result = props.item[field]
            props.item[field] = result.toString()
        }
    }
})

const thumb = computed(() => {
    return props.item['image_sml'];
})

const highres = computed(() => {
    return props.item['image_lrg'];
})

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

const bookedDate = computed(() => {
    return moment(props.item.booked_date).format('YYYY-MM-DD');
})

const height = computed(() => {
    return {
        1: 'screen',
        2: '72',
        3: '64',
        4: '48',
        5: '32'
    }[props.gridSize]
})

function limitedValue(str, len = 40) {
    let adjustedLen = Math.floor(len/props.gridSize) + (10 * 2/props.gridSize)
    return str.length > adjustedLen ? str.substring(0, adjustedLen) + '...' : str;
}

function isLimited(str, len = 40) {
    return str.length > len;
}

const colClass = computed(() => {
    return props.gridSize === 5 ? 'col-2' : 'col-' + (12 / props.gridSize) + ' ' + props.extraClass;
})

function isEcode(str) {
    return str.match(/^[0-9]{6}[eE]$/);
}

const ecode = computed(() => {
    return parseInt(props.item.pcm_type_profile_name.toString().toLowerCase().replace('e', ''));
})

const checked = ref(false);

function clearSelection() {
    checked.value = false;
}

defineExpose({
    clearSelection
})

</script>

<template>
    <!-- Article -->
    <div class="p-2" :class="colClass">

        <article class="hover:drop-shadow-2xl p-2 surface-border surface-card border-round relative shadow-sm">
            <Checkbox v-model="checked" :binary="true" @click="$emit('selection-changed', item)"/>

            <div class="ribbon z-0" v-if="item.tag !== '' && item.tag !== 'text_search' && item.tag !== 'textsearch'">
                <span :title="item.tag"
                      class="text-xxs text-white font-semibold text-center"
                      :class="{
                    'bg-indigo-400': item.tag === 'barcode_search',
                    'bg-yellow-400' : item.tag === 'doc_search',
                    'bg-red-400' : item.tag === 'visual_search',
                    'bg-blue-400' : item.tag === 'entity_search'
                }">
                    {{ item.tag.substring(0, 3) }}
                </span>
            </div>

            <a @click.prevent="$emit('on-click-view', item)"
               v-tooltip.top="'Open fullscreen image'"
               class="flex justify-center block overflow-hidden"
               :class="'h-'+height" href="#">

                <template v-if="imageAvailable">
                    <inner-image-zoom
                        v-if="backgroundMode==='zoom'"
                        :src="zoomSrc"
                        :zoomSrc="highres"
                        zoomType="hover"
                        className="my-auto"
                    />
                    <img v-else :src="backgroundImage" loading="lazy" class="responsive"
                         :style="imageStyle" @error="imageAvailable = false"
                    />
                </template>
                <template v-else>
                    <p class="text-center align-middle my-auto text-gray-200">
                        🚫<br>
                        The Thumbnail for this job was not captured as part of the standard workflow
                    </p>
                </template>
            </a>


            <!-- External links -->
            <div class="flex px-2 mt-2 justify-between leading-none">
                <GenericField field="formatted_job_number" :value="item['formatted_job_number']"/>

                <div class="flex flex-col text-right">

                    <template v-if="isEcode(item.pcm_type_profile_name)">
                        <GenericField field="pcm_type_profile_name" :value="item['pcm_type_profile_name']"/>
                    </template>

                    <template v-if="item.printer_spec_url">
                        <GenericField field="printer_spec_url" :value="item['printer_spec_url']" icon="🖨"/>
                    </template>
                </div>
            </div>

            <!-- main content -->
            <div class="flex flex-col items-stretch cursor-pointer"
                 v-tooltip.bottom="'Open quick view'"
                 @click="$emit('on-click-quick-view', item)">
                <header class="flex items-center justify-between leading-tight px-2 mt-2">
                    <h1 class="text-md">
                        <span title="Simplified Group Name">{{ item.simplified_group_name }}</span>
                    </h1>
                </header>

                <footer class="flex items-center justify-between leading-none text-xs px-2">
                    <div class="md:grid grid-flow-row auto-rows-max gap-y-1">
                        <span title="Brand">{{ item.brand }}</span>
                        <span title="Variety">{{ item.variety }}</span>
                        <span v-if="item.number_of_colors" title="Number of Colors"><i class="pi pi-palette"></i> {{ item.number_of_colors }}</span>
                    </div>

                    <div class="md:grid grid-flow-row auto-rows-max gap-y-1 text-right">
                        <template v-if="!isEcode(item.pcm_type_profile_name)">
                            <span class="bg-blue-100 px-1" :title="'Profile Name' + (isLimited(item.pcm_type_profile_name) ? ': ' + item.pcm_type_profile_name : '')">
                                {{ limitedValue(item.pcm_type_profile_name, 20) }}
                            </span>
                        </template>

                        <span v-if="item.customer_design_ref" title="Design / End User Reference">
                            <i class="text-xs pi pi-ticket"></i>
                            {{ item.customer_design_ref }}
                        </span>

                        <span title="Print Process">{{ item.print_process }}</span>

                        <span title="Booked Date" class="text-xs whitespace-nowrap">&#128467; {{ bookedDate }}</span>
                    </div>
                </footer>
            </div>

        </article>
    </div>
    <!-- END Article -->
</template>

