<script setup>
import AppChip from '@/components/AppChip.vue';

const props = defineProps({
  data: {
    type: [Object, String, Number],
    default: null
  },
  isStatus: {
    type: Boolean,
    default: false
  },
  isLink: {
    type: Boolean,
    default: false
  },
  url: {
    type: String,
    default: null
  },
  url: {
    type: String,
    default: null
  },
  isTitle: {
    type: Boolean,
    default: false,
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


const formatName = (name) => {
  // console.log("name", name)
  const arr = name.split(' ')
  return `${arr[0]}.${arr[1].charAt(0)}`
}
</script>

<template>
  <template v-if="isLink">
    <a type="button" :href="`${url}`" class="text-primary-500 font-semibold hover:underline" target="_blank">
      {{ data }}
    </a>
    <!-- <button class="text-primary-500 font-semibold hover:underline"  type="button" @click="openEditModal(data)">
      {{ data.id }}
    </button> -->
  </template>

  <template v-else-if="isTitle">
    <div class="flex flex-col  gap-4">
      <span class="font-semibold whitespace-pre-wrap">
        {{ data.title }}
      </span>
      <div class="flex items-center gap-x-1">
        <AppChip v-if="data.priority == 2" color="red">Urgent</AppChip>
        <AppChip v-else-if="data.priority == 1" color="yellow">High</AppChip>
        <AppChip v-else color="green">Normal</AppChip>
        <AppChip v-if="data.createdBy" color="sky">
          {{ formatName(data.createdBy) }}
        </AppChip>
        <span v-if="data.created_at" class="ml-5 whitespace-nowrap text-gray-500 dark:text-gray-400">
          {{ dateFilter.formatDate(data.created_at) }}
        </span>
      </div>
    </div>
  </template>

  <template v-else-if="isStatus">
    <AppChip v-if="data.value == '0'" color="red">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '1'" color="violet">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '2'" color="yellow">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '3'" color="blue">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '4'" color="cyan">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '5'" color="green">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '6'" color="lime">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '7'" color="orange">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '8'" color="orange">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '9'" color="amber">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '10'" color="teal">{{ data?.name }}</AppChip>
    <AppChip v-else-if="data.value == '11'" color="green">{{ data?.name }}</AppChip>
    <!-- <AppChip v-else-if="data.value == '12'" color="green">Live uploadedss {{ data.value }}</AppChip> -->
    <AppChip v-else color="sky">{{ data.name ?  data.name :  data.value }}</AppChip>
  </template>
  <template v-else-if="isCollaborators">
    <div class="p-1" v-for="elem in data">
      <AppChip color="sky" class=" whitespace-nowrap">
        {{ formatName(typeof elem.collaborator == 'number' ? elem.name : elem.collaborator) }}
      </AppChip>
    </div>
  </template>


</template>
