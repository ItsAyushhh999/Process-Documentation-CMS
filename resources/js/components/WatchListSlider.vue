<script setup>
import { inject, ref } from 'vue';
import AppIcon from './AppIcon.vue';
import AppChip from './AppChip.vue';
import AppLoader from './AppLoader.vue';
import { Link, useForm } from '@inertiajs/vue3';
import axios from 'axios';

const emit = defineEmits(['onClose', 'reloadData']);
const isVisible = ref(false);
const data = ref([]);
const loading = ref(false);

const filters = inject('filters');

const openWatchList = async () => {
  isVisible.value = true;
  fetchData();
};

const fetchData = async () => {
  loading.value = true;
  const res = await axios.get('/watchLists');

  if (res.status == 200) {
    data.value = res.data.listTaskWatchLists;
    loading.value = false;
  } else {
    console.log('error');
    loading.value = false;
  }
};

const formatName = (name) => {
  // console.log("name", name)
  const arr = name.split(' ');
  return `${arr[0]}.${arr[1].charAt(0)}`;
};

const closeModal = () => {
  // emit('onClose');
  isVisible.value = false;
};

const removeFromWatchList = (id) => {
  const form = useForm({
    remove_task_id: id
  });

  form.post(route('task.watchlists.destroy'), {
    onSuccess: () => {
      console.log('success');
      fetchData();
    },
    onError: (error) => {
      console.log('error');
    }
  });
};
</script>

<template>
  <button
    class="items-center inline-flex gap-3 py-2 px-4 rounded-lg font-medium hover:bg-primary-50/50 dark:hover:bg-primary-500/50 dark:hover:text-slate-100 hover:text-primary-500 transition-all"
    type="button"
    @click="openWatchList"
  >
    <AppIcon name="bookmark" class="text-gray-400 dark:text-slate-400"/>
    <span>Watch List</span>
  </button>

  <Transition leave-active-class="duration-200">
    <div v-if="isVisible" class="fixed inset-0 z-10 bg-black/50" @click="closeModal"></div>
  </Transition>
  <Transition
    enter-active-class="ease-out duration-300"
    enter-from-class="opacity-0 translate-x-full"
    enter-to-class="opacity-100 translate-x-0"
    leave-active-class="ease-in duration-200"
    leave-from-class="opacity-100 translate-x-0"
    leave-to-class="opacity-0 translate-x-1/2"
  >
    <div
      v-show="isVisible"
      class="fixed top-0 right-0 bottom-0 w-2/5 z-30 bg-white dark:bg-slate-800 flex flex-col overflow-hidden"
    >
      <div class="bg-white dark:bg-slate-800 px-6 pt-6 pb-3 flex justify-between">
        <h3 class="text-lg font-semibold">Watch List</h3>
        <button type="button" class="w-8 h-8 flex items-center justify-center" @click="closeModal">
          <app-icon name="fa-close" />
        </button>
      </div>
      <div class="flex-1 overflow-y-auto bg-gray-100 dark:bg-slate-900 relative">
        <div class="flex flex-col gap-3 px-6">
          <template v-if="loading">
            <AppLoader />
          </template>
          <template v-for="(watchlist, index) in data" :key="watchlist.id">
            <div
              class="flex flex-col gap-4 bg-white dark:bg-slate-800 p-5 rounded-xl relative transition-all"
              :class="index == 0 ? 'mt-4' : ''"
            >
              <Link
                class="text-primary-500 dark:text-slate-200 font-semibold whitespace-pre-wrap pr-10 hover:underline"
                :href="`/tasks/${watchlist.task.id}/edit`"
              >
                <span><strong>{{ watchlist.task.id }}: </strong></span>
                {{ watchlist.task.title }}
              </Link>
              <div class="flex items-center gap-x-1 justify-between">
                <div class="flex items-center gap-x-2">
                  <AppChip color="cyan">{{ watchlist.task.project_name}}</AppChip>
                  <AppChip v-if="watchlist.task.priority == 2" color="red">Urgent</AppChip>
                  <AppChip v-else-if="watchlist.task.priority == 1" color="yellow">High</AppChip>
                  <AppChip v-else color="green">Normal</AppChip>
                  <AppChip color="teal">{{ watchlist.task.status }}</AppChip>
                  <AppChip color="sky">{{ formatName(watchlist.task.created_by) }}</AppChip>
                </div>
              </div>
              <button
                type="button"
                class="absolute top-3 right-3 p-2 rounded-full text-gray-300 hover:bg-red-50 hover:text-red-400"
                @click="removeFromWatchList(watchlist.task.id)"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
                  fill="currentColor"
                  class="bi bi-bookmark-x-fill"
                  viewBox="0 0 16 16"
                >
                  <path
                    fill-rule="evenodd"
                    d="M2 15.5V2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.74.439L8 13.069l-5.26 2.87A.5.5 0 0 1 2 15.5M6.854 5.146a.5.5 0 1 0-.708.708L7.293 7 6.146 8.146a.5.5 0 1 0 .708.708L8 7.707l1.146 1.147a.5.5 0 1 0 .708-.708L8.707 7l1.147-1.146a.5.5 0 0 0-.708-.708L8 6.293z"
                  />
                </svg>
              </button>
            </div>
          </template>
        </div>
      </div>
    </div>
  </Transition>
</template>
