<script lang="js">
import Sidebar from "primevue/sidebar";

export default {
    extends: Sidebar,
    name: "FullModal",
    emits: ['update:visible', 'show', 'hide', 'after-hide', 'prev', 'next'],
}

</script>

<template>
    <Portal>
        <div v-if="containerVisible" :ref="maskRef" :class="maskClass" @mousedown="onMaskClick">
            <transition name="p-sidebar" @enter="onEnter" @after-enter="onAfterEnter" @before-leave="onBeforeLeave" @leave="onLeave" @after-leave="onAfterLeave" appear>
                <div v-if="visible" :ref="containerRef" v-focustrap :class="containerClass" role="complementary" :aria-modal="modal" @keydown="onKeydown" v-bind="$attrs">
                    <div :ref="headerContainerRef" class="p-sidebar-header">
                        <div v-if="$slots.header" class="p-sidebar-header-content">
                            <slot name="header"></slot>
                        </div>
                        <button v-if="showCloseIcon"
                                :ref="closeButtonRef" v-ripple type="button"
                                class="p-sidebar-close p-sidebar-icon p-link"
                                :aria-label="closeAriaLabel"
                                @click="hide" @keydown.esc="hide">
                            <slot name="closeicon">
                                <component :is="closeIcon ? 'span' : 'TimesIcon'" :class="['p-sidebar-close-icon ', closeIcon]"></component>
                            </slot>
                        </button>
                    </div>
                    <div :ref="contentRef" class="p-sidebar-content">
                        <slot></slot>
                    </div>
                </div>
            </transition>
        </div>
    </Portal>
</template>
