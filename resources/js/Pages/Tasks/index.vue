<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, useForm, router } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import {  inject, ref, watch, onMounted, onUnmounted } from 'vue';
import ActionButton from './ActionButton.vue';
import formatterComponent from '@/utils/formatterComponent';
import CreateTaskForm from './CreateTaskForm.vue';
import AppDatepicker from '../../components/AppDatepicker.vue';


const is_super_admin =  usePage().props.auth.user.is_super_admin;

const props = defineProps({
  tasks: {
    type: Array,
    default: []
  },
  projects: {
    type: Array,
    default: []
  },
  type: {
    type: String,
    default: ''
  },
  search: {
    type: String,
    default: ''
  },
  start_from: {
    type: String,
    default: null
  },
  end_to: {
    type: String,
    default: null
  },
  task_types: {
    type: Array,
    default: []
  },
  users: {
    type: Array,
    default: []
  },
  user: {
    type: Array,
    default: []
  },
  routeFlag: {
    type: Boolean,
    default: false
  }
});

const filters = inject('filters');

const customHeaderFilter = (headerValue, rowValue, rowData, filterParams) => {
  const nameArr = rowValue.map(elem => {
    elem.collaborator
    if(filterParams.flag == elem.flag){
      return elem.collaborator.toLowerCase()
    }
    return ''
  })
  let isIncluded = nameArr.map((char) => {
      return char.includes(headerValue.toLowerCase());
    });

  return isIncluded.some((e) => e == true)
}
const columns = [
  {
    title: 'Task No',
    field: 'id',
    headerFilter: true,
    minWidth: 120,

    formatter: 'link',
    formatterParams: (cell) => {
      return{
        'target': '_blank',
        'url': `/tasks/${ cell.getData().id}/edit`
      }
    }
  },
  {
    title: 'Title',
    field: 'title',
    minWidth: 380,
    headerFilter: true,
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
    formatter:"textarea",
    headerFilter: 'input',
    minWidth: 180,
  },
  {
    title: 'Status',
    field: 'status',
    minWidth: 180,
    headerFilter: 'list',
    headerFilterParams: {
      values: {
        '15': "Dev - Ready to upload",
        '7': "Created",
        '0': "Assigned",
        '1': "In Progress",
        '2': "Assigned for Review",
        '3': "Reviewing",
        '4': "Reviewed",
        '16': "Dev - uploaded",
        '8': "Staging - Ready to upload",
        '9': "Staging - Uploaded",
        '10': "Live - Ready to upload",
        '11': "Live - Uploaded",
        '5': "Completed",
        '6': "Closed"
    },
      clearable: true
    },
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          isStatus: true,
          data: cell.getData().task_status,
        }
      });
    }
  },
  { title: 'Type', field: 'types' , headerFilter: 'input', formatter:"textarea",   minWidth: 180,},
  {
    title: 'Assignee(s)',
    field: 'collaborators',
    minWidth: 180,
    headerFilter: true,
    headerFilterFunc:customHeaderFilter,
    headerFilterFuncParams: {'flag':'0'},
    formatter: (cell) => {
      const collaborators = cell.getData().collaborators;
      const assignee = collaborators.filter((elem) => elem.flag == '0')
      // console.log('assigne', assignee)
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
    minWidth: 180,
    headerFilter: true,
    headerFilterFunc:customHeaderFilter,
    headerFilterFuncParams: {'flag':'1'},
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
    field: 'completed',
    headerFilter: true,
    minWidth: 180,
    formatter: (cell) => {
      const data = cell.getData().completed
      if(data) return `${data} days`
      return '-'
    }
  }
];


const dateRange = ref([props.start_from , props.end_to])
const dateForm = useForm({
  star_from: props.start_from ? props.start_from : new Date(),
  end_to: props.end_to ? props.end_to : new Date(),
})

const task_typeIds = ref([]);
const taskDetail = ref(null);
const currentProject = ref();
const loading = ref(false);
const currentTaskId = ref(null)

const handleDateFilter = () => {
  if (dateRange.value?.[0] && dateRange.value?.[1]) {
    dateForm.transform((prev) => ({
      ...prev,
      star_from: dateRange.value[0],
      end_to: dateRange.value[1]
    }));
  }
  const userId = props.user.id;
  const routeName = userId ? 'users.userTaskList' : 'tasks.index';
  const routeParams = userId ? { user: userId } : undefined;

    dateForm.get(route(routeName, routeParams));
}

const assignedStatusArr = ref([]);

let refreshInterval = null;

onMounted(() => {
  // Refresh full page every 10 minutes
  refreshInterval = setInterval(() => {
    window.location.reload();
  }, 600000);
});

onUnmounted(() => {
  if (refreshInterval) clearInterval(refreshInterval);
});

</script>
<template>

  <Head title="Tasks" />
  <AuthenticatedLayout>
    <template #pageTitle>{{routeFlag ? 'Draft Task' : (props.user && props.user.name ? `Tasks Assigned to ${props.user.name}` : 'Tasks')}}</template>
    <template #header  v-if="!props.user.id">
      <div class="flex gap-x-4 items-center">
        <CreateTaskForm :projects="projects" :task_types="task_types"  :users="users"/>
        <!-- {{ routeFlag }} -->
        <template v-if="routeFlag">
          <app-button type="submit" :to="(route('tasks.index'))" outline >Assigned Task</app-button>
        </template>

        <template v-else>
          <app-button type="submit" :to="(route('task.draft'))" outline v-if="is_super_admin == 1">Draft Task</app-button>
        </template>
        <app-button type="submit" :to="(route('tasks.taskList'))" outline v-if="is_super_admin == 1">Tasks Past Deadline</app-button>
      </div>
    </template>
    <div class="w-full h-full" v-if="tasks && tasks.length > 0">
      <AppDataTable :data="tasks" :columns="columns">
        <template #left>
          <form class="flex gap-x-3 items-center" @submit.prevent="handleDateFilter">
            <!-- {{ dateForm.star_from }} -->
            <AppDatepicker
              name="star_from"
              placeholder="From Date"
              v-model="dateRange"
              range
              format="dd/MM/yyyy"
            />
            <app-button type="submit">Search</app-button>
            <!-- <div class="text-3xl">yello</div> -->
          </form>
        </template>
      </AppDataTable>
    </div>
  </AuthenticatedLayout>
</template>
