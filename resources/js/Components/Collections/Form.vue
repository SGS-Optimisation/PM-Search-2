<script setup>
import {inject, onMounted, ref} from "vue";
import FormSection from "@/Components/Jetstream/FormSection.vue";
import InputLabel from "@/Components/Jetstream/InputLabel.vue";
import TextInput from "@/Components/Jetstream/TextInput.vue";
import InputError from "@/Components/Jetstream/InputError.vue";
import PrimaryButton from "@/Components/Jetstream/PrimaryButton.vue";
import ActionMessage from "@/Components/Jetstream/ActionMessage.vue";
import {useForm} from "@inertiajs/vue3";

const form = useForm({
    name: null,
    filters: {},
})

const search = ref();
const collection_id = ref();

const dialogRef = inject('dialogRef');
const addCollection = () => {
    console.log('adding collection');
    let formId = collection_id ? collection_id.value : search.value
    form.post(route('api.collections.create-from-search', {id: formId}),{
        preserveScroll: true,
        onSuccess: (data) => {
            dialogRef.value.close(data);

            form.reset();
        },
    });
}

onMounted(() => {
    if(dialogRef.value) {
        collection_id.value = dialogRef.value.data.collection_id;
        form.filters = dialogRef.value.data.filters;
    }
});

</script>

<template>
    <FormSection @submitted="addCollection">
        <template #form>

            <InputLabel>Name</InputLabel>
            <TextInput required type="text" v-model="form.name"/>
            <InputError :message="form.errors.name" class="mt-2"/>

        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </ActionMessage>

            <PrimaryButton>Save</PrimaryButton>
        </template>

    </FormSection>
</template>
