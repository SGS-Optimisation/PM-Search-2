import {describe, it, expect} from "vitest";
import {mount, shallowMount} from "@vue/test-utils";
import Tooltip from "primevue/tooltip";
import ImageSearchComponent from "@/Components/Search/ImageSearchComponent.vue";
describe('ImageSearchComponent', () => {
    //test 1
    it('html renders properly', () => {
        const wrapper = mount(ImageSearchComponent, {
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

                },
                compactMode: false
            }
        })
        console.log(wrapper.html())
        expect(wrapper.find('h3').text()).toBe('Image Search')

    })
});
