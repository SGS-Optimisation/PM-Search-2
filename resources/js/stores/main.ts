import {reactive, readonly, watch, ref} from 'vue';
import {set, get} from 'idb-keyval';


export abstract class Store<T extends Object> {
    protected state: T;


    constructor(readonly storeName: string) {
        let data = this.data();
        this.state = reactive(data) as T;
    }

    protected abstract data(): T

    public getState(): T {
        return readonly(this.state) as T
    }

    /*
    Only added for compat purposes
     */
    async init(shouldReload = true) {
        // empty
    }
}

export abstract class PersistentStore<T extends Object> extends Store<T> {

    private isInitialized = ref(false);

    constructor(readonly storeName: string) {
        super(storeName);
    }

    async init() {
        if(this.isInitialized) {
            let stateFromIndexedDB: string = await get(this.storeName);
            if(stateFromIndexedDB) {
                Object.assign(this.state, JSON.parse(stateFromIndexedDB))
            }
            watch(() => this.state, (val) => set(this.storeName, JSON.stringify(val)), {deep: true})

            this.isInitialized.value = true;
        }
    }

    getIsInitialized(): Ref<boolean> {
        return this.isInitialized
    }
}

export abstract class PersistentLocalStore<T extends Object> extends Store<T> {

    private isInitialized = ref(false);

    constructor(readonly storeName: string) {
        super(storeName);
    }

    async init(shouldReload = true) {
        if(this.isInitialized) {
            let stateFromLocalStorage: string = localStorage.getItem(this.storeName);

            if(stateFromLocalStorage && shouldReload) {
                console.log('got data from storage for ' + this.storeName);
                Object.assign(this.state, JSON.parse(stateFromLocalStorage))
            } else {
                console.log('empty data for ' + this.storeName);
                localStorage.setItem(this.storeName, JSON.stringify({}));
            }
            watch(() => this.state, (val) => localStorage.setItem(this.storeName, JSON.stringify(val)), {deep: true})

            this.isInitialized.value = true;
        }
    }

    getIsInitialized(): Ref<boolean> {
        return this.isInitialized
    }
}
