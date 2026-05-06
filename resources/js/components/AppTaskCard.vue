<script setup>
import { computed, inject } from 'vue';
import AppChip from '@/components/AppChip.vue';
const filters = inject('filters');
// import { formatTimeAgo } from '@vueuse/core'

const props = defineProps({
    data: {
        type: undefined,
        required: true,
    },
});


const assignee = computed(()=> {
    return props.data.collaborators.filter(elem => elem.flag == '0')
})

const reviewers = computed(()=> {
    return props.data.collaborators.filter(elem => elem.flag == '1')
} )

const ageCalculator = (date) => {
    const oneDay = 24 * 60 * 60 * 1000;
    const today = new Date();
    const created_date = new Date(date)
    const diffDays = Math.round(Math.abs((today - created_date)/oneDay))
    // console.log("diffDays", diffDays)
    return diffDays
}

const formatNameToInitials = (name) => {
    // console.log("name", name)
    const arr = name.split(' ');
    return `${arr[0].charAt(0)}${arr[1].charAt(0)}`;
};

const statusMapping = {
    0: {color: 'red', value:'Assigned'},
    1: {color: 'violet', value:'In Progress'},
    2: {color: 'yellow', value:'Assigned for Review'},
    3: {color: 'blue', value:'Reviewing'},
    4: {color: 'cyan', value:'Reviewed'},
    5: {color: 'green', value:'Completed'},
    6: {color: 'lime', value:'Closed'},
    7: {color: 'orange', value:'Created'},
    8: {color: 'orange', value:'Staging - Ready to upload'},
    9: {color: 'purple', value:'Staging - Uploaded'},
    10: {color: 'teal', value:'Live - Ready to upload'},
    11: {color: 'green', value:'Live - Uploaded'},
    15: {color: 'green', value:'Dev - Ready to upload',},
    16: {color: 'green', value:'Dev - Uploaded'},
};

const taskStatus = computed(() => {
    return statusMapping[props.data.status]
})
</script>

<template>
 <div class="bg-white dark:bg-slate-900/50 rounded-lg border border-gray-100 dark:border-slate-700 grid pt-5 h-auto relative w-[380px] cursor-pointer hover:scale-105 hover:shadow-sm transition-transform">
    <div class="absolute top-2 right-0">
        <div v-if="data.priority == '2'" class="text-white dark:text-red-100 bg-red-500 pl-3 pr-4 py-1 rounded-l-full flex gap-x-3 items-center">
            <div class="w-2 h-2 bg-red-100 rounded-full outline outline-red-300 animate-pulse"></div>
            <span class="text-sm font-semibold whitespace-nowrap">
                Urgent
            </span>
        </div>
        <div v-if="data.priority == '1'" class="text-white bg-yellow-500 pl-3 pr-4 py-1 rounded-l-full flex gap-x-3 items-center">
            <div class="w-2 h-2 bg-yellow-100 rounded-full outline outline-yellow-300 animate-pulse"></div>
            <span class="text-sm font-semibold whitespace-nowrap">
                High
            </span>
        </div>
    </div>
    <div class="flex items-start flex-col gap-1 px-5 mb-4">
        <div
            class="flex items-center gap-x-2 w-full divide-x text-gray-500 dark:text-slate-300"
            @click.stop
        >
            <a :href="`/tasks/${data.id}/edit`" >
            <span><strong>#{{ data.id }}</strong></span>
            </a>
            <span class="text-sm pl-2 line-clamp-1">{{data.projectName}}</span>
        </div>
        <div class=" w-full">
            <!-- <button class="style-none" data-modal="modal-drawer" data-target="#task_card_modal_slider"
                data-id="{{data.id}}"> -->
                <!-- <h4 class="mb-2 text-lg font-semibold capitalize text-darkblue text-left hover:underline transition-all">
                    {{data.title}}
                </h4> -->
                <h4 class="mb-1 text-lg font-semibold capitalize dark:text-slate-200 text-left">
                    {{data.title}}
                </h4>
            <!-- </button> -->
        </div>
        <app-chip  :color="taskStatus.color" v-if="taskStatus.value != 'Completed'">{{ taskStatus.value }}</app-chip>
    </div>


    <div class="grid grid-cols-2 px-5">
        <div class="flex flex-col">
            <span class="text-gray-500 whitespace-normal dark:text-slate-400 text-sm">
                Assignee
            </span>
            <div class="flex">
                <div class="flex">
                    <div
                        v-for="elem in assignee"
                        :key="elem.collaborator"
                        class="h-8 w-8 bg-gray-500 rounded-full border-2 shadow border-white dark:border-slate-700 overflow-hidden relative z-0 text-center last:mr-0 -mr-2.5 text-white text-xs flex items-center justify-center">
                        <img v-if="elem.profile_picture" :src="`/storage/profiles/${elem.profile_picture}`"
                            class="w-10 h-10 rounded-full object-cover object-center" src="path/to/profile/image.jpg"
                            :alt="elem.profile_picture"
                            >
                        <span v-else>
                          {{ formatNameToInitials(elem.name) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col items-end">
            <span class="text-gray-500 whitespace-normal dark:text-slate-400 text-sm">
                Reviewers
            </span>
            <div class="flex">
                <div class="flex">
                    <div
                        v-for="elem in reviewers"
                        :key="elem.collaborator"
                        class="h-8 w-8 bg-gray-500 rounded-full border-2 shadow border-white dark:border-slate-700 overflow-hidden relative z-0 text-center last:mr-0 -mr-2.5 text-white text-xs flex items-center justify-center">
                        <img v-if="elem.profile_picture" :src="`/storage/profiles/${elem.profile_picture}`"
                            class="w-10 h-10 rounded-full object-cover object-center" src="path/to/profile/image.jpg"
                            :alt="elem.profile_picture"
                            >
                        <span v-else>
                          {{ formatNameToInitials(elem.name) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between gap-2 px-5 dark:border-slate-700 py-3">
        <div class="flex flex-col">
            <span class="text-gray-500 whitespace-normal dark:text-slate-400 text-sm">
                Created At
            </span>
            <span class="text-gray-600 whitespace-normal dark:text-slate-200 font-semibold">
                {{ ageCalculator(data.created_at) }} days ago
            </span>
        </div>
        <div class="flex flex-col text-right">
            <span class="text-gray-500 whitespace-normal dark:text-slate-400 text-sm">
                Deadline
            </span>
            <span class="text-gray-600 whitespace-normal dark:text-slate-200 font-semibold">
                {{ filters.formatDate(data.deadline) }}
            </span>
        </div>
    </div>

</div>
</template>
