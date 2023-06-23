import {PersistentStore, Store, PersistentLocalStore} from "./main";
import {ADVANCED_FILTERS_STORE_NAME} from "@/stores/store-names";

interface AdvancedFiltersStore extends Object {
    selectedTaxonomy: Object
}

class AdvancedFiltersStore extends Store<AdvancedFiltersStore> {
    protected data(): AdvancedFiltersStore {
        return {
            selectedTaxonomy: {},
        };
    }

    setSelectedTaxonomy(taxonomy) {
        this.state.selectedTaxonomy = taxonomy;
    }

    getSelectedTaxonomy() {
        return this.state.selectedTaxonomy;
    }
}

export const advancedFiltersStore = new AdvancedFiltersStore(ADVANCED_FILTERS_STORE_NAME);
