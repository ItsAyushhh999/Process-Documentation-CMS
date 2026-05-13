<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import { inject } from 'vue';
import ActionButton from './ActionButton.vue';
import formatterComponent from '@/utils/formatterComponent';

const props = defineProps({
  githubWebhooks: {
    type: Array,
    default: []
  },
  users: {
    type: Array,
    default: []
  },
});

const filters = inject('filters');

let columns = [
  {
    title: 'PR #',
    field: 'pull_request_id',
    minWidth: 160,
    formatter: 'link',
    formatterParams: (cell) => ({
      url: cell.getData().pull_request_url,
      target: '_blank'
    })
  },
  { title: 'PR Title', field: 'pull_request_title', minWidth: 200, formatter: 'textarea' },
  {
    title: 'Sender Username',
    field: 'pull_request_sender_username',
    minWidth: 200,
    formatter: 'link',
    formatterParams: (cell) => ({
      url: cell.getData().pull_request_sender_url,
      target: '_blank'
    })
  },
  {
    title: 'Repository',
    field: 'repository_name',
    minWidth: 240,
    formatter: 'link',
    formatterParams: (cell) => ({
      url: cell.getData().repository_url,
      target: '_blank'
    })
  },
  {
    title: 'Comment',
    field: 'pull_request_comment',
    minWidth: 340,
    formatter: (cell) => formatterComponent({
      component: ActionButton,
      props: {
        isHtml: true,
        data: cell.getData().pull_request_comment,
      }
    })
  },
  {
    title: 'Status',
    field: 'status',
    minWidth: 200,
    formatter: (cell) => formatterComponent({
      component: ActionButton,
      props: {
        isStatus: true,
        data: cell.getData().status,
      }
    })
  },
  {
    title: 'Task',
    field: 'task_id',
    minWidth: 160,
    formatter: 'link',
    formatterParams: (cell) => ({
      url: cell.getData().task_id
        ? `https://tdms.shikhartech.com/tasks/${cell.getData().task_id}/edit`
        : '#',
      target: '_blank'
    })
  },
  {
    title: 'User',
    field: 'user_id',
    minWidth: 200,
    formatter: (cell) => {
      const user = props.users.find((elem) => elem.id === cell.getData().user_id);
      return user?.name ?? '-';
    }
  },
  {
    title: 'Date',
    field: 'created_at',
    minWidth: 200,
    formatter: (cell) => filters.formatDate(cell.getData().created_at)
  },
];
</script>

<template>
  <Head title="Github Prs" />
  <AuthenticatedLayout>
    <template #pageTitle>
      Github Pull Requests
    </template>
    <div class="w-full h-full" v-if="githubWebhooks && githubWebhooks.length > 0">
      <AppDataTable :data="githubWebhooks" :columns="columns" />
    </div>
    <div class="w-full h-full flex items-center justify-center p-10" v-else>
      <p class="text-gray-500">No pull requests found.</p>
    </div>
  </AuthenticatedLayout>
</template>