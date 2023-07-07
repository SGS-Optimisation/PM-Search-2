import {defineStore} from 'pinia'
import {ref} from "vue";

export const userPreferencesStore = defineStore(
    'main',
    () => {
        const gridSize = ref(4)
        const imageSize = ref('sml')
        const backgroundMode = ref('fit')
        const perPage = ref(25)
        const layout = ref('grid');

        const sortOrder = ref(-1)
        const sortField = ref('score')

        const selectedTaxonomy = ref([]);

        return {
            gridSize, layout,
            imageSize, backgroundMode, perPage,
            sortOrder, sortField,
            selectedTaxonomy
        }
    },
    {
        persist: true,
    },
)
