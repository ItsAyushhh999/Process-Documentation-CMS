<script setup>
import AppIcon from '@/components/AppIcon.vue';
import { computed } from 'vue';
const props = defineProps({
  handleClear: {
    type: Function,
    default: () => Void
  },
  filterType: {
    type: String,
    default: ''
  }
});

// const getClass = computed(() => {
//   switch (props.filterType) {
//     case 'Urgent':
//       return 'text-red-500 dark:text-red-100 border-red-00'
//       break;
//     case 'High':
//       return 'text-orange-500 dark:text-orange-100 border-orange-00'
//       break;
//     default:

//       return 'text-sky-500 dark:text-sky-100 border-sky-00'
//       break;
//   }
// })
</script>

<template>
  <div class="border rounded-full pl-2 px-4 py-1 flex gap-2 items-center h-8 dark:border-slate-700" :class="getClass">
    <!-- <div v-if="filterType == 'Urgent'" class="bg-red-400 w-4 h-4 rounded-full"></div> -->
    <div
      v-if="filterType == 'Urgent'"
      class="w-3 h-3 rounded-full bg-red-400"
      ></div>
      <!-- class="w-2 h-2 bg-red-100 rounded-full outline outline-red-300 animate-pulse" -->
    <div
    class="w-3 h-3 rounded-full bg-orange-400"
      v-else-if="filterType == 'High'"
      ></div>
      <!-- class="w-2 h-2 bg-yellow-100 rounded-full outline outline-yellow-300 animate-pulse" -->
    <div
      v-else
      class="w-3 h-3 rounded-full bg-sky-400"
      ></div>
      <!-- class="w-2 h-2 bg-sky-100 rounded-full outline outline-sky-300 animate-pulse" -->
    <span>
      {{ filterType }}
    </span>
  </div>
</template>
