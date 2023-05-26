import {describe, it, expect} from "vitest";
import {mount, shallowMount} from "@vue/test-utils";
import TextSearchComponent from "@/Components/Search/TextSearchComponent.vue";
import {configStore} from "@/stores/config-store";
import Tooltip from "primevue/tooltip";

describe('TextSearchComponent', () => {
    //test 1
    it('html renders properly', () => {
        const searchFields = {
            booked_date: {key: 'date', type: 'date'},
            customer_design_ref: {key: 'ref', type: 'text'}
        }
        configStore.state.advancedSearchFields = searchFields
        const wrapper = mount(TextSearchComponent, {
            global: {
                mocks: {
                    ['$primevue']: {
                        config: {
                            'ripple': true
                        }
                    }
                },
                directives: {
                    Tooltip: Tooltip
                }
            },
            props: {
                initialValues: {
                    textsearchstrings: {
                        advanced_search: "N",
                        textsearch: "Y"
                    },
                    textsearchoperator: "and",
                    searchparameters: ["fanta", "passion"]
                },
                compactMode: false
            }
        })
        console.log(wrapper.html())
        expect(wrapper.find('h3').text()).toBe('Text Search')
        expect(wrapper.find('.text-right .p-buttonset .p-component').html()).toContain("And");
        expect(wrapper.find('button').exists()).toBe(true)

        for (const key in configStore.getAdvancedSearchFields()) {
            if (configStore.getAdvancedSearchFields()[key].type === 'text') {
                console.log("hi")
            }
        }
    })
});
