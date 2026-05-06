<script setup>
import AppButton from '@/components/AppButton.vue';
import AppEditor from '@/components/AppEditor.vue';
import AppTextInput from '@/components/AppTextInput.vue';
import AppAutocomplete from '@/components/AppAutocomplete.vue';
import AppDatepicker from '@/components/AppDatepicker.vue';

import Modal from '@/components/Modal.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import { computed, inject, ref, watch } from 'vue';

const filters = inject('filters');
const is_super_admin = usePage().props.auth.user.is_super_admin;
const props = defineProps({
  data: {
    type: Object,
    default: null
  },
  projects: {
    type: Array,
    default: []
  },
  task_types: {
    type: Array,
    default: []
  },
  users: {
    type: Array,
    default: []
  }
});

const isModelOpen = ref(false);
const selectedProject = ref(null);
const assignees = ref([]);
const reviewers = ref([]);
const selectedImage = ref();
const selectedTaskTypes = ref([]);

const selectFiles = (e) => {
  const files = Array.from(e.target.files);
  selectedImage.value = files;
};

const closeModal = () => {
  isModelOpen.value = false;
  selectedProject.value = null;
  selectedImage.value = null;
  assignees.value = [];
  reviewers.value = [];
  selectedTaskTypes.value = [];
  form.reset();

  const formattedDate = filters.formatDateForDatePicker(new Date().setHours(17, 0, 0, 0));
  form.deadline = formattedDate;
};

var newDate = new Date();
newDate.setHours(17, 0, 0, 0);
const form = useForm({
  project_id: null,
  title: '',
  description: '',
  task_type: [],
  assignees: [],
  reviewer: [],
  deadline: newDate,
  priority: '0',
  attachments: []
});

const handleSubmitCreate = () => {
  form.project_id = selectedProject.value ? selectedProject.value.id : null;
  form.task_type = selectedTaskTypes.value.map((elem) => elem.id);
  form.assignees = assignees.value.map((elem) => elem.id);
  form.reviewer = reviewers.value.map((elem) => elem.id);
  form.attachments = selectedImage.value;

  form.post(route('tasks.store'), {
    onSuccess: () => {
      closeModal();
    },
    onError: (error) => {
      console.log('error', error);
    }
  });
};

const openModel = () => {
  isModelOpen.value = true;
  router.visit(route('tasks.index'), {
    method: 'GET', // Request method (GET in this case)
    preserveState: true, // Keep page state (no reload)
    only: ['projects', 'task_types', 'users'] // Update only the props that need to change
  });
};
</script>

<template>
  <app-button @click="openModel()" v-if="is_super_admin == 1">Create Task</app-button>

  <Modal :show="isModelOpen" @close="closeModal" maxWidth="4xl" :closeable="false">
    <template #header>
      <h2 class="text-xl font-semibold">Create Task</h2>
    </template>
    <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitCreate">
      <!-- {{ selectedProject }} -->
      <app-autocomplete
        v-model="selectedProject"
        placeholder="Project"
        :options="projects"
        select-label="Enter"
        track-by="id"
        name="name"
        label="name"
        group-values="subprojects"
        group-label="group_name"
      >
        <template #default="{ option }">
          <div class="flex items-center gap-2">
            <div class="grow">{{ option.name }}</div>
          </div>
        </template>
      </app-autocomplete>
      <AppTextInput
        label="Title"
        v-model="form.title"
        name="title"
        required
        placeholder="Title"
        :errorMessage="form.errors.title"
        @focus="form.clearErrors('title')"
      />

      <div class="flex flex-col">
        <label
          for=""
          class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
          >Description*</label
        >
        <AppEditor v-model="form.description" name="description" />
        <div v-if="form.errors.description" class="text-red-500">
          {{ form.errors.description }}
        </div>
      </div>

      <div class="w-full z-10">
        <app-autocomplete
          v-model="selectedTaskTypes"
          placeholder="Task Type*"
          :options="task_types"
          select-label="Enter"
          track-by="id"
          name="type"
          label="type"
          :errorMessage="form.errors.task_type"
          @focus="form.clearErrors('task_type')"
          multiple
        >
          <template #default="{ option }">
            <div class="flex items-center gap-2 w-full overflow-hidden">
              <span class="grow truncate">{{ option.type }}</span>
            </div>
          </template>
        </app-autocomplete>
      </div>

      <div class="grid lg:grid-cols-2 gap-5">
        <app-autocomplete
          v-model="assignees"
          placeholder="Assignees"
          :options="users"
          select-label="Enter"
          track-by="id"
          name="name"
          label="name"
          :errorMessage="form.errors.assignees"
          @focus="form.clearErrors('assignees')"
          multiple
        >
          <template #default="{ option }">
            <div class="flex items-center gap-2 w-full overflow-hidden">
              <span class="grow truncate">{{ option.name }}</span>
            </div>
          </template>
        </app-autocomplete>

        <app-autocomplete
          v-model="reviewers"
          placeholder="Reviewers*"
          :options="users"
          select-label="Enter"
          track-by="id"
          name="name"
          label="name"
          :errorMessage="form.errors.reviewer"
          @focus="form.clearErrors('reviewer')"
          multiple
          required
        >
          <template #default="{ option }">
            <div class="flex items-center gap-2 w-full overflow-hidden">
              <span class="grow truncate">{{ option.name }}</span>
            </div>
          </template>
        </app-autocomplete>
      </div>

      <div class="grid lg:grid-cols-2 gap-5">
        <AppDatepicker
          name="date"
          placeholder="Select Deadline"
          label="Deadline*"
          v-model="form.deadline"
          timePicker
          isTaskDetailForm
        />
        <!-- v-model="deadline" -->
        <div v-if="form.errors.deadline" class="text-red-500">{{ form.errors.deadline }}</div>
        <div class="">
          <div class="">
            <label
              class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
              >Priority*</label
            >
            <select
              v-model="form.priority"
              class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-2 h-[44px]"
              name="priority"
            >
              <option disabled>Choose a Priority Type</option>
              <option value="0">Normal</option>
              <option value="1">High</option>
              <option value="2">Urgent</option>
            </select>
          </div>
          <div v-if="form.errors.priority" class="text-red-500">{{ form.errors.priority }}</div>
        </div>
      </div>

      <div class="Attachments">
        <span class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
          >Attachments</span
        >
        <label class="block border border-dashed border-primary-200 p-4 rounded-md">
          <span class="sr-only">Select Files</span>
          <input
            ref="image"
            type="file"
            @change="selectFiles"
            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
            name="bannerImage"
            @focus="form.clearErrors('logo')"
            multiple
          />
        </label>
        <transition
          enter-active-class="duration-300 ease-out"
          enter-from-class="-translate-y-2 opacity-0"
          enter-to-class="translate-y-0 opacity-100"
          leave-active-class="duration-100 ease-in"
          leave-from-class="translate-y-0 opacity-100"
          leave-to-class="translate-y-1 opacity-0 "
        >
          <div v-show="form.errors.logo">
            <p class="text-sm text-red-600 text-left">
              {{ form.errors.logo }}
            </p>
          </div>
        </transition>
      </div>

      <div class="flex justify-end gap-3 mt-8">
        <AppButton @click="closeModal" text> Cancel </AppButton>
        <AppButton type="submit" :loading="form.processing">Create Task</AppButton>
      </div>
    </form>
  </Modal>
</template>
