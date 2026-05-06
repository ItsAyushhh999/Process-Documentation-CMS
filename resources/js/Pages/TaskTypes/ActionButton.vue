<script setup>
// import { getCurrentInstance, onMounted, ref } from 'vue';
import AppIcon from '@/components/AppIcon.vue'

const props = defineProps({
  data: {
    type: Object,
    default: null
  },
  openEditModal: {
    type: Function
  }
});
</script>

<template>
  <!-- <span>ActionButton {{ data }}</span> -->
   <!-- <div class="flex gap-2"> -->
  <button type="button" @click="openEditModal(data)" class="text-orange-400 dark:text-orange-500 bg-orange-50/50 dark:bg-orange-900/20 w-10 h-10 rounded-full hover:bg-orange-100 hover:text-orange-500 dark:hover:bg-orange-900/50 transition-all">
    <AppIcon name="fa-pen-to-square"></AppIcon>
   </button>
   <!-- <button type="button" @click="openEditModal(data)" class="text-red-400 bg-red-50/50 w-10 h-10 rounded-full hover:bg-red-100 hover:text-red-500 transition-all">
    <AppIcon name="fa-trash-alt"></AppIcon>
   </button> -->
  <!-- </div> -->
</template>
