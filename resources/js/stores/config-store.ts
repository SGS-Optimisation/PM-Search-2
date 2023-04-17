import {PersistentStore, Store, PersistentLocalStore} from "./main";
import {CONFIG_STORE_NAME} from "./store-names";
import _ from 'lodash';

interface Config extends Object {
    fields: Array<any>,
    bridgeFields: Array<any>,

    autocompleteSuggester: string,

    advancedSearchFields: Array<any>,
}

class ConfigStore extends PersistentLocalStore<Config> {
    protected data(): Config {
        return {
            fields: [],
            bridgeFields: [],
            autocompleteSuggester: '',
            advancedSearchFields: [],
        };
    }

    setFields(fields) {
        this.state.fields = fields;
    }

    getFields() {
        return this.state.fields;
    }

    setBridgeFields(fields) {
        this.state.bridgeFields = fields;
    }

    getBridgeFields() {
        return this.state.bridgeFields;
    }

    setAutocompleteSuggester(suggester) {
        this.state.autocompleteSuggester = suggester;
    }

    getAutocompleteSuggester() {
        return this.state.autocompleteSuggester;
    }

    setAdvancedSearchFields(fields) {
        this.state.advancedSearchFields = fields;
    }

    getAdvancedSearchFields() {
        return this.state.advancedSearchFields;
    }

}

export const configStore = new ConfigStore(CONFIG_STORE_NAME);
