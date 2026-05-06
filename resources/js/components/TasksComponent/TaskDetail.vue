<script setup>
import { inject } from 'vue';
import AppIcon from '@/components/AppIcon.vue';
import AppUserChip from '@/components/AppUserChip.vue'

const filters = inject('filters');

const props = defineProps({
    data: {
        type: Object,
        default: undefined
    },
});
</script>

<style scoped>
.description :deep(p) {
    /* margin: .3em .3em; */
}
</style>

<template>
    <div class="">
        <h1 class="text-xl text-gray-800 font-bold dark:text-gray-300 mb-6">
            {{ data.task.title }}
        </h1>
        <div class="grid gap-6">
            <div class="flex items-center" v-if="data.projectName">
                <div
                    class="w-[200px] inline-flex items-center gap-x-2 text-slate-600 dark:text-slate-300"
                >
                    <AppIcon name="fa-code" />
                    <span>Project</span>
                </div>
                <div class="flex items-center gap-x-3">
                    <span class="font-semibold text-slate-600 dark:text-slate-300">
                        {{ data.projectName }}
                    </span>
                </div>
            </div>
            <div class="flex items-center">
                <div
                    class="w-[200px] inline-flex items-center gap-x-2 text-slate-600 dark:text-slate-300"
                >
                    <AppIcon name="fa-calendar-days" />
                    <span>Created At/By</span>
                </div>
                <div class="flex items-center gap-x-3">
                    <span class="font-semibold text-slate-600 dark:text-slate-300">
                        {{ filters.formatDate(data.task.created_at) }}
                    </span>
                    <AppUserChip :name="data.task.createdBy" />
                </div>
            </div>
            <div class="flex items-center">
                <div
                    class="w-[200px] inline-flex items-center gap-x-2 text-slate-600 dark:text-slate-300"
                >
                    <AppIcon name="fa-calendar-days" />
                    <span>Deadline</span>
                </div>
                <div class="flex items-center">
                    <span class="font-semibold text-slate-600 dark:text-slate-300">
                        <!-- {{ $data -> created_at -> format('m/d/Y h:i A') }} -->
                        {{ filters.formatDate(data.task.deadline) }}
                    </span>
                </div>
            </div>

            <div class="flex items-center">
                <div
                    class="w-[200px] inline-flex items-center gap-x-2 text-slate-600 dark:text-slate-300"
                >
                    <AppIcon name="fa-calendar-days" />
                    <span>Last Updated At</span>
                </div>
                <div class="flex items-center gap-x-3">
                    <span class="font-semibold text-slate-600 dark:text-slate-300">
                        {{ filters.formatDate(data.task.updated_at) }}
                    </span>
                    <AppUserChip :name="data.task.updatedBy" />
                </div>
            </div>

            <div class="flex items-center">
                <div
                    class="w-[200px] inline-flex items-center gap-x-2 text-slate-600 dark:text-slate-300"
                >
                    <AppIcon name="fa-circle-dot" />
                    <span>Status</span>
                </div>
                <!-- <span>{{ data.task.status }}</span> -->
                <div class="flex items-center">
                    <!-- @if($task->status == '0') -->
                    <div v-if="data.taskStatus && data.taskStatus.length > 0" class="flex items-center">
                    <span
                        v-for="status in data.taskStatus"
                        :key="status.id"
                    >
                    <span  v-if =" data.task.status  == status.value">
                        {{ status.name }}
                    </span>

                    </span>
                </div>

                </div>
            </div>

            <div class="flex items-center">
                <div
                    class="w-[200px] inline-flex items-center gap-x-2 text-slate-600 dark:text-slate-300"
                >
                    <AppIcon name="fa-circle-exclamation" />
                    <span>Priority</span>
                </div>
                <div class="flex items-center">
                    <!-- @if ($task->priority == '0') -->
                    <span
                        v-if="data.task.priority == '0'"
                        class="px-3 py-1 text-blue-900 bg-blue-200 rounded-full text-base font-semibold"
                    >
                        Normal
                    </span>
                    <!-- @elseif($task->priority == '1') -->
                    <span
                        v-else-if="data.task.priority == '1'"
                        class="px-3 py-1 text-yellow-900 bg-yellow-200 rounded-full text-base font-semibold"
                    >
                        High
                    </span>
                    <!-- @else -->
                    <span
                        v-else-if="data.task.priority == '2'"
                        class="px-3 py-1 text-red-900 bg-red-200 rounded-full text-base font-semibold"
                    >
                        Urgent
                    </span>
                    <!-- @endif -->
                </div>
            </div>

            <div class="flex items-start">
                <div
                    class="w-[200px] inline-flex items-center gap-x-2 text-slate-600 dark:text-slate-300"
                >
                    <AppIcon name="fa-users" />
                    <span>Assignees</span>
                </div>
                <div class="flex gap-2 flex-wrap flex-1">
                    <template v-for="elem in data.assignees" :key="elem.collaborator">
                        <AppUserChip
                            :image="elem.profile_picture"
                            :name="elem.collaboratorName"
                        ></AppUserChip>
                    </template>
                </div>
            </div>

            <div class="flex items-start">
                <div
                    class="w-[200px] inline-flex items-center gap-x-2 text-slate-600 dark:text-slate-300"
                >
                    <AppIcon name="fa-users" />
                    <span>Reviewers</span>
                </div>
                <div class="flex gap-2 flex-wrap flex-1">
                    <template v-for="elem in data.reviewers" :key="elem.collaborator">
                        <AppUserChip
                            :image="elem.profile_picture"
                            :name="elem.collaboratorName"
                        ></AppUserChip>
                    </template>
                </div>
            </div>

            <div>
                <div
                    class="w-[200px] inline-flex items-center gap-x-2 text-slate-600 dark:text-slate-300 mb-1.5"
                >
                    <AppIcon name="fa-list-alt" />
                    <span>Description</span>
                </div>
                <div
                    class="p-4 rounded-lg border dark:border-slate-700 text-gray-500 whitespace-normal dark:text-gray-300 truncate"
                >
                    <span v-html="data.task.description" class="description  prose prose-ul:mt-0 prose-p:m-0.5 prose-li:m-0 prose-a:text-sky-500"></span>
                </div>
            </div>


            <div  v-if="data.attachments.length > 0">
                <div
                    class="w-[200px] inline-flex items-center gap-x-2 text-slate-600 dark:text-slate-300 mb-1.5"
                >
                    <AppIcon name="fa-images" />
                    <span>Attachments</span>
                </div>

                <div class="flex gap-2 flex-wrap">
                    <a
                        v-for="image in data.attachments"
                        :key="image.id"
                        :href="`/storage/tasks/${image.name}`"
                        class="inline-flex items-center px-3 py-1 space-x-2 text-base transition-all border rounded-full hover:bg-gray-100 text-gray-600 border-gray-300 dark:border-slate-700 dark:text-slate-300"
                        target="_blank"
                    >
                        <AppIcon name="fa-images" />
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
