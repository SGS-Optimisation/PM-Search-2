<script setup>

import {computed} from "@vue/reactivity";

const props = defineProps({
    field: {
        type: String,
        required: true
    },
    value: {
        required: false,
    },
    icon: {
        type: String,
        required: false,
        default: null
    },
})

const computedValue = computed(() => {
    return Object.values(props.value).map(function(k) { return k }).join(" , ")
})

function isEcode(str) {
    return str.match(/^[0-9]{6}[eE]$/);
}

function ecode(str) {
    return parseInt(str.toString().toLowerCase().replace('e', ''));
}

function limitedValue(str) {
    return str.length > 40 ? str.substring(0, 40) + '...' : str;
}

function isLimited(str) {
    return str.length > 40;
}

</script>

<template>
    <span>{{icon ? icon + ' ' : ''}}
        <template v-if="field==='formatted_job_number'">
            <a class="text-blue-500 hover:text-blue-700" target="_blank"
               :href="'https://pm.mysgs.sgsco.com/Job/' + value"
               v-tooltip="'Open job in MySGS'"
            >
                {{ value }} <i class="text-xs pi pi-external-link"></i>
            </a>
        </template>
        <template v-else-if="field==='pcm_type_profile_name' && isEcode(value)">
            <a class="text-blue-500 hover:text-blue-700 whitespace-nowrap" target="_blank"
               :href="'https://cmf.sgsco.com/color-profile/' + ecode(value)"
               v-tooltip="'Open color profile in CMF'"
            >
                {{ value }} <i class="text-xs pi pi-external-link"></i>
            </a>
        </template>
        <template v-else-if="field==='file_location'">
            <a class="text-blue-500 hover:text-blue-700" target="_blank"
               :href="'cejf://?openjoburl=smb:' + value.replace(/\\/g, '/')"
               v-tooltip="'Open with CEJF'"
            >
                {{ value }} <i class="text-xs pi pi-external-link"></i>
            </a>
        </template>
        <template v-else-if="value && field==='printer_spec_url'">
            <a class="text-blue-500 hover:text-blue-700 whitespace-nowrap" target="_blank"
               :href="'https://automation.sgsco.com/prepress/details?id=' + value"
               v-tooltip="'Open in Printer Spec Database'"
            >
                {{ value }} <i class="text-xs pi pi-external-link"></i>
            </a>
        </template>
        <template v-else-if="typeof value==='object'">
             {{ computedValue }}
        </template>
        <template v-else>
            {{ value }}
        </template>
    </span>
</template>
