<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppButton from '@/components/AppButton.vue';
import AppTextInput from '@/components/AppTextInput.vue';
import AppAutocomplete from '@/components/AppAutocomplete.vue';
import AppEditor from '@/components/AppEditor.vue';
import AppDatepicker from '@/components/AppDatepicker.vue';
import AppIcon from '@/components/AppIcon.vue';

const props = defineProps({
  taskDetail: {
    type: Object,
    default: undefined
  },
  taskIds: {
    type: Array,
    default: []
  },
  collaborators: {
    type: Array,
    default: []
  }
});

// console.log('taskDetail', props.taskDetail)

const task_typeIds = ref([]);
const currentProject = ref();
// const title = ref('');
// const description = ref('');
// const deadline = ref();
// const priority = ref('');
// const assignees = ref([]);
// const reviewers = ref([]);
const errors = ref({});
const btnLoading = ref(false);
const emit = defineEmits(['reloadPage']);

task_typeIds.value = props.taskIds;

// const index = props.taskDetail.projects.findIndex(
//   (elem) => elem.id == props.taskDetail.task.project_id
// );
// console.log('ssprops.taskDetail.projects', props.taskDetail.projects)
// console.log('index', props.taskDetail)
currentProject.value = {
  id: props.taskDetail.task.project_id,
  name: props.taskDetail.task.projectName
};

const title = ref(props.taskDetail.task.title);
const description = ref(props.taskDetail.task.description);
const deadline = ref(props.taskDetail.task.deadline);
const priority = ref(props.taskDetail.task.priority);
const assignees = ref(props.taskDetail.assignees);
const reviewers = ref(props.taskDetail.reviewers);

const handleUpdateTask = () => {
  btnLoading.value = true;
  const taskTypesIds = task_typeIds.value.map((elem) => {
    return elem.id;
  });

  const assigneesIdArr = assignees.value.map((elem) => {
    return elem.collaborator;
  });

  const reviewersIdArr = reviewers.value.map((elem) => {
    return elem.collaborator;
  });

  const form = useForm({
    project_id: currentProject.value.id,
    title: title.value,
    description: description.value,
    task_type: taskTypesIds.length ? taskTypesIds : null,
    assignees: assigneesIdArr.length ? assigneesIdArr : null,
    reviewers: reviewersIdArr.length ? reviewersIdArr : null,
    deadline: deadline.value,
    priority: priority.value
  });

  form.post(route('updateV2', props.taskDetail.task.id), {
    onFinish: () => {
      console.log('success');
      btnLoading.value = false;
    },
    onError: (error) => {
      console.log(error);
      errors.value = error;
    },
    onSuccess: () => {
      emit('reloadPage');
    }
  });
};
</script>

<style scoped>
.description :deep(p) {
    /* margin: .3em .3em; */
}
</style>

<template>
  <form class="flex flex-col gap-5 pr-4" @submit.prevent="handleUpdateTask" v-if="taskDetail">
    <div class="grid">
      <!-- {{ currentProject }} -->
      <!-- {{ currentProject }} -->
      <label
        for=""
        class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
        >Project*</label
      >
        <div class="p-4 rounded-lg border dark:border-slate-700 text-gray-500 whitespace-normal dark:text-gray-300 truncate">
            <span  class="title">{{ currentProject.name }}</span>
        </div>
    </div>
    <div class="grid">
        <label for="" class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
        >Title*</label
      >
        <div class="p-4 rounded-lg border dark:border-slate-700 text-gray-500 whitespace-normal dark:text-gray-300 truncate">
            <span  class="title">{{ title }}</span>
        </div>
    </div>

    <div class="flex flex-col">
      <label for="" class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400">Description*</label>
      <div class=" p-4 rounded-lg border dark:border-slate-700 text-gray-500 whitespace-normal dark:text-gray-300 truncate">
             <span v-html="description" class="description prose prose-ul:mt-0 prose-p:m-0.5 prose-li:m-0 prose-a:text-sky-500"></span>
      </div>
    </div>

    <div class="grid">
      <app-autocomplete
        v-model="task_typeIds"
        placeholder="Task Type"
        :options="taskDetail.task_types"
        select-label="Enter"
        track-by="id"
        name="taskType"
        label="type"
        multiple
        required
      >
        <template #default="{ option }">
          <div class="flex items-center gap-2">
            <div class="grow">{{ option.type }}</div>
          </div>
        </template>
      </app-autocomplete>
      <div v-for="(task_typeId, index) in task_typeIds" :key="index">
        <span v-if="errors['task_type.' + index]" class="text-red-500">
          {{ errors['task_type.' + index] }}
        </span>
      </div>
      <div v-if="errors.task_type" class="text-red-500">{{ errors.task_type }}</div>
    </div>

    <div class="grid" v-if="assignees">
      <app-autocomplete
        v-model="assignees"
        placeholder="Select Assignees*"
        :options="collaborators"
        select-label="Enter"
        track-by="collaborator"
        name="assignees"
        label="collaboratorName"
        multiple
      >
        <template #default="{ option }">
          <div class="flex items-center gap-2">
            <div class="grow">
              {{ option.collaboratorName }}
            </div>
          </div>
        </template>
      </app-autocomplete>
      <div v-for="(assignee, index) in assignees" :key="index">
        <span v-if="errors['assignees.' + index]" class="text-red-500">
          {{ errors['assignees.' + index] }}
        </span>
      </div>
      <div v-if="errors.assignees" class="text-red-500">{{ errors.assignees }}</div>
    </div>

    <div class="grid">
      <app-autocomplete
        v-model="reviewers"
        placeholder="Select reviewers*"
        :options="collaborators"
        select-label="Enter"
        track-by="collaborator"
        name="reviewers"
        label="collaboratorName"
        multiple
      >
        <template #default="{ option }">
          <div class="flex items-center gap-2">
            <div class="grow">
              {{ option.collaboratorName }}
            </div>
          </div>
        </template>
      </app-autocomplete>

      <div v-for="(reviewer, index) in reviewers" :key="index">
        <span v-if="errors['reviewers.' + index]" class="text-red-500">
          {{ errors['reviewers.' + index] }}
        </span>
      </div>
      <div v-if="errors.reviewers" class="text-red-500">{{ errors.reviewers }}</div>
    </div>
    <div class="grid grid-cols-2 gap-5">
      <AppDatepicker
        name="date"
        placeholder="Select Deadline"
        label="Deadline*"
        v-model="deadline"
        timePicker
        isTaskDetailForm
      />
      <div v-if="errors.date" class="text-red-500">{{ errors.date }}</div>
      <div class="">
        <div class="">
          <label class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
            >Priority*</label
          >
          <select
            v-model="priority"
            class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-2 h-[44px]"
            name="priority"
          >
            <option disabled>Choose a Priority Type</option>
            <option value="0">Normal</option>
            <option value="1">High</option>
            <option value="2">Urgent</option>
          </select>
        </div>
        <div v-if="errors.priority" class="text-red-500">{{ errors.priority }}</div>
      </div>
    </div>

    <div class="" v-if="taskDetail.attachments && taskDetail.attachments.length > 0">
      <label class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
        >Attachments</label
      >

      <div class="flex gap-2">
        <a
          v-for="image in taskDetail.attachments"
          :href="`/storage/tasks/${image.name}`"
          class="inline-flex items-center px-3 py-1 space-x-2 text-base transition-all border rounded-full hover:bg-gray-100 text-gray-600 border-gray-300 dark:border-slate-700 dark:text-slate-300"
          target="_blank"
        >
          <AppIcon name="fa-images" />
          <span class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre">{{
            image.name
          }}</span>
        </a>
      </div>
    </div>

    <div class="flex justify-end">
      <AppButton type="submit" :loading="btnLoading">Update</AppButton>
    </div>
  </form>
</template>
