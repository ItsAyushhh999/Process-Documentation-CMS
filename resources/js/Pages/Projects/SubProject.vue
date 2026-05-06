<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import AppButton from '@/components/AppButton.vue';
import AppTextInput from '@/components/AppTextInput.vue';
import AppSelect from '@/components/AppSelect.vue';
import Modal from '@/components/Modal.vue';
import { ref,computed } from 'vue';
import ProjectCard from '../../components/ProjectComponent/ProjectCard.vue';
import AppEditor from '@/components/AppEditor.vue';

// import Modal from '@/components/Modal.vue'
// import AppButton from '@/components/AppButton.vue'
// import AppTextInput from '@/components/AppTextInput.vue'
// import AppEditor from '@/components/AppEditor.vue'

const props = defineProps({
  sub_projects: {
    type: Array,
    default: []
  },
  project_id: {
    type: Number,
    default: null
  }
  // status: {
  //   type: String ,
  //   default: '1'
  // },
  // documentId: {
  //   type: [Number, String],
  //   default: null
  // }
});

const is_super_admin = usePage().props.auth.user.is_super_admin;

const isModelOpen = ref(false);
const isSubProjectModalOpen = ref(false);

const form = useForm({
  project_id_for_sub_project: null,
  id: null,
  name: null,
  description: '',
  url: null,
  development_pipeline: null,
  staging_pipeline: null,
  production_Pipeline: null,
  repository_name: null,
  deployment_mappings: [],
});

const deploymentStages =  [
        { value: '0', label: 'Development' },
        { value: '1', label: 'Staging' },
        { value: '2', label: 'Production' },
];

const closeModal = () => {
  isModelOpen.value = false;
  isSubProjectModalOpen.value = false;
  form.reset();
  form.clearErrors()
};
// For confirmation modal
const mappingToDeleteIndex = ref(null);
const mappingCount = ref(null);

const mappingStage = computed(() => {
  // Derive mappingStage reactively from form.deployment_mappings
  return form.deployment_mappings.map((mapping) => mapping.stage || '');
});


// Utility function to initialize or map deployment_mappings
const initializeDeploymentMappings = (mappings) => {
  if (!mappings || mappings.length === 0) {
    // Default if no mappings exist
    return [];
  }
  // Map existing mappings
  return mappings.map((mapping) => ({
    id: mapping.id || null,
    stage: mapping.stage || '',
    account_identifier: mapping.account_identifier || '',
    role_session_name: mapping.role_session_name || '',
  }));
};

const openEditModal = (data) => {
  console.log("data", data)
  form.id = data.id;
  form.name = data.name;
  form.description = data.description;
  form.url = data.url;
  form.development_pipeline = data.development_pipeline;
  form.staging_pipeline = data.staging_pipeline;
  form.production_Pipeline = data.production_Pipeline;
  form.repository_name = data.repository_name;
  // Use utility function to initialize deployment mappings
  form.deployment_mappings = initializeDeploymentMappings(data.deployment_mappings);
  mappingCount.value = form.deployment_mappings.length - 1;
  isModelOpen.value = true;
};

const subProjects = ref(props.sub_projects);


const updateProjectInList = (updatedProject) => {
  const index = subProjects.value.findIndex(project => project.id === updatedProject.id);
  if (index !== -1) {
    subProjects.value.splice(index, 1, updatedProject); // Replaces the project at the given index
  }
};

const handleSubmitEdit = () => {
    // eidt
    console.log('form', form.data())

  form.put(route('projects.update', form.id), {
    onSuccess: () => {
      const updatedProject = { ...form.data() };
      updateProjectInList(updatedProject);
      closeModal();
    },
    onError: (error) => {
      console.log('error', error);
    }
  });
};

const isConfirmModalOpen = ref(false); // Confirmation modal for delete

const firstColumn = computed(() => {
  return props.sub_projects.filter((_, index) => index % 3 === 0);
});

const secondColumn = computed(() => {
  return props.sub_projects.filter((_, index) => index % 3 === 1);
});

const thirdColumn = computed(() => {
  return props.sub_projects.filter((_, index) => index % 3 === 2);
});

const confirmRemoveMapping = (index) =>
  mappingCount.value >= index
    ? (mappingToDeleteIndex.value = index, isConfirmModalOpen.value = true)
    : removeMapping(index);

const addMapping =()=> {
    if (form.deployment_mappings.length < 3) {
       form.deployment_mappings.push({ id: null , stage: '',account_identifier:'', role_session_name: 'PipelineApprovalSession' });
    }
    }

const removeMapping = (index = mappingToDeleteIndex.value) => {
  if (index <= mappingCount.value) mappingCount.value--;
  form.deployment_mappings.splice(index, 1);
  closeConfrimModal();
};


const closeConfrimModal = () => {
  isConfirmModalOpen.value = false;
  mappingToDeleteIndex.value = null;
};



const isCurrentMappingValid = computed(() => {
  const lastMapping = form.deployment_mappings[form.deployment_mappings.length - 1];

  return (
    lastMapping &&
    lastMapping.stage &&
    lastMapping.role_session_name &&
    lastMapping.account_identifier !== ''
  );
});

const openMapping = () => {
  form.deployment_mappings.push({
    stage: '',
    account_identifier: '',
    role_session_name: 'PipelineApprovalSession'
  });
};

</script>
<template>
  <Head title="Projects" />
  <AuthenticatedLayout>
    <div
      class="w-full  grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-5 items-start"
      v-if="sub_projects && sub_projects.length > 0"
    >
      <!-- {{ sub_projects }} -->
      <!-- <template v-for="project in sub_projects">
        <ProjectCard
          :data="project"
          @openEditModal="openEditModal"
          :is_super_admin="is_super_admin == 1"
          isSubProject
        />
      </template> -->

      <div className="grid gap-4">
        <template v-for="project in firstColumn">
          <ProjectCard
            :data="project"
            @openEditModal="openEditModal"
            :is_super_admin="is_super_admin == 1"
            @openAddSubProject="openAddSubProject"
            :isSubProject="true"
          />
        </template>
      </div>
      <div className="grid gap-4">
        <template v-for="project in secondColumn">
          <ProjectCard
            :data="project"
            @openEditModal="openEditModal"
            :is_super_admin="is_super_admin == 1"
            @openAddSubProject="openAddSubProject"
            :isSubProject="true"
          />
        </template>
      </div>
      <div className="grid gap-4">
        <template v-for="project in thirdColumn">
          <ProjectCard
            :data="project"
            @openEditModal="openEditModal"
            :is_super_admin="is_super_admin == 1"
            @openAddSubProject="openAddSubProject"
            :isSubProject="true"
          />
        </template>
      </div>
    </div>

    <!-- add Sub project -->
    <Modal :show="isModelOpen" @close="closeModal">
        <Modal :show="isConfirmModalOpen" @close="closeConfrimModal">
        <template #header>
        <div class="sm:flex sm:items-start">
            <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-red-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                <svg class="w-6 h-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="pt-1 text-center sm:mt-0 sm:ml-4 sm:text-left">
                Confirm Delete
            </div>
        </div>
        </template>
        <div class="p-4">
            <p class="mt-4">Are you sure you want to delete this mapping? </p>
        </div>
            <div class="flex justify-end gap-2 mt-4 p-2 ">
                <AppButton  variant="secondary" @click="closeConfrimModal">No</AppButton>
                <AppButton  class="ms-3 bg-red-500" @click="removeMapping(mappingToDeleteIndex.value)">Yes</AppButton>
            </div>

    </Modal>
      <template #header>
        <h2 class="text-xl font-semibold">
          {{ form.id ? 'Edit Project' : 'Add Project' }}
        </h2>
      </template>
      <form class="p-6 px-8 grid gap-5 w-full" @submit.prevent="handleSubmitEdit">
        <AppTextInput
          label="Project Name"
          v-model="form.name"
          name="name"
          required
          placeholder="Project Name"
          :errorMessage="form.errors.name"
          @focus="form.clearErrors('name')"
        />

        <div class="flex flex-col">
          <label
            for=""
            class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
            >Description</label
          >
          <AppEditor v-model="form.description" name="description" />
          <div v-if="form.errors.description" class="text-red-500">
            {{ form.errors.description }}
          </div>

        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <AppTextInput
            label="Url"
            v-model="form.url"
            name="url"
            placeholder="Url"
            :errorMessage="form.errors.url"
            @focus="form.clearErrors('url')"
        />

        <AppTextInput
            label="Repository Name"
            v-model="form.repository_name"
            name="repository_name"
            placeholder="Github Repository Name"
            :errorMessage="form.errors.repository_name"
            @focus="form.clearErrors('repository_name')"
        />

        <AppTextInput
            label="Development Pipeline"
            v-model="form.development_pipeline"
            name="development_pipeline"
            placeholder="Development Pipeline"
            :errorMessage="form.errors.development_pipeline"
            @focus="form.clearErrors('development_pipeline')"
        />

        <AppTextInput
            label="Staging Pipeline"
            v-model="form.staging_pipeline"
            name="staging_pipeline"
            placeholder="Staging Pipeline"
            :errorMessage="form.errors.staging_pipeline"
            @focus="form.clearErrors('staging_pipeline')"
        />

        <AppTextInput
            label="Production Pipeline"
            v-model="form.production_Pipeline"
            name="production_Pipeline"
            placeholder="Production Pipeline"
            :errorMessage="form.errors.production_Pipeline"
            @focus="form.clearErrors('production_Pipeline')"
        />
</div>
<div class="pt-3 mt-6 border-t border-gray-300 dark:border-gray-600">
<AppButton
      text
      @click="openMapping"
      v-if = "!form.deployment_mappings.length > 0"
      class="add_field_button"
    >
      Add Deployment Accounts
    </AppButton>
    </div>

     <div v-if="form.deployment_mappings.length > 0">
        <h3 class="text-lg font-semibold mt-4">Deployment Accounts</h3>
        <div
          v-for="(mapping, index) in form.deployment_mappings"
          :key="index"
          class="relative grid grid-cols-1 gap-3 mt-4 border border-gray-300 p-4 rounded-lg"
        >
          <AppButton
            text
            class="absolute top-2 text-red-500 ml-1 transition-all bg-red-400 rounded-full cursor-pointer -right-6 bg-opacity-10 hover:bg-opacity-20"
             @click="confirmRemoveMapping(index)"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-4 h-4"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
              />
            </svg>
          </AppButton>
          <label
              class="block text-sm font-medium text-gray-700 capitalize dark:text-slate-400"
            >
              Stage <span class="text-red-500">*</span>

              <div>
                <div v-if="mapping?.stage && index <= mappingCount"  class="p-4 rounded-lg border dark:border-slate-700 text-gray-500 whitespace-normal dark:text-gray-300 truncate">
                    <span>{{ deploymentStages.find(stage => stage.value === mapping.stage)?.label || 'Unknown Stage' }}</span>
                </div>
                <select
            v-else
            label="Stage"
            v-model="mapping.stage"
            name="stage"
            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-2 h-[44px]"
            required
        >
            <option disabled value="">Select Stage Name</option>
            <option
                v-for="stage in deploymentStages.filter(stage => stage.value === mapping?.stage || !mappingStage.includes(stage.value))"
                :key="stage.value"
                :value="stage.value"
            >
                {{ stage.label }}
            </option>
        </select>


        </div>

          <span v-if="form.errors[`deployment_mappings.${index}.stage`]">
            {{ form.errors[`deployment_mappings.${index}.stage`] }}
          </span>
          </label>

          <AppTextInput
            label="Role Session Name"
            v-model="mapping.role_session_name"
            name="role_session_name"
            placeholder="Role Session Name"
            :errorMessage="form.errors[`deployment_mappings.${index}.role_session_name`] || ''"
            @focus="form.clearErrors(`deployment_mappings.${index}.role_session_name`)"
            required
          />

          <div>
            <label
              class="block text-sm font-medium text-gray-700 capitalize dark:text-slate-400"
            >
              Account Identifier <span class="text-red-500">*</span>
            </label>
            <div class="flex items-center space-x-4">
              <label class="flex items-center space-x-2">
                <input
                  type="radio"
                  :name="'account_identifier_' + index"
                  :value="0"
                  v-model="mapping.account_identifier"
                  class="form-radio h-4 w-4 text-primary-500 border-gray-300 focus:ring-primary-500"
                  required
                />
                <span class="block text-sm font-medium text-gray-700 capitalize dark:text-slate-400">Voxmg</span>
              </label>
              <label class="flex items-center space-x-2">
                <input
                  type="radio"
                  :name="'account_identifier_' + index"
                  :value="1"
                  v-model="mapping.account_identifier"
                  class="form-radio h-4 w-4 text-primary-500 border-gray-300 focus:ring-primary-500"
                  required
                />
                <span class="block text-sm font-medium text-gray-700 capitalize dark:text-slate-400">Voxships</span>
              </label>
            </div>

          </div>
          </div>
          <div class="pt-3 mt-6 border-t border-gray-300 dark:border-gray-600">
           <AppButton outline
                @click="addMapping"
                :disabled="!isCurrentMappingValid"
                v-if="form.deployment_mappings.length< 3"
                class="add_field_button dark:text-gray-300 dark:border-gray-600">Add
                More Role</AppButton>
         </div>
        </div>
        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">{{
            form.id ? 'Update' : 'Add Project'
          }}</AppButton>
        </div>
      </form>
    </Modal>

</AuthenticatedLayout>
</template>
