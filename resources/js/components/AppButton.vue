<template>
    <!-- for link -->
    <Link
      v-if="to"
      :href="to"
      v-bind="$attrs"
      :type="linkType === 'button' ? type : undefined"
      class="py-2.5 text-center justify-center transition-all items-center rounded-md inline-flex cursor-pointer font-medium"
      :class="[
        icon ? 'pl-4 pr-6' : 'px-4',
        buttonOutline,
        loading ? 'opacity-50' : '',
        disabled ? 'opacity-50 cursor-not-allowed' : '',
      ]"
      :disabled="loading ? true : disabled"
    >
      <template v-if="loading">
        <AppIcon
          name="fa-spinner"
          size="16"
          class="animate-spin text-white mr-2"
        />
      </template>
      <template v-else>
        <app-icon v-if="icon" :name="icon" class="pr-2" />
      </template>
  
      <span class="whitespace-pre"><slot /></span>
    </Link>

    <!-- for button -->
    <component
      v-else
      :is="linkType"
      v-bind="$attrs"
      :type="linkType === 'button' ? type : undefined"
      class="py-2.5 text-center justify-center transition-all items-center rounded-md inline-flex cursor-pointer font-medium"
      :class="[
        icon ? 'pl-4 pr-6' : 'px-4',
        buttonOutline,
        loading ? 'opacity-50' : '',
        disabled ? 'opacity-50 cursor-not-allowed' : '',
      ]"
      :disabled="loading ? true : disabled"
    >
      <template v-if="loading">
        <AppIcon
          name="fa-spinner"
          size="16"
          class="animate-spin text-white mr-2"
        />
      </template>
      <template v-else>
        <app-icon v-if="icon" :name="icon" class="pr-2" />
      </template>
  
      <span class="whitespace-pre"><slot /></span>
    </component>
  </template>
  <script>
  import { Link } from "@inertiajs/vue3";
import AppIcon from "./AppIcon.vue"
  export default {
    name: "StButton",
    inheritAttrs: false,
    components: {
        AppIcon,
        Link
    },
    props: {
      type: {
        type: String,
        default: "button",
      },
      to: {
        type: String,
        default: null,
      },
      icon: {
        type: String,
        default: null,
      },
      color: {
        type: String,
        default: "blue",
      },
      text: {
        type: Boolean,
        default: false,
      },
      outline: {
        type: Boolean,
        default: false,
      },
      rounded: {
        type: String,
        default: "rounded",
      },
      disabled: {
        type: Boolean,
        default: false,
      },
      loading: {
        type: Boolean,
        default: false,
      },
    },
    computed: {
      linkType() {
        return this.to ? "router-link" : "button";
      },
      buttonOutline() {
        return this.text
          ? `hover:bg-${this.color}-50 dark:hover:bg-slate-700`
          : this.outline
          ? `border border-${this.color}-500 text-${this.color}-500 hover:bg-${this.color}-500 hover:text-white  dark:text-slate-300`
          : `bg-${this.color}-500 text-white border border-transparent hover:bg-${this.color}-600 shadow hover:shadow-lg shadow-${this.color}-500/50 hover:shadow-${this.color}-500/50`;
      },
    },
  };
  </script>
  