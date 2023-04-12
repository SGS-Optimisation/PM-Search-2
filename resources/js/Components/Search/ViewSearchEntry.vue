<script lang="ts" setup>
import Fieldset from 'primevue/fieldset';
import Image from 'primevue/image';
import ProgressSpinner from 'primevue/progressspinner';
import VueLoadImage from 'vue-load-image'
import {configStore} from "@/stores/config-store";
import {ref, onMounted, computed} from "vue";

const emit = defineEmits(['next', 'prev', 'exit']);

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

const modal = ref(null);

onMounted(() => {
    modal.value.focus()
})

const fields_config = computed(() => {
    return configStore.getFields();
})

const bridge_fields = computed(() => {
    return configStore.getBridgeFields();
})

const thumb = computed(() => {
    return props.entry['image_sml'];
})

const highres = computed(() => {
    return props.entry['image_lrg'];
})

const titleCase = (str) => window.titleCase(str);

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

        <div class="">
            <div class="mx-auto max-w-[90%]" id="job-image">
                <vue-load-image>
                    <template v-slot:image>
                        <img :src="highres"/>
                    </template>
                    <template v-slot:preloader>
                        <div class="flex h-screen w-screen justify-items-center items-center">
                            <ProgressSpinner/>
                        </div>
                    </template>
                    <template v-slot:error>Failed to load image</template>
                </vue-load-image>
                <!--<Image class="" :src="entry.image_lrg" alt=""/>-->
                <!--<vue-image-zoomer
                    :regular="thumb"
                    :zoom="highres"
                    :zoom-amount="5"
                    :click-zoom="true"
                    close-pos="top-right"
                    img-class="img-fluid"
                />-->
            </div>

            <Fieldset legend="Details" id="job-meta">
                <div class="text-sm grid grid-flow-col">
                    <template v-for="(params, field) in config">
                        <template v-if="params.display && !bridge_fields.includes(field)">
                            <div class="col-2"><b>{{ titleCase(field) }}</b>:
                                <template v-if="field==='formatted_job_number'">
                                    <a class="text-blue-500 hover:text-blue-700"
                                       :href="'https://pm.mysgs.sgsco.com/Job/' + entry[field]"
                                    >
                                        {{ entry[field] }}
                                    </a>
                                </template>
                                <template v-else>
                                    {{ entry[field] }}
                                </template>
                            </div>
                        </template>
                    </template>
                </div>
            </Fieldset>

            <div class="grid grid-flow-col mt-5">

                <Fieldset class="col-2" v-for="bconf in bridge_fields" :legend="titleCase(bconf)" :key="bconf">
                        <ul v-if="fields_config[bconf].response_type === 'list'">
                            <template v-for="(row) in entry[bconf]">
                                <li>{{ row }}</li>
                            </template>
                        </ul>
                        <p v-else>
                            {{ entry[bconf] }}
                        </p>
                </Fieldset>

            </div>
        </div>

    </div>
</template>

