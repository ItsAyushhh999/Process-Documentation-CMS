<script setup>
import { ref } from 'vue';
import AppNotificationDrawer from './AppNotificationDrawer.vue';
import AppProfileDrawer from './AppProfileDrawer.vue';
import AppSearchbar from './AppSearchbar.vue';
import NavLink from './NavLink.vue';
import { routes } from '../routes.js';
import WatchListSlider from './WatchListSlider.vue';

const base_url = route().t.url;

const isMobileNavVisible = ref(false);
</script>
<template>
    <!-- id="scroll" -->
    <nav
        class="fixed left-0 right-0 top-0 z-30 bg-white border-b dark:bg-slate-900 dark:border-gray-700 h-16"
    >
        <div class="flex items-center justify-between px-5 h-full">
            <div class="h-16 flex items-center gap-14">
            <NavLink :href="route('dashboard')" class="flex items-center gap-x-4 dark:text-slate-300 text-primary-500">
                <svg
                    width="60"
                    height="40"
                    viewBox="0 0 60 40"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M7.99702 32.0005L15.9933 40H0L7.99702 32.0005ZM41.9831 0V40H33.986V0H41.9831ZM5.55751 3.99978C5.57775 4.87991 9.18641 8.37005 12.9499 11.8301L13.7036 12.5209C17.2212 15.7356 20.6743 18.7708 21.2712 19.4799C24.5503 22.2804 27.9487 25.5602 27.789 30.1599C27.789 32.2405 27.1488 34.3202 25.9898 36.0002C26.1124 34.4865 22.5443 30.9332 18.7926 27.5279L18.2916 27.075C14.8649 23.9883 11.4153 21.1051 10.5955 20.0799C5.3176 15.9999 1.79927 10.2803 5.55751 3.99978ZM59.9758 0L51.9788 8.0003L43.9825 0H59.9758ZM31.9873 0L23.9903 8.0003L15.9933 0H31.9873Z"
                        fill="currentcolor"
                    />
                </svg>
                <span class="leading-5 font-bold tracking-wide whitespace-nowrap">Task Management <br> system</span>
            </NavLink>
            <AppSearchbar />
        </div>
        <div class="flex items-center  gap-x-5">
            <WatchListSlider />
            <AppNotificationDrawer />
            <AppProfileDrawer />
        </div>
        </div>
    </nav>
</template>
