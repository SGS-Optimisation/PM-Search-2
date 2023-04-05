<script lang="ts" setup>
import {useForm, usePage} from "@inertiajs/vue3";
import {defineComponent, watch, ref, reactive} from "vue";
import AppLayout from '@/Layouts/AppLayout.vue';
import {computed} from "@vue/reactivity";
import VuePictureCropper, {cropper} from 'vue-picture-cropper'
import FileUpload from 'primevue/fileupload';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import { router } from '@inertiajs/vue3'
import route from "ziggy-js";

const isShowModal = ref<boolean>(false)
const uploadInput = ref<HTMLInputElement | null>(null)
const pic = ref<string>('')
const result = reactive({
    dataURL: '',
    blobURL: '',
    sourceURL: '',
    sourceFile: '',
})
const form = useForm({
    document: <any>Object,
    search_techs: ['visualsearch'],
})
const searchTechs = ref([
    { name: 'barcodesearch', label: 'Barcode'},
    { name: 'visualsearch', label: 'Visual'}
])

const hasDocument = computed(() => {
    return form.document.name !== undefined && form.document.name !== 'Object'
}) ;


function selectFile(e: Event) {
    // Reset last selection and results
    pic.value = ''
    result.dataURL = ''
    result.blobURL = ''

    // Get selected files
    const files = e.files
    //if (!files || !files.length) return

    // Convert to dataURL and pass to the cropper component
    const file = files[0]
    result.sourceFile = file
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = () => {
        // Update the picture source of the `img` prop
        pic.value = String(reader.result)
        result.sourceURL = String(reader.result)

        // Show the modal
        isShowModal.value = true

        // Clear selected files of input element
        if (!uploadInput.value) return
        uploadInput.value.value = ''
    }
}

async function getResult() {
    if (!cropper) return
    const base64 = cropper.getDataURL()
    const blob: Blob | null = await cropper.getBlob()
    if (!blob) return

    name

    const file = await cropper.getFile({
        fileName: result.sourceFile.name + '_' + Date.now()
    })

    console.log({base64, blob, file})
    result.dataURL = base64
    result.blobURL = URL.createObjectURL(blob)
    form.document = file
    isShowModal.value = false
}

async function noCrop() {

    form.document = result.sourceFile;
    /*this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
    this.$refs.cropper.getCroppedCanvas().toBlob(blob => {
        this.form.document = new File([blob], "[Cropped] " + this.filename);
    }, 'image/jpeg')*/
    result.blobURL = result.sourceURL;
    isShowModal.value = false
}

/**
 * Clear the crop box
 */
function clear() {
    if (!cropper) return
    cropper.clear()
}

/**
 * Reset the default cropping area
 */
function reset() {
    if (!cropper) return
    cropper.reset()
}

function rotate() {
    if (!cropper) return
    cropper.rotate(90)
}

function submitSearch() {
    //router.post(route('image-search'), form)
    form.post(route('image-search'));

    /*form.transform((data) => ({
        ...data,
        search_techs: () => {
            let techs = {}
            for(let tech of searchTechs.value) {
                techs[tech.name] = data.search_techs.includes(tech.name) ? 'Y' : 'N'
            }
            console.log(techs)
            return techs;
        }
    })).post(route('image-search'))*/
}

</script>

<template>
    <AppLayout title="Image Search">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Image Search
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                    <div class="flex flex-col justify-center py-6">

                        <div class="flex justify-center">
                            <FileUpload mode="basic" name="imageSearch" ref="uploadInput"
                                        :url="route('image-search')"
                                        chooseLabel="Select Image"
                                        accept="image/*" :maxFileSize="1000000"
                                        @select="selectFile"/>

                            <div class="px-2">
                                <Button type="submit" @click="submitSearch"
                                        :disabled="!hasDocument"
                                        label="Search" severity="success"/>
                            </div>

                            <div class="px-2 flex flex-col">
                                <div v-for="tech of searchTechs" :key="tech.name" class="px-2">
                                    <Checkbox v-model="form.search_techs" :inputId="tech.name"
                                              name="search_tech" :value="tech.name"
                                              true-value="Y" false-value="N"/>

                                    <label class="ml-1 text-sm" :for="tech.name">{{ tech.label }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-center px-12 pt-2">
                            <img :src="result.blobURL"/>
                        </div>
                    </div>

                    <Dialog modal v-model:visible="isShowModal"
                            :style="{ width: '80vw' }">
                        <template #header>
                            <div class="w-full flex justify-between">
                                <div class="grid grid-cols-5 gap-1">
                                    <Button severity="secondary" size="small" @click="isShowModal = false"
                                            label="Cancel"/>

                                    <Button severity="info" size="small" @click="clear" label="Clear"/>
                                    <Button severity="info" size="small" @click="reset" label="Reset"/>
                                    <Button severity="info" size="small" @click="rotate" label="Rotate"/>

                                    <Button severity="success" size="small" @click="getResult" label="Crop"/>
                                </div>
                                <div class="mr-2">
                                    <Button severity="success" @click="noCrop" label="Use uncropped"/>
                                </div>
                            </div>


                        </template>
                        <VuePictureCropper
                            :boxStyle="{
                                width: '100%',
                                height: '100%',
                                backgroundColor: '#f8f8f8',
                                margin: 'auto',
                            }"
                            :img="pic"
                            :options="{
                                viewMode: 1,
                                dragMode: 'crop',
                                rotatable: true,
                            }"
                        />
                    </Dialog>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
