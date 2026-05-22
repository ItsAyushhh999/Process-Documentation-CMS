<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import { computed, inject, ref, watch } from 'vue';
import TaskDetail from '@/components/TasksComponent/TaskDetail.vue';
import TaskUpdateForm from '@/components/TasksComponent/TaskUpdateForm.vue';
import CommentsAndDeploy from '@/components/TasksComponent/CommentsAndDeploy.vue';
import PullRequests from '@/components/TasksComponent/PullRequests.vue';
import DeployLogsModal from '@/components/TasksComponent/DeployLogsModal.vue';
import DeployModal from '@/components/TasksComponent/DeployModal.vue';
import AddToWatchListModal from '@/components/TasksComponent/AddToWatchListModal.vue';
import {onMounted, onUnmounted } from 'vue';
import pusher from '@/utils/pusher.js';
import { useIdle } from '@vueuse/core'



const props = defineProps({
  task: {},
  users: {},
    auth: {},
  deploy_permission_granted: {},
  assignees: {},
  reviewers: {},
  attachments: {},
  projects: {},
  assigneesIds: {},
  reviewersIds: {},
  isSuperAdmin: {},
  task_types: {},
  task_typeIds: {},
  activities: {},
  taskStatus: {},
  userPermissions: {},
  permissions: {},
  collaborators: [],
  isReview: {
    type: Boolean,
    default: false
  },
  hasDeploymentPermission: {
    type: Boolean,
    default: true
  },
  hasStagingPermission: {
    type: Boolean,
    default: true
  },
  hasProductionPermission: {
    type: Boolean,
    default: true
  },
  watchListTask : {
    type: Boolean,
  }
});

const taskAddedToWatchList = computed(()=> props.watchListTask); 

const { idle, lastActive, reset } = useIdle(5 * 60 * 1000) // 5 min

const selectedType = computed(() => {
  let arr = [];
  props.task_typeIds.map((elem) => {
    props.task_types.map((item) => {
      if (elem === item.id) {
        arr.push(item);
      }
    });
  });
  return arr;
});

const _collaborators = computed(() => {
  return props.users.map((elem) => {
    return {
      ...elem,
      collaboratorName: elem.name,
      collaborator: elem.id
    };
  });
});

const form = useForm({
  id: null,
  // task_type: null
  name: null,
  description: null,
  projects: null
  // logo: null
});


const reloadPage = () => {
  // location.reload()
  router.reload('tasks.edit', {
    only: ['tasks.edit']
  })
};

const liveComments = ref(null);

onMounted(() => {
  const channel = pusher.subscribe(`task.${props.task.id}`);

  channel.bind('pusher:subscription_succeeded', () => {
    console.log('Subscribed to task.' + props.task.id);
  });

  channel.bind('task.comment', (data) => {
    console.log('comment received', data);
    liveComments.value = data; // pass down to CommentsAndDeploy
  });

  channel.bind('task.status.updated', (data) => {
    console.log('Status update received:', data);
    props.task.status = data.new_status;
    liveComments.value = { ...data, _ts: Date.now() };
  });
});

onUnmounted(() => {
  pusher.unsubscribe(`task.${props.task.id}`);
});

watch(idle, (idleValue) => {
  console.log('idle', idleValue)
  if (idleValue) {
    reset() // restarts the idle timer. Does not change lastActive value
    reloadPage()
  }
})

</script>
<template>
  <Head title="Tasks" />
  <AuthenticatedLayout>
    <template #pageTitle>Tasks</template>
    <template #header>
      <div class="flex gap-x-5 items-center">
        <AddToWatchListModal :taskId="task.id" :taskAddedToWatchList="taskAddedToWatchList"/>
        <DeployModal
          :taskId="task.id"
          :project="task.project"
          :projects="projects"
          :hasDeploymentPermission="hasDeploymentPermission"
          :hasStagingPermission="hasStagingPermission"
          :hasProductionPermission="hasProductionPermission"
          :collaborators = "props.collaborators"
          :user="props.auth.user"
        />
        <DeployLogsModal :project="task.project" :taskId="task.id" :taskStatus="task.status" />
        <PullRequests
          :project="task.project"
          :isReview="isReview"
          :assigneesIds="assigneesIds"
          :reviewersIds="reviewersIds"
          :taskId="task.id"
          :taskStatus="task.status"
        />
      </div>
    </template>
    <div v-if="task" class="w-full h-full">
      <div class="grid lg:grid-cols-2 gap-20 h-full">
        <!-- <TaskDetail :data="task" /> -->
        <div class="h-auto overflow-y-auto pt-5 md:block hidden pb-24 pr-3">
            <template v-if="isSuperAdmin">
              <TaskUpdateForm
                :task-detail="{
                  task: task,
                  projects: projects,
                  assignees: assignees,
                  reviewers: reviewers,
                  attachments: attachments,
                  task_types: task_types
                }"
                :taskIds="selectedType"
                @reloadPage="reloadPage"
                :collaborators="_collaborators"
              />
            </template>
            <template v-else>
              <TaskDetail
                :data="{
                  task: task,
                  assignees: assignees,
                  reviewers: reviewers,
                  attachments: attachments,
                  projectName: task.project.name,
                  taskStatus: taskStatus,
                }"
              />
            </template>
        </div>

        <div class="h-auto overflow-y-auto md:pr-4 w-full pb-24">
          <div class="md:hidden block mb-14 mt-4">
            <TaskDetail
              :data="{
                task: task,
                assignees: assignees,
                reviewers: reviewers,
                attachments: attachments
              }"
            />
          </div>
          <CommentsAndDeploy
            :task-detail="{
              task: task,
              activities: activities
            }"
            :assignedStatusArr="taskStatus"
            :liveComment="liveComments"
            :project="task.project"
            :isReview="isReview"
            :assigneesIds="assigneesIds"
            :reviewersIds="reviewersIds"
            :taskId="task.id"
            @reloadPage="reloadPage"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
