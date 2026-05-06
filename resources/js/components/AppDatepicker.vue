<script setup>
import { ref, watch,nextTick, defineEmits, inject } from 'vue';
import { onClickOutside } from '@vueuse/core';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { useFloating, offset, flip, autoUpdate } from '@floating-ui/vue';
import AppIcon from './AppIcon.vue';
// import { Field, useField } from 'vee-validate'

defineOptions({
  name: 'AppDatepicker',
  inheritAttrs: false
})


const filters = inject('filters');

const props = defineProps({
  label: {
    type: String,
    default: null
  },
  range: {
    type: Boolean,
    default: false
  },
  format: {
    type: String,
    default:'dd/MM/yyyy hh:mm aa'
  },
  clearable: {
    type: Boolean,
    default: true
  },
  modelValue: {
    type: null,
  },
  timePicker: {
    type: Boolean,
    default: false
  },
  isTaskDetailForm: {
    type: Boolean,
    default: false,
  }
})

// const inputValue = ref()
// const name = toRef(props, 'name')
const emit = defineEmits(['update:modelValue'])
const inputPdding = ref('12px 16px')
const iconTransform = ref('-50%')
const triggerRef = ref(null);
const popoverRef = ref(null);
const showPopover = ref(false);

const inputValue = ref(props.modelValue);
const previousValue = ref(props.modelValue);


const { x, y, strategy } = useFloating(triggerRef, popoverRef, {
  placement: 'bottom',
  middleware: [offset(4),  flip({ fallbackPlacements: ['bottom-end', 'top-start', 'top-end'] }),],
  whileElementsMounted: autoUpdate,
})

function formatDate(timestamp) {
  const date = new Date(timestamp);
  const mm = String(date.getMonth() + 1).padStart(2, '0');
  const dd = String(date.getDate()).padStart(2, '0');
  const yyyy = date.getFullYear();
  return `${mm}/${dd}/${yyyy}`;
}



const togglePopover = () => {
  nextTick(() => {
    showPopover.value = !showPopover.value;
  });
};

onClickOutside(
  popoverRef,
  (event) => {
    if (triggerRef.value && triggerRef.value.contains(event.target)) {
      return; 
    }
    showPopover.value = false;
  },
  { ignore: [triggerRef] } 
);

const updateModelValue = (val) => {
  console.log('Updating model value:', val);
  if (props.isTaskDetailForm) {
    emit('update:modelValue', filters.formatDateForDatePicker(val));
  } else {
    emit('update:modelValue', val);
  }
};

watch(inputValue, (newValue) => {
  if (newValue !== previousValue) {
    updateModelValue(newValue);
  } else {
    updateModelValue(previousValue);
  }
},{ immediate: true });

</script>
<template>

  <div v-if="range" class="inline-block">
    <!-- Trigger Button -->
    <div
      ref="triggerRef"
      @click.prevent="togglePopover"
      class="py-2 border rounded-md border-slate-300 text-gray-900 flex gap-3 px-4 h-[44px] items-center shadow min-w-[240px] bg-white"
    >
      <AppIcon name="fa-calendar" />
      <p class="w-full">{{ formatDate(inputValue[0]) }} - {{formatDate( inputValue[1]) }}</p>
    </div>

    <!-- Popover -->
    <div
      v-show="showPopover"
      ref="popoverRef"
      :style="{ position: strategy, top: `${y ?? 0}px`, left: `${x ?? 0}px` }"
      class="p-6 bg-white border rounded shadow-xl flex flex-col z-[999]"
    >
      <!-- <h4 class="text-lg font-bold pb-4 text-center">Select Dates</h4> -->
      <div class="flex gap-12 p-2">
        <!-- Start Date -->
        <div>
          <!-- <label class="block text-gray-700 mb-1">From Date</label> -->
          <VueDatePicker
            v-model="inputValue[0]"
            :enable-time-picker="timePicker"
            :format="format"
            :auto-apply="true"
            inline
            :is-24="false"
            auto-apply
            auto-position
            required
            modal-type="dd/MM/yyyy hh:mm aa"
            :dark="false"
            :class="range ? 'range-picker' : 'single-picker'"
          />
        </div>

        <!-- End Date -->
        <div>
          <!-- <label class="block text-gray-700 mb-1"><span>End Date</span></label> -->
          <VueDatePicker
            v-model="inputValue[1]"
            :format="format"
            :auto-apply="true"
            inline
           :enable-time-picker="timePicker"
            :is-24="false"
            auto-apply
            modal-type="dd/MM/yyyy hh:mm aa"
            auto-position
            required
            :dark="false"
            :class="range ? 'range-picker' : 'single-picker'"
          />
        </div>
      </div>
    </div>
  </div>
    <div v-else class="">
    <label
        class="block text-sm font-medium text-gray-700 flex items-center capitalize mb-1 dark:text-slate-400"
        >{{ label }}</label
      >
    <div
      class="border border-gray-300 dark:border-gray-500 rounded-md h-[44px] relative  overflow-hidden"
      :class="range ? 'min-w-[300px]' : 'min-w-[240px]'"
    >
      <VueDatePicker
        v-model="inputValue"
        :enable-time-picker="timePicker"
        :is-24="false"
        auto-apply
        auto-position
        allow-prevent-default
        :teleport="true"
        required
        :dark="false"
        :range="range"
        :format="format"
        class="app_datepicker"
        :clearable="clearable"
        modal-type='dd/MM/yyyy hh:mm aa'
        :preview-format="format"
        :class="range ? 'range-picker' : 'single-picker'"
        />
    </div>
  </div>
</template>

<style>
.app_datepicker .dp__input {
  @apply border-none transition-all bg-white dark:bg-gray-800 dark:text-gray-200;
  --dp-font-size: 14px;
  --dp-input-padding: v-bind(inputPdding);
}
.app_datepicker .dp__input_icon {
  @apply transition-all;
  transform: translateY(v-bind(iconTransform));
}
.dp__range_end,
.dp__range_start,
.dp__active_date {
  @apply bg-blue-400 text-blue-50 dark:bg-blue-800 dark:text-blue-50;
}
.dp__today {
  @apply border-blue-300 dark:border-slate-300;
}
.dp__range_between {
  @apply bg-blue-50/70 border-blue-50/70 dark:bg-gray-700 dark:border-gray-700;
}
.dp__arrow_top,
.dp__theme_light {
  @apply dark:bg-gray-800;
}
.dp__btn,
.dp--time-overlay-btn,
.dp--time-invalid,
.dp__calendar_header_item,
.dp__cell_inner {
  @apply dark:text-gray-300;
}
.dp__cell_offset {
  @apply dark:text-gray-500;
}
.dp__calendar_header_separator {
  height: 0;
}
.range-picker .dp__menu {
  border: none !important;
  outline: none !important;
}
.range-picker .dp__menu_inner {
  padding: 0;
}

.single-picker .dp__menu {
  @apply border-gray-200 dark:border-gray-500 shadow-md dark:shadow-gray-600;
}


</style>
