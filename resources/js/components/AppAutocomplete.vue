<template>
  <div ref="autocomplete_wrap" class="w-full" :class="[isModal && 'relative']" :name="name">
    <div ref="autocomplete" class="relative w-full">
      <label v-if="!disableLabel"
        ref="labelElement"
        class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
      >
        {{ placeholder }}
      </label>
      <button
        ref="button"
        type="button"
        :disabled="disabled"
        class="relative z-10 w-full px-3 overflow-hidden text-left peer border appearance-none focus:outline-none focus:ring-0 focus:border-blue-500 dark:focus:border-slate-400 peer placeholder:opacity-0 focus:placeholder:opacity-50  rounded-md text-gray-900  dark:hover:bg-slate-700 bg-white dark:bg-slate-800/50 dark:text-slate-300 min-h-[44px] py-1"
        :class="[
          disabled ? 'cursor-not-allowed' : '',
         errorMessage ? 'border-red-200' : 'border-slate-300 dark:border-slate-700'
        ]"
        @click="open"
      >
        <span
          v-if="inputValue != null && inputValue != ''"
          class="block truncate line-clamp-1 whitespace-nowrap"
        >
          <template v-if="multiple">
            <div class="flex flex-wrap items-center gap-1">
              <template v-for="(item, index) in inputValue" :key="item.id">
                <div
                  v-if="index < 3"
                  class="inline-flex pl-4 pr-2  bg-blue-50 dark:bg-primary-400/50 text-blue-900 dark:text-slate-300 border border-blue-200 dark:border-slate-700 font-medium rounded align-center items-center justify-between py-1"
                >
                  <span class="">{{ item[label] }}</span>
                  <span
                    class="flex items-center justify-center px-1 text-blue-300 rounded hover:text-red-600"
                    @click="select(item)"
                  >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                  stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                  </span>
                </div>
              </template>
              <template v-if="inputValue.length >= 4">
                <span class="dark:text-gray-400">and {{ inputValue.length - 3 }} more</span>
              </template>
            </div>
          </template>
          <template v-else>
            <slot :option="inputValue">
              <template v-if="optionType !== 'object'">
                <span class="dark:text-gray-200">{{ modelValue }}</span>
              </template>
              <template v-else>
                <span class="dark:text-gray-200">{{ modelValue[label] }}</span>
              </template>
            </slot>
          </template>
        </span>
        <template v-else>
          <span v-if="disableLabel">
            {{placeholder}}
          </span>
        </template>
        <!-- next update fix -->
        <!-- <div class="absolute inset-y-0 flex items-center gap-2 right-1">
          <AppIcon
            v-if="inputValue != null && inputValue != '' && !multiple"
            color="light"
            icon="close"
            @click="clear"
            class="text-gray-400 hover:text-gray-500 dark:text-gray-200"
          />
          <AppIcon
            v-else
            icon="expand-up-down-line"
            class="text-gray-400 pointer-events-none dark:text-gray-200"
          />
        </div> -->
      </button>
      <!-- <fieldset
        class="absolute bottom-0 left-0 right-0 pl-2 bg-white border rounded pointer-events-none -top-1"
        :class="[
          errorMessage ? 'border-red-500' : 'border-gray-300',
          colorClasses,
          disabled ? 'bg-slate-50' : '',
        ]"
        aria-hidden="true"
      >
        <legend
          class="block overflow-hidden text-xs whitespace-nowrap"
          :class="[
            isActive ? 'max-w-[100%] visible' : 'max-w-[0.01px] invisible',
          ]"
        >
          <span class="px-2 opacity-0">{{ placeholder }}</span>
        </legend>
      </fieldset> -->
      <!-- <span
        class="absolute left-0 z-10 flex justify-between w-full h-5 text-xs text-left -bottom-5"
        :class="{ 'text-red-500': errorMessage }"
      >
        {{ errorMessage }}
      </span> -->

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
    <Teleport to="body">
      <div
        v-show="isOpen"
        ref="dropdown"
        class="absolute left-0 z-[999] w-full py-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-500 rounded top-full shadow-md dark:shadow-gray-600 dark:text-slate-300"
      >
        <div class="px-3">
          <input
            ref="search"
            v-model="searchValue"
            type="text"
            class="w-full h-10 px-3 py-3 mb-3 bg-white border border-gray-300 rounded dark:bg-gray-800 dark:border-gray-500 focus:ring-0 focus:outline-none focus:border-blue-500 dark:focus:border-gray-400 dark:text-gray-300"
            :class="borderClasses"
            @keydown.esc="closeSearch"
            @keydown.down="highlightNext"
            @keydown.up="highlightPrev"
            @keydown.enter.prevent="selectHighlighted"
            @keydown.tab.prevent
          />
        </div>
        <ul
          v-if="filteredOptions.length > 0"
          ref="optionsList"
          class="max-h-[240px] overflow-y-auto"
        >
        <li v-for="(option, i) in filteredOptions" :key="option">
            <div 
              v-if="option[groupLabel]" 
              class="pt-3 pb-1 relative cursor-pointer group"   
             
              @click="select(option, i)"
              >
              <h4
                class="px-3 pb-2 py-2 font-bold text-gray-400 border-b border-dashed dark:text-gray-300 border-slate-200 cursor-pointer group-hover:bg-primary-50"
                :class="[
                i === highlightedIndex ? `bg-${color}-50 dark:bg-gray-700` : '',
                hoverClasses
              ]" 
              >
                {{ option[groupLabel] }}
              </h4>
                <template v-if="checkSelected(option)">
                  <div
                    class="absolute top-1 left-0 right-0 flex items-center justify-end h-full px-2 transition-all z-100 group"
                  >
                    <span
                      class=" transition-opacity text-green-500 px-2 py-0.5 text-[12px] border rounded-full bg-green-50 border-green-200"
                      :class="multiple ? 'group-hover:hidden' : ''"
                      >Selected</span
                    >
                    <span
                      v-if="multiple"
                      class="hidden group-hover:block transition-opacity text-red-500 px-2 py-0.5 text-[12px] border rounded-full bg-red-50 border-red-200"
                      >Remove</span
                    >
                  </div>
                </template>
            </div>

            <div
              v-else
              class="relative grid items-center px-3 py-2 transition cursor-pointer"
              :class="[
                i === highlightedIndex ? `bg-${color}-50 dark:bg-gray-700` : '',
                hoverClasses
              ]"
              @click="select(option, i)"
              :data-cy="`${name}SelectItem`"
            >
              <slot :option="option">
                <template v-if="optionType !== 'object'">
                  <span class="dark:text-gray-200">{{ option }}</span>
                </template>
                <template v-else>
                  <span class="dark:text-gray-200">{{ option[label] }}</span>
                </template>
              </slot>

                <template v-if="checkSelected(option)">
                  <div
                    class="absolute top-0 left-0 right-0 flex items-center justify-end h-full px-2 transition-all z-100 group"
                  >
                    <span
                      class="transition-opacity text-green-500 px-2 py-0.5 text-[12px] border rounded-full bg-green-50 border-green-200"
                     :class="multiple ? 'group-hover:hidden' : ''"
                      >Selected</span
                    >
                    <span
                      v-if="multiple"
                      class="hidden group-hover:block transition-opacity text-red-500 px-2 py-0.5 text-[12px] border rounded-full bg-red-50 border-red-200"
                      >Remove</span
                    >
                  </div>
                </template>
            </div>
          </li>
        </ul>
        <div v-if="filteredOptions.length === 0" class="px-3 dark:text-slate-300">
          No results found for {{ searchValue }}
        </div>
      </div>
    </Teleport>
  </div>
</template>
<script>
import { toRef, watch, ref, computed, onMounted, nextTick } from 'vue'
// import { useField } from 'vee-validate'
import { onClickOutside } from '@vueuse/core'
import { createPopper } from '@popperjs/core'
import flatMapDeep from 'lodash/flatMapDeep'
import AppIcon from './AppIcon.vue'

const hoverColors = {
  primary: 'hover:bg-blue-100 dark:hover:bg-gray-600',
  secondary: 'hover:bg-secondary-100',
  blue: 'hover:bg-blue-100',
  red: 'hover:bg-red-100',
  gray: 'hover:bg-gray-100',
  slate: 'hover:bg-slate-100',
  green: 'hover:bg-green-100',
  purple: 'hover:bg-purple-100',
  teal: 'hover:bg-teal-100',
  pink: 'hover:bg-pink-100',
  yellow: 'hover:bg-indigo-100',
  indigo: 'hover:bg-blue-100',
  cyan: 'hover:bg-cyan-100'
}
const borderColors = {
  primary: 'border-blue-100',
  secondary: 'border-secondary-100',
  blue: 'border-blue-100',
  red: 'border-red-100',
  gray: 'border-gray-100',
  slate: 'border-slate-100',
  green: 'border-green-100',
  purple: 'border-purple-100',
  teal: 'border-teal-100',
  pink: 'border-pink-100',
  yellow: 'border-indigo-100',
  indigo: 'border-blue-100',
  cyan: 'border-cyan-100'
}
const colors = {
  primary: 'peer-focus:border-blue-500 hover:bg-blue-100',
  secondary: 'peer-focus:border-secondary-500 hover:bg-secondary-100',
  blue: 'peer-focus:border-blue-500 hover:bg-blue-100',
  red: 'peer-focus:border-red-500 hover:bg-red-100',
  gray: 'peer-focus:border-gray-500 hover:bg-gray-100',
  slate: 'peer-focus:border-slate-500 hover:bg-slate-100',
  green: 'peer-focus:border-green-500 hover:bg-green-100',
  purple: 'peer-focus:border-purple-500 hover:bg-purple-100',
  teal: 'peer-focus:border-teal-500 hover:bg-teal-100',
  pink: 'peer-focus:border-pink-500 hover:bg-pink-100',
  yellow: 'peer-focus:border-indigo-500 hover:bg-indigo-100',
  indigo: 'peer-focus:border-blue-500 hover:bg-blue-100',
  cyan: 'peer-focus:border-cyan-500 hover:bg-cyan-100'
}
export default {
  props: {
    modelValue: {
      type: null
    },
    options: {
      type: [Array, Object],
      default: null
    },
    label: {
      type: String,
      default: null
    },
    disabled: {
      type: Boolean,
      default: false
    },
    name: {
      type: String,
      default: 'default',
      validator: (prop) => {
        if (prop === 'default') {
          console.error('😳 The input prop name is required.')
        }
        return true
      }
    },
    trackBy: {
      type: String,
      default: null
    },
    trackValue: {
      type: String,
      default: null
    },
    rules: {
      type: [String, Object],
      default: null
    },
    placeholder: {
      type: String,
      default: ''
    },
    color: {
      type: String,
      default: 'primary',
      validator: (prop) => {
        const valid = [
          'blue',
          'red',
          'gray',
          'slate',
          'green',
          'teal',
          'purple',
          'cyan',
          'pink',
          'indigo',
          'yellow',
          'light',
          'primary',
          'secondary'
        ].includes(prop.toLowerCase())
        if (!valid) {
          console.error('😳 The input prop must include valid color name.')
        }
        return valid
      }
    },
    multiple: {
      type: Boolean,
      default: false
    },
    group: {
      type: Boolean,
      default: false
    },
    groupValues: {
      type: String,
      default: null
    },
  groupLabel: {
    type: String,
    default: null
  },
  errorMessage: {
    type: String,
    default: null
  },
  disableLabel: {
    type: Boolean,
    default: false
  }
  },
  emits: ['update:modelValue', 'change', 'focus'],
  setup(props, { emit }) {
    
    const autocomplete_wrap = ref(null)
    const isModal = ref(false)
    const isActive = ref(false)
    const labelElement = ref(0)
    const labelWidth = ref(0)
    const autocomplete = ref(false)
    const isOpen = ref(false)
    const button = ref(null)
    const search = ref(null)
    const dropdown = ref(null)
    const optionsList = ref(null)
    const searchValue = ref('')
    const highlightedIndex = ref(0)
    const isSelected = ref(0)

    const rules = toRef(props, 'rules')
    const name = toRef(props, 'name')
    const label = toRef(props, 'as')

    const inputValue = computed(() => props.modelValue)

    // const {
    //   value: inputValue,
    //   handleChange,
    //   handleBlur,
    //   validate,
    //   errorMessage
    // } = useField(name, rules, {
    //   initialValue: props.modelValue,
    //   label
    // })
    // console.log('props', props.options)

    onClickOutside(dropdown, () => {
      closeSearch()
      searchValue.value = ''
    })
    const open = async () => {
      emit('focus',)
      if (isOpen.value) {
        return
      }
      if (autocomplete_wrap.value.offsetParent.querySelector('.modal-body') === null) {
        isModal.value = true
      } else {
        isModal.value = false
      }
      isOpen.value = true
      await nextTick()
      setUpPoper()
      search.value.focus()
      highlightedIndex.value = 0
      // scrollToHighlighted();
    }
    const setUpPoper = () => {
      const sameWidth = {
        name: 'sameWidth',
        enabled: true,
        phase: 'beforeWrite',
        requires: ['computeStyles'],
        fn: ({ state }) => {
          state.styles.popper.width = `${state.rects.reference.width}px`
        },
        effect: ({ state }) => {
          state.elements.popper.style.width = `${state.elements.reference.offsetWidth}px`
        }
      }
      createPopper(button.value, dropdown.value, {
        // placement: "top",
        modifiers: [
          sameWidth,
          {
            name: 'flip',
            options: {
              fallbackPlacements: ['top', 'bottom']
            }
          }
        ]
      })
    }
    const closeSearch = () => {
      if (!isOpen.value) {
        return
      }
      if (!inputValue.value !== null && !props.modelValue !== null) {
        // validate()
      }
      isOpen.value = false
      button.value.focus()
    }
    const select = (option) => {
      let data = option;
      if(data[props.groupLabel]){
        data = {
          ...data,
          name: data[props.groupLabel]
        }
      }

      if (props.multiple) {
        selectMultipel(data)
        return
      } else {
        if (props.trackValue) {
          emit('update:modelValue', data[props.trackValue])
        } else {
          emit('update:modelValue', data)
        }
        emit('change', data)
        isActive.value = true
      }

      searchValue.value = ''
      highlightedIndex.value = 0
      closeSearch()
    }

    const selectMultipel = (option) => {
      let selectedArray = props.modelValue !== undefined ? props.modelValue : []

      if (selectedArray.some((data) => data[props.trackBy] === option[props.trackBy]) === true) {
        let arr = selectedArray.filter((data) => data[props.trackBy] !== option[props.trackBy])
        selectedArray = arr
        // console.log('oddd', props.trackBy)
      } else {
        selectedArray.push(option)
      }
      isActive.value = selectedArray.length > 0
      emit('update:modelValue', selectedArray)
    }

    const selectHighlighted = () => {
      select(filteredOptions.value[highlightedIndex.value])
    }
    const scrollToHighlighted = () => {
      optionsList.value.children[highlightedIndex.value].scrollIntoView({
        block: 'nearest'
      })
    }
    const filteredOptions = computed(() => {
      const getOptions = (item) => {
        const option = { ...item }
        delete option[props.groupValues]
        if (!item[props.groupValues] || !item[props.groupValues].length) {
          return option // return copied
        }
        // console.log("ssss", [option, flatMapDeep(item[props.groupValues], getOptions)])
        return [option, flatMapDeep(item[props.groupValues], getOptions)]
      }

      let options = flatMapDeep(props.options, getOptions)
      // console.log("options", options)
      return options.filter((option) => {
        if (option[props.groupLabel]) {
          return option[props.groupLabel]
            .toString()
            .toLowerCase()
            .includes(searchValue.value.toLowerCase())
        } else {
          let item = ''
          if (typeof option !== 'object') {
            item = option
          } else {
            item = option[props.label]
          }

          if (item) {
            return item.toString().toLowerCase().includes(searchValue.value.toLowerCase())
          }
        }
      })
    })

    const highlight = (index) => {
      highlightedIndex.value = index

      if (highlightedIndex.value > filteredOptions.value.length - 1) {
        highlightedIndex.value = 0
      }

      if (highlightedIndex.value < 0) {
        highlightedIndex.value = filteredOptions.value.length - 1
      }
      scrollToHighlighted()
    }

    const highlightNext = () => {
      highlight(highlightedIndex.value + 1)
    }
    const highlightPrev = () => {
      highlight(highlightedIndex.value - 1)
    }

    // you need to emit `update:modelValue`
    watch(inputValue, (newVal) => {
      // if (inputValue.value && props.modelValue) {
      //   isActive.value = true
      // } else {
      //   isActive.value = false
      // }
      if (newVal === props.modelValue) {
        return
      }
      // sync the model value with vee-validate model
      emit('update:modelValue', newVal)
    })

    // and you need to listen for `modelValue` prop changes
    watch(
      () => props.modelValue,
      (val) => {
        if (val !== inputValue.value) {
          inputValue.value = val
        }
      }
    )

    // const focusBlur = () => {
    //   validate()
    // }

    onMounted(() => {
      if (inputValue.value && props.modelValue) {
        if (Array.isArray(props.modelValue)) {
          if (props.modelValue.length > 0) {
            isActive.value = true
          }
        } else {
          isActive.value = true
        }
      }
    })

    const colorClasses = computed(() => {
      return colors[props.color]
    })

    const hoverClasses = computed(() => {
      return hoverColors[props.color]
    })
    const borderClasses = computed(() => {
      return borderColors[props.color]
    })
    const optionType = computed(() => {
      let ot = 'string'
      if (props.options.length > 0 && typeof props.options[0] !== 'string') {
        ot = 'object'
      }
      return ot
    })
    const clear = () => {
      inputValue.value = null
      isActive.value = false
    }

    const checkSelected = (elem) => {
      if (inputValue.value === undefined || inputValue.length == 0 || inputValue.value === null)
        return false
      if(!props.multiple){
        return inputValue.value[props.trackBy] == elem[props.trackBy]
      }
      return inputValue.value.some((data) => data[props.trackBy] === elem[props.trackBy])
    }
    return {
      clear,
      optionType,
      autocomplete_wrap,
      isModal,
      isOpen,
      button,
      search,
      searchValue,
      isSelected,
      open,
      closeSearch,
      select,
      filteredOptions,
      highlightedIndex,
      highlightNext,
      highlightPrev,
      optionsList,
      selectHighlighted,
      autocomplete,
      dropdown,
      inputValue,
      // handleChange,
      // handleBlur,
      // focusBlur,
      // errorMessage,
      labelWidth,
      labelElement,
      colorClasses,
      hoverClasses,
      borderClasses,
      isActive,
      checkSelected
    }
  }
}
</script>
