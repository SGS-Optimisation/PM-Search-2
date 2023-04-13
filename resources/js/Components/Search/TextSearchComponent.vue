<script lang="ts" setup>
import {defineComponent, watch, ref, reactive} from "vue";
import JetFormSection from '@/Components/Jetstream/FormSection.vue';
import route from "ziggy-js";
import Button from 'primevue/button';
import SelectButton from 'primevue/selectbutton';
import Vue3TagsInput from 'vue3-tags-input';
import {useForm} from "@inertiajs/vue3";

const form = useForm({
    search_string: Array,
    operator: <String>'and',
})
const tags = ref([])
const resetOnSuccess = ref<boolean>(false)
const operatorOptions = ref([
    {name: 'And', value: 'and'},
    {name: 'Or', value: 'or'},
]);

function handleChangeTag(event) {
    console.log('tags', event);
    tags.value = event;
}

function doSearch(this: any) {

    form.transform(data => ({
        ...data,
        search_string: tags.value,
    })).post(route('search.store'), {
        preserveScroll: true
    })
}

</script>


<template>
    <div>
        <jet-form-section @submitted="doSearch" class="mb-4">
            <template #title>
                Text Search
            </template>
            <template #description>
                Search for text from the artwork pdf consisting of 400K plus files collected since July 2018 along with other parameters such as brand, variety, promotion, substrate, dieline.
            </template>
            <template #form>
                <div class="col-span-4 ">
                    <div class="items-center">
                        <!-- <jet-label for="search_string" value="Query" />-->
                        <vue3-tags-input
                            :tags="tags"
                            :max-tags=8
                            placeholder="Enter up to 8 terms, separated by a comma"
                            @on-tags-changed="handleChangeTag"
                        />
                        <!-- <jet-input-error :message="form.error('search_string')" class="mt-2"/> -->
                    </div>
                </div>

                <div class="">
                    <div class="flex flex-col">
                        <SelectButton v-model="form.operator" :options="operatorOptions"
                                      optionLabel="name" optionValue="value"/>
                    </div>
                </div>

                <Button type="submit" @click="doSearch"
                        label="Search" severity="success"/>


            </template>

        </jet-form-section>
    </div>
</template>
