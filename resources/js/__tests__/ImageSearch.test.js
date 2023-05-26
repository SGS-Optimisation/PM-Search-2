import {describe, it, expect} from "vitest";
import { shallowMount } from "@vue/test-utils";
import {Head} from "@inertiajs/vue3";
import ImageSearch from "@/Pages/Search/ImageSearch.vue";
import ImageSearchComponent from "@/Components/Search/ImageSearchComponent.vue";

describe('ImageSearch', () => {
    //Test Case 1: Check if h2 is correct
    it('h2 renders properly', () => {
        const stubs = {
            Head
        };
        const wrapper = shallowMount(ImageSearch, { stubs } , {
            global: {
                components: {
                    'ImageSearchComponent': ImageSearchComponent
                }
            }
        })
        expect(wrapper.text()).toBe('Image Search')
    })

    //Test Case 2: Check if ImageSearchComponent exists or not
    it('ImageSearchComponent exists', () => {
        const stubs = {
            Head
        };
        const wrapper = shallowMount(ImageSearch, { stubs } , {
            global: {
                components: {
                    'ImageSearchComponent': ImageSearchComponent
                }
            }
        })
        expect(wrapper.find('Image-Search-Component-stub').exists()).toBe(true)
    })
});
