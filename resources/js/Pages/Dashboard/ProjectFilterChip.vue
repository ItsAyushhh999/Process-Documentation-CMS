<script setup>
import AppIcon from '@/components/AppIcon.vue';
import { computed } from 'vue';
const props = defineProps({
  handleClear: {
    type: Function,
    default: () => Void
  },
  projectName: {
    type: String,
    default: ''
  }
});
</script>

<template>
  <div class="border dark:border-slate-700 rounded-full pl-3 pr-4 py-1 h-8 flex gap-2 items-center relative max-w-60 group">
    <!-- <div class="w-2 h-2 bg-red-100 rounded-full outline outline-red-300 animate-pulse"></div> -->
    <AppIcon name="code"/>
    <span class="truncate flex-1 ">{{ projectName }}</span>

    <div
      class="absolute flex whitespace-nowrap bg-white dark:bg-slate-900 border px-3 py-1 text-sm invisible group-hover:visible rounded-full top-10 dark:border-slate-700"
    >
      {{ projectName }}
    </div>
  </div>
</template>
