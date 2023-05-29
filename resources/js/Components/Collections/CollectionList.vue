<script lang="ts" setup>
import Button from "primevue/button";
import DataView from "primevue/dataview";
import InputText from "primevue/inputtext";
import {Link, router, useForm} from "@inertiajs/vue3";
import Tag from "primevue/tag";
import {configStore} from "@/stores/config-store";
import route from "ziggy-js";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";
import {ref} from "vue";

const props = defineProps({
    collections: Array,
    perPage: {
        type: Number,
        default: 5
    },
    detailed: {
        type: Boolean,
        default: false
    }
})

const toast = useToast();
const confirm = useConfirm();

const editing = ref(false);
const editingCollection = ref(null);

function formatAdvancedSearchField(field, value) {
    if (!configStore.getAdvancedSearchFields().hasOwnProperty(field)) {
        return field + ':' + value;
    }
    const config = configStore.getAdvancedSearchFields()[field];

    if (configStore.getAdvancedSearchFields()[field].type === 'date') {
        value = JSON.parse(value);
        value = (new Date(value[0])).toLocaleDateString() + ' - ' + (new Date(value[1])).toLocaleDateString();
    }

    return config.key + ':' + value;
}

function editCollection(collection) {
    editingCollection.value = collection;
    editing.value = true;
}

const updateForm = useForm({
    id: null,
    name: null,
})

function saveCollection(collection) {
    updateForm.id = collection.id;
    updateForm.name = collection.name;
    updateForm.put(route('collections.update', collection.id), {
        preserveScroll: true,
        onSuccess: () => {
            updateForm.reset();
            editing.value = false;
            editCollection.valuet = null;
            toast.add({severity: 'success', summary: 'Confirmed', detail: 'Collection updated', life: 3000});
            router.reload();
        },
    })
}

const deleteForm = useForm({
    id: null,
})

const confirmDelete = (event, collection) => {
    confirm.require({
        target: event.currentTarget,
        message: 'Are you sure you want to proceed? ',
        icon: 'pi pi-exclamation-triangle',
        acceptClass: 'p-button-danger',
        accept: () => {
            console.log('collection', collection);
            deleteForm.id = collection.id;
            deleteForm.delete(route('collections.delete', collection.id), {
                preserveScroll: true,
                onSuccess: () => {
                    deleteForm.reset();
                    toast.add({severity: 'success', summary: 'Confirmed', detail: 'Collection deleted', life: 3000});
                    router.reload();
                },
            })

        },
        reject: () => {
            toast.add({severity: 'info', summary: 'Cancelled', detail: 'Action cancelled', life: 3000});
        }
    });
};


</script>

<template>
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg pt-2 pb-12 px-2">

        <DataView :value="collections" paginator :rows="perPage">
            <template #header>
                <h4 class="h4 font-bold text-lg">My Collections</h4>
            </template>

            <template #list="slotProps">
                <div class="col-12 flex border border-gray-100 hover:bg-gray-50 item-row">
                    <div :class="{'col-6': detailed, 'col-12': !detailed}">
                        <div class="flex flex-column xl:flex-row xl:align-items-start pb-1 gap-4 ">

                            <template v-if="editing && editingCollection.id === slotProps.data.id">
                                <div class="flex flex-row">
                                    <InputText v-model="slotProps.data.name"/>
                                    <Button @click="saveCollection(slotProps.data)" icon="pi pi-check"
                                            class="p-button-rounded p-button-success ml-1 "/>
                                </div>
                            </template>

                            <Link v-else class="text-blue-500 hover:text-blue-700 underline"
                                  :href="route('collections.show', [slotProps.data.id])">
                                {{ slotProps.data.name }}
                            </Link>
                        </div>
                    </div>
                    <div class="col-6 flex justify-content-between" v-if="detailed">
                        <div class="flex flex-column xl:flex-row xl:align-items-start pb-1 gap-4">
                            <span>
                                <i class="pi" :class="{
                                    'pi-language': slotProps.data.search_mode === 'text',
                                    'pi-image': slotProps.data.search_mode === 'image',
                                }"
                                   :title="slotProps.data.search_mode === 'text' ? 'Text search' : 'Image search'"
                                ></i>
                            </span>
                            <span v-if="slotProps.data.search_mode === 'text'">
                                <template v-if="slotProps.data.search_data.hasOwnProperty('search_string')">
                                    {{
                                        slotProps.data.search_data.search_string.join(' ' + slotProps.data.search_data.operator.toUpperCase() + ' ')
                                    }}
                                </template>
                                <template v-else>
                                    {{
                                        slotProps.data.search_data.textsearchstrings.join(' ' + slotProps.data.search_data.textsearchoperator.toUpperCase() + ' ')
                                    }}

                                    <template v-if="slotProps.data.search_data.hasOwnProperty('fields')">
                                        <Tag v-for="(value, field) in slotProps.data.search_data.fields" :key="field"
                                             class="ml-1 mb-1"
                                             severity=""
                                             :value="formatAdvancedSearchField(field, value)" rounded></Tag>
                                    </template>
                                </template>
                            </span>
                            <span v-else>
                                {{ slotProps.data.working_data.original_filename }}
                            </span>
                        </div>
                        <div class="flex actions">
                            <Button severity="info" icon="pi pi-pencil" class="mx-2" size="sm"
                                    @click="editCollection(slotProps.data)"
                            ></Button>
                            <Button severity="danger" icon="pi pi-trash" class="mx-2" size="sm"
                                    @click="confirmDelete($event, slotProps.data)"
                            ></Button>
                        </div>
                    </div>
                </div>
            </template>


        </DataView>
    </div>
</template>

<style lang="scss" scoped>
.item-row {
    .actions {
        visibility: hidden;
    }

    &:hover .actions {
        visibility: visible;
    }
}
</style>
