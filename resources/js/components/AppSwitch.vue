<template>
  <label class="relative inline-flex items-center gap-3 cursor-pointer">
    <input
      type="checkbox"
      v-model="currentValue"
      class="sr-only peer"
      :true-value="trueValue"
      :false-value="falseValue"
      :disabled="disabled"
    />

    <span v-if="labelLeft" class="text-sm text-gray-600 dark:text-gray-300 capitalize">{{
      labelLeft
    }}</span>
    <div
      class="w-11 h-6 bg-gray-200 relative peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-slate-200 peer-focus:ring-opacity-60 dark:peer-focus:ring-green-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-500"
      :class="
        disabled &&
        ' after:bg-gray-100 after:border-gray-100 after:peer-checked:border-gray-100  cursor-pointer'
      "
    ></div>
    <span v-if="label" class="text-sm text-gray-600 dark:text-gray-300 capitalize">{{
      label
    }}</span>
  </label>
</template>

<script>
export default {
  name: 'AppSwitch',
  props: {
    modelValue: [Boolean, String, Number],
    // value: {
    //   type: [String, Number],
    //   default: null,
    // },
    label: {
      type: [String, Number],
      default: null
    },
    labelLeft: {
      type: [String, Number],
      default: null
    },
    name: {
      type: String,
      default: null
    },
    disabled: {
      type: Boolean,
      default: false
    },
    trueValue: {
      type: [Boolean, Number, String],
      default: 1
    },
    falseValue: {
      type: [Boolean, Number, String],
      default: 0
    }

  },
  emits: ['update:modelValue'],
  computed: {
    currentValue: {
      get() {
        return this.modelValue
      },
      set(newValue) {
        // return newValue;
        this.$emit('update:modelValue', newValue)
      }
    }
  }
}
</script>
