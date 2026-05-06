<script setup>
// import { getCurrentInstance, onMounted, ref } from 'vue';
import AppIcon from '@/components/AppIcon.vue';
import AppChip from '@/components/AppChip.vue';
import { inject } from 'vue';

const props = defineProps({
  data: {
    type: [Object, String, Number],
    default: null
  },
  isStatus: {
    type: Boolean,
    default: false
  },
  // isLink: {
  //   type: Boolean,
  //   default: false
  // },
  // url: {
  //   type: String,
  //   default: null
  // },
  isPriority: {
    type: Boolean,
    default: false
  },
  dateFilter: {
    // type: Function
  },
  isCollaborators: {
    type: Boolean,
    default: false
  },
  openEditModal: {
    type: Function
  }
});

const base_url = route().t.url;
// console.log('data', props.data)

const formatName = (name) => {
  // console.log("name", name)
  const arr = name.split(' ');
  return `${arr[0]}.${arr[1].charAt(0)}`;
};
</script>

<template>
  <!-- <template v-if="isLink">
    <a
      type="button"
      :href="`${url}`"
      class="text-primary-500 font-semibold hover:underline"
      target="_blank"
    >
      {{ data }}
    </a>
  </template> -->

  <template v-if="isPriority">
    <AppChip v-if="data.priority == 2" color="red">Urgent</AppChip>
    <AppChip v-else-if="data.priority == 1" color="yellow">High</AppChip>
    <AppChip v-else color="green">Normal</AppChip>
    <AppChip v-if="data.createdBy" color="sky">
      {{ formatName(data.createdBy) }}
    </AppChip>
  </template>
  <!-- <span v-if="data.created_at" class="ml-5 whitespace-nowrap text-gray-500 dark:text-gray-400">
    {{ dateFilter.formatDate(data.created_at) }}
  </span> -->

  <template v-else-if="isStatus">
    <AppChip v-if="data == '0'" color="red">{{ data }}</AppChip>
    <AppChip v-else-if="data == '1'" color="violet">Assigned</AppChip>
    <AppChip v-else-if="data == '2'" color="yellow">In progress</AppChip>
    <AppChip v-else-if="data == '3'" color="blue">Assigned for Review</AppChip>
    <AppChip v-else-if="data == '4'" color="cyan">Reviewing</AppChip>
    <AppChip v-else-if="data == '5'" color="green">Reviewed</AppChip>
    <AppChip v-else-if="data == '6'" color="lime">Completed</AppChip>
    <AppChip v-else-if="data == '7'" color="orange">Closed</AppChip>
    <AppChip v-else-if="data == '8'" color="orange">Created</AppChip>
    <AppChip v-else-if="data == '9'" color="purple">Staging - Ready to Upload</AppChip>
    <AppChip v-else-if="data == '10'" color="teal">Staging - Uploaded</AppChip>
    <AppChip v-else-if="data == '11'" color="green">Live - Ready to Upload</AppChip>
    <AppChip v-else-if="data == '12'" color="sky">Live - Uploaded</AppChip>
    <AppChip v-else-if="data == '15'" color="sky">Dev - Ready to upload</AppChip>
    <AppChip v-else-if="data == '16'" color="sky">Dev - uploaded</AppChip>
    <AppChip v-else color="sky">{{ data }}</AppChip>
  </template>

</template>
