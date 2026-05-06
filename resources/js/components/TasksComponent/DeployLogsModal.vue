<script setup>
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import Modal from '@/components/Modal.vue';
import { inject, ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLoader from '@/components/AppLoader.vue';


const props = defineProps({
  project: {
    type: Object,
    default: null
  },
  isReview: {
    type: Boolean,
    default: false
  },
  assigneesIds: {
    type: Array,
    default: []
  },
  reviewersIds: {
    type: Array,
    default: []
  },
  taskId: {
    type: [Number, String],
    default: null
  }
});

const isModelOpen = ref(false);
const deployLogsData = ref([]);
const filters = inject('filters');
const isLoading = ref(true);
const { flashMessage } = usePage().props;

const columns = [
{
    title: 'Created By',
    field: 'created_by',
    minWidth: 160,
  },
  {
    title: 'Deploy Pull Request',
    field: 'pull_request',
    minWidth: 220,
    formatter: (cell) => {
      const data = cell.getData().pull_request
      return data ? data : '-' ;
    }
  },
  {
    title: 'Deploy Summery',
    field: 'summary',
    minWidth: 200,
    formatter: (cell) => {
      const data = cell.getData().summary
      return data ? data : '-' ;
    }
  },
  {
    title: 'Task Id',
    field: 'task_id',
    minWidth: 120,
  },
  {
    title: 'Project Name',
    field: 'project_name',
    minWidth: 160,
  },
  {
    title: 'Deploy',
    field: 'deploy',
    minWidth: 160,
  },
  {
    title: 'Log Created Date',
    field: 'created_at',
    minWidth: 200,
    formatter: (cell) => {
      return filters.formatDate(cell.getData().created_at);
  },
  }

];

const openDeployLogsModal = async() => {

  isLoading.value = true
  const form = {
    project_id: props.project.id,
    task_id: props.taskId,
  }

  const res = await axios.get('/deploy/log_list', {params: form});
   if(res.status === 200){
    res.data.length === 0
  ?  sendFlashMessage("error", "No Data found")
  : (isModelOpen.value = true, deployLogsData.value = res.data, isLoading.value = false);

  }else{
    console.log('error')
    isLoading.value = false
  }
};

const closeModal = () => {
  isModelOpen.value = false;
  deployLogsData.value = []
  isLoading.value = false
};

const sendFlashMessage = (type, message) => {
    flashMessage.value = { [type]: message };
};
</script>

<template>
  <div class="inline-flex gap-3">
    <app-button outline @click="openDeployLogsModal">Deploy Logs</app-button>
  </div>
  <Modal :show="isModelOpen" @close="closeModal" maxWidth="7xl">
    <template #header>
    </template>

    <div class="h-[300px] w-full" v-if="isLoading">
      <AppLoader />
    </div>
    <div class="min-h-[30vh] max-h-[60vh] px-8 py-4" v-if="deployLogsData && deployLogsData.length > 0">
      <AppDataTable :data="deployLogsData" :columns="columns"></AppDataTable>
    </div>
    <div v-else  class="h-[300px] w-full flex items-center justify-center">
      <span>No Data found</span>
    </div>
  </Modal>

</template>
