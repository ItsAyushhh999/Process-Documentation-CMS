<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppTaskCard from '@/components/AppTaskCard.vue';
import { computed, ref, watch } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppBottomSheet from '@/components/AppBottomSheet.vue';
import AppButton from '@/components/AppButton.vue';
import AppLoader from '@/components/AppLoader.vue';
import AppIcon from '@/components/AppIcon.vue';

import axios from 'axios';
import CommentsAndDeploy from '@/components/TasksComponent/CommentsAndDeploy.vue';
// import TaskDetail from './TaskDetail.vue';
import TaskUpdateForm from '@/components/TasksComponent/TaskUpdateForm.vue';
import TaskDetail from '@/components/TasksComponent/TaskDetail.vue';
import { useIdle } from '@vueuse/core';
import AppTextInput from '@/components/AppTextInput.vue';
import AppUserChip from '@/components/AppIcon.vue';
import DashboardFilter from './DashboardFilter.vue';

// const filters = inject('filters');

const { props: pageProps } = usePage();
const userId = pageProps.auth?.user?.id;

const props = defineProps({
  tasks: {
    type: Array,
    default: []
  },
  users: {
    type: Array,
    default: []
  },
  taskTypes: {
    type: [Array, Object],
    default: []
  },
  projects: {
    type: Array,
    default: []
  },
  user_id: {
    type: Number || String || null,
    default: null
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
  taskProjects: {
    type: Array,
    default: []
  }
});

const collaborators = computed(() => {
  return props.users.map((elem) => {
    return {
      ...elem,
      collaboratorName: elem.name,
      collaborator: elem.id
    };
  });
});

const filteredData = computed(() => ({
  taskAssignee: props.taskAssignee,
  taskReviewer: props.taskReviewer,
  taskPriority: props.taskPriority,
  taskProjects: props.taskProjects
}));

const isModalVisible = ref(false);
const { idle, lastActive, reset } = useIdle(5 * 60 * 1000); // 5 min

const assignedTask = ref([]);
const underReviewTask = ref([]);
const deploymentTasks = ref([]);
const completedTasks = ref([]);

const task_typeIds = ref([]);
const taskDetail = ref(null);
// const currentProject = ref();
const loading = ref(false);
const currentTaskId = ref(null);
const filteredTasks = ref([]);

const base_url = route().t.url;

filteredTasks.value = props.tasks;

const sortTask = (tasks) => {
  // filteredTask.value = tasks;
  assignedTask.value = tasks.filter(task =>
    task.collaborators.some(c => c.collaborator === userId && c.flag === '0') &&
    ['0', '1'].includes(String(task.status))
  );

  underReviewTask.value = tasks.filter((task) =>['2', '3', '4'].includes(String(task.status)));

  deploymentTasks.value = tasks.filter((task) =>['8', '9', '10', '11', '15', '16'].includes(String(task.status)));

  completedTasks.value = tasks.filter((task) => task.status == '5');
};

sortTask(props.tasks);

const handleTaskModal = (data) => {
  // console.log('data', data.id);

  getTaskDetailById(data.id);
  currentTaskId.value = data.id;
  // taskDetail.task.value = data;
  // assignee.value = data.collaborators.filter((elem) => elem.flag == '0')
  // tasks/{taskId}/editV2
};

const getTaskDetailById = async (id) => {
  isModalVisible.value = true;
  loading.value = true;
  // tasks/{taskId}/editV2
  const res = await axios.get(`tasks/${id}/editV3`);
  // console.log('resmpnse', res)
  if (res.status === 200) {
    taskDetail.value = res.data;

    setArrOfSelectedTaskTypes();
    assignedStatusArr.value = taskDetail.value.taskStatus;
    loading.value = false;
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

const reloadPage = () => {
  // console.log('reload page called');
  getTaskDetailById(currentTaskId.value);
};

const onModalClose = () => {
  taskDetail.value = null;
  isModalVisible.value = false;
};

const handleSearch = (key) => {
  var output = props.tasks.filter((s) => {
    const arr = [];
    Object.values(s).forEach((elem) => {
      if (elem == null || elem == undefined) {
        return;
      }
      if (typeof elem == 'object' && elem.length > 0) {
        elem.map((collaborators) => {
          arr.push(String(collaborators.name).toLowerCase());
        });
        return;
      }
      arr.push(String(elem).toLowerCase());
    });
    let isIncluded = arr.map((char) => {
      return char.includes(key.toLowerCase());
    });
    return isIncluded.some((e) => e == true);
  });
  sortTask(output);
};

// teak detail if modal open reload in every 5 minutes;
watch(idle, (idleValue) => {
  // console.log('idle', idleValue);
  if (idleValue && isModalVisible.value) {
    reset(); // restarts the idle timer. Does not change lastActive value
    reloadPage();
  }
});

const isAssignedDescending = ref(false);
const isUnderReviewTaskTaskDescending = ref(false);
const isDeploymentDescending = ref(false);
const isCompletedDescending  = ref(false);
const originalTasks = {
  assigned: [...assignedTask.value],
  underReview: [...underReviewTask.value],
  completed: [...completedTasks.value],
  deployment: [...deploymentTasks.value]
};

const taskSortState = {
  assigned: 'original',
  underReview: 'original',
  completed: 'original',
  deployment: 'original'
};

const handleTaskAscendingDescending = (type) => {
  const taskLists = {
    assigned: assignedTask,
    underReview: underReviewTask,
    completed: completedTasks,
    deployment: deploymentTasks
  };

  if (!taskLists[type]) return;

  if (taskSortState[type] === 'original') {
    taskLists[type].value.sort((a, b) => a.id - b.id);
    taskSortState[type] = 'asc';
  } else if (taskSortState[type] === 'asc') {
    taskLists[type].value.sort((a, b) => b.id - a.id);
    taskSortState[type] = 'desc';
  } else {
    taskLists[type].value = [...originalTasks[type]];
    taskSortState[type] = 'original';
  }
};
</script>

<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <!-- <div class="bg-yellow-500 h-screen w-10 block"></div> -->
    <template #header>
      <DashboardFilter
        @handleSearch="handleSearch"
        :users="users"
        :taskTypes="taskTypes"
        :taskAssignee="filteredData.taskAssignee"
        :taskReviewer="filteredData.taskReviewer"
        :taskPriority="filteredData.taskPriority"
        :taskProjects="taskProjects"
        :projects="projects"
        />
    </template>
    <div class="gap-4 flex w-full h-full relative">
      <div class="flex gap-5 items-start pr-6" v-if="tasks.length > 0">
        <div
          v-if="assignedTask && assignedTask.length > 0"
          class="min-w-[360px] max-w-[420px] h-auto max-h-full flex flex-col bg-gray-100 dark:bg-slate-800 rounded-lg overflow-hidden"
        >
          <div
            class="inline-flex items-center justify-between gap-x-3 pt-4 pb-3 px-5 border-b border-b-white dark:border-slate-600 dark:bg-slate-700"
          >
            <div class="flex gap-3 items-center">
              <div class="inline-flex gap-3 items-center">
                <div class="bg-gray-500 w-3 h-3 rounded-full"></div>
                <h4 class="text-lg font-semibold sm:text-xl dark:text-gray-200 uppercase">
                  Assigned
                </h4>
              </div>

              <div
                class="rounded-full h-6 bg-gray-300 dark:bg-slate-400 flex items-center justify-center font-semibold text-sm px-3"
              >
                <span>{{ assignedTask.length }}</span>
              </div>
            </div>

            <button
              type="button"
              @click="handleTaskAscendingDescending('assigned')"
              class="w-8 h-8 border rounded-lg dark:border-slate-600"
            >
              <AppIcon v-if="isAssignedDescending" name="arrow-up-wide-short"/>
              <AppIcon v-else name="arrow-down-wide-short"/>
            </button>
          </div>
          <!-- <div class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4">
            <template v-for="task in assignedTask" :key="task.id">
              <AppTaskCard @click="handleTaskModal(task)" :data="task" />
            </template>
          </div> -->
          <TransitionGroup class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4 list-none" tag="ul" name="fade">
            <li v-for="task in assignedTask" :key="task.id" class="item">
              <AppTaskCard @click="handleTaskModal(task)" :data="task" />
            </li>
          </TransitionGroup>
        </div>

        <div
          v-if="underReviewTask && underReviewTask.length > 0"
          class="min-w-[360px] max-w-[420px] h-auto max-h-full flex flex-col bg-orange-50 dark:bg-slate-800 rounded-lg overflow-hidden"
        >
          <div
            class="inline-flex items-center gap-x-3 pt-4 pb-3 px-5 border-b border-b-white dark:border-slate-600 dark:bg-slate-700 justify-between"
          >
            <div class="flex gap-3 items-center">
              <div class="inline-flex gap-3 items-center">
                <div class="bg-orange-500 w-3 h-3 rounded-full"></div>

                <h4 class="text-lg font-semibold sm:text-xl dark:text-gray-200 uppercase">
                  Under Review
                </h4>
              </div>

              <div
                class="rounded-full h-6 bg-orange-300 dark:bg-orange-600 flex items-center justify-center font-semibold text-sm px-3"
              >
                <span>{{ underReviewTask.length }}</span>
              </div>
            </div>

            <button
              type="button"
              @click="handleTaskAscendingDescending('underReview')"
              class="w-8 h-8 border rounded-lg dark:border-slate-600"
            >
              <!-- <div
                :class="isUnderReviewTaskTaskDescending ? 'rotate-180' : 'rotate-0'"
                class="transition-transform"
              >
                <app-icon name="fa-chevron-down" />
              </div> -->
              <AppIcon v-if="isUnderReviewTaskTaskDescending" name="arrow-up-wide-short"/>
              <AppIcon v-else name="arrow-down-wide-short"/>
            </button>
          </div>
          <!-- <div class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4">
            <template v-for="task in underReviewTask" :key="task.id">
              <AppTaskCard @click="handleTaskModal(task)" :data="task" />
            </template>
          </div> -->
          <TransitionGroup class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4 list-none" tag="ul" name="fade">
            <li v-for="task in underReviewTask" :key="task.id" class="item">
              <AppTaskCard @click="handleTaskModal(task)" :data="task" />
            </li>
          </TransitionGroup>
        </div>

          <div
          v-if="deploymentTasks && deploymentTasks.length > 0"
          class="min-w-[360px] max-w-[420px] h-auto max-h-full flex flex-col bg-lime-50 dark:bg-slate-800 rounded-lg overflow-hidden"
        >
          <div
            class="inline-flex items-center gap-x-3 pt-4 pb-3 px-5 border-b border-b-white dark:border-slate-600 dark:bg-slate-700 justify-between"
          >
            <div class="flex gap-3 items-center">
              <div class="inline-flex gap-3 items-center">
                <div class="bg-lime-500 w-3 h-3 rounded-full"></div>

                <h4 class="text-lg font-semibold sm:text-xl dark:text-gray-200 uppercase">
                  Deployment
                </h4>
              </div>

              <div
                class="rounded-full h-6 bg-lime-300 dark:bg-lime-600 flex items-center justify-center font-semibold text-sm px-3"
              >
                <span>{{ deploymentTasks.length }}</span>
              </div>

            </div>

            <button
              type="button"
              @click="handleTaskAscendingDescending('deployment')"
              class="w-8 h-8 border rounded-lg dark:border-slate-600"
            >
              <!-- <div
                :class="isDeploymentDescending? 'rotate-180' : 'rotate-0'"
                class="transition-transform"
              >
                <app-icon name="fa-chevron-down" />
              </div> -->
              <AppIcon v-if="isDeploymentDescending" name="arrow-up-wide-short"/>
              <AppIcon v-else name="arrow-down-wide-short"/>
            </button>
          </div>
          <!-- <div class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4">
            <template v-for="task in deploymentTasks" :key="task.id">
              <AppTaskCard @click="handleTaskModal(task)" :data="task" />
            </template>
          </div> -->
          <TransitionGroup class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4 list-none" tag="ul" name="fade">
            <li v-for="task in deploymentTasks" :key="task.id" class="item">
              <AppTaskCard @click="handleTaskModal(task)" :data="task" />
            </li>
          </TransitionGroup>
        </div>

      <div
          v-if="completedTasks && completedTasks.length > 0"
          class="min-w-[360px] max-w-[420px] h-auto max-h-full flex flex-col bg-blue-50 dark:bg-slate-800 rounded-lg overflow-hidden"
        >
          <div
            class="inline-flex items-center gap-x-3 pt-4 pb-3 px-5 border-b border-b-white dark:border-slate-600 dark:bg-slate-700 justify-between"
          >
            <div class="flex gap-3 items-center">
              <div class="inline-flex gap-3 items-center">
                <div class="bg-blue-500 w-3 h-3 rounded-full"></div>

                <h4 class="text-lg font-semibold sm:text-xl dark:text-gray-200 uppercase">
                  Completed
                </h4>
              </div>

              <div
                class="rounded-full h-6 bg-blue-200 dark:bg-blue-600 flex items-center justify-center font-semibold text-sm px-3"
              >
                <span>{{ completedTasks.length }}</span>
              </div>
            </div>

            <button
              type="button"
              @click="handleTaskAscendingDescending('completed')"
              class="w-8 h-8 border rounded-lg dark:border-slate-600"
            >
              <!-- <div
                :class="isCompletedDescending? 'rotate-180' : 'rotate-0'"
                class="transition-transform"
              >
                <app-icon name="fa-chevron-down" />
              </div> -->
              <AppIcon v-if="isCompletedDescending" name="arrow-up-wide-short"/>
              <AppIcon v-else name="arrow-down-wide-short"/>
            </button>
          </div>
          <!-- <div class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4">
            <template v-for="task in completedTasks" :key="task.id">
              <AppTaskCard @click="handleTaskModal(task)" :data="task" />
            </template>
          </div> -->
          <TransitionGroup class="grid gap-y-3 overflow-y-auto hide_scrollbar p-4 list-none" tag="ul" name="fade">
            <li v-for="task in completedTasks" :key="task.id" class="item">
              <AppTaskCard @click="handleTaskModal(task)" :data="task" />
            </li>
          </TransitionGroup>
        </div>
      </div>
      <div v-else class="flex w-full justify-center items-center">
        <div
          v-if="filteredData.taskAssignee.length > 0 || filteredData.taskReviewer.length > 0 || filteredData.taskPriority.length > 0"
          class="flex flex-col items-center justify-center"
        >
          <img :src="`${base_url}/img/404.png`" alt="" class="object-cover" />
          <h3 class="text-[24px] font-bold mt-4 mb-3">No Task Found!</h3>
          <span>Looks like Task's for Search Result not found</span>
        </div>
        <div v-else class="flex flex-col items-center justify-center">
          <img :src="`${base_url}/img/404.png`" alt="" class="object-cover" />
          <h3 class="text-[24px] font-bold mt-4 mb-3">No Task Found!</h3>
          <span>Looks like you are not assigned to Task's </span>
        </div>
      </div>
    </div>

    <AppBottomSheet :showModal="isModalVisible" @close="onModalClose">
      <!-- <span>{{ taskDetail }}</span> -->
      <template #header>
        <div class="flex items-center justify-between grow" v-if="taskDetail && taskDetail">
          <div class="flex items-center gap-x-2 flex-1 divide-x">
            <span class="text-darkblue dark:text-slate-300"
              ><strong>#{{ currentTaskId }}</strong></span
            >
            <span class="text-gray-500 dark:text-slate-300 text-sm pl-2 line-clamp-1">{{
              taskDetail?.task?.projectName
            }}</span>
          </div>
          <div class="pr-10">
            <Link :href="`/tasks/${taskDetail.task.id}/edit`">
              <AppButton icon="fa-gear" outline>More Options</AppButton>
            </Link>
          </div>
        </div>
      </template>

      <div  class="w-full h-full ">
  <div class="grid lg:grid-cols-2 gap-20 h-full">
    <div class="lg:h-[90vh] h-auto overflow-y-auto pt-5  pb-24 md:block hidden relative">
      <!-- Task Update Form with loading state -->
      <template v-if="loading ">
        <AppLoader />
      </template>
      <template v-if="taskDetail">
        <template v-if="taskDetail.isSuperAdmin">
        <TaskUpdateForm
          :task-detail="taskDetail"
          :taskIds="task_typeIds"
          :collaborators="collaborators"
          @reloadPage="reloadPage"
        />
        </template>
      <template v-else>
        <TaskDetail :data="taskDetail" />
      </template>
      </template>

    </div>

    <div class="lg:h-[90vh] h-auto overflow-y-auto pb-24 pt-5 md:pr-4 w-full">
      <template v-if="taskDetail">
        <CommentsAndDeploy
              :taskDetail="taskDetail"
              :assignedStatusArr="assignedStatusArr"
            />
      </template>
    </div>
  </div>
</div>
    </AppBottomSheet>
  </AuthenticatedLayout>
</template>

<style scoped>
/* 1. declare transition */
.fade-move,
.fade-enter-active,
.fade-leave-active {
  transition: all 0.5s cubic-bezier(0.55, 0, 0.1, 1);
}

/* 2. declare enter from and leave to state */
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: scaleY(0.01) translate(30px, 0);
}

/* 3. ensure leaving items are taken out of layout flow so that moving
      animations can be calculated correctly. */
.fade-leave-active {
  position: absolute;
}
</style>
