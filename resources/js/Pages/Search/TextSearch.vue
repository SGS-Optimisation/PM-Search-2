<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {defineComponent, watch, ref, reactive} from "vue";
import JetFormSection from '@/Components/Jetstream/FormSection.vue';
import JetInputError from '@/Components/Jetstream/InputError.vue';
//import VueTagsInput from '@johmun/vue-tags-input'; // npm i -D <name of package>
import route from "ziggy-js";
import InputSwitch from 'primevue/inputswitch';

const form = useForm({
    search_string: Array,
    operator: Boolean,
    isPhrase: Boolean,
    isOCR: Boolean,
    type: String,
})
const tags = ref([])
const resetOnSuccess = ref<boolean>(false)
const advanced = ref<boolean>(false)

function doSearch(this: any) {
    this.form.search_string = this.tags.map((item) => item.text);

    this.form.post(route('search.store'), {
        preserveScroll: true
    })
}

function onChangeFilterCondition(this: any, condition) {
    console.log('switching operator');
    this.operator = condition;

    this.form.operator = this.operator ? 'and' : 'or';
}

function onChangePhraseMode(this: any, state) {
    console.log('switching phrase mode');
    this.phraseMode = state;

    this.form.isPhrase = this.phraseMode;
}

function onChangeOCR(this: any, state) {
    console.log('switching ocr');
    this.ocr = state;

    this.form.isOCR = this.ocr;
}

function onChangeType(this: any, state) {
    console.log('switching type');
    this.type = state;

    this.form.type = this.type ? 'thumbnail' : 'mysgs';
}
</script>

<template>
    <div>
        <jet-form-section @submitted="doSearch" class="mb-4">
            <template #title>
                Text Search
            </template>
            <template #description>
                Enter up to 8 terms, separated by a comma
            </template>
            <template #form>
                <div class="col-span-3 ">
                    <div class="items-center">
                        <!-- <jet-label for="search_string" value="Query" />-->
                        <vue-tags-input
                            v-model="tag"
                            :tags="tags"
                            :max-tags=8
                            :save-on-key="saveOnKey"
                            :add-on-key="saveOnKey"
                            placeholder="Search"
                            @tags-changed="newTags => tags = newTags"
                        />
                        <!-- <jet-input-error :message="form.error('search_string')" class="mt-2"/> -->
                    </div>
                </div>

                <div class="">
                    <div class="flex flex-col">
                        <InputSwitch v-model="checked"
                                     @on-switch="onChangeFilterCondition"
                                     chooserMode=true
                                     :initial-state="form.operator==='and' ? '1' : '0'"
                                     left-text="OR"
                                     right-text="AND"/>
                    </div>
                </div>
                <div>
                    <div class="flex flex-row justify-center cursor-pointer text-xs bg-gray-100 z-50 mt-2 border border-gray-700 rounded-lg"
                         @click="advanced=!advanced">
                        Advanced
                        <template v-if="advanced">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-3" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 13l-7 7-7-7m14-8l-7 7-7-7"/>
                            </svg>
                        </template>
                        <template v-if="!advanced">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-3" fill="none" viewBox="0 0 24 24"
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 11l7-7 7 7M5 19l7-7 7 7"/>
                            </svg>
                        </template>
                    </div>
                </div>

                <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Search
                </jet-button>

                <div :class="['col-span-6', {'h-0' : !advanced}]">
                    <div :class="['grid grid-cols-8 gap-2 border border-gray-100 transition transition-all duration-100 ease-in-out transform',
                            {'opacity-0' : !advanced},
                            {'translate-y-12' : !advanced},
                            {'opacity-100' : advanced},
                    ]">
                        <div class="col-span-2">
                            <div class="mt-3">
                                <InputSwitch v-model="checked"
                                             @on-switch="onChangePhraseMode"
                                             name="Exact Phrase"
                                             :initial-state="form.isPhrase  ? '1' : '0'"
                                             left-text="No"
                                             right-text="Yes"/>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <div class="mt-3">
                                <InputSwitch v-model="checked"
                                             @on-switch="onChangeOCR"
                                             name="OCR"
                                             :initial-state="form.isOCR ? '1' : '0'"
                                             left-text="No"
                                             right-text="Yes"/>
                            </div>
                        </div>
                        <div class="col-span-2">
                            <div class="mt-3">
                                <InputSwitch v-model="checked"
                                             @on-switch="onChangeType"
                                             name="Type"
                                             chooserMode=true
                                             :initial-state="form.type==='thumbnail' ? '1' : '0'"
                                             left-text="MySGS"
                                             right-text="Thumbnail"/>
                            </div>
                        </div>
                    </div>
                </div>


            </template>

        </jet-form-section>
    </div>
</template>

<style scoped>

</style>
