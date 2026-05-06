<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import AppAutocomplete from '@/components/AppAutocomplete.vue';
import AppIcon from '@/components/AppIcon.vue';
import AppButton from '@/components/AppButton.vue';
import ProjectFilterChip from './ProjectFilterChip.vue';
import PriorityChip from './PriorityChip.vue';
import {useForm, router } from '@inertiajs/vue3';
const emit = defineEmits(['handleSearch', 'handleFilter']);
// const filters = inject('filters');
const props = defineProps({
  users: {
    type: Array,
    default: []
  },
  taskTypes: {
    type: [Array, Object],
    default: []
  },
  taskAssignee: {
    type: Array,
    default: []
  },
  taskReviewer: {
    type: Array,
    default: []
  },
  taskPriority: {
    type: Array,
    default: []
  },
  projects: {
    type: Array,
    default: []
  },
  taskProjects: {
    type: Array,
    default: []
  }
});

const searchQuery = ref('');
const isModalVisible = ref(false);
const deadline = ref(null);
// const filteredKeyObjArr = ref({});
const filterModalRef = ref(null);

const priorities = [
  {
    value: 0,
    name: 'Normal'
  },
  {
    value: 1,
    name: 'High'
  },
  {
    value: 2,
    name: 'Urgent'
  }
];

const openModal = function () {
  isModalVisible.value = !isModalVisible.value;
  router.visit(route('dashboard'), {
    method: 'GET', // Request method (GET in this case)
    preserveState: true, // Keep page state (no reload)
    only: ['users', /* 'taskTypes', */ 'projects'] // Update only the props that need to change
  });
}
const selectedAssignee = computed(() => {
  // console.log('props.collaborators sssss', props.collaborators)
  let arr = [];
  props.users.map((elem) => {
    // console.log('elem', elem)
    const isIncluded = props.taskAssignee.some((e) => e == elem.id);
    if (isIncluded) {
      arr.push(elem);
    }
  });
  return arr;
});
const selectedReviewer = computed(() => {
  // console.log('props.collaborators sssss', props.collaborators)
  let arr = [];
  props.users.map((elem) => {
    // console.log('elem', elem)
    const isIncluded = props.taskReviewer.some((e) => e == elem.id);
    if (isIncluded) {
      arr.push(elem);
    }
  });
  return arr;
});

const selectedPriority = computed(() => {
  // console.log('props.collaborators sssss', props.collaborators)
  let arr = [];
  priorities.map((elem) => {
    // console.log('elem', elem)
    const isIncluded = props.taskPriority.some((e) => e == elem.value);
    if (isIncluded) {
      arr.push(elem);
    }
  });
  return arr;
});

const selectedProjects = computed(() => {
  let arr = [];
  props.projects.map((elem) => {
    // console.log('elem', elem);
    const isIncluded = props.taskProjects.some((e) => e == elem.id);
    if (isIncluded) {
      arr.push(elem);
      // return
    }
    elem.subprojects.map((subElem) => {
      // console.log('subElem', subElem)
      const isIncludedInSub = props.taskProjects.some((e) => e == subElem.id);
      if (isIncludedInSub) {
        arr.push(subElem);
        // return
      }
    });
  });
  return arr;
});

const form = useForm({
  taskAssignee: selectedAssignee.value,
  taskReviewer: selectedReviewer.value,
  taskPriority: selectedPriority.value,
  taskProjects: selectedProjects.value
});


const formatNameToInitials = (name) => {
  // console.log("name", name)
  if(!name) return
  const arr = name.split(' ');
  return `${arr[0].charAt(0)}${arr[1].charAt(0)}`;
};

watch(searchQuery, (val) => {
  emit('handleSearch', val);
});

const handleSubmit = () => {
  // console.log('form', form.data());
  form.taskAssignee = form.taskAssignee.map((elem) => elem.id);
  form.taskReviewer = form.taskReviewer.map((elem) => elem.id);
  form.taskPriority = form.taskPriority.map((elem) => elem.value);
  form.taskProjects = form.taskProjects.map((elem) => elem.id);
  console.log('form 2', form.data());
  form.get(
    route('dashboard', {
      onSuccess: () => console.log('success'),
      onError: (error) => console.log('error', error)
    })
  );
};

const clearFilter = () => {
  // console.log('clikc')
  form.reset();
  form.taskAssignee = [];
  form.taskReviewer = [];
  form.taskPriority = [];
  form.taskProjects = [];
  // window.history.back()
  // route('dashboard')
  if (
    selectedAssignee.value.length != 0 ||
    selectedReviewer.value.length !=0 ||
    selectedPriority.value.length != 0 ||
    selectedProjects.value.length != 0
  ) {
    form.get(
      route('dashboard', {
        onSuccess: () => console.log('success'),
        onError: (error) => console.log('error', error)
      })
    );
  }
};
const handleCancel = () => {
  form.reset();
  isModalVisible.value = false;
};

const uniqueAssigneesAndReviewers = computed(() => {
  const combined = [...form.taskAssignee, ...form.taskReviewer];
  
  const seenIds = new Set();
  const uniqueUsers = combined.filter(user => {
    const isDuplicate = seenIds.has(user.id);
    seenIds.add(user.id);
    return !isDuplicate;
  });
  console.log(uniqueUsers);
  return uniqueUsers;
});
</script>
<template>
  <div class="flex items-center gap-x-5 w-full" ref="filterModalRef">
    <label class="lg:min-w-[500px] min-w-[320px] max-w-1/2 flex relative">
      <span class="absolute top-1/2 -translate-y-1/2 left-2">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="14"
          height="14"
          viewBox="0 0 16 16"
          role="img"
          class="fill-current"
        >
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M10.6002 12.0498C9.49758 12.8568 8.13777 13.3333 6.66667 13.3333C2.98477 13.3333 0 10.3486 0 6.66667C0 2.98477 2.98477 0 6.66667 0C10.3486 0 13.3333 2.98477 13.3333 6.66667C13.3333 8.15637 12.8447 9.53194 12.019 10.6419C12.0265 10.6489 12.0338 10.656 12.0411 10.6633L15.2935 13.9157C15.6841 14.3063 15.6841 14.9394 15.2935 15.33C14.903 15.7205 14.2699 15.7205 13.8793 15.33L10.6269 12.0775C10.6178 12.0684 10.6089 12.0592 10.6002 12.0498ZM11.3333 6.66667C11.3333 9.244 9.244 11.3333 6.66667 11.3333C4.08934 11.3333 2 9.244 2 6.66667C2 4.08934 4.08934 2 6.66667 2C9.244 2 11.3333 4.08934 11.3333 6.66667Z"
          ></path>
        </svg>
      </span>
      <input
        type="text"
        v-model="searchQuery"
        placeholder="Search For Task.."
        class="pl-10 border-none outline-none focus:outline-transparent active:outline-transparent w-full focus:ring-0 dark:bg-slate-900/50"
      />
    </label>
    <div class="relative">
      <button
        class="hover:bg-blue-50 px-2 py-1 gap-2 flex items-center justify-center rounded-lg relative border border-blue-50 dark:border-slate-700 dark:hover:bg-slate-800"
        type="button"
        @click="openModal()"
      >
        <app-icon name="fa-filter" />
        <span>Filters</span>
      </button>
      <Transition
        enter-active-class="ease-out duration-300"
        enter-from-class="opacity-0 -translate-x-1/2"
        enter-to-class="opacity-100 translate-x-0"
        leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100 translate-x-0"
        leave-to-class="opacity-0 -translate-x-1/2"
      >
        <div
          v-show="isModalVisible"
          class="absolute top-14 right-0 w-[420px] z-10 dark:bg-slate-800 rounded-xl flex flex-col overflow-hidden shadow border dark:border-slate-700"
        >
          <div class="bg-white dark:bg-slate-800 px-6 py-3 flex justify-between border-b dark:border-slate-700">
            <h3 class="text-lg font-semibold">Filters</h3>
            <button
              type="button"
              class="flex items-center justify-center gap-x-2"
              @click="clearFilter"
            >
              <!-- @click="closeModal" -->
              <!-- <app-icon name="fa-close" />  -->
              <span class="text-sm whitespace-nowrap underline">Clear Filter</span>
            </button>
          </div>
          <form
            class="flex-1 bg-white dark:bg-slate-900 relative py-6"
            @submit.prevent="handleSubmit"
          >
            <div class="grid gap-5 px-6">
              <!-- <AppDatepicker label="Deadline" v-model="deadline" /> -->

              <app-autocomplete
                v-model="form.taskProjects"
                placeholder="Project"
                :options="projects"
                select-label="Enter"
                track-by="id"
                name="project"
                label="name"
                group-values="subprojects"
                group-label="group_name"
                multiple
              >
                <template #default="{ option }">
                  <div class="flex items-center gap-2">
                    {{ option.name }}
                  </div>
                </template>
              </app-autocomplete>
              <AppAutocomplete
                v-model="form.taskAssignee"
                :options="users"
                select-label="name"
                track-by="id"
                name="name"
                label="name"
                multiple
                placeholder="Assignee"
              >

                <template #default="{ option }">
                  <div class="flex items-center gap-2">
                    <div class="grow">
                      {{ option.name }}
                    </div>
                  </div>
                </template>
              </AppAutocomplete>
                <AppAutocomplete
                v-model="form.taskReviewer"
                :options="users"
                select-label="name"
                track-by="id"
                name="name"
                label="name"
                multiple
                placeholder="Reviewer"
              >

                <template #default="{ option }">
                  <div class="flex items-center gap-2">
                    <div class="grow">
                      {{ option.name }}
                    </div>
                  </div>
                </template>
              </AppAutocomplete>

              <AppAutocomplete
                v-model="form.taskPriority"
                :options="priorities"
                select-label="value"
                track-by="value"
                name="taskPriority"
                label="name"
                multiple
                placeholder="taskPriority"
              >
                <template #default="{ option }">
                  <div class="flex items-center gap-2">
                    <div class="grow">
                      {{ option.name }}
                    </div>
                  </div>
                </template>
              </AppAutocomplete>

              <!-- <AppSelect v-model="taskPriority" label="Task Priority">
                <template #options>
                  <option value="0">Normal</option>
                  <option value="1">High</option>
                  <option value="2">Urgent</option>
                </template>
              </AppSelect> -->
            </div>
            <div class="flex justify-end gap-2 items-center pt-10 px-6">
              <app-button text type="button" @click="handleCancel">Cancel</app-button>
              <app-button type="submit">Filter</app-button>
            </div>
          </form>
        </div>
      </Transition>
    </div>
    <div class="flex flex-wrap gap-x-3 gapy-y-2 items-center">
      <!-- v-if="deadline" -->
      <!-- <FilterItemChip :handleClear="() => (deadline = null)" v-if="deadline">
        <span>{{ filters.formatDate(deadline) }}</span>
      </FilterItemChip> -->
      <div
        class="flex gap-x-2 items-center"
        v-if="
          selectedAssignee.length != 0 ||
          selectedReviewer.length != 0 ||
          selectedPriority.length != 0 ||
          selectedProjects.length != 0
        "
      >
    <template v-if="uniqueAssigneesAndReviewers.length > 0">
  <div class="flex mr-3">
    <!-- Unique Assignees and Reviewers -->
    <div v-for="elem in uniqueAssigneesAndReviewers" :key="elem.collaborator" class="relative group">
      <div
        class="h-8 w-8 bg-gray-500 rounded-full border-2 shadow border-white dark:border-slate-700 overflow-hidden relative z-0 text-center last:mr-0 -mr-2.5 text-white text-xs flex items-center justify-center cursor-pointer"
      >
        <img
          v-if="elem.profile_picture"
          :src="`/storage/profiles/${elem.profile_picture}`"
          class="w-10 h-10 rounded-full object-cover object-center"
          :alt="elem.profile_picture"
        />
        <span v-else>
          {{ formatNameToInitials(elem.name) }}
        </span>
      </div>
      <div
        class="absolute flex whitespace-nowrap bg-white dark:bg-slate-900 border px-3 py-1 text-sm invisible group-hover:visible rounded-full top-10 dark:border-slate-700"
      >
        {{ elem.name }}
      </div>
    </div>
  </div>
</template>
        <template v-for="elem in form.taskPriority" :key="elem.id">
          <PriorityChip :filterType="elem.name" />
        </template>
        <template v-for="elem in form.taskProjects">
          <ProjectFilterChip :projectName="elem.name" />
        </template>
        <button type="button" class="px-5">
          <span class="underline" @click="clearFilter">Clear Filter</span>
        </button>
      </div>
    </div>
  
  </div>
  <div v-if="isModalVisible" class="fixed inset-0 z-0" @click="handleCancel" />
</template>
