<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Jetstream/Welcome.vue';

defineProps({
    latestSearches: Array,
});


</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-12 px-2">
                    <Welcome />
                </div>
            </div>
        </div>
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg py-12 px-2">
                    <h4 class="h4 font-bold text-lg">My latest searches</h4>
                    <ul v-if="latestSearches.length">
                        <li v-for="search in latestSearches">
                            <a class="text-blue-500 hover:text-blue-700 underline"
                               href="#">
                                [{{ search.search_mode }}]
                                <span v-if="search.search_mode == 'text'">
                                    {{ search.search_data.search_string.join(' ' + search.search_data.operator.toUpperCase() + ' ') }}
                                </span>
                                <span v-else>
                                    {{ search.working_data.original_filename }}
                                </span>
                            </a>
                        </li>

                    </ul>
                    <p v-else>
                        Nothing to show
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
