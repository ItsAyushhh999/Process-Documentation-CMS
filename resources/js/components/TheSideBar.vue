<script setup>
import { routes } from '../routes.js';
import MenuItem from './MenuItem.vue';
import { ref } from 'vue';
import AppIcon from './AppIcon.vue';

const base_url = route().t.url;

const emit = defineEmits(['toggleChange'])

const sidebarRef = ref(null);
const isMinimized = ref(false);

const toggleSideBar = () => {
    isMinimized.value = !isMinimized.value
    emit('toggleChange', isMinimized.value )
};
</script>
<template>
    <aside
        class="fixed top-0 z-20 bg-white border-r dark:bg-slate-900 dark:border-gray-700 h-screen flex flex-col pt-16 transition-all"
        :class="isMinimized ? 'w-20' : 'w-64'"
        ref="sidebarRef"
    >
        <div class="relative w-full h-full">
            <button
                class="bg-white dark:bg-slate-800 absolute w-8 h-8 top-2 -right-5 z-20 rounded-lg border dark:border-slate-700 transition-all"
                @click="toggleSideBar"
            >
            <AppIcon name="fa-chevron-right transition-transform" :class="isMinimized ? 'rotate-0' : 'rotate-180'"/>
            </button>
            <div class="overflow-y-auto w-full h-full">
                <nav class="flex flex-col gap-2 px-4 pt-4">
                    <ul class="flex flex-col gap-2">
                        <template v-for="item in routes" :key="item.name">
                            <li>
                                <MenuItem :base_url="base_url" :item="item" :isMinimized="isMinimized"/>
                            </li>
                        </template>
                    </ul>
                </nav>
            </div>
        </div>
    </aside>
</template>
