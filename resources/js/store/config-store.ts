import {PersistentStore, Store, PersistentLocalStore} from "./main";
import {CONFIG_STORE_NAME} from "./store-names";
import _ from 'lodash';

interface Config extends Object {
    fields: Array<any>,
    bridgeFields: Array<any>,

    resultsPagePreferences: Object,
}

class ConfigStore extends PersistentStore<Config> {
    protected data(): Config {
        return {
            fields: [],
            bridgeFields: [],
            resultsPagePreferences: {}
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

    setResultsPagePreferences(preferences) {
        this.state.resultsPagePreferences = preferences;
    }

    getResultsPagePreferences() {
        return this.state.resultsPagePreferences;
    }


}

export const configStore = new ConfigStore(CONFIG_STORE_NAME);
