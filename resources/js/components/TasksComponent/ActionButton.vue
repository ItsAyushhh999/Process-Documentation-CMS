<script setup>
// import { getCurrentInstance, onMounted, ref } from 'vue';
import AppIcon from '@/components/AppIcon.vue';
import AppChip from '@/components/AppChip.vue';
import { inject } from 'vue';
import AppButton from '@/components/AppButton.vue';




const props = defineProps({
  data: {
    type: [Object, String, Number],
    default: null
  },
  isSource: {
    type: Boolean,
    default: false
  },
  isTarget: {
    type: Boolean,
    default: false
  },
  onHandleMerge: {
    type: Function,
    default: () => Void
  }
});



// const base_url = route().t.url;
// // console.log('data', props.data)

// const formatName = (name) => {
//   // console.log("name", name)
//   const arr = name.split(' ')
//   return `${arr[0]}.${arr[1].charAt(0)}`
// }
</script>

<template>
  <template v-if="isSource">
    <AppChip color="sky">{{ data }}</AppChip>
  </template>
  <template v-else-if="isTarget">
    <AppChip color="green">{{ data }}</AppChip>
  </template>
  <AppButton v-else type="button" @click="onHandleMerge(data)" outline>Merge</AppButton>
</template>
