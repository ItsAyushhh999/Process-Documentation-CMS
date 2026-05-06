<script setup>
import { computed, ref} from 'vue';
import TheNavbar from '@/components/TheNavbar.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import TheSideBar from '../components/TheSideBar.vue';
import AppIcon from '@/components/AppIcon.vue';
const isMinimized = ref(false);
import { usePage } from '@inertiajs/vue3';

const userId = usePage().props.auth.user.id;

const handleToggleChange = (e) => {
  // console.log('changed', e)
  isMinimized.value = e;
};

const urls = computed(() => {
  const routes = route().current().split('.');
  return routes;
});

const isHeaderVisible = computed(() => {
  if (urls.value.includes('dashboard')) {
    return false;
  }
   if (urls.value.includes('documents')) {
    return false;
  }
  else if (urls.value.includes('document')) {
    return false;
  }
  return true;
});

// const title = ref('')
</script>

<template>
  <!-- <div id="root"> -->
  <div class="relative w-screen dark:bg-slate-900 " id="main">
    <TheNavbar />
    <TheSideBar @toggleChange="handleToggleChange" />
    <div class="fixed z-50 -translate-x-1/2 top-4 left-1/2">
      <FlashMessage />
    </div>
    <div
      class="pt-16 h-screen w-screen inset-0 bg-gray-50 dark:bg-slate-900 transition-all"
      :class="isMinimized ? 'pl-20' : 'pl-64'"
    >
      <div class="w-full h-full overflow-auto">
        <header class="pl-10 pr-5  flex py-5 justify-between items-center sticky left-0" v-if="isHeaderVisible">
          <div class="flex flex-col gap-1">
            <span class="text-xl font-bold capitalize">
              <template v-if="$slots.pageTitle">
                <slot name="pageTitle" />
              </template>
              <span v-else>{{ urls[0] }}</span>
              <span v-if="urls[0] === 'dailyFeeds'">
             <a
                :href="`/users/${userId}/tasks`"
                target="_blank"
                class="text-blue-600 hover:underline ml-2"
            >(# Tasks)</a>
            </span>
            </span>
            <div class="flex gap-2 items-center text-sm">
              <div class="flex items-center gap-2">
                <AppIcon name="home" />
                <span>Home</span>
              </div>
              <AppIcon name="fa-chevron-right" />
              <div class="">
                <template v-if="$slots.pageTitle">
                  <slot name="pageTitle" />
                </template>
                <span v-else class="capitalize">{{ urls[0] }}</span>
              </div>
            </div>
          </div>
          <template v-if="$slots.header">
            <slot name="header" />
          </template>
        </header>
        <header class="pl-10 pr-5  flex pt-4 pb-3 justify-between items-center sticky left-0 z-10 border-b mb-5 bg-white dark:bg-slate-900 dark:border-slate-700 " v-if="urls == 'dashboard'">
          <template v-if="$slots.header">
            <slot name="header" />
          </template>
        </header>
        <div
        class="pl-10 pr-5"
          :class="
            urls != 'dashboard' ? 'w-full h-[calc(100vh_-_140px)]' : 'w-full h-[calc(100vh_-_160px)]'
          "
        >
          <slot />
        </div>
      </div>
    </div>
  </div>
  <!-- </div> -->
</template>
