<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import AppTextInput from '@/components/AppTextInput.vue';
import Modal from '@/components/Modal.vue';
import { ref } from 'vue';
import formatterComponent from '@/utils/formatterComponent';
import AppSwitch from '@/components/AppSwitch.vue';
import ProjectCard from '../../components/ProjectComponent/ProjectCard.vue';
import AddProjectModal from '../../components/ProjectComponent/AddProjectModal.vue';
import AppEditor from '@/components/AppEditor.vue';

// import Modal from '@/components/Modal.vue'
// import AppButton from '@/components/AppButton.vue'
// import AppTextInput from '@/components/AppTextInput.vue'
// import AppEditor from '@/components/AppEditor.vue'

const props = defineProps({
  projects: {
    type: Array,
    default: []
  }
  // status: {
  //   type: String ,
  //   default: '1'
  // },
  // documentId: {
  //   type: [Number, String],
  //   default: 2
  // }
});

// console.log('projects', props.projects)

const is_super_admin = usePage().props.auth.user.is_super_admin;

const isModelOpen = ref(false);
const isSubProjectModalOpen = ref(false);

const closeModal = () => {
  isModelOpen.value = false;
  isSubProjectModalOpen.value = false;
  form.reset();
  form.clearErrors();
};

const form = useForm({
  project_id_for_sub_project: null,
  id: null,
  name: null,
  description: '',
  url: null,
  development_pipeline: null,
  staging_pipeline: null,
  production_Pipeline: null,
  repository_name: null
});

const handleSubmitCreateEdit = () => {
  if (form.id) {
    // eidt
    form.put(route('projects.update', form.id), {
      onSuccess: () => {
        closeModal();
      },
      onError: (error) => {
        console.log('error', error);
      }
    });
  } else {
    // create
    form.post(route('projects.store'), {
      onSuccess: () => {
        closeModal();
      },
      onError: (error) => {
        console.log('error', error);
      }
    });
  }
};
const openEditModal = (data) => {
  form.id = data.id;
  form.name = data.name;
  form.description = data.description;
  form.url = data.url;
  form.development_pipeline = data.development_pipeline;
  form.staging_pipeline = data.development_pipeline;
  form.production_Pipeline = data.production_Pipeline;

  isModelOpen.value = true;
};

const openAddSubProject = (id) => {
  form.project_id_for_sub_project = id;
  isSubProjectModalOpen.value = true;
};
const handleAddSubProjectSubmit = () => {
  form.post(route('subProjects'), {
    onSuccess: () => {
      closeModal();
    },
    onError: (error) => {
      console.log('error', error);
    }
  });
};

const firstColumn = props.projects.filter((_, index) => index % 3 === 0);
const secondColumn = props.projects.filter((_, index) => index % 3 === 1);
const thirdColumn = props.projects.filter((_, index) => index % 3 === 2);

</script>
<template>
  <Head title="Projects" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center gap-4">
        <app-button @click="isModelOpen = true" v-if="is_super_admin == 1">Add Projects</app-button>
      </div>
    </template>
    <div
      class="w-full grid xl:grid-cols-3 lg:grid-cols-2 grid-cols-1 gap-5 items-start"
      v-if="projects && projects.length > 0"
    >
      <div className="grid gap-4">
        <template v-for="project in firstColumn">
          <ProjectCard
            :data="project"
            @openEditModal="openEditModal"
            :is_super_admin="is_super_admin == 1"
            @openAddSubProject="openAddSubProject"
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
          />
        </template>
      </div>
    </div>

    <!-- add project -->
    <Modal :show="isModelOpen" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">
          {{ form.id ? 'Edit Project' : 'Add Project' }}
        </h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitCreateEdit">
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

        <AppTextInput
          label="Url"
          v-model="form.url"
          name="url"
          placeholder="Url"
          :errorMessage="form.errors.url"
          @focus="form.clearErrors('url')"
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
          label="Production Pipelilne"
          v-model="form.production_Pipeline"
          name="production_Pipeline"
          placeholder="Production Pipeline"
          :errorMessage="form.errors.production_Pipeline"
          @focus="form.clearErrors('production_Pipeline')"
        />
        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">{{
            form.id ? 'Edit Project' : 'Add Project'
          }}</AppButton>
        </div>
      </form>
    </Modal>

    <!-- add sub Project -->
    <Modal :show="isSubProjectModalOpen" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Add Sub Project</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleAddSubProjectSubmit">
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
          label="Production Pipelilne"
          v-model="form.production_Pipeline"
          name="production_Pipeline"
          placeholder="Production Pipeline"
          :errorMessage="form.errors.production_Pipeline"
          @focus="form.clearErrors('production_Pipeline')"
        />
        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Add Project</AppButton>
        </div>
      </form>
    </Modal>
  </AuthenticatedLayout>
</template>
