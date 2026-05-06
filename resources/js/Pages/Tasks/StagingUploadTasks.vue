<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {  useForm, router } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import {  inject, ref, watch } from 'vue';
import ActionButton from './ActionButton.vue';
import formatterComponent from '@/utils/formatterComponent';
import AppDatepicker from '../../components/AppDatepicker.vue';


const props = defineProps({
  tasks: {
    type: Array,
    default: []
  },

  start_from: {
    type: String,
    default: null
  },
  end_to: {
    type: String,
    default: null
  },
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
  start_from: props.start_from ? props.start_from : new Date(),
  end_to: props.end_to ? props.end_to : new Date(),
})


const handleDateFilter = () => {
  dateForm.start_from =  dateRange.value[0],
  dateForm.end_to = dateRange.value[1]
  dateForm.get(route('StagingUploadedTasks'));
}

</script>
<template>


  <AuthenticatedLayout>
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
        </form>
        </template>
      </AppDataTable>
    </div>



  </AuthenticatedLayout>
</template>
