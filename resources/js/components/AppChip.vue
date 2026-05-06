<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  color: string
  outline?: boolean
}>()

const filled: any = {
  blue: 'bg-blue-300 text-blue-800 dark:bg-blue-200 dark:bg-blue-300/20 dark:text-blue-200',
  green: 'bg-emerald-300 text-green-800 dark:bg-green-200 dark:bg-green-300/20 dark:text-green-200',
  red: 'bg-red-300 text-red-800 dark:bg-red-200 dark:bg-red-300/20 dark:text-red-200',
  indigo: 'bg-indigo-300 text-indigo-800 dark:bg-indigo-300/20 dark:text-indigo-200',
  purple: 'bg-purple-300 text-purple-800 dark:bg-purple-300/20 dark:text-purple-200',
  lime: 'bg-lime-300 text-lime-800 dark:bg-lime-300/20 dark:text-lime-200',
  yellow:
    'bg-yellow-300 text-yellow-800 dark:bg-yellow-200 dark:bg-yellow-300/20 dark:text-yellow-200',
  orange: 'bg-orange-300 text-orange-800 dark:bg-orange-300/20 dark:text-orange-200',
  cyan: 'bg-cyan-300 text-cyan-800 dark:bg-cyan-300/20 dark:text-cyan-200',
  amber: 'bg-amber-300 text-amber-800 dark:bg-amber-300/20 dark:text-amber-200',
  sky: 'bg-sky-300 text-sky-800 dark:bg-sky-300/20 dark:text-sky-200',
  rose: 'bg-rose-300 text-rose-800 dark:bg-rose-300/20 dark:text-rose-200',
  teal: 'bg-teal-300 text-teal-800 dark:bg-teal-300/20 dark:text-teal-200',
  violet: 'bg-violet-300 text-violet-800 dark:bg-violet-200 dark:bg-violet-300/20 dark:text-violet-200',
}
const outlined: any = {
  blue: 'border border-blue-500 text-blue-500',
  green: 'border border-emerald-500 text-emerald-500',
  red: 'border border-red-500 text-red-500',
  indigo: 'border border-indigo-500 text-indigo-500',
  purple: 'border border-purple-500 text-purple-500',
  yellow: 'border border-yellow-500 text-yellow-500',
  lime: 'border border-lime-500 text-lime-500',
  orange: 'border border-orange-500 dark:border-orange-800 text-orange-500 dark:text-orange-600 ',
  cyan: 'border border-cyan-500 text-cyan-500',
  amber: 'border border-amber-500 text-amber-500',
  sky: 'border border-sky-500 text-sky-500',
  rose: 'border border-rose-500 text-rose-500',
  teal: 'border border-teal-500 text-teal-500 ',
  violet: 'border border-violet-500 text-violet-500',
}

const themes = computed(() => {
  return props.outline ? outlined[props.color] : filled[props.color]
})
</script>

<template>
  <span
    class="px-3 py-1 rounded-full bg-opacity-50 whitespace-nowrap font-semibold leading-4 capitalize"
    :class="[themes]"
  >
    <slot />
  </span>
</template>
