<script setup>
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import Modal from '@/components/Modal.vue';
import { computed, ref, inject, onMounted } from 'vue';
import { useForm, usePage, router} from '@inertiajs/vue3';
import axios from 'axios';
import ActionButton from './ActionButton.vue';
import formatterComponent from '@/utils/formatterComponent';
import AppLoader from '@/components/AppLoader.vue'
import AppIcon from '@/components/AppIcon.vue';


const props = defineProps({
  project: {},
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
  taskId: {},

  // 0: text, 1:icon
  buttonFlag:{
    type: Number,
    default: 0
  }

});

const isModelOpen = ref(false);
const pullRequestData = ref([]);
const isMergeModalOpen = ref(false);
const isMainProject = ref(false)
const isLoading = ref(false);
const modalTitle = ref(null);
const modalDescription = ref(null)
const { flashMessage } = usePage().props;

const repo = ref('0');

const columns = [
  {
    title: 'PR no.',
    field: 'number',
    minWidth: 120,
  },
  {
    title: 'Ttitle',
    field: 'title',
    minWidth: 200,
  },
  {
    title: 'Description',
    field: 'body',
    minWidth: 240,
  },
  {
    title: 'Source',
    field: 'source',
    minWidth: 160,
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          data: cell.getData().source,
          isSource: true
        }
      });
    }
  },
  {
    title: 'Target',
    field: 'target',
    minWidth: 160,
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          data: cell.getData().target,
          isTarget: true
        }
      });
    }
  },
  {
    title: 'Created By',
    field: 'username',
    minWidth: 160,
  },
  {
    title: 'Action',
    field: 'number',
    minWidth: 120,
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          // isLink: true,
          // url: `./tasks/${ cell.getData().id}/edit`,
          data: cell.getData(),
          onHandleMerge: openMergeModal
        }
      });
    }
  }

];


// console.log('Modal', props.project);

// /pull_request
const openPullRequestModal = async() => {
  // if(!repo.value){
  //   console.log("false")
  //   return
  // }
  isLoading.value = true;
  const prForm = useForm({
    repository_name: repo.value,
    assigneesIds: props.assigneesIds,
    reviewersIds: props.reviewersIds
  })
  const form = {
    repository_name: repo.value,
    assigneesIds: props.assigneesIds,
    reviewersIds: props.reviewersIds
  }

  const res = await axios.get('/pull_requests', {params: form});
    if(res.status == 200){
        const error = res.data?.status === "error"
        ? res.data.message
        : (res.data?.data?.length === 0 ? "No Data Found" : null);

        if (error) {
            sendFlashMessage('error', error);
            isModelOpen.value = false;
            isLoading.value = true;
        } else {
            pullRequestData.value = res.data?.data || [];
            isModelOpen.value = true;
            isLoading.value = false;
        }
}

};

const sendFlashMessage = (type, message) => {
  flashMessage.value = { [type]: message };
};

const closeModal = () => {
  isModelOpen.value = false;
};
const form = useForm({
  reviewersIds: [],
    repository_name: null,
    pull_request: null,
    taskId: null
})

const openMergeModal = (data) => {

  modalTitle.value = data.title
  modalDescription.value = data.description

  isMergeModalOpen.value = true
  form.reviewersIds=props.reviewersIds,
  form.repository_name= repo.value,
  form.pull_request= JSON.stringify(data),
  form.taskId= props.taskId
  console.log('modal open', form.data())
}

const handleSubmitMerge = async() => {
 console.log( form.data())
 router.post(route('merge-pull-request'), form.data());
 closeMergeModal()
 closeModal()
}

const closeMergeModal = () => {
  isMergeModalOpen.value = false
}

const deployPermissions = ref({});
const isLoadingPermissions = ref(false);

const getTaskDeployPermissions = async (id) => {
  try {
    const result = await axios.get(route("task.deploy.permission", [id]));
    console.log(result);
    deployPermissions.value[id] = result.data.permissions; // Store permission status
  } catch (error) {
    console.error(`Error fetching permissions for project ID ${id}:`, error);
    deployPermissions.value[id] = []; // Default to no permission
  }
};

const hasPermission = (id) => {
  return deployPermissions?.value[id]?.length > 0; // Return true if permission exists and not empty
};

const fetchPermissions = async () => {
  isLoadingPermissions.value = true; // Start loading
  const subprojects =
  props.project?.subprojects?.length > 0
    ? props.project.subprojects
    : props.project?.parent_project?.subprojects || [];
  for (const subproject of subprojects) {
    await getTaskDeployPermissions(subproject.id);
  }

  isLoadingPermissions.value = false; // End loading
};

// Updated logic for selecting the repository
const processSubprojects = () => {
  if (props.project?.subprojects && props.project?.subprojects?.length > 0) {

    // Collect subprojects with permission in an array
    let foundSubprojects = [];
    for (const subproject of props.project.subprojects) {
      if (hasPermission(subproject.id)) {
        foundSubprojects.push(subproject);
      }
    }

    if (foundSubprojects.length > 0) {
      isMainProject.value = true;
      repo.value = foundSubprojects[0].repository_name; // Select the first repository with permission
    } else {
      // No subprojects with permission
      console.log('No accessible subprojects');
      isMainProject.value = false;
      repo.value = null; // Or handle as needed if no accessible repos
    }
  } else if (
    props.project?.parent_project?.subprojects &&
    props.project?.parent_project?.subprojects?.length > 0
  ) {
    console.log('Checking parent project subprojects');

    // Collect parent subprojects with permission in an array
    let foundParentSubprojects = [];
    for (const subproject of props.project.parent_project.subprojects) {
      if (hasPermission(subproject.id)) {
        foundParentSubprojects.push(subproject);
      }
    }

    if (foundParentSubprojects.length > 0) {
      console.log('parent project subprojects');
      isMainProject.value = true;
      repo.value = foundParentSubprojects[0].repository_name; // Select the first repository with permission
    } else {
      // No parent subprojects with permission
      console.log('No accessible parent project subprojects');
      isMainProject.value = false;
      repo.value = null; // Or handle as needed if no accessible repos
    }
  } else {
    // No subprojects or parent project subprojects available
    console.log('No subprojects or parent project subprojects');
    isMainProject.value = false;
    repo.value = null;
  }
};

// Fetch permissions and process subprojects after fetching is complete
onMounted(async () => {
  await fetchPermissions();
  processSubprojects(); // Ensure permissions are fully loaded before this is called
});

</script>

<template>
  <div class="inline-flex gap-3" v-if=" Object.values(deployPermissions).filter(value => value.length > 0).length > 0  && isReview">
    <!-- <AppTextInput v-model="repo" name="Repo" required placeholder="Category"/> -->

    <div class="w-[240px]" v-if="project?.subprojects && project?.subprojects?.length > 0">
  <select
    v-model="repo"
    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 text-sm rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-2 h-[44px]"
    placeholder="Select Repository"
  >
    <option value="null" disabled>Select Repository</option>
    <template v-for="elem in project.subprojects" :key="elem.id">
      <option
        v-if="elem.repository_name && hasPermission(elem.id)"
        :value="elem.repository_name"
      >
        {{ elem.repository_name }}
      </option>
    </template>
  </select>
</div>
<div class="w-[240px]" v-else>
  <select
    v-model="repo"
    class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 text-sm rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-2 h-[44px]"
    name="priority"
    placeholder="Select Repository"
  >
    <option value="null" disabled>Select Repository</option>
    <template v-for="elem in project.parent_project.subprojects" :key="elem.id">
      <option
        v-if="elem.repository_name && hasPermission(elem.id)"
        :value="elem.repository_name"
      >
        {{ elem.repository_name }}
      </option>
    </template>
  </select>
</div>

    <app-button outline @click="openPullRequestModal">
      <span v-if="props.buttonFlag === 0">
          Pull Request
        </span>
        <AppIcon v-else name="fa-code-pull-request"/>
        </app-button>
  </div>
  <Modal :show="isModelOpen" @close="closeModal" maxWidth="8xl">
    <template #header>
      <h2 class="text-xl font-semibold">Pull Requests</h2>
    </template>

    <div class="h-[100px] w-full" v-if="isLoading">
      <AppLoader />
    </div>
    <div class="w-3xl max-h-[80vh]" v-if="pullRequestData && pullRequestData.length > 0">
      <AppDataTable :data="pullRequestData" :columns="columns"></AppDataTable>
    </div>
    <div v-else  class="h-[300px] w-full flex items-center justify-center">
      <span>No Data found</span>
    </div>
  </Modal>

  <Modal :show="isMergeModalOpen" @close="closeMergeModal" maxWidth="xl">
    <template #header>
      <h2 class="text-xl font-semibold">{{ modalTitle }}</h2>
    </template>

    <div class="flex flex-col gap-10 px-8 pt-3 pb-6">
      <span v-html="modalDescription" v-if="modalDescription"></span>
      <span v-else>
        Ary sure you want to merge?
      </span>
      <div class="flex justify-end gap-4">
        <app-button text type="button" @click="closeMergeModal">Cancel</app-button>
        <app-button type="button" @click="handleSubmitMerge">Merge</app-button>
      </div>
    </div>
  </Modal>
</template>
