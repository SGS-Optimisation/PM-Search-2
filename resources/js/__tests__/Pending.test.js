import {test, describe, it, expect} from "vitest";
import { shallowMount, mount } from "@vue/test-utils";
import Pending from "@/Pages/Search/Pending.vue";
import {configStore} from "@/stores/config-store";

describe('Pending', () => {
    //Test Case 1: search_mode = 'image'
    it('testing text search mode', () => {
        const wrapper = mount(Pending, {
            global: {
                mocks: {
                    ['$primevue']: {
                        config: {
                            'ripple': true
                        }
                    }
                },
            },
            props: {
                "search": {
                    "id": 597,
                    "parent_id": null,
                    "search_mode": "image",
                    "user_id": 1,
                    "source_filepath": null,
                    "image_path": null,
                    "working_data": {
                        "processed": false,
                        "source_as_image": false
                    },
                },
                "filename": "test.png"
            }
        })
        expect(wrapper.vm.$props.search.id).toEqual(597);
        expect(wrapper.vm.$props.search.search_mode).toEqual('image');
        if (wrapper.vm.$props.search.search_mode === 'image') {
            expect(wrapper.text()).toBe("Awaiting result for file " + wrapper.vm.$props.filename)
        } else {
            expect(wrapper.text()).toContain('Searching for ' + wrapper.vm.$props.search.search_data.textsearchstrings[0] + ' ' + wrapper.vm.$props.search.search_data.textsearchoperator + ' ' + wrapper.vm.$props.search.search_data.textsearchstrings[1]);
        }
        expect(wrapper.find('.p-progress-spinner').exists()).toBe(true)
    })

    //Test Case 2: search_mode = 'text' and advanced_search = 'Y'
    it('testing advancedSearchFields', () => {
        const searchFields = {
            "brand": "LION",
            "printer_name": "S Lerner"
        }
        configStore.state.advancedSearchFields = searchFields
        const wrapper = shallowMount(Pending, {
            global: {
                mocks: {
                    ['$primevue']: {
                        config: {
                            'ripple': true
                        }
                    }
                },
            },
            props: {
                search: {
                    id: 598,
                    parent_id: null,
                    search_mode: "text",
                    user_id: 1,
                    search_data: {
                        textsearchstrings: [
                            "fanta",
                            "passion"
                        ],
                        textsearchoperator: "AND",
                        searchparameters: {
                            textsearch: "Y",
                            advanced_search: "Y"
                        },
                        fields: {
                            brand: "LION",
                            printer_name: "S Lerner"
                        }
                    },
                    source_filepath: null,
                    image_path: null,
                    working_data: {
                        processed: false,
                        source_as_image: false
                    },
                    report: null
                },
                filename: ""
            }
        })
        expect(wrapper.vm.$props.search.id).toEqual(598);
        expect(wrapper.vm.$props.search.search_mode).toEqual('text');

        if (wrapper.vm.$props.search.search_mode === 'image') {
            expect(wrapper.text()).toBe("Awaiting result for file " + wrapper.vm.$props.filename)
        }
        if (wrapper.vm.$props.search.search_mode === 'text') {
            expect(wrapper.text()).toContain('Searching for ' + wrapper.vm.$props.search.search_data.textsearchstrings[0] + ' ' + wrapper.vm.$props.search.search_data.textsearchoperator + ' ' + wrapper.vm.$props.search.search_data.textsearchstrings[1]);
        }
        if (wrapper.vm.$props.search.search_data.fields !== undefined) {
            expect(wrapper.find('tag-stub').exists()).toBe(true)
        }
        expect(wrapper.find('progress-spinner-stub').exists()).toBe(true)
    })

    //Test Case 3: error occurs
    it('testing text search mode', () => {
        const wrapper = mount(Pending, {
            global: {
                mocks: {
                    ['$primevue']: {
                        config: {
                            'ripple': true
                        }
                    }
                },
            },
            props: {
                "search": {
                    "id": 599,
                    "parent_id": null,
                    "search_mode": "image",
                    "user_id": 1,
                    "source_filepath": null,
                    "image_path": null,
                    "working_data": {
                        "processed": false,
                        "source_as_image": false,
                        "error": true
                    },
                },
                "filename": "test.png"
            }
        })
        expect(wrapper.text()).toContain("An error has occured. Please try again later.");
    })
});
