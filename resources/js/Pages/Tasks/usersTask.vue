<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import {inject } from 'vue';
import ActionButton from './ActionButton.vue';
import formatterComponent from '@/utils/formatterComponent';



// const is_super_admin =  usePage().props.auth.user.is_super_admin;

const props = defineProps({
  data: {
    type: Array,
    default: []
  },
  user: {
    type: Array,
    default: []
  }
});


const filters = inject('filters');
const columns = [
  {
    title: 'Task Number', 
    field: 'id',
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          isLink: true,
          url: `/tasks/${ cell.getData().id}/edit`,
          data: cell.getData().id
        }
      });
    }
  },
  {
    title: 'Title',
    field: 'title',
    width: 440,
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          isTitle: true,
          data: cell.getData(),
          dateFilter: filters
        }
      });
    }
  },
  { title: 'Project', 
    field: 'projectName',
    formatter:"textarea"
  },
  {
    title: 'Assignee(s)',
    field: 'collaborators',
    formatter: (cell) => {
      const collaborators = cell.getData().collaborators;
      const assignee = collaborators.filter((elem) => elem.flag == '0')
      return formatterComponent({
        component: ActionButton,
        props: {
          isCollaborators: true,
          data: assignee,
        }
      });
    }
  },
  {
    title: 'Reviewer(s)',
    field: 'collaborators',
    formatter: (cell) => {
      const collaborators = cell.getData().collaborators;
      const assignee = collaborators.filter((elem) => elem.flag == '1')
      return formatterComponent({
        component: ActionButton,
        props: {
          isCollaborators: true,
          data: assignee,
        }
      });
    }
  },
  {
    title: 'Completed In',
    field: 'completedAt',
  }
];

</script>
<template>

  <Head title="Tasks" />
  <AuthenticatedLayout>
    <template #pageTitle>Tasks assigned to {{ user }}</template>
    <div class="w-full h-full" v-if="data && data.length > 0">
      <AppDataTable :data="data" :columns="columns"></AppDataTable>
    </div>
  </AuthenticatedLayout>
</template>
