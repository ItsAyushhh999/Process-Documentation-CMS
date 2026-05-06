<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import {inject } from 'vue';
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
const is_super_admin =  usePage().props.auth.user.is_super_admin;
const filters = inject('filters');

let columns = [
  { title: 'PR #',
    field: 'pull_request_id',
    minWidth: 160,
    formatter: 'link',
    formatterParams: (cell) => {
      return{
        url: `${cell.getData().pull_request_url}`,
        target: '_blank'
      }
    }
   },
  { title: 'PR Title', field: 'pull_request_title',  minWidth: 200, formatter:"textarea"},
  { title: 'Sender Username',
    field: 'pull_request_sender_username',
    minWidth: 200,
    formatter: 'link',
    formatterParams: (cell) => {
      return{
        url: `${cell.getData().pull_request_sender_url}`,
        target: '_blank'
      }
    }
   },
  { title: 'Repository',
    field: 'repository_name',
    minWidth: 240,
    formatter: 'link',
    formatterParams: (cell) => {
      return{
        url: `${cell.getData().repository_url}`,
        target: '_blank'
      }
    }
   },
  { title: 'Comment',
   field: 'pull_request_comment',
   minWidth: 340,
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          isHtml: true,
          data: cell.getData().pull_request_comment,
        }
      });
    }
   },
  { title: 'Status',
   field: 'status',
   minWidth: 200,
   formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          isStatus: true,
          data: cell.getData().status,
        }
      });
    }
  },
  {
    title: 'Task',
    field: 'task_id',
    minWidth: 160,
      formatter: 'link',
      formatterParams: (cell) => {
          return{
              url: `https://tdms.shikhartech.com/tasks/${cell.getData().task_id}/edit`,
              target: '_blank'
          }
      }
  },
  {
    title: 'User',
    field: 'user_id',
    minWidth: 200,
    formatter: (cell) => {
      const user = props.users.filter((elem) => elem.id === cell.getData().user_id);
      return user[0]?.name;
    }
  },
  {
    title: 'Date',
    field: 'created_at',
    minWidth: 200,
    formatter: (cell) => {
      return filters.formatDate(cell.getData().created_at);
    }
  },
];

const showActiveInactive = (val) => {
  router.get(route('githubPrs.index', {
    show: val == '1' ? 'all' : 'all'
  }));
}


</script>
<template>
  <Head title="Github Prs" />
  <AuthenticatedLayout>
    <template #pageTitle>
      Github Pull Requests
    </template>
    <template #header>
      <div class="flex items-center gap-4" v-if="is_super_admin">
      <app-button @click="showActiveInactive(status)" outline> {{status == '1' ? 'Show Inactive': 'Show Active'}}</app-button>
    </div>
    </template>
    <div class="w-full h-full" v-if="githubWebhooks && githubWebhooks.length > 0">
      <AppDataTable :data="githubWebhooks" :columns="columns"/>
    </div>
  </AuthenticatedLayout>
</template>
