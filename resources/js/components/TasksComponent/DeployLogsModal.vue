<script setup>
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import Modal from '@/components/Modal.vue';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLoader from '@/components/AppLoader.vue';

const props = defineProps({
  project: { type: Object, default: null },
  taskId: { type: [Number, String], default: null },
  taskStatus: { default: null },
});

const getStage = (status) => {
  const s = String(status);
  if (['10', '11'].includes(s)) return { label: 'Production',  class: 'bg-green-100 text-green-800' };
  if (['8',  '9' ].includes(s)) return { label: 'Staging',     class: 'bg-yellow-100 text-yellow-800' };
  return                                { label: 'Development', class: 'bg-blue-100 text-blue-800' };
};

const isModelOpen = ref(false);
const pullRequestData = ref([]);
const isLoading = ref(false);
const { flashMessage } = usePage().props;

const columns = [
  {
    title: 'PR no.',
    field: 'pull_request_id',
    minWidth: 120,
  },
  {
    title: 'Title',
    field: 'pull_request_title',
    minWidth: 200,
  },
  {
    title: 'Repository',
    field: 'repository_name',
    minWidth: 160,
  },
  {
    title: 'Status',
    field: 'status',
    minWidth: 120,
  },
  {
    title: 'Target Branch',
    field: 'target_branch',
    minWidth: 140,
    formatter: (cell) => {
      const branch = cell.getValue();
      if (!branch) return '-';
      const styles = {
        main:       'bg-green-100 text-green-800',
        master:     'bg-green-100 text-green-800',
        production: 'bg-green-100 text-green-800',
        staging:    'bg-yellow-100 text-yellow-800',
      };
      const style = styles[branch] ?? 'bg-blue-100 text-blue-800';
      return `<span class="px-2 py-1 rounded-full text-xs font-semibold ${style}">${branch}</span>`;
    }
  },
  {
    title: 'Created By',
    field: 'pull_request_sender_username',
    minWidth: 160,
  },
  {
    title: 'PR Link',
    field: 'pull_request_url',
    minWidth: 160,
    formatter: (cell) => {
      const url = cell.getValue();
      return url ? `<a href="${url}" target="_blank" class="text-blue-500 underline">View PR</a>` : '-';
    }
  },
];

const openModal = async () => {
  isLoading.value = true;
  isModelOpen.value = true;

  const res = await axios.get('/github/pull-request/logs', {
    params: { task_id: props.taskId }
  });

  pullRequestData.value = res.data?.githubWebhooks || [];
  isLoading.value = false;
};

const closeModal = () => {
  isModelOpen.value = false;
  pullRequestData.value = [];
};
</script>

<template>
  <div class="inline-flex gap-3">
    <AppButton outline @click="openModal">Pull Requests</AppButton>
  </div>

  <Modal :show="isModelOpen" @close="closeModal" maxWidth="7xl">
    <template #header>
      <h2 class="text-xl font-semibold">Pull Requests</h2>
    </template>

    <div class="h-[300px] w-full flex items-center justify-center" v-if="isLoading">
      <AppLoader />
    </div>
    <div class="min-h-[30vh] max-h-[60vh] px-8 py-4" v-else-if="pullRequestData.length > 0">
      <AppDataTable :data="pullRequestData" :columns="columns" />
    </div>
    <div v-else class="h-[300px] w-full flex items-center justify-center">
      <span>No pull requests found</span>
    </div>
  </Modal>
</template>