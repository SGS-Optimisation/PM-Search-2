<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {defineComponent, watch, ref, reactive, provide} from "vue";
import route from "ziggy-js";
import AppLayout from "@/Layouts/AppLayout.vue";
import TextSearchComponent from "@/Components/Search/TextSearchComponent.vue";

defineOptions({layout: AppLayout})
provide('filters', {});

</script>

<template>

    <div>
        <Head title="Text Search"/>
        <TextSearchComponent/>
    </div>
</template>

<style scoped>

</style>
