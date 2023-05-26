import {describe, it, expect} from "vitest";
import { shallowMount } from "@vue/test-utils";
import TextSearch from "@/Pages/Search/TextSearch.vue";
import {Head} from "@inertiajs/vue3";
import TextSearchComponent from "@/Components/Search/TextSearchComponent.vue";

describe('TextSearch', () => {
    //Test Case 1: Check if page-title is correct
    it('title renders properly', () => {
        const stubs = {
            Head
        };
        const wrapper = shallowMount(TextSearch, { stubs } , {
            global: {
                components: {
                    'TextSearchComponent': TextSearchComponent
                }
            },
            propsData: {
                data: {
                    compactMode: false,
                }
            }
        })
        expect(wrapper.html()).toContain('Text Search')
    })

    //Test Case 2: Check if TextSearchComponent exists or not
    it('TextSearchComponent exists', () => {
        const stubs = {
            Head
        };
        const wrapper = shallowMount(TextSearch, { stubs } , {
            global: {
                components: {
                    'TextSearchComponent': TextSearchComponent
                }
            },
            propsData: {
                data: {
                    compactMode: false,
                }
            }
        })
        expect(wrapper.find('Text-Search-Component-stub').exists()).toBe(true)
    })
});
