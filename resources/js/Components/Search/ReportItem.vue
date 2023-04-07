<script lang="ts" setup>
import moment from 'moment';
import {computed} from "@vue/reactivity";

const emit = defineEmits(['on-click-view']);

const props = defineProps({
    item: {type: Object, required: true},
    backgroundMode: {type: String, default: 'cover'},
    imageSize: {type: String, default: 'sml'},
    gridSize: {type: Number, default: 3},
});

const backgroundImage = computed(() => {
    return props.item['image_' + props.imageSize];
})

const backgroundStyle = computed(() => {
    return `background: url(${backgroundImage.value}); background-size: ${props.backgroundMode}; background-repeat: no-repeat;`;
})

const bookedDate = computed(() => {
    return moment(props.item.booked_date).format('YYYY-MM-DD');
})

const height = computed(() => {
    return {
        1: 'screen',
        2: '64',
        3: '48',
        4: '36'
    }[props.gridSize]
})

const colClass = computed(()=>{
    return 'col-' + (12/props.gridSize);
})
</script>

<template>
    <!-- Article -->
    <div class="p-2" :class="colClass">

        <article class="p-4 border-1 surface-border surface-card border-round relative shadow-lg"
                 @click="$emit('on-click-view', item)">

            <div class="ribbon z-0" v-if="item.tag !== '' && item.tag !== 'textsearch'">
                <span :title="item.tag"
                      class="text-xxs text-white font-semibold text-center"
                      :class="{
                    'bg-indigo-400': item.tag === 'barcode_search',
                    'bg-yellow-400' : item.tag === 'doc_search',
                    'bg-red-400' : item.tag === 'visual_search',
                    'bg-blue-400' : item.tag === 'entity_search'
                }">
                    {{ item.tag.substring(0, 3) }}
                </span>
            </div>

            <a @click.prevent="" class="block" :class="'h-'+height" href="#">
                <div class="block h-full" :style="backgroundStyle"></div>
            </a>

            <div class="flex flex-col items-stretch">
                <header class="flex items-center justify-between leading-tight p-2 md:p-4">
                    <h1 class="text-lg">
                        <a class="no-underline hover:underline text-black cursor-pointer">
                            {{ item.brand }}
                        </a>
                    </h1>
                    <p v-if="item.tag !== 'barcodesearch'" class="text-grey-darker text-xs">
                        Score: {{ item.score }}
                    </p>
                </header>

                <footer class="flex items-center justify-between leading-none p-2 md:p-4">
                    <p class="ml-2 text-sm">
                        {{ item.variety }}<br>
                        {{ item.package_type }}<br>
                    </p>
                    <p class="mr-2 text-sm text-right">
                        {{ item.JobId }}<br>
                        <span class="text-xs">&#128467; {{ bookedDate }}</span>
                    </p>
                </footer>
            </div>

        </article>
    </div>
    <!-- END Article -->
</template>

