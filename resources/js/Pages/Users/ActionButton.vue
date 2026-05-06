<script setup>
// import { getCurrentInstance, onMounted, ref } from 'vue';
import AppIcon from '@/components/AppIcon.vue';
import AppChip from '@/components/AppChip.vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
  data: {
    type: Object,
    default: null
  },
  openEditModal: {
    type: Function
  },
  isStatus: {
    type: Boolean,
    default: false
  },
  isTaskListing: {
    type: Boolean,
    default: false
  },
  isDepartmentHead: {
    type: Boolean,
    default: false
  }
});

const base_url = route().t.url;
const is_super_admin = usePage().props.auth.user.is_super_admin;
</script>

<template>
  <!-- <span>ActionButton {{ data }}</span> -->
  <!-- <span>{{ isDepartmentHead }}</span> -->
  <template v-if="isStatus">
    <AppChip color="red" v-if="data == 0">Inactive</AppChip>
    <AppChip color="green" v-else>Active</AppChip>
  </template>
  <template v-else-if="isTaskListing">
    <span
      >{{ data.name }}
      <strong class="text-primary-500"
        >(<a :href="`${base_url}/users/${data.id}/tasks`">{{ data.task_count }}</a
        >)</strong
      ></span
    >
  </template>
  <div v-else class="flex gap-2">
    <!-- <a
      type="button"
      :href="`${base_url}/users/${data.id}/edit`"
      class="text-orange-400 dark:text-orange-500 bg-orange-50/50 dark:bg-orange-900/20 w-10 h-10 rounded-full hover:bg-orange-100 hover:text-orange-500 dark:hover:bg-orange-900/50 transition-all flex items-center justify-center"
    >
      <AppIcon name="fa-pen-to-square"></AppIcon>
    </a> -->
    <button
      v-if="is_super_admin == 1 || usePage().props.auth.user.id == props.data.id"
      type="button"
      @click="openEditModal(data)"
      class="text-orange-400 dark:text-orange-500 bg-orange-50/50 dark:bg-orange-900/20 w-10 h-10 rounded-full hover:bg-orange-100 hover:text-orange-500 dark:hover:bg-orange-900/50 transition-all"
    >
      <AppIcon name="fa-pen-to-square"></AppIcon>
    </button>
    <Link v-if="isDepartmentHead && is_super_admin == 1" :href="`/permissions/${data.id}/edit`"
      ><div
        class="text-red-400 bg-red-50/50 dark:bg-red-900/20 w-10 h-10 rounded-full hover:bg-red-100 dark:hover:bg-red-900/50 hover:text-red-500 transition-all flex items-center justify-center"
      >
        <AppIcon name="fa-user-shield"></AppIcon>
      </div>
    </Link>
  </div>
</template>
