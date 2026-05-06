<script setup>
import { useForm } from '@inertiajs/vue3';
import AppIcon from '@/components/AppIcon.vue';
import { computed } from 'vue';

const props = defineProps({
  taskId: {
    type: [Number, String],
    default: null
  },

  taskAddedToWatchList : {
    type : Boolean,
  }
})

const watchlisted = computed(() => props.taskAddedToWatchList) ;

const form = useForm({
  task_id: null
});

// const checkTaskWatchList = ref(false);

// const openPullRequestModal = () => {
//   form.task_id = props.taskId
//   isMergeModalOpen.value = true;
// };

// const closeModal = () => {
//   isMergeModalOpen.value = false;
//   form.reset()
// };

const handleSaveToWatchList = () => {
  const form = useForm({
  task_id: props.taskId
});

// console.log('MERRY JOHN', props.taskId, props.watchListTask);
  form.post(route('task.watchlists.store'), {
    onSuccess: (response) => {
     console.log('success');
    },
    onError: (error) => {
      console.log('error', error)
    }
  })
}

</script>
<template>
  <button
    type="button"
    class="h-10 w-10 flex items-center justify-center rounded-full transition-colors bg-primary-50 dark:bg-slate-700 dark:text-slate-400 dark:hover:bg-slate-500"
    :class="{
      'text-primary-400 hover:bg-red-100': watchlisted,
      'hover:bg-slate-300 hover:text-slate-500': !watchlisted
    }"
    :title="watchlisted ? 'Remove from watchlist' : 'Add to watchlist'"
    @click="handleSaveToWatchList"
  >
    <app-icon name="bookmark" :class="{
        'text-red-500': watchlisted,
        'text-white': !watchlisted
      }"/>
  </button>

  <!-- <Modal :show="isMergeModalOpen" @close="closeModal" maxWidth="xl">
    <template #header>
      <h2 class="text-xl font-semibold dark:text-gray-300">Merge Modal</h2>
    </template>

    <div class="flex flex-col gap-10 px-8 pt-3 pb-6">
      Ary sure you want to save to watch list?
      <div class="flex justify-end gap-4">
        <app-button text type="button" @click="closeModal">Cancel</app-button>
        <app-button type="button" @click="handleSaveToWatchList" :loading="form.processing">Save to Watch List</app-button>
      </div>
    </div>
  </Modal> -->
</template>
