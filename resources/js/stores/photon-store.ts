import {PersistentStore, Store, PersistentLocalStore} from "./main";
import {CONFIG_STORE_NAME, PHOTON_AUTH_STORE_NAME} from "./store-names";
import _ from 'lodash';

interface PhotonAuth extends Object {
    token: string,
    token_expiry: string,
    subscription: string,
    host: string,
}


class PhotonAuthStore extends PersistentLocalStore<PhotonAuth> {

    protected data(): PhotonAuth {
        return {
            token: '',
            token_expiry: '',
            subscription: '',
            host: '',
        };
    }

    setToken(token) {
        this.state.token = token;
    }

    getToken() {
        return this.state.token;
    }

    setTokenExpiry(token_expiry) {
        this.state.token_expiry = token_expiry;
    }

    getTokenExpiry() {
        return this.state.token_expiry;
    }

    setSubscription(subscription) {
        this.state.subscription = subscription;
    }

    getSubscription() {
        return this.state.subscription;
    }

    setHost(host) {
        this.state.host = host;
    }

    getHost() {
        return this.state.host;
    }
}

export const photonAuthStore = new PhotonAuthStore(PHOTON_AUTH_STORE_NAME);
