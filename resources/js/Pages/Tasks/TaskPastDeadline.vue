<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import { computed, inject, ref, watch } from 'vue';
import ActionButton from './ActionButton.vue';
import formatterComponent from '@/utils/formatterComponent';
import AppDatepicker from '../../components/AppDatepicker.vue';
import CreateTaskForm from './CreateTaskForm.vue';
import axios from 'axios';


const is_super_admin =  usePage().props.auth.user.is_super_admin;

const props = defineProps({
  tasks: {
    type: Array,
    default: []
  },
});

// console.log("tasks", props.users)

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
    // formatter: (cell) => {
    //   return formatterComponent({
    //     component: ActionButton,
    //     props: {
    //       isLink: true,
    //       openEditModal: openEditModal,
    //       data: cell.getData(),

    //     }
    //   });
    // }
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
    // formatter: (cell) => {
    //   return formatterComponent({
    //     component: ActionButton,
    //     props: {
    //       isStatus: true,
    //       data: cell.getData().task_status,
    //     }
    //   });
    // }
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
    title: 'Completed In',
    field: 'completedAt',
    headerFilter: true,
    minWidth: 180,
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

const getTaskDetailById = async (id) => {
    loading.value = true;
    const res = await axios.get(`tasks/${id}/editV2`);
    if (res.status === 200) {
        taskDetail.value = res.data;

        // currentProject.value = props.projects.filter(elem => taskDetail.value.task.project_id == elem.id)
        const index = props.projects.findIndex(
            (elem) => elem.id == taskDetail.value.task.project_id
        );
        currentProject.value = props.projects[index];
        setArrOfSelectedTaskTypes();
        assignedStatusArr.value = taskDetail.value.taskStatus;
        // sortStatusBasedOnAccess(taskDetail.value.taskStatus, taskDetail.value.task.status);
        loading.value = false;

        // console.log('success',)
    } else {
        console.log('error');
        loading.value = false;
    }
    
};

const setArrOfSelectedTaskTypes = () => {
    task_typeIds.value = [];
    taskDetail.value.task_typeIds.map((elem) => {
        const arr = taskDetail.value.task_types.filter((item) => item.id == elem);
        // task_typeIds.value = [...task_typeIds.value, arr]
        task_typeIds.value = task_typeIds.value.concat(arr);
    });
};
const assignedStatusArr = ref([]);

</script>
<template>

  <Head title="Tasks" />
  <AuthenticatedLayout>
    <template #pageTitle>Tasks Past Deadline</template>
    <div class="w-full h-full" v-if="tasks && tasks.length > 0">
      <AppDataTable :data="tasks" :columns="columns"></AppDataTable>
    </div>


    

  </AuthenticatedLayout>
</template>
