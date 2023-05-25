<script lang="ts" setup>
import {Head, useForm, usePage} from "@inertiajs/vue3";
import {defineComponent, watch, ref, reactive, onMounted} from "vue";
import {computed} from "@vue/reactivity";
import VuePictureCropper, {cropper} from 'vue-picture-cropper'
import JetFormSection from '@/Components/Jetstream/FormSection.vue';
import FileUpload from 'primevue/fileupload';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import ProgressSpinner from "primevue/progressspinner";
import {router} from '@inertiajs/vue3'
import route from "ziggy-js";
import Image from 'primevue/image';
import axios from "axios";

const props = defineProps({
    initialValues: {
        type: Object,
        required: false,
        default: null
    },
    compactMode: {
        type: Boolean,
        required: false,
        default: false,
    }
})

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
    {name: 'barcodesearch', label: 'Barcode'},
    {name: 'visualsearch', label: 'Visual'}
])

const isConverting = ref(false);
const hasDocument = computed(() => {
    return form.document.name !== undefined && form.document.name !== 'Object'
});

onMounted(() => {
    if (props.initialValues) {
        initRefine();
    }
})


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

    console.log('uploaded file', file);

    if (file.type === 'application/pdf') {
        let formData = new FormData();
        formData.append('document', file);
        isConverting.value = true;
        axios.post(route('api.pdf-to-image'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            },
            responseType: "json",
        }).then(({data}) => {
            fetch(data.path).then(res => res.blob()).then(blob => {
                const image = new File([blob], file.name, {type: 'image/jpeg'});
                isConverting.value = false;
                loadImage(image);
            });
        });
    } else {
        loadImage(file);
    }
}

function loadImage(file, showModal = true) {
    result.sourceFile = file
    const reader = new FileReader()
    reader.readAsDataURL(file)
    reader.onload = () => {
        // Update the picture source of the `img` prop
        pic.value = String(reader.result)
        result.sourceURL = String(reader.result)

        // Show the modal
        isShowModal.value = showModal;

        // Clear selected files of input element
        if (!uploadInput.value) return
        uploadInput.value.value = ''
    }
}

function initRefine(showModal = false) {
    fetch(props.initialValues.image_path).then(res => res.blob()).then(blob => {
        const file = new File([blob], props.initialValues.image_path.split('/').pop(), {type: 'image/jpeg'});
        loadImage(file, showModal);
    })
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
    form.post(route('image-search'));
}

const hasSearchedImage = computed(() => {
    return props.initialValues !== null && props.initialValues.hasOwnProperty('image_path');
})

const isCurrentSearchOpen = ref(false);

const currentSearchImage = computed(() => {
    if (isCurrentSearchOpen.value) {
        return props.initialValues.image_path;
    }
    return props.initialValues.thumb;
})

</script>

<template>

    <div>
        <jet-form-section @submitted="submitSearch" :compact-mode="compactMode" class="mb-4">
            <template #title>
                Image Search
            </template>
            <template #description>
                Search by file upload
            </template>
            <template #form>

                <div class="py-2 col-span-6">

                    <div class="flex justify-start">
                        <FileUpload mode="basic" name="imageSearch" ref="uploadInput"
                                    :url="route('image-search')"
                                    chooseLabel="Select File"
                                    accept="image/*,.pdf" :maxFileSize="10000000"
                                    @select="selectFile"/>


                        <div class="px-2 flex flex-col">
                            <div v-for="tech of searchTechs" :key="tech.name" class="px-2">
                                <Checkbox v-model="form.search_techs" :inputId="tech.name"
                                          name="search_tech" :value="tech.name"
                                          true-value="Y" false-value="N"/>

                                <label class="ml-1 text-sm" :for="tech.name">{{ tech.label }}</label>
                            </div>
                        </div>

                        <div class="px-2">
                            <Button type="submit" @click="submitSearch"
                                    :disabled="!hasDocument"
                                    label="Search" severity="success"/>
                        </div>
                        <Button v-if="hasSearchedImage" @click="initRefine(true)">
                            Refine
                        </Button>
                    </div>
                    <template v-if="isConverting">
                        <ProgressSpinner/>
                        Converting PDF to Image...
                    </template>
                    <div class="flex justify-center px-12 pt-2">
                        <img :src="result.blobURL"/>
                    </div>
                </div>


                <template v-if="props.initialValues && props.initialValues.image_path">
                    <div style="text-align: center">
                        <Image :src="currentSearchImage" alt="" preview
                               @show="isCurrentSearchOpen = true"
                               @hide="isCurrentSearchOpen = false"
                        />
                    </div>
                </template>

            </template>
        </jet-form-section>

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
</template>

<style scoped>
.p-image {
    height: 6rem;
    width: 6rem;
    overflow: hidden;
    display: inline-block;
}
</style>
