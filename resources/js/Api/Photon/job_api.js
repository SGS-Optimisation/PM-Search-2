import {photonAuthStore} from "@/stores/photon-store";

export default {
    headers(obj = {}) {
        return Object.assign(obj ? obj : {}, {
            Authorization: `Bearer ${photonAuthStore.getToken()}`,
            'Ocp-Apim-Subscription-Key': photonAuthStore.getSubscription(),
        });
    },

    call(url, obj = {}) {
        return axios(Object.assign({}, obj, {
            url,
            baseURL: photonAuthStore.getHost(),
            headers: this.headers(obj.hasOwnProperty('headers') ? obj.headers : null),
        }));
    },

    jobColour: function (jobNumber) {
        return this.call(`getjobcolour/${jobNumber}/?jobNumber=${jobNumber}`)
            .catch(error => {
                return axios.get(route('api.photon.job-colours', {jobNumber}));
            });
    }
}
