<script setup>
import AppChip from '@/components/AppChip.vue';

const props = defineProps({
  data: {
    type: [Object, String, Number] ,
    default: null
  },
  isStatus: {
    type: Boolean,
    default: false
  },
  // isLink: {
  //   type: Boolean,
  //   default: false,
  // },
  // isSender: {
  //   type: Boolean,
  //   default: false,
  // },
  // url: {
  //   type: String,
  //   default: null,
  // },
  isHtml: {
    type: [String, Boolean],
    default: null,
  }

});


const convertStringToLowerCase = (val) => {
  return val.toLowerCase()
}
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


  <template v-if="isStatus">
    <AppChip color="indigo" v-if="data == 'ATTACHED'">{{convertStringToLowerCase(data)}}</AppChip>
    <AppChip color="red" v-else-if="data == 'UN-ATTACHED'" class="lowercase">{{convertStringToLowerCase(data)}}</AppChip>
    <AppChip color="blue" v-else class="lowercase">{{convertStringToLowerCase(data)}}</AppChip>
  </template>
  <template v-else-if="isHtml">
   <span v-html="data" class=" whitespace-nowrap "></span>
  </template>
</template>
