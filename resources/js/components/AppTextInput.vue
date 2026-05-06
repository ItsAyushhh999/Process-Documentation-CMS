<script setup>
import { onMounted, ref } from 'vue'

defineProps({
    modelValue: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'text',
    },
    label: {
        type: String,
        default: null,
    },
    errorMessage: {
        type: String,
        default: null,
    },
    required: {
        type: Boolean,
        default: false
    },
    textArea: {
        type: Boolean,
        default: false,
    },
    idDummy: {
        type: Boolean,
        default: false,
    }
})

defineEmits(['update:modelValue', 'onFocus'])

const input = ref(null)

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus()
    }
})


// defineExpose({ focus: () => input.value.focus() })
</script>

<template>
    <div class="relative">
        <div class="flex items-end justify-between mb-1"  v-if="label">
            <label :for="label" class="block text-sm font-medium text-gray-700 capitalize dark:text-slate-400">
                <span >{{ label }}<span v-if="required" class="text-red-500">*</span></span>
            </label>
        </div>
        <div  v-if="idDummy"   ref="input" class="flex items-center rounded-md px-2.5 h-[44px] w-full text-gray-900 dark:text-slate-200 appearance-none border  bg-white dark:bg-slate-800 dark:bg-slate-800/50 ">
            <span>{{ modelValue }}</span>
        </div>
        <textarea 
        v-else-if="textArea" 
        class="block transition rounded-md px-2.5 min-h-32 w-full text-gray-900 dark:text-slate-200 appearance-none focus:outline-none focus:ring-0 peer focus:border-slate-400 bg-white dark:bg-slate-800 dark:bg-slate-800/50"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            ref="input"
            autocomplete="off"
            v-bind="$attrs"
            :type="type"
            :class="[errorMessage ? 'border-red-200' : 'border-slate-300 dark:border-slate-700']"
            :aria-label="label"
        />
        <input
            v-else
            class="block transition rounded-md px-2.5 h-[44px] w-full text-gray-900 dark:text-slate-200 appearance-none focus:outline-none focus:ring-0 peer focus:border-slate-400 bg-white dark:bg-slate-800 dark:bg-slate-800/50"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            ref="input"
            autocomplete="off"
            v-bind="$attrs"
            :type="type"
            :class="[errorMessage ? 'border-red-200' : 'border-slate-300 dark:border-slate-700']"
            :aria-label="label"
        >
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
