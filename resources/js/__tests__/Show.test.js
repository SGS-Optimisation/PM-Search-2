import {test, describe, it, expect, beforeEach, afterEach} from "vitest";
import {shallowMount, mount, config} from "@vue/test-utils";
import Show from "@/Pages/Search/Show.vue";
import {userPreferencesStore} from "@/stores/userPreferencesStore";
import { setActivePinia, createPinia } from 'pinia'
import {Head} from "@inertiajs/vue3";
import TextSearchComponent from "@/Components/Search/TextSearchComponent.vue";
import ImageSearchComponent from "@/Components/Search/ImageSearchComponent.vue";
import ResultsSidebar from "@/Components/Results/ResultsSidebar.vue";
import PreviewCard from "@/Components/Results/PreviewCard.vue";
import SelectButton from 'primevue/selectbutton';
import Tooltip from "primevue/tooltip";
import Dropdown from "primevue/dropdown";
import QuickViewSearchEntry from "@/Components/Results/QuickViewSearchEntry.vue";
import Results from "@/Components/Results/Results.vue";

describe('Show', () => {
    //Test Case 1: Child component = 'TextSearchComponent'
    let store = null
    beforeEach(() => {
        setActivePinia(createPinia())
        store = userPreferencesStore()
    })

    it('testing child component', () => {
        const wrapper = shallowMount(Show, {
            global: {
                mocks: {
                    ['$primevue']: {
                        config: {
                            'ripple': true
                        }
                    }
                },
                components: {
                    'Head': Head,
                    'Results': Results
                },
                directives: {
                    Tooltip: Tooltip
                },
                stubs: {
                    PreviewCard: true,
                },
            },
            slots: {
                header: Dropdown
            },
            props: {
                search_id: 598,
                parent_search_id: 599,
                mode: "text",
                search_data: {
                    searchparameters: {
                        textsearch: "Y",
                        advanced_search: "N"
                    },
                    advanced_search: "N",
                    textsearch: "Y",
                    textsearchoperator: "and",
                    textsearchstrings: ["fanta", "passion"]
                },
                thumb: "http://pmsearch2.test/storage/none.jpg",
                filename: "[Sir/Ma'am, this is a Text Search]",
                report: [
                    {
                        "account_manager_name": "Jade Coulson",
                        "barcode_number": "5000112546521",
                        "barcode_type": "EAN-13",
                        "booked_date": "2021-10-25T08:50:28Z",
                        "brand": "Fanta",
                    },
                    {
                        "account_manager_name": "Jade Coulson",
                        "barcode_number": "5000112546521",
                        "barcode_type": "EAN-13",
                        "booked_date": "2021-10-25T08:50:28Z",
                        "brand": "Fanta",
                    }
                ],
                image_path: "http://pmsearch2.test/storage/",
                working_data: {
                    image_linking: true,
                    processed: true,
                    source_as_image: false,
                    text_webservice_duration: "4.2s"
                },
                fields: {
                    variety: ["fanta", "fanta-zero", "exotic", "zero exotic"],
                    simplified_group_name: ["COCA COLA", "SAICA PACK"]
                },
                fields_config: {
                    variety: {
                        type: "list"
                    },
                    simplified_group_name: {
                        type: "list"
                    }
                },
                meta: {
                    webservice_response_time: "4.2s"
                }
            }
        })
        expect(wrapper.vm.$props.search_id).toEqual(598);
        expect(wrapper.find('head-stub').attributes('title')).toBe('Search results')
        expect(wrapper.find('results-stub').attributes('mode')).toBe('text')
        expect(wrapper.find('results-stub').attributes('collectionmode')).toBe('false')
    })
});
