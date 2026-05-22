<script setup>
import { computed, onMounted, ref } from 'vue';
import AppButton from './AppButton.vue';
import Modal from '@/components/Modal.vue';
import AppEditor from '@/components/AppEditor.vue';
import { router, useForm } from '@inertiajs/vue3';
import { inject } from 'vue';
import AppIcon from './AppIcon.vue';
import axios from 'axios';
const filters = inject('filters');
const props = defineProps({
    data: {
        type: Object,
        default: null
    },
    isPinned: {
        type: Boolean,
        default: false
    },
    statusOptions: {
        type: Array,
        default: null
    }

});

const replyComment = ref('');
const replySelectedImg = ref([]);
const emit = defineEmits(['reloadPage'])
const isLoading = ref(false);

const formatNameToInitials = (name) => {
  if (!name || name === 0) return 'BOT';
  if (typeof name === 'string' && name.length > 0) {
    const arr = name.split(' ');
    return `${arr[0]?.charAt(0) ?? ''}${arr[1]?.charAt(0) ?? ''}`;
  }
  return 'AI';
};

const pinModal = async (id) => {
    try {
        await router.post(route('tasks.pinComment', { id }), {}, {
            preserveScroll: true, // Keeps the scroll position intact
            preserveState: true, // Prevents a full reload of the page state
            onSuccess: () => {
                // After a successful request, toggle `isPinned` directly
                if (props.data && props.data.id === id) {
                    props.data.isPinned = props.data.isPinned ^ 1;
                }
            },
        });
    } catch (error) {
        console.error('Error pinning the comment:', error);
    }
};

const isCommentModalVisible = ref(false);

const CommentReplyModal = (id) => {
    // currentCommentId.value = id;
    // console.log('app modal', props.data);
    isCommentModalVisible.value = true;
    // tasks.comments.store
};

const handleReplyComment = async () => {
  isLoading.value = true;

  const formData = new FormData();
  formData.append('comment_id', props.data.id);
  formData.append('taskId', props.data.taskId);
  formData.append('comment', replyComment.value);
  if (replySelectedImg.value) {
    replySelectedImg.value.forEach((file) => {
      formData.append('attachments[]', file);
    });
  }

  try {
    await axios.post(route('tasks.comments.storeV2'), formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    closeModal();
  } catch (error) {
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};


const selectFiles = (e) => {
    const files = Array.from(e.target.files);
    replySelectedImg.value = files;
};

const closeModal = () => {

    isCommentModalVisible.value = false;
    replyComment.value = '',
    replySelectedImg.value = []
    isLoading.value = false

};

const commentCheck = (id) => {
    router.post(route('tasks.checkedComment', { id: id }));
}

const status = (value) => {
    const option = props.statusOptions.find((elem) => elem.value === value);
    return option ? option.name : 'Unknown';
};
</script>

<template>
    <!-- if the the data is comment -->
    <div v-if="isPinned" class="flex space-x-6">
        <div class="flex flex-col items-center mt-4">
            <div
                v-if="data.profie_picture"
                class="w-10 h-10 rounded-full flex items-center justify-center text-white"
            >
                <img
                    :src="`/storage/profiles/${data.profie_picture}`"
                    title="{{data.profie_picture}}"
                    alt="profile picture"
                    class="rounded-full w-full h-full object-center object-cover cursor-pointer"
                    loading="lazy"
                />
            </div>
            <span
                v-else
                class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white"
            >
                {{ formatNameToInitials(data.createdBy) }}
            </span>
        </div>

        <div class=" flex flex-col w-full gap-y-5">
            <div
                class="border border-gray-300 dark:border-gray-600 flex grow relative rounded-lg mt-4 w-full"
            >
                <div
                    class="absolute border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-slate-800 w-4 h-4 rotate-45 top-3 -left-2 z-10"
                ></div>
                <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                    <div
                        class="flex justify-between items-center bg-gray-100 dark:bg-slate-800 py-2 px-3"
                    >
                        <div class="flex gap-3 items-center">
                            <span class="text-base dark:text-gray-300">
                                <strong>{{ data.user }} </strong>
                            </span>
                            <span class="text-sm text-gray-500">({{data.pinnedBy }} pinned a comment)
                            </span>
                        </div>
                    </div>
                    <div class="px-3 py-2">
                        <div
                            class="dividerforcommentandreply scrolling-touch overflow-x-auto scroll-none"
                        >
                            <!-- <span>{{data}}</span> -->
                            <span
                                class="dark:text-slate-300 comment_description prose prose-ul:mt-0 prose-p:m-0.5 prose-li:m-0 prose-a:text-sky-500"
                                v-html="data.comments"
                                loading="lazy"
                            >
                            </span>
                        </div>

                        <div
                            class="mt-1"
                            v-if="data.get_comment_image && data.get_comment_image.length > 0"
                        >
                            <a
                                v-for="image in data.get_comment_image"
                                :key="image.id"
                                :href="`/storage/tasks/${image.name}`"
                                class="inline-flex items-center px-3 py-1 mb-2 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700 dark:text-slate-300"
                                target="_blank"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true"
                                    role="img"
                                    class="w-3 h-3 iconify iconify--bi"
                                    preserveAspectRatio="xMidYMid meet"
                                    viewBox="0 0 16 16"
                                >
                                    <g fill="currentColor">
                                        <path
                                            d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z"
                                        ></path>
                                    </g>
                                </svg>
                                <span
                                    class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre"
                                    >{{ image.name }}</span
                                >
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="flex space-x-6" v-else-if="data.comments">
        <div class="flex flex-col items-center mt-4">
            <div
                v-if="data.profie_picture"
                class="w-10 h-10 rounded-full flex items-center justify-center text-white"
            >
                <img
                    :src="`/storage/profiles/${data.profie_picture}`"
                    title="{{data.profie_picture}}"
                    alt="profile picture"
                    class="rounded-full w-full h-full object-center object-cover cursor-pointer"
                    loading="lazy"
                />
            </div>
            <span
                v-else
                class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white"
            >
                {{ formatNameToInitials(data.user) }}
            </span>
            <span class="w-[1px] block bg-gray-300 dark:bg-slate-700 grow mt-2"></span>
        </div>

        <div class="flex flex-col w-full gap-y-5">
            <div
                class="border border-gray-300 dark:border-gray-600 flex grow relative rounded-lg mt-4 w-full bg-white dark:bg-slate-900"
            >
                <div
                    class="absolute border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-slate-800 w-4 h-4 rotate-45 top-3 -left-2 z-10"
                ></div>
                <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                    <div
                        class="flex justify-between items-center bg-gray-100 dark:bg-slate-800 py-2 px-3"
                    >
                        <div class="flex gap-3">
                            <span class="text-base dark:text-gray-300">
                                <strong>{{ data.user }} </strong>
                            </span>
                            <button
                                @click="pinModal(data.id)"
                                class=" rounded-full  p-1 w-7 h-7 flex items-center justify-center rotate-45 transition-colors"
                                :class="data.isPinned == 1 ? 'bg-red-100  text-red-500 hover:bg-red-200 hover:text-red-600' : 'bg-gray-100  text-gray-500 hover:bg-gray-200 hover:text-gray-600'"
                            >
                                <AppIcon name="fa-thumbtack" class="w-4 h-4"/>
                            </button>
                            <div :title="data.check === '1' ? `Marked as completed by ${data.checkedBy}` : 'Mark as completed'">
                                <input
                                type="checkbox"
                                :checked="data.check === '1'"
                                @click="commentCheck(data.id)"
                                class="hidden peer"
                                :id="'checkbox-' + data.id"
                                />
                                <label
                                :for="'checkbox-' + data.id"
                                class="w-6 h-6 rounded-full peer-checked:text-green-500 flex items-center justify-center
                                 cursor-pointer relative"
                                >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                </svg>

                                </label>
                            </div>
                        </div>

                        <div class="px-3 text-end flex items-center gap-2">
                            <span class="text-base text-gray-600 dark:text-gray-300 pr-3">
                                <!-- {{ $comment->created_at->format('m/d/Y h:i A') }} -->
                                {{ filters.formatDate(data.created_at) }}
                            </span>
                            <!-- <AppButton
                                id="reply_mode_on"
                                @click="CommentReplyModal(data.id)"
                                outline
                            >
                                Reply
                            </AppButton> -->
                            <button
                                id="reply_mode_on"
                                @click="CommentReplyModal(data.id)"
                                class="text-blue-500 bg-transparent border px-4 py-1 rounded border-blue-500 hover:bg-blue-500 hover:text-white transition-all dark:border-blue-400 dark:text-blue-300"
                            >
                                Reply
                            </button>
                        </div>
                    </div>
                    <div class="px-3 py-2">
                        <div
                            class="dividerforcommentandreply scrolling-touch overflow-x-auto scroll-none"
                        >
                            <!-- <span>{{data}}</span> -->
                            <span
                                class="dark:text-slate-300 comment_description prose prose-ul:mt-0 prose-p:m-0.5 prose-li:m-0 prose-a:text-sky-500"
                                v-html="data.comments"
                                loading="lazy"
                            >
                            </span>
                        </div>

                        <div
                            class="mt-1"
                            v-if="data.get_comment_image && data.get_comment_image.length > 0"
                        >
                            <a
                                v-for="image in data.get_comment_image"
                                :key="image.id"
                                :href="`/storage/tasks/${image.name}`"
                                class="inline-flex items-center px-3 py-1 mb-2 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700 dark:text-slate-300"
                                target="_blank"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                    aria-hidden="true"
                                    role="img"
                                    class="w-3 h-3 iconify iconify--bi"
                                    preserveAspectRatio="xMidYMid meet"
                                    viewBox="0 0 16 16"
                                >
                                    <g fill="currentColor">
                                        <path
                                            d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z"
                                        ></path>
                                    </g>
                                </svg>
                                <span
                                    class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre"
                                    >{{ image.name }}</span
                                >
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid gap-4" v-if="data.replies">
                <template v-for="replies in data.replies" :key="replies.id">

                    <div class="relative">
                        <div
                            class="absolute -left-11 top-2 w-8 h-4 rounded-bl-full border-l border-b dark:border-slate-700"
                        ></div>

                        <div class="flex space-x-6">

            <div v-if="replies.reply_creator.profile_picture" class="w-10 h-10 rounded-full overflow-hidden">
                <img
                    :src="`/storage/profiles/${ replies.reply_creator.profile_picture}`"
                    :title="data.profie_picture"
                    alt="profile picture"
                    class="rounded-full w-full h-full object-center object-cover cursor-pointer"
                    loading="lazy"
                />
                </div>
                <div v-else class="w-10 h-10 rounded-full flex items-center justify-center text-white bg-blue-500">
                {{ formatNameToInitials(replies.reply_creator.name) }}
                </div>
                            <div
                                class="border border-gray-300 dark:border-gray-600 flex grow relative rounded-lg w-full flex-col items-start flex-1 overflow-hidden"
                            >
                                <div
                                    class="flex justify-between items-center bg-gray-100 dark:bg-slate-800 py-2 px-3 w-full relative z-10"
                                >
                                    <span class="text-base dark:text-gray-300 flex gap-3">
                                        <strong>{{ replies.reply_creator.name }} </strong>
                                        <div :title="replies.check === '1' ? `Marked as completed by ${replies.checked_by.name} ` : 'Mark as completed'">
                                            <input
                                            type="checkbox"
                                            :checked="replies.check === '1'"
                                            @click="commentCheck(replies.id)"
                                            class="hidden peer"
                                            :id="'checkbox-' + replies.id"
                                            />
                                            <label
                                                :for="'checkbox-' + replies.id"
                                                class="w-6 h-6 rounded-full peer-checked:text-green-500 flex items-center justify-center
                                                cursor-pointer relative"
                                                >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                                </svg>
                                            </label>
                                         </div>
                                    </span>


                                    <span class="text-base text-gray-600 dark:text-gray-300">
                                        <!-- {{ \Carbon\Carbon::parse($comment_reply->created_at)->format('m/d/Y h:i
                                                  A') }} -->
                                        {{ filters.formatDate(replies.created_at) }}
                                    </span>
                                </div>

                                <div class="p-3">
                                    <span
                                        class="dark:text-slate-300 comment_description prose prose-ul:mt-0 prose-p:m-0.5 prose-li:m-0 prose-a:text-sky-500"
                                        v-html="replies.comments"
                                        loading="lazy"
                                    >
                                    </span>
                                </div>
                                <div v-if="replies.get_reply_image && replies.get_reply_image.length > 0" class="inline-flex gap-1 flex-wrap px-3 pb-3">
                                    <a
                                        v-for="image in replies.get_reply_image"
                                        :key="image.id"
                                        :href="`/storage/tasks/${image.name}`"
                                        class="inline-flex items-center px-3 py-1 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700 dark:text-slate-300"
                                        target="_blank"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink"
                                            aria-hidden="true"
                                            role="img"
                                            class="w-3 h-3 iconify iconify--bi"
                                            preserveAspectRatio="xMidYMid meet"
                                            viewBox="0 0 16 16"
                                        >
                                            <g fill="currentColor">
                                                <path
                                                    d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z"
                                                ></path>
                                            </g>
                                        </svg>
                                        <span
                                            class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre"
                                            >{{ image.name }}</span
                                        >
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
    <!-- if activities -->
    <div v-else-if="data.property" class="flex space-x-6">


        <div  class="flex flex-col items-center mt-4">
            <span
                class="w-10 h-10 border border-gray-300 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-300"
            >
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
                        d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"
                    />
                </svg>
            </span>
            <span class="w-[1px] block bg-gray-300 dark:bg-slate-700 grow mt-2"></span>
        </div>
        <div
            class="border border-gray-300 dark:border-gray-600 w-20 flex grow relative rounded-lg mt-4"
        >
            <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                <div
                    class="flex justify-between items-center py-2 px-3 bg-gray-100 dark:bg-slate-800"
                >
                    <span class="text-base dark:text-slate-300">
                        <strong>{{ data.createdBy }} </strong>
                    </span>

                    <span class="text-base text-gray-500 dark:text-slate-300">
                        <!-- {{ $comment->created_at->format('m/d/Y h:i A') }} -->
                        {{ filters.formatDate(data.created_at) }}
                    </span>
                </div>
                <template v-if="data.property === '0'">
                    <div class="px-3 py-2 flex items-center dark:text-slate-300">
                        <div  v-if="data.action === '0'">
                            <span class="gap-1 m-1">Added </span><strong>{{ data.addedUser }}</strong><span> as a </span><strong> assginee</strong>
                        </div>
                        <div  v-if="data.action === '1'">
                            <span class="gap-1 m-1">Removed </span><strong> {{ data.removedUser }}</strong><span> from a </span><strong> assignee</strong>
                        </div>
                    </div>
                </template>
                 <template v-else-if="data.property === '1'">
                        <div class="px-3 py-2 flex items-center dark:text-slate-300">
                            <div  v-if="data.action === '0'">
                                <span class="gap-1 m-1">Added </span><strong>{{ data.addedUser }}</strong><span> as a </span><strong>reviewer</strong>
                            </div>
                            <div  v-if="data.action === '1'">
                                <span class="gap-1 m-1">Removed </span><strong>{{ data.removedUser }}</strong><span> from a </span><strong>reviewer</strong>
                            </div>
                        </div>
                </template>
                <template v-else-if="data.property === '2'">
                        <div class="px-3 py-2 flex items-center dark:text-slate-300">
                            <div  v-if="data.action === '2'">
                                <span class="gap-1 m-1">Changed deadline to</span>
                                <strong>{{filters.formatDate(data.newValue)}}</strong>
                                form
                                <strong>{{filters.formatDate(data.oldValue)}}</strong>
                            </div>
                        </div>
                </template>
                    <template v-else-if="data.property === '3'">
                        <div class="px-3 py-2 flex items-center dark:text-slate-300">
                            <div  v-if="data.action === '2'">
                                <span class="gap-1 m-1">Changed priority to</span>
                                <span v-if="data.newId == '1'" class="text-red-400">
                                    <strong>Normal</strong>
                                    </span>
                                    <span v-if="data.newId == '2'" class="text-violet-700">
                                        <strong>High</strong>
                                    </span>
                                    <span v-if="data.newId == '3'" class="text-yellow-500">
                                        <strong>Urgent</strong>
                                    </span>
                                     form
                                <span v-if="data.oldId == '1'" class="text-red-400">
                                        <strong>Normal</strong>
                                    </span>
                                    <span v-if="data.oldId == '2'" class="text-violet-700">
                                        <strong>High</strong>
                                    </span>
                                    <span v-if="data.oldId == '3'" class="text-yellow-500">
                                    <strong>Urgent</strong>
                                </span>
                             </div>
                        </div>

                    </template>
                    <template v-else>
                        <div class="px-3 py-2 flex items-center dark:text-slate-300">
                            <div  v-if="data.action === '0'">
                                <span class="gap-1 m-1">Added </span><strong>{{ data.addedTaskTypes }}</strong><span> as a </span><strong>taskTypes</strong>
                            </div>
                            <div  v-if="data.action === '1'">
                                <span class="gap-1 m-1">Removed </span><strong>{{ data.removedTaskTypes }}</strong><span> from a </span><strong>taskTypes</strong>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
    </div>
    <!-- Add more conditions based on data.property if needed -->
    <div v-else class="flex space-x-6">
        <div  class="flex flex-col items-center mt-4">
            <span
                class="w-10 h-10 border border-gray-300 rounded-lg flex items-center justify-center text-gray-500 dark:text-gray-300"
            >
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
                        d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"
                    />
                </svg>
            </span>
            <span class="w-[1px] block bg-gray-300 dark:bg-slate-700 grow mt-2"></span>
        </div>
        <div
            class="border border-gray-300 dark:border-gray-600 w-20 flex grow relative rounded-lg mt-4"
        >
            <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                <div
                    class="flex justify-between items-center py-2 px-3 bg-gray-100 dark:bg-slate-800"
                >
                    <span class="text-base dark:text-slate-300">
                        <strong>{{ data.createdBy }} </strong>
                    </span>

                    <span class="text-base text-gray-500 dark:text-slate-300">
                        <!-- {{ $comment->created_at->format('m/d/Y h:i A') }} -->
                        {{ filters.formatDate(data.created_at) }}
                    </span>
                </div>

                <div class="px-3 py-2 flex items-center dark:text-slate-300">
                    <template v-if="data.currentStatus === data.previousStatus">
                        <span v-if="data.currentStatus == '7'" class="flex flex-wrap items-center">
                            <span class="text-orange-500 p-1"> <strong>Created</strong> </span>
                            a task.
                        </span>
                        <span v-else class="flex flex-wrap items-center">
                            <span class="text-orange-500 p-1"> <strong>Created</strong> </span>
                            <span class="p-1">and</span>
                            <span class="text-blue-500 p-1"> <strong>Assigned</strong> </span>
                            <span>a task.</span>
                        </span>
                    </template>
                    <template v-else>
                        <span class="gap-1 m-1">Changed status to</span>
                        <span class="flex flex-wrap items-center gap-x-1">
                            <span v-if="data.currentStatus == '0'" class="text-red-400">
                                <strong>Assigned</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '1'" class="text-violet-700">
                                <strong>In Progress</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '2'" class="text-yellow-500">
                                <strong>Assigned for Review</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '3'" class="text-blue-500">
                                <strong>Reviewing</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '4'" class="text-cyan-500">
                                <strong>Reviewed</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '5'" class="text-green-500">
                                <strong>Completed</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '6'" > <strong>Closed</strong> </span>
                            <span v-else-if="data.currentStatus == '7'" class="text-gray-500">
                                <strong>Created</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '8'" class="text-purple-700">
                                <strong>Staging - Ready to Upload</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '9'" class="text-purple-900">
                                <strong>Staging - Uploaded</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '10'" class="text-teal-600">
                                <strong>Live - Ready to Upload</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '11'" class="text-teal-900">
                                <strong>Live - Uploaded</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '15'" class="text-teal-600">
                                <strong>Dev - Ready to Upload</strong>
                            </span>
                            <span v-else-if="data.currentStatus == '16'" class="text-teal-900">
                                <strong>Dev - Uploaded</strong>
                            </span>
                            <span v-else class="text-teal-900">
                                <strong> {{ status(data.currentStatus) }}</strong>
                            </span>
                        </span>
                        <span class="gap-1 m-1">from</span>
                        <span class="flex flex-wrap items-center gap-x-1">
                            <span v-if="data.previousStatus == '0'" class="text-red-400">
                                <strong>Assigned</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '1'" class="text-violet-700">
                                <strong>In Progress</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '2'" class="text-yellow-500">
                                <strong>Assigned for Review</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '3'" class="text-blue-500">
                                <strong>Reviewing</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '4'" class="text-cyan-500">
                                <strong>Reviewed</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '5'" class="text-green-500">
                                <strong>Completed</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '6'"> <strong>Closed</strong> </span>
                            <span v-else-if="data.previousStatus == '7'" class="text-gray-500">
                                <strong>Created</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '8'" class="text-purple-700">
                                <strong>Staging - Ready to Upload</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '9'" class="text-purple-900">
                                <strong>Staging - Uploaded</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '10'" class="text-teal-600">
                                <strong>Live - Ready to Upload</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '11'" class="text-teal-900">
                                <strong>Live - Uploaded</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '15'" class="text-teal-600">
                                <strong>Dev - Ready to Upload</strong>
                            </span>
                            <span v-else-if="data.previousStatus == '16'" class="text-teal-900">
                                <strong>Dev - Uploaded</strong>
                            </span>
                            <span v-else class="text-teal-900">
                                <strong> {{ status(data.previousStatus) }}</strong>
                            </span>
                        </span>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <Modal :show="isCommentModalVisible" @close="closeModal">
        <template #header>
            <h2 class="text-xl font-semibold">Reply</h2>
        </template>
        <form  v-if="isCommentModalVisible" class="p-6 grid gap-5" @submit.prevent="handleReplyComment">
            <div class="grid">
                <AppEditor v-model="replyComment"></AppEditor>
            </div>
            <div class="gird">
                <div class="grid mb-5">
                    <label for="attachments">Attachments:</label>
                    <input
                        ref="image"
                        type="file"
                        @change="selectFiles"
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        name="attachments[]"
                        accept="application/pdf,image/jpeg,image/png, .csv"
                        multiple
                    />
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <AppButton @click="closeModal" text> Cancel </AppButton>
                <AppButton type="submit" :loading="isLoading">Reply Comment</AppButton>
            </div>
        </form>
    </Modal>
</template>

<style>
/* .comment_description{
    @apply break-words;
}
.comment_description p{
    @apply break-words;
}
.comment_description  a {
    @apply !text-blue-500 !underline;
}
.comment_description ol{
    @apply !list-decimal ml-6;
}
.comment_description ul{
    @apply !list-disc ml-6;
}
.comment_description pre{
    @apply bg-slate-800 rounded-lg p-5 text-slate-300 break-words whitespace-pre-wrap dark:border dark:border-slate-700;
} */


</style>
