<script setup>
import axios from 'axios';
import { ref, inject, nextTick } from 'vue';
import { onClickOutside, useInfiniteScroll } from '@vueuse/core';
import AppBgCard from '@/components/AppBgCard.vue';
import AppIcon from '@/components/AppIcon.vue';

const filters = inject('filters');
const notifications = ref([]);
const notificationDrawer = ref(null);
const isDrawerVisible = ref(false);
const notificationContainer = ref(null);
const page = ref(1);
const hasMore = ref(true);

// Close drawer when clicking outside
onClickOutside(notificationDrawer, () => {
  if (isDrawerVisible.value) {
    isDrawerVisible.value = false;
  }
});

// Fetch notifications
const fetchNotifications = async () => {
  try {
    console.log('hello');
    const res = await axios.get(`/notifications?page=${page.value}`);
    if (res.status === 200) {
      if (res.data.data.length) {
        notifications.value.push(...res.data.data);
        page.value++;
      } else {
        hasMore.value = false; // Stop loading if no more data
      }
    }
  } catch (error) {
    console.error('Error fetching notifications:', error);
  }
};

// Open modal and fetch notifications
const openModal = async () => {
  isDrawerVisible.value = !isDrawerVisible.value;
  if (isDrawerVisible.value) {
    notifications.value = []; // Reset list
    page.value = 1;
    hasMore.value = true;
    useInfiniteScroll(
      notificationContainer,
      async () => {
        if (hasMore.value) {
          await fetchNotifications();
        }
      },
      { distance: 50 }
    );
    await nextTick(); // Ensure the ref is set before scrolling
  }
};
</script>

<template>
  <div class="relative">
    <button
      class="z-20 top-0 right-0 bg-white dark:bg-slate-900 p-2 rounded-full hover:bg-primary-50/50 dark:hover:bg-primary-500/50 transition-all relative"
      @click="openModal"
    >
      <svg
        class="w-6 h-6"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
      </svg>
      <span
        class="absolute w-2 h-2 transform -translate-x-2.5 translate-y-2.5 bg-red-500 rounded-full bottom-full left-full"
      />
    </button>

    <!-- Notification Drawer -->
    <AppBgCard
      ref="notificationDrawer"
      v-if="isDrawerVisible"
      class="absolute top-12 right-0 shadow-lg border dark:border-slate-700"
    >
      <div ref="notificationContainer" class="min-w-[320px] max-h-[70vh] overflow-y-auto">
        <div class="pt-3 text-xl px-5">
          <strong> Notifications </strong>
        </div>
        <div class="px-2 pb-4 flex flex-col">
          <template v-for="elem in notifications" :key="elem.id">
            <div
              class="border-b dark:border-slate-700 flex gap-4 p-4 items-start last:border-0 relative hover:rounded-lg hover:bg-primary-50/50 hover:dark:bg-slate-700 hover:border-b-transparent transition-all"
            >
              <div class="w-10 h-10 bg-primary-200 rounded-full flex items-center justify-center">
                ST
              </div>
              <div class="flex flex-col flex-1 gap-3">
                <div>
                  <span>
                    <strong>{{ elem.created_by }}</strong> commented on
                    <strong>{{ elem.task_name }}</strong
                    >.
                  </span>
                </div>
                <div
                  class="bg-gray-50 dark:bg-slate-700 px-4 py-2 border border-gray-100 dark:border-slate-800 rounded-lg"
                >
                  <span class="line-clamp-2 text-justify" v-html="elem.notification"></span>
                </div>
                <div
                  class="flex items-center justify-between text-sm text-gray-500 dark:text-slate-300"
                >
                  <span
                    ><strong>#{{ elem.task_id }}</strong></span
                  >
                  <span>
                    <AppIcon name="fa-calendar-days" class="mr-2" />
                    <span> {{ filters.formatDate(elem.created_at) }}</span>
                  </span>
                </div>
              </div>
              <a :href="`/tasks/${elem.task_id}/edit`" target="_blank" class="absolute inset-0"></a>
            </div>
          </template>
        </div>
      </div>
    </AppBgCard>
  </div>
</template>
