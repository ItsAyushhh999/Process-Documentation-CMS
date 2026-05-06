<script setup>
import AppChip from '@/components/AppChip.vue';
import AppIcon from '@/components/AppIcon.vue';
import { inject } from 'vue';
const props = defineProps({
  data: {
    type: Object,
    default: null
  },
});

const filters = inject('filters');
const getStatusColor = (status) => {
  let color = 'blue';
  switch (status) {
    case 'INPROGRESS':
      color = 'yellow';
      break;
    case 'MERGE':
      color = 'lime';
      break;
    case 'CREATED':
      color = 'sky';
      break;
    case 'CLOSE':
      color = 'green';
      break;
    case 'Pull Request':
      color = 'orange';
      break;
    case 'Deployment':
      color = 'green';
      break;
    default:
      color = 'blue';
  }
  return color;
};


const convertStringToLowerCase = (val) => {
  return val.toLowerCase()
}


const formatNameToInitials = (name) => {
    if (!name) return 'BOT';
    const arr = name.split(' ');
    return `${arr[0].charAt(0)}${arr[1] ? arr[1].charAt(0) : ''}`.toUpperCase();
};

const getStatusLabel = (status) => {
    const statusLabels = {
        '0': { label: 'Assigned', color: 'text-red-400' },
        '1': { label: 'In Progress', color: 'text-violet-700' },
        '2': { label: 'Assigned for Review', color: 'text-yellow-500' },
        '3': { label: 'Reviewing', color: 'text-blue-500' },
        '4': { label: 'Reviewed', color: 'text-cyan-500' },
        '5': { label: 'Completed', color: 'text-green-500' },
        '6': { label: 'Closed', color: '' },
        '7': { label: 'Created', color: 'text-gray-500' },
        '8': { label: 'Staging - Ready to Upload', color: 'text-purple-700' },
        '9': { label: 'Staging - Uploaded', color: 'text-purple-900' },
        '10': { label: 'Live - Ready to Upload', color: 'text-teal-600' },
        '11': { label: 'Live - Uploaded', color: 'text-teal-900' },
        '15': { label: 'Dev - Ready to Upload', color: 'text-teal-600' },
        '16': { label: 'Dev - Uploaded', color: 'text-teal-900' }
    };
    return statusLabels[status] || { label: 'Unknown', color: '' };
};

</script>

<template>
 <div v-if="data.type" class="flex flex-col space-y-6  mx-auto">
                <div v-if="data.type === 'comment'" class="flex space-x-6  w-7/12 flex gap-8">
                    <div class="flex flex-col items-center ">
                        <div v-if="data.profile_picture"
                            class="w-10 h-10 rounded-full flex items-center justify-center text-white">
                            <img :src="`/storage/profiles/${data.profile_picture}`" title="{{data.profie_picture}}"
                                alt="profile picture"
                                class="rounded-full w-full h-full object-center object-cover cursor-pointer" />
                        </div>
                        <span v-else
                            class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                            {{ formatNameToInitials(data.CreatorName) }}
                        </span>
                        <span class="w-[1px] block bg-gray-300 dark:bg-slate-700 grow mt-2"></span>
                    </div>
                    <div class="flex flex-col w-full gap-y-5">

                        <div
                            class="border border-gray-300 dark:border-gray-600 flex grow relative rounded-lg  w-full bg-white dark:bg-slate-900">

                            <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                                <div class="flex justify-between items-center bg-gray-100 dark:bg-slate-800 py-2 px-3">
                                    <div class="flex gap-3">
                                        <span class="text-base dark:text-gray-300">
                                            <a :href="`tasks/${data.taskId}/edit`" v-if="data.taskId"
                                                class="flex items-center gap-x-2 divide-x text-gray-500 dark:text-slate-400 hover:text-primary-500 dark:hover:text-primary-300"><span
                                                    class=""><strong>#{{ data.taskId }}</strong></span><span
                                                    class="text-sm pl-2 line-clamp-1">{{ data.taskTitle ?
                                                        data.taskTitle : '-' }}</span></a>
                                        </span>
                                    </div>
                                    <div class="px-3 text-end flex items-center gap-2">
                                        <span class="text-base text-gray-500 dark:text-gray-300 pr-3">
                                            {{ filters.formatDate(data.created_at) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="px-3 py-2">
                                    <div class="flex gap-2 flex-wrap flex-1 mb-3">
                                        <div
                                            class="inline-flex items-center gap-2 rounded-full px-2 bg-blue-50 dark:bg-blue-600">
                                            {{ data.CreatorName }}
                                        </div>
                                        <div
                                            class="inline-flex items-center gap-2 rounded-full px-2 bg-blue-50 dark:bg-blue-600">
                                            {{ data.projectName }}
                                        </div>
                                    </div>
                                    <div class="dividerforcommentandreply scrolling-touch  overflow-x-auto scroll-none">
                                        <span class="dark:text-slate-300 comment_description"
                                            v-html="data.comments"></span>
                                    </div>
                                    <div v-if="data.get_comment_image && data.get_comment_image.length > 0"
                                        class="inline-flex gap-1 flex-wrap mt-3 pb-3">
                                        <a v-for="image in data.get_comment_image"
                                            :href="`/storage/tasks/${image.name}`"
                                            class="inline-flex items-center px-3 py-1 mb-2 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700 dark:text-slate-300"
                                            target="_blank">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img"
                                                class="w-3 h-3 iconify iconify--bi" preserveAspectRatio="xMidYMid meet"
                                                viewBox="0 0 16 16">
                                                <g fill="currentColor">
                                                    <path
                                                        d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z">
                                                    </path>
                                                </g>
                                            </svg>
                                            <span class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre">{{
                                                image.name }}</span>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- Replies Section -->
                        <div v-show="data.replies" :class="{ hidden: data.replies.length === 0 }">
                            <div v-for="(reply, replyIndex) in data.replies" :key="replyIndex" class="relative  ">

                                <div class="flex space-x-6 mb-3">
                                    <div v-if="reply.reply_creator.profile_picture"
                                        class="w-10 h-10 rounded-full overflow-hidden">
                                        <img :src="`/storage/profiles/${reply.reply_creator.profile_picture}`"
                                            :title="reply.reply_creator.profie_picture" alt="profile picture"
                                            class="rounded-full w-full h-full object-center object-cover cursor-pointer" />
                                    </div>
                                    <div v-else
                                        class="w-10 h-10 rounded-full flex items-center justify-center text-white bg-blue-500">
                                        {{ formatNameToInitials(reply.reply_creator.name) }}
                                    </div>
                                    <div
                                        class="border border-gray-300 dark:border-gray-600 ml-24 flex grow bg-white relative rounded-lg w-4/5 flex-col items-start flex-1 overflow-hidden">
                                        <div
                                            class="flex justify-between items-center bg-gray-100 dark:bg-slate-800 py-2 px-3 w-full relative z-10">
                                            <span class="text-base dark:text-gray-300 flex gap-3">
                                                <span class="text-base dark:text-gray-300">
                                                    <a :href="`tasks/${data.taskId}/edit`" v-if="data.taskId"
                                                        class="flex items-center gap-x-2 divide-x text-gray-500 dark:text-slate-400 hover:text-primary-500 dark:hover:text-primary-300"><span
                                                            class=""><strong>#{{ data.taskId }}</strong></span><span
                                                            class="text-sm pl-2 line-clamp-1">{{ data.taskTitle ?
                                                                data.taskTitle : '-' }}</span></a>
                                                </span>
                                            </span>
                                            <span class="text-base text-gray-500 dark:text-gray-300">
                                                {{ filters.formatDate(reply.created_at) }}
                                            </span>
                                        </div>
                                        <div class="px-3 py-2">
                                            <div class="flex gap-2 flex-wrap flex-1 mb-3">
                                                <div
                                                    class="inline-flex items-center gap-2 rounded-full px-2 bg-blue-50 dark:bg-blue-600">
                                                    {{ reply.reply_creator.name }}
                                                </div>
                                                <div
                                                    class="inline-flex items-center gap-2 rounded-full px-2 bg-blue-50 dark:bg-blue-600">
                                                    {{ data.projectName }}
                                                </div>
                                            </div>
                                            <span class="dark:text-slate-300 comment_description"
                                                v-html="reply.comments"></span>
                                        </div>
                                        <div v-if="reply.get_reply_image && reply.get_reply_image.length > 0"
                                            class="inline-flex gap-1 flex-wrap px-3 pb-3">
                                            <a v-for="image in reply.get_reply_image"
                                                :href="`/storage/tasks/${image.name}`"
                                                class="inline-flex items-center px-3 py-1 space-x-2 text-base transition-all border rounded-full hover:bg-primary-100 text-primary-700 border-primary-700 dark:text-slate-300"
                                                target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                                                    role="img" class="w-3 h-3 iconify iconify--bi"
                                                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16">
                                                    <g fill="currentColor">
                                                        <path
                                                            d="M.002 3a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-12a2 2 0 0 1-2-2V3zm1 9v1a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V9.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71l-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12zm5-6.5a1.5 1.5 0 1 0-3 0a1.5 1.5 0 0 0 3 0z">
                                                        </path>
                                                    </g>
                                                </svg>
                                                <span
                                                    class="max-w-[110px] text-ellipsis overflow-hidden whitespace-pre">{{
                                                        image.name }}</span>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div v-if="data.type === 'status'" class="flex  w-7/12 gap-8 space-x-6 ">
                    <div class="flex flex-col items-center ">
                        <div v-if="data.profile_picture"
                            class="w-10 h-10 rounded-full flex items-center justify-center text-white">
                            <img :src="`/storage/profiles/${data.profile_picture}`" title="{{data.profile_picture}}"
                                alt="profile picture"
                                class="rounded-full w-full h-full object-center object-cover cursor-pointer" />
                        </div>
                        <span v-else
                            class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                            {{ formatNameToInitials(data.createdByName) }}
                        </span>
                        <span class="w-[1px] block bg-gray-300 dark:bg-slate-700 grow mt-2"></span>
                    </div>
                    <div class="flex flex-col w-full gap-y-5">
                        <div
                            class="border border-gray-300 dark:border-gray-600 flex grow relative rounded-lg  w-full bg-white dark:bg-slate-900">
                            <div class="z-20 w-full h-full overflow-hidden rounded-lg">
                                <div class="flex justify-between items-center bg-gray-100 dark:bg-slate-800 py-2 px-3">
                                    <div class="flex gap-3">
                                        <span class="text-base dark:text-gray-300">
                                            <a :href="`tasks/${data.taskId}/edit`" v-if="data.taskId"
                                                class="flex items-center gap-x-2 divide-x text-gray-500 dark:text-slate-400 hover:text-primary-500 dark:hover:text-primary-300"><span
                                                    class=""><strong>#{{ data.taskId }}</strong></span><span
                                                    class="text-sm pl-2 line-clamp-1">{{ data.taskTitle ?
                                                        data.taskTitle : '-' }}</span></a>
                                        </span>
                                    </div>
                                    <div class="px-3 text-end flex items-center gap-2">
                                        <span class="text-base text-gray-500 dark:text-gray-300 pr-3">
                                            {{ filters.formatDate(data.created_at) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="px-3 py-2  items-center dark:text-slate-300">
                                    <div class="flex gap-2 flex-wrap flex-1 mb-2">
                                        <div
                                            class="inline-flex items-center gap-2 rounded-full px-2 bg-blue-50 dark:bg-blue-600">
                                            {{ data.createdByName }}
                                        </div>
                                        <div
                                            class="inline-flex items-center gap-2 rounded-full px-2 bg-blue-50 dark:bg-blue-600">
                                            {{ data.projectName }}
                                        </div>
                                    </div>
                                    <div class="flex">
                                        <template v-if="data.currentStatus === data.previousStatus">
                                            <span v-if="data.currentStatus == '7'" class="flex flex-wrap items-center">
                                                <span class="text-orange-500 p-1"><strong>Created</strong></span>
                                                a task.
                                            </span>
                                            <span v-else class="flex flex-wrap items-center">
                                                <span class="text-orange-500 p-1"><strong>Created</strong></span>
                                                <span class="p-1">and</span>
                                                <span class="text-blue-500 p-1"><strong>Assigned</strong></span>
                                                <span>a task.</span>
                                            </span>
                                        </template>
                                        <template v-else>
                                            <span class="gap-1 m-1">Changed status to</span>
                                            <span class="flex flex-wrap items-center gap-x-1">
                                                <span :class="getStatusLabel(data.currentStatus).color">
                                                    <strong>{{ getStatusLabel(data.currentStatus).label }}</strong>
                                                </span>
                                            </span>
                                            <span class="gap-1 m-1">from</span>
                                            <span class="flex flex-wrap items-center gap-x-1">
                                                <span :class="getStatusLabel(data.previousStatus).color">
                                                    <strong>{{ getStatusLabel(data.previousStatus).label }}</strong>
                                                </span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
  <div class="relative w-7/12 flex gap-8">
    <div v-if="data.source">
      <div v-if="data.source === 'AWS'"
        class="w-12 h-12 flex items-center justify-center bg-[#181616] dark:bg-slate-800 text-white rounded-lg">
        <svg viewBox="0 0 128 128" class="w-8 h-8">
          <g fill="currentColor">
            <path fill-rule="evenodd" clip-rule="evenodd"
              d="M64 5.103c-33.347 0-60.388 27.035-60.388 60.388 0 26.682 17.303 49.317 41.297 57.303 3.017.56 4.125-1.31 4.125-2.905 0-1.44-.056-6.197-.082-11.243-16.8 3.653-20.345-7.125-20.345-7.125-2.747-6.98-6.705-8.836-6.705-8.836-5.48-3.748.413-3.67.413-3.67 6.063.425 9.257 6.223 9.257 6.223 5.386 9.23 14.127 6.562 17.573 5.02.542-3.903 2.107-6.568 3.834-8.076-13.413-1.525-27.514-6.704-27.514-29.843 0-6.593 2.36-11.98 6.223-16.21-.628-1.52-2.695-7.662.584-15.98 0 0 5.07-1.623 16.61 6.19C53.7 35 58.867 34.327 64 34.304c5.13.023 10.3.694 15.127 2.033 11.526-7.813 16.59-6.19 16.59-6.19 3.287 8.317 1.22 14.46.593 15.98 3.872 4.23 6.215 9.617 6.215 16.21 0 23.194-14.127 28.3-27.574 29.796 2.167 1.874 4.097 5.55 4.097 11.183 0 8.08-.07 14.583-.07 16.572 0 1.607 1.088 3.49 4.148 2.897 23.98-7.994 41.263-30.622 41.263-57.294C124.388 32.14 97.35 5.104 64 5.104z">
            </path>
            <path
              d="M26.484 91.806c-.133.3-.605.39-1.035.185-.44-.196-.685-.605-.543-.906.13-.31.603-.395 1.04-.188.44.197.69.61.537.91zm2.446 2.729c-.287.267-.85.143-1.232-.28-.396-.42-.47-.983-.177-1.254.298-.266.844-.14 1.24.28.394.426.472.984.17 1.255zM31.312 98.012c-.37.258-.976.017-1.35-.52-.37-.538-.37-1.183.01-1.44.373-.258.97-.025 1.35.507.368.545.368 1.19-.01 1.452zm3.261 3.361c-.33.365-1.036.267-1.552-.23-.527-.487-.674-1.18-.343-1.544.336-.366 1.045-.264 1.564.23.527.486.686 1.18.333 1.543zm4.5 1.951c-.147.473-.825.688-1.51.486-.683-.207-1.13-.76-.99-1.238.14-.477.823-.7 1.512-.485.683.206 1.13.756.988 1.237zm4.943.361c.017.498-.563.91-1.28.92-.723.017-1.308-.387-1.315-.877 0-.503.568-.91 1.29-.924.717-.013 1.306.387 1.306.88zm4.598-.782c.086.485-.413.984-1.126 1.117-.7.13-1.35-.172-1.44-.653-.086-.498.422-.997 1.122-1.126.714-.123 1.354.17 1.444.663zm0 0">
            </path>
          </g>
        </svg>
      </div>

      <div v-else
        class="w-12 h-12 flex items-center justify-center bg-[#252f3e] dark:bg-slate-800 text-white rounded-lg">
        <svg viewBox="0 0 128 128" class="w-8 h-8">
          <path fill="currentColor"
            d="M108.59 26.148c-1.852 0-3.622.211-5.305.715-1.684.504-3.117 1.223-4.379 2.188a10.829 10.829 0 0 0-3.031 3.453c-.757 1.348-1.137 2.906-1.137 4.676 0 2.187.716 4.25 2.106 6.105 1.386 1.895 3.66 3.324 6.734 4.293l6.106 1.895c2.062.675 3.496 1.391 4.254 2.191.757.801 1.136 1.765 1.136 2.945 0 1.726-.758 3.074-2.191 4-1.43.925-3.492 1.391-6.145 1.391-1.687 0-3.328-.168-5.011-.504a23.102 23.102 0 0 1-4.633-1.476c-.421-.168-.801-.336-1.051-.418a2.357 2.357 0 0 0-.758-.13c-.634 0-.969.423-.969 1.305v2.149a2.919 2.919 0 0 0 .254 1.18c.168.38.629.8 1.305 1.18 1.094.628 2.734 1.179 4.84 1.683 2.105.504 4.297.758 6.484.758 2.15 0 4.129-.297 6.024-.883 1.808-.551 3.367-1.309 4.672-2.36 1.304-1.01 2.316-2.273 3.074-3.707.714-1.429 1.094-3.07 1.094-4.882 0-2.188-.633-4.168-1.938-5.895-1.304-1.727-3.491-3.074-6.523-4.043l-5.98-1.895c-2.23-.713-3.79-1.516-4.634-2.316-.84-.797-1.261-1.808-1.261-2.988 0-1.726.671-2.95 1.98-3.746 1.305-.801 3.199-1.18 5.598-1.18 2.988 0 5.683.547 8.086 1.64.714.337 1.261.508 1.597.508.633 0 .969-.463.969-1.347v-1.98c0-.59-.125-1.051-.379-1.391-.25-.378-.672-.715-1.262-1.051-.422-.254-1.011-.504-1.77-.758a32.528 32.528 0 0 0-2.398-.676c-.886-.168-1.769-.336-2.738-.46a21.347 21.347 0 0 0-2.82-.169zm-86.822.082c-2.316 0-4.508.254-6.57.801-2.063.505-3.831 1.137-5.303 1.895-.59.297-.97.59-1.18.883-.211.296-.293.8-.293 1.476v2.063c0 .882.293 1.304.883 1.304.168 0 .378-.043.674-.125.293-.086.796-.254 1.472-.547a33.416 33.416 0 0 1 4.547-1.433A19.176 19.176 0 0 1 20.547 32c3.242 0 5.513.633 6.863 1.938 1.304 1.303 1.98 3.534 1.98 6.734v3.074c-1.683-.379-3.283-.715-4.843-.926-1.558-.21-3.031-.336-4.461-.336-4.34 0-7.75 1.094-10.316 3.286-2.571 2.187-3.832 5.093-3.832 8.671 0 3.368 1.05 6.063 3.113 8.086 2.066 2.02 4.887 3.032 8.422 3.032 4.97 0 9.097-1.938 12.379-5.813a34.153 34.153 0 0 0 1.304 2.484 13.28 13.28 0 0 0 1.516 1.98c.422.38.844.59 1.266.59.334 0 .714-.128 1.093-.378l2.653-1.77c.546-.42.8-.843.8-1.261a1.86 1.86 0 0 0-.293-.97 22.469 22.469 0 0 1-1.347-3.03c-.297-.925-.465-2.19-.465-3.75h-.086V40c0-4.633-1.176-8.086-3.492-10.36-2.36-2.273-6.025-3.41-11.033-3.41zm19.58 1.012c-.676 0-1.012.379-1.012 1.051 0 .297.129.844.379 1.687l9.894 32.547c.254.8.547 1.387.887 1.641.336.297.84.422 1.598.422h3.62c.759 0 1.347-.125 1.684-.422.34-.293.591-.84.801-1.684l6.485-27.117 6.527 27.16c.168.84.46 1.387.8 1.684.337.292.883.422 1.684.422h3.621c.715 0 1.262-.167 1.598-.422.34-.253.633-.8.887-1.64L90.949 30.02c.168-.46.25-.797.293-1.051.043-.254.086-.466.086-.676 0-.715-.379-1.05-1.055-1.05H86.36c-.757 0-1.308.166-1.644.421-.293.25-.59.8-.84 1.64L76.59 57.517l-6.653-28.211c-.166-.8-.464-1.39-.8-1.64-.336-.298-.884-.423-1.684-.423h-3.367c-.758 0-1.348.167-1.688.422-.335.25-.588.8-.796 1.64l-6.57 27.876-7.075-27.875c-.25-.8-.504-1.39-.84-1.64-.297-.298-.844-.423-1.644-.423h-4.125zM21.64 47.496a31.816 31.816 0 0 1 3.96.25 34.401 34.401 0 0 1 3.872.719v1.765c0 1.435-.168 2.653-.422 3.665-.25 1.01-.758 1.895-1.43 2.695-1.137 1.262-2.484 2.187-4 2.695-1.516.504-2.949.758-4.336.758-1.937 0-3.41-.508-4.422-1.559-1.054-1.01-1.558-2.484-1.558-4.464 0-2.106.675-3.704 2.062-4.84 1.391-1.137 3.454-1.684 6.274-1.684zM118 73.348c-4.432.063-9.664 1.052-13.621 3.832-1.223.883-1.012 2.062.336 1.894 4.508-.547 14.44-1.726 16.21.547 1.77 2.23-1.976 11.62-3.663 15.79-.504 1.26.59 1.769 1.726.8 7.41-6.231 9.348-19.242 7.832-21.137-.757-.925-4.388-1.79-8.82-1.726zM1.63 75.859c-.926.116-1.347 1.236-.368 2.121 16.508 14.902 38.359 23.872 62.613 23.872 17.305 0 37.43-5.43 51.281-15.66 2.273-1.689.298-4.254-2.02-3.204-15.533 6.57-32.421 9.77-47.788 9.77-22.778 0-44.8-6.273-62.653-16.633-.39-.231-.755-.304-1.064-.266z">
          </path>
        </svg>
      </div>
    </div>
    <div  v-if="data.source" class="border flex-1  rounded-lg bg-white dark:bg-slate-800 dark:border-slate-700 relative">
      <div class="flex justify-between text-sm text-gray-500 px-5 pt-5">
        <a :href="`tasks/${data.task.id}/edit`" v-if="data.task"
          class="flex items-center gap-x-2 divide-x text-gray-500 dark:text-slate-400 hover:text-primary-500 dark:hover:text-primary-300"><span
            class=""><strong>#{{ data.task_id }}</strong></span><span class="text-sm pl-2 line-clamp-1">{{ data.task ?
        data.task.title : '-' }}</span></a>
        <span v-else class="text-sm line-clamp-1">Amazon Web Service</span>
        <div class="inline-flex gap-2 items-center">
          <AppIcon name="fa-calendar-days" />
          {{ filters.formatDate(data.created_at) }}
        </div>
      </div>
      <div class="px-5 pb-5 pt-2">
        <div class="flex flex-col gap-2 mb-6">
          <span>{{ data.title }}</span>
          <div v-if="data.project">
            <a :href="`project/${data.project.id}/document`"
              class="text-primary-500 dark:text-primary-300 hover:underline cursor-pointer transition-all text-sm">{{ data.project.name }}
              <strong>({{ data.project.id }})</strong></a>
          </div>
        </div>
        <div class="flex items-center gap-2">
          <AppChip :color="getStatusColor(data.type)">{{ data.type }}</AppChip>
          <AppChip :color="getStatusColor(data.status)">{{ convertStringToLowerCase(data.status) }}</AppChip>
        </div>
      </div>
      <div
        class="absolute h-4 w-4 top-4 -left-2 bg-white dark:bg-slate-800 dark:border-slate-700 rotate-45 border-l border-b">
      </div>
    </div>
  </div>
</template>
