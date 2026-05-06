<script setup>
import { onMounted, ref,watch } from 'vue';

defineProps({
  modelValue: {
    type: [String, Object],
    default: null
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    default: null
  },
  errorMessage: {
    type: String,
    default: null
  },
  required: {
    type: Boolean,
    default: false
  },
  textArea: {
    type: Boolean,
    default: false
  },
  idDummy: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:modelValue', 'onFocus']);

const input = ref(null);

// onMounted(() => {
//     if (input.value.hasAttribute('autofocus')) {
//         input.value.focus()
//     }
// })

// defineExpose({ focus: () => input.value.focus() })
watch(input, (val) => {
  // console.log('input', val)
  emit('update:modelValue', val)
})

</script>

<template>
  <div class="relative">
    <div class="flex items-end justify-between mb-1" v-if="label">
      <label
        :for="label"
        class="block text-sm font-medium text-gray-700 capitalize dark:text-slate-400"
      >
        <span>{{ label }}<span v-if="required" class="text-red-500">*</span></span>
      </label>
    </div>
    <!-- @select="$emit('update:modelValue', $event.target)" -->
    <select
       v-model="input"
      class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-500 text-primary-900 dark:text-gray-300 rounded-md outline-none focus:ring-0 hover:bg-gray-100 dark:hover:bg-gray-700 focus:border-primary-500 block w-full py-3 px-3 h-[44px]"
      name="priority"
    >
      <slot name="options" />
    </select>
    <transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="-translate-y-2 opacity-0"
      enter-to-class="translate-y-0 opacity-100"
      leave-active-class="duration-100 ease-in"
      leave-from-class="translate-y-0 opacity-100"
      leave-to-class="translate-y-1 opacity-0 "
    >
      <div v-show="errorMessage">
        <p class="text-sm text-red-600 text-left">
          {{ errorMessage }}
        </p>
      </div>
    </transition>
  </div>
</template>
