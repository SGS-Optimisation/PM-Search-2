import {defineStore} from 'pinia'
import {ref} from "vue";

export const userPreferencesStore = defineStore(
    'main',
    () => {
        const gridSize = ref(40)
        const imageSize = ref('sml')
        const backgroundMode = ref('cover')
        const perPage = ref(40)

        const sortOrder = ref(-1)
        const sortField = ref('Score')

        return {
            gridSize, imageSize, backgroundMode, perPage,
            sortOrder, sortField,
        }
    },
    {
        persist: true,
    },
)
