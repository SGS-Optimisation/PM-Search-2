<script lang="ts" setup>
import Fieldset from 'primevue/fieldset';
import {configStore} from "@/store/config-store";
import {ref, onMounted, computed} from "vue";
const emit = defineEmits(['next', 'prev', 'exit']);

defineProps({
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
        @keyup.37="prev"
        @keyup.39="next"
    >

        <img :src="entry.image_lrg" alt="">

        <Fieldset legend="Details">
            <div class="text-sm grid grid-cols-4">
                <template v-for="(params, field) in config">
                    <template v-if="params.display && !bridge_fields.includes(field)">
                        <div class="col-3"><b>{{ titleCase(field) }}</b>:
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

        <Fieldset v-for="bconf in bridge_fields" :legend="titleCase(bconf)" :key="bconf">
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
</template>

