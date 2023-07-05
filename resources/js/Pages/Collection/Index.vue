<script lang="ts" setup>
import {Head} from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import CollectionList from "@/Components/Collections/CollectionList.vue";

const props = defineProps({
    collections: {type: Array, required: true},
})

defineOptions({layout: AppLayout})


</script>

<template>
    <Head title="My Collections"/>

    <div class="max-w-7xl mx-auto mt-5">
        <CollectionList :collections="collections" :per-page="20" :detailed="true"/>
    </div>

</template>

<style lang="scss" scoped>

</style>
