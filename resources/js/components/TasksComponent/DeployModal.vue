<script setup>
import AppButton from '@/components/AppButton.vue';
import Modal from '@/components/Modal.vue';
import { computed, inject, ref, watch,onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import AppLoader from "@/components/AppLoader.vue"
import axios from 'axios';
import AppIcon from '@/components/AppIcon.vue';
import { useDateFormat } from '@vueuse/core';

const props = defineProps({
  project: {
    type: Object,
    default: null
  },
  projects: {
    type: Array,
    default: []
  },
  taskId: {
    type: [Number, String],
    default: null
  },
  collaborators: {
    type: []
  },
  user: {
    type: [Object, Boolean]
  },
  // 0: text, 1:icon
  buttonFlag:{
    type: Number,
    default: 0
  }
});


const filters = inject('filters');
const isModelOpen = ref(false);
const isModelOpenInfo = ref(false);
const deployPermissions = ref({});
const Permissions = ref([]);
const isLoadingPermissions = ref(false);
const deployInformation = ref({});
const isProcessingForm = ref(false);
const hasPermission = ref(false);
const loading = ref(true);
const { flashMessage } = usePage().props;

const deploy = ref({
  project_id: props.project.id,
  stage_name: null,
  task_id: props.taskId
});
const isApproved = ref(false);
const deployForm = useForm({
  deployProjectId: '',
  deploy_token: '',
  deploy_taskId: '',
  deploy_projectName: '',
  project_id: '',
  deploy_pipeline_name: '',
  deploy_stage_name: '',
  stage_name: '',
  deploy_action_name: '',
  deploy: ''
});

const form = useForm({
  stage_name: null,
  project_id: props.project.id,
  task_id: props.taskId
});

const errors = ref({
      stage_name: null,
    });
function checkIdExists(idToCheck) {
  const filteredCollaborators = props.collaborators.filter(item => item.flag === '1');
  return filteredCollaborators.some(item => item.collaborator === idToCheck);
}

hasPermission.value = typeof props.user  == 'boolean' ? props.user : checkIdExists(props.user.id);
// console.log('props.auth.user', hasPermission.value)
watch(
  () => props.collaborators,
  () => {
    hasPermission.value = typeof props.user =='boolean' ? props.user :  checkIdExists(props.user.id);
  }
);

const getTaskDeployPermissions = async (id) => {
  try {
    const result = await axios.get(route("task.deploy.permission", [id]));
    deployPermissions.value[id] = result?.data?.permissions; // Store permission status
    const uniquePermissions = [...new Set(result.data.permissions)];

    // Assign only the unique permissions to deployPermissions.value
    Permissions.value = uniquePermissions;

  } catch (error) {
    console.error(`Error fetching permissions for project ID ${id}:`, error);
    deployPermissions.value[id] = []; // Default to no permission
  }
};

const hasDeployPermission = (id) => {
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

const openDeployLogsModal = async () => {
  isModelOpen.value = true;
};

const openDeployInfoModal = () => {
  isModelOpenInfo.value = true;
};

const handleDeploySubmit = async () => {
  try {
    isProcessingForm.value = true;
    loading.value = true;
    console.log(deploy.value);
    const response = await axios.post(route('deploy'), deploy.value);
    if (response.status === 200) {
      deployInformation.value = response.data;
      isProcessingForm.value = false;
      closeModal();
      deployInformation.value.pipeline_error
        ? sendFlashMessage("error",deployInformation.value.pipeline_error)
        : (openDeployInfoModal());
    } else {
      isProcessingForm.value = false;
    }
    loading.value = false;
  } catch (error) {
    console.error("Deployment error:", error);
    errors.value.stage_name = error.response.data.message;
    isProcessingForm.value = false;
    loading.value = false;
  }
};

const sendFlashMessage = (type, message) => {
  flashMessage.value = { [type]: message };
};

const handleDeploySubmitResult = () => {
  axios.post(route('deploy.result'), deployForm).then((res) => {
    if (res.data.code === 200) {
      handleDeploySubmit();
    } else {
      console.log('deploy failed.');
    }
    isProcessingForm.value = false;
  });
};

const prepareDeployData = (actionstate, stageName) => {
  if (actionstate?.latestExecution?.status === 'InProgress') {
    const pipeline_token = actionstate.latestExecution.token;
    deployForm.deploy_action_name = actionstate.actionName;
    isApproved.value = pipeline_token ? true : false;
    deployForm.deploy_token = pipeline_token;
    deployForm.deploy_stage_name = stageName;
    deployForm.deployProjectId = deployInformation.value.parentProjectId;
    deployForm.deploy_taskId = deployInformation.value.taskId;
    deployForm.deploy_projectName = deployInformation.value.task_details;
    deployForm.project_id = deployInformation.value.projectId;
    deployForm.stage_name = deployInformation.value.stage_name;
    deployForm.deploy_pipeline_name = deployInformation.value.pipeline_name;
  }
};

const onChangeProject = (event) => {
  getTaskDeployPermissions(event.target.value);
};

const prepareUrl = (data) => {
  let url = data.revisionUrl || null;
  let value = ``;

  if (!url) return;

  let urlObj = new URL(url);
  let commit = urlObj.searchParams.get('Commit');
  let commit_hash = (commit || '').substring(0, 7);
  let repositoryId = urlObj.searchParams.get('FullRepositoryId');

  if (commit) {
    value +=
    `<div class="mb-2">
        <span class="text-red-900 bg-red-200 px-3 py-1 rounded-full text-sm font-semibold whitespace-nowrap">
            <a href="https://github.com/${repositoryId}/commit/${commit}" target="_blank">
                ${commit_hash}
            </a>
        </span>
    </div>`;
  }

  if (repositoryId) {
    value += `<div class="text-center"> <span class="text-green-900 bg-green-200 px-3 py-1 rounded-full text-sm font-semibold whitespace-nowrap">${repositoryId}</span> </div>`;
  }

  return value;
};

const closeModal = () => {
  isModelOpen.value = false;
  errors.value.stage_name = '';
  //   deploy.value = {
  //       project_id: props.project.id,
  //       stage_name: null,
  //       task_id: props.taskId
  //   }
};

const closeModalInfo = () => {
  isModelOpenInfo.value = false;
  deployForm.reset();
};

const copyText = (elem) => {
  if(elem){
    navigator.clipboard.writeText(elem)
  }else{
    navigator.clipboard.writeText('')
  }
}

onMounted(async () => {
  await fetchPermissions();
});


</script>

<template>
  <div class="inline-flex gap-3">
    <app-button
      outline
      @click="openDeployLogsModal"
      v-if="( Object.values(deployPermissions).filter(value => value.length > 0).length > 0 || (typeof props.user  == 'boolean' && props.user)) && hasPermission"
      >
        <span v-if="props.buttonFlag === 0">
          Deploy
        </span>
        <AppIcon v-else name="fa-rocket"/>
      </app-button>
  </div>

  <Modal :show="isModelOpen" @close="closeModal" maxWidth="xl">
    <template #header>
      <h2 class="text-xl font-semibold">Deploy</h2>
    </template>
    <div class="px-8 pt-4 pb-8">
      <form @submit.prevent="handleDeploySubmit" class="grid gap-5">
        <!-- {{ console.log("projects potato", projects) }} -->
        <!-- {{ project }} -->
        <div class="grid" v-if="project?.subprojects && project?.subprojects?.length > 0">
          <label for="stage_name">Select Project</label>
          <!-- {{ filteredProjects }} ss -->
          <select
            v-model="deploy.project_id"
            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 text-sm rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-2 h-[44px]"
            name="stage_name"
            @change="onChangeProject"
          >
            <!-- @change="handleProjectChange" -->
            <option :value="null" disabled>Select Project</option>
            <template v-for="item in project.subprojects">
              <option v-if="item.repository_name && hasDeployPermission(item.id)"  :value="item.id">{{ item.name }}</option>
            </template>
          </select>
        </div>
        <div class="grid" v-else>
        <label for="stage_name">Select Project</label>
        <select
            v-model="deploy.project_id"
            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 text-sm rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-2 h-[44px]"
            name="stage_name"
            @change="onChangeProject"
        >
            <option :value="null" disabled>Select Project</option>
            <template v-if="project?.parent_project && project?.parent_project?.subprojects" v-for="item in project.parent_project.subprojects">
            <option v-if="item.repository_name && hasDeployPermission(item.id)"  :value="item.id">{{ item.name }}</option>
            </template>
        </select>
        </div>

        <div class="grid">
          <!-- {{ deployPermissions }} -->
          <label for="stage_name">Select Deployment Branch</label>

          <select
            v-model="deploy.stage_name"
            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 text-sm rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-2 h-[44px]"
            name="stage_name"
          >
            <option :value="null" disabled>Select Branch</option>
            <option
              v-for="deployPermission in Permissions"
              :key="deployPermission"
              :value="deployPermission.toLowerCase()"
            >
              {{ deployPermission }}
            </option>
          </select>
          <span v-if="errors.stage_name">{{ errors.stage_name }}</span>
        </div>

        <div class="flex justify-end gap-3 pt-10">
          <AppButton type="button" text @click="closeModal">Cancel</AppButton>
          <AppButton type="submit" :disabled="deploy.processing">{{
            !isProcessingForm ? 'Request Deploy' : 'Processing'
          }}</AppButton>
        </div>
      </form>
    </div>
  </Modal>
  <Modal :show="isModelOpenInfo" @close="closeModalInfo" maxWidth="8xl">
    <template #header>
        <div class="flex justify-center items-center gap-4">
      <h2 class="text-xl font-semibold">Deploy</h2>
      <button title="Copy Entity Url" @click="handleDeploySubmit()" class="hover:bg-primary-50 rounded-full p-1">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
            />
          </svg>
        </button>
        </div>
    </template>
    <div class="px-8 pt-4 pb-8 min-h-[320px]">
      <div v-if="loading">
        <AppLoader></AppLoader>
      </div>
      <div>
        <div class="overflow-x-auto">
            <table class="min-w-full max-w-[80vw] border border-gray-200 divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                    Stage Name
                </th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                    Action Name
                </th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                    Status
                </th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                    Summary
                </th>
                <th class="py-3 text-left text-xs font-medium text-gray-500 uppercase">Changes</th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                    Entity Url
                </th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                    Revision Url
                </th>
                <th
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                >
                    Updated Date
                </th>
                </tr>
            </thead>
            <tbody  class="bg-white divide-y divide-gray-200">
                <template v-for="stage in deployInformation?.stageStates">
                <tr v-for="action in stage?.actionStates">
                    {{
                    prepareDeployData(action, stage.stageName)
                    }}
                    <td class="px-6 py-4 whitespace-normal max-w-20">
                    <p class="text-sm text-gray-900 capitalize">{{ stage.stageName }}</p>
                    </td>
                    <td class="px-6 py-4 whitespace-normal max-w-20">
                    <p class="text-sm text-gray-900 capitalize">{{ action.actionName }}</p>
                    </td>
                    <td class="px-6 py-4 whitespace-normal max-w-20">
                    <span class="text-sm text-gray-900">{{ action.latestExecution?.status }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-normal">
                    <p class="text-sm text-gray-900">{{ action.latestExecution?.summary }}</p>
                    </td>
                    <td v-html="prepareUrl(action)"></td>

                    <td class="px-6 py-4 whitespace-normal max-w-20">
                    <button title="Copy Entity Url" type="button" @click="copyText(action.entityUrl)">
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6"
                        >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"
                        />
                        </svg>
                    </button>
                    </td>
                    <td class="px-6 py-4 whitespace-normal max-w-[20%]">
                    <button title="Copy Revision Url"  type="button" @click="copyText(action.revisionUrl)">
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6"
                        >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"
                        />
                        </svg>
                    </button>
                    </td>

                    <td class="px-6 py-4 whitespace-normal">
                    <p class="text-sm text-gray-900 whitespace-nowrap">
                        <!-- {{ formatDate(action?.latestExecution?.lastStatusChange) }} -->
                        {{ useDateFormat(action?.latestExecution?.lastStatusChange, 'DD/MM/YYYY hh:mm A', { locales: 'en-US' }).value}}
                          <!-- {{ filters.formatDate(action?.latestExecution?.lastStatusChange) }} -->
                    </p>
                    </td>
                </tr>
                </template>
            </tbody>
            </table>
        </div>
        <div v-if="isApproved" class="bg-gray-50 border-gray-300 border rounded-md my-5">
            <div class="rounded mb-3 px-3 py-3 max-w-[480px]">
            <form @submit.prevent="handleDeploySubmitResult">
                <span>Approve or Reject the Deployment Changes {{ isApproved }}</span>
                <div class="flex items-center gap-5">
                <div class="grid w-[40rem] grid-cols-2 gap-2 rounded-xl bg-gray-200 p-2">
                    <div>
                    <input
                        type="radio"
                        v-model="deployForm.deploy"
                        name="deploy"
                        id="approved"
                        value="Approved"
                        class="peer hidden"
                        required
                    />
                    <label
                        for="approved"
                        class="border border-blue-300 block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-blue-900 peer-checked:font-bold hover:bg-blue-500 peer-checked:text-white hover:text-white"
                        >Approve</label
                    >
                    </div>
                    <div>
                    <input
                        type="radio"
                        name="deploy"
                        v-model="deployForm.deploy"
                        id="rejected"
                        value="Rejected"
                        class="peer hidden"
                    />
                    <label
                        for="rejected"
                        class="border border-red-300 block cursor-pointer select-none rounded-xl p-2 text-center peer-checked:bg-red-900 hover:bg-red-500 peer-checked:font-bold peer-checked:text-white hover:text-white"
                    >
                        Reject
                    </label>
                    </div>
                </div>

                <AppButton type="submit" :disabled="isProcessingForm">{{
                    !isProcessingForm ? 'Deploy' : 'Processing'
                }}</AppButton>
                </div>
            </form>
            </div>
        </div>
      </div>
    </div>
  </Modal>
</template>
