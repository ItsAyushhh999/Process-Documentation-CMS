<script setup>
import { ref ,onMounted,watch,onUnmounted} from 'vue';
import AppEditor from '@/components/AppEditor.vue';
import { Head, Link, usePage, useForm,router } from '@inertiajs/vue3';
import AppCommentCard from '@/components/AppCommentCard.vue';
import AppLoader from '@/components/AppLoader.vue';
import AppButton from '@/components/AppButton.vue';
import axios from 'axios';
import AppIcon from '../../components/AppIcon.vue';
import pusher from '@/utils/pusher.js';
import PullRequests from '@/components/TasksComponent/PullRequests.vue';

const props = defineProps({
  taskDetail: {
    type: Object,
    default: undefined
  },
  assignedStatusArr: {
    type: Array,
    default: []
  },
  liveComment: { type: Object, default: null },
});

const emit = defineEmits(['reloadPage']);
const TasksComment = ref(null);
const loadingComment = ref(true);
const base_url = route().t.url;
const activeTab = ref(0);
const selectedImage = ref();
const comment = ref('');
const deployType = ref('development');
const deployLogs = ref([]);
const btnLoading = ref(false);
const isPinnedVisible = ref(false);
const currentPage = ref(1);
const hasMore = ref(true);
const loadingMore = ref(false);

const selectFiles = (e) => {
  // selectedImage.value = e.target.files;
  const files = Array.from(e.target.files);
  selectedImage.value = files;
};

const form = useForm({
  taskId: props.taskDetail.task.id,
  comment: null,
  status: props.taskDetail.task.status,
  attachments: null
});

// Modify getComment to properly handle loading state
/*const getComment = async (taskId) => {

    const response = await axios.get(`/tasks/${taskId}/comments`);
    // console.log(response.data);
    if (response.status === 200) {
      TasksComment.value = response.data;
      loadingComment.value = false;
    } else {
      console.error('Error fetching comments.');
      loadingComment.value = false;
    }
};
*/

const getComment = async (taskId, page = 1) => {
  if (page === 1) loadingComment.value = true;
  else loadingMore.value = true;

  const response = await axios.get(`/tasks/${taskId}/comments`, {
    params: { page }
  });

  if (response.status === 200) {
    if (page === 1) {
      TasksComment.value = response.data;
    } else {
      // Append older activities on load more
      TasksComment.value.activities.push(...response.data.activities);
    }
    hasMore.value = response.data.has_more;
    currentPage.value = page;
  }

  loadingComment.value = false;
  loadingMore.value = false;
};

const loadMore = () => {
  if (hasMore.value && !loadingMore.value) {
    getComment(props.taskDetail.task.id, currentPage.value + 1);
  }
};

/*const createComment = () => {
  btnLoading.value = true;
  form.taskId = props.taskDetail.task.id;
  form.comment = comment.value;
  form.status = props.taskDetail.task.status;
  form.attachments = selectedImage.value;

  // console.log('create comment', form.data());
  form.post(route('tasks.comments.store'), {
    forceFormData: true,
    onFinish: () => {
      console.log('success');
      btnLoading.value = false;
      //reloadComments();
    },
    onError: (error) => {
      console.log(error);
      // Optionally, show an error message
    },
    onSuccess: () => {
      (comment.value = ''), (selectedImage.value = null);
    }
  });
};
*/

const createComment = async () => {
  btnLoading.value = true;

  const formData = new FormData();
  formData.append('taskId', props.taskDetail.task.id);
  formData.append('comment', comment.value);
  formData.append('status', props.taskDetail.task.status);
  if (selectedImage.value) {
    selectedImage.value.forEach(file => formData.append('attachments[]', file));
  }

  try {
    await axios.post(route('tasks.comments.store'), formData);
    comment.value = '';
    selectedImage.value = null;
  } catch (error) {
    console.error(error);
  } finally {
    btnLoading.value = false;
  }
};


const getDeployLogs = async () => {
  try {
    const response = await axios.get(route('deploy.log.list'));
    if (response.status === 200) {
      deployLogs.value = response.data;
    }
  } catch (error) {
    console.error('Error fetching deploy logs:', error);
  }
};

const reloadComments = async() => {
  await getComment(props.taskDetail.task.id);
};

onMounted(() => {
  if (props.taskDetail && props.taskDetail.task.id) {
    getComment(props.taskDetail.task.id);
    getDeployLogs();
    
    // Subscribe to task status updates
    const channel = pusher.subscribe(`task.${props.taskDetail.task.id}`);
    
    channel.bind('task.status.updated', (data) => {
      // Update the task status in the component
      if (props.taskDetail && props.taskDetail.task) {
        props.taskDetail.task.status = data.new_status;
      }
      //reloadComments();
    });
  }
});

// Watch for changes in taskDetail and reload comments only after the first mount
/*watch(() => props.taskDetail, (newValue, oldValue) => {
  // Check if taskDetail has actually changed, and is not the initial load
  if (newValue && newValue.task.id == oldValue?.task.id) {
    reloadComments();
  }
}, { immediate: false });
*/

watch(() => props.taskDetail?.task?.id, (newId, oldId) => {
  if (newId && newId !== oldId) {
    currentPage.value = 1;
    getComment(newId);
  }
});

watch(() => props.liveComment, (data) => {
  if (!data || !TasksComment.value) return;
  console.log('📨 watcher triggered', data);

  if (data.reply_id && data.reply_id !== 0) {
    const parent = TasksComment.value.activities.find(
      (c) => c.id === data.reply_id
    );
    if (parent) {
      parent.replies = [...(parent.replies || []), data];
    }
  } else {
    TasksComment.value.activities.unshift(data);
  }
});

onUnmounted(() => {
  if (props.taskDetail && props.taskDetail.task.id) {
    pusher.unsubscribe(`task.${props.taskDetail.task.id}`);
  }
});

</script>
<template>
  <div class="w-full h-auto relative flex flex-col gap-5">
    <!-- <div class="w-full py-2 flex items-center justify-between mb-3 sticky top-0 z-30 bg-white/90     dark:bg-slate-900/90 backdrop-blur-sm border-b dark:text-slate-300">
            <h3 class="text-xl font-bold">Comments & Activities</h3>

            <a :href="`${base_url}/tasks/${taskDetail.task.id}/edit`">
                <AppButton icon="fa-gear" outline>More Options</AppButton>
            </a>
        </div> -->
    <div
      class="p-5 border dark:border-slate-700 border-primary rounded-lg bg-white dark:bg-slate-900"
    >
      <form id="commentForm" @submit.prevent="createComment">
        <input type="hidden" name="taskId" value="{{taskDetail.task.id}}" />
        <label class="dark:text-slate-400" for="comment" required>Add Comment:</label>

        <div class="flex flex-col gap-5">
          <div class="flex flex-col gap-y-1">
            <AppEditor v-model="comment" :errorMessage="form.errors.comment "/>
          </div>
          <div class="" v-if="assignedStatusArr && assignedStatusArr.length > 0">
            <label
              class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
              >Status*</label
            >
            <select
              v-model="taskDetail.task.status"
              class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-2 h-[44px]"
              name="priority"
            >
              <template v-for="elem in assignedStatusArr" :key="elem.id">
                <option :value="elem.value" :disabled="elem.disabled">
                  {{ elem.name }}
                </option>
              </template>
            </select>
          </div>
          <div class="gird">
            <div class="grid mb-5">
              <label for="attachments" class="dark:text-slate-400">Attachments:</label>
              <input
                ref="image"
                type="file"
                @change="selectFiles"
                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-2"
                name="attachments[]"
                accept="application/pdf,image/jpeg,image/png,.docx,.csv,.xls,.xlsx,.txt"
                multiple
                @focus="form.clearErrors('attachments.0')"
              />
              <transition
                enter-active-class="duration-300 ease-out"
                enter-from-class="-translate-y-2 opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="duration-100 ease-in"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="translate-y-1 opacity-0 "
              >
                <div v-show="form.errors['attachments.0']">
                  <p class="text-sm text-red-600 text-left">
                    {{ form.errors['attachments.0'] }}
                  </p>
                </div>
              </transition>
            </div>
          </div>
        </div>

        <AppButton type="submit" :loading="btnLoading">Comment</AppButton>
      </form>
    </div>

      <div v-if="loadingComment" class="border border-dashed border-blue-200 rounded-lg p-5 relative lg:h-[40vh]" >
          <AppLoader />
        </div>
    <div v-else-if="TasksComment">
  <div
      v-if="TasksComment.total_pinned_comments"
      class="border border-dashed border-blue-200 rounded-lg p-5"
    >
      <div
        class="flex justify-between items-center dark:text-slate-300 cursor-pointer"
        @click="isPinnedVisible = !isPinnedVisible"
      >
        <h3 class="text-xl font-bold">Pinned ({{TasksComment.total_pinned_comments}})</h3>
          <div  class="transition-transform w-8 h-8 flex items-center justify-center cursor-pointer" :class="isPinnedVisible ? 'rotate-180' : 'rotate-0'">
            <app-icon name="fa-chevron-down"></app-icon>
          </div>
        </div>

        <!-- Show pinned comments only when isPinnedVisible is true and comments are fetched -->
        <div v-show="isPinnedVisible">
          <template v-for="elem in TasksComment.activities" :key="elem.id">
            <AppCommentCard :data="elem" statusOptions="assignedStatusArr" v-if="elem.isPinned == 1" isPinned />
          </template>
        </div>
      </div>


        <template v-for="elem in TasksComment?.activities" :key="elem.id">
          <AppCommentCard :data="elem" :statusOptions="assignedStatusArr" @reloadPage="reloadComments" />
        </template>

        <div v-if="hasMore" class="text-center py-3">
        <AppButton @click="loadMore" :loading="loadingMore" outline>
          Load More
        </AppButton>
        </div>
      </div>
      </div>
  </template>
