import {defineStore} from 'pinia'
import {ref} from "vue";

export const userPreferencesStore = defineStore(
    'main',
    () => {
        const gridSize = ref(40)
        const imageSize = ref('sml')
        const backgroundMode = ref('cover')
        const perPage = ref(40)

        return {gridSize, imageSize, backgroundMode, perPage}
    },
    {
        persist: true,
    },
)
