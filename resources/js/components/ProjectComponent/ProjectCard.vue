<script setup>
import AppButton from '@/components/AppButton.vue';
import { Link } from '@inertiajs/vue3';
const base_url = route().t.url;
const props = defineProps({
  data: {
    type: Object,
    default: null
  },
  is_super_admin: {
    type: Boolean,
    default: false
  },
  isSubProject: {
    type: Boolean,
    default: false
  },
  documentId: {
    type: [Number, String],
    default: 1
  }
});

const emit = defineEmits(['openEditModal', 'openAddSubProject']);

const openEditModal = (data) => {
  emit('openEditModal', data);
};

const openAddSubProject = (data) => {
  emit('openAddSubProject', data);
};
</script>

<template>
    <div
      class="border p-4 bg-white rounded-lg flex flex-col dark:bg-slate-800 dark:border-slate-700"
    >
      <div class="flex flex-col border-b pb-3 mb-2">
        <div
          class="grid pb-8 gap-x-5 xl:grid-flow-col grid-flow-row items-center xl:gap-y-0 gap-y-2"
        >
          <div class="xl:col-span-1 w-28 col-span-12">
            <img
              :src="`${base_url}/img/logo.png`"
              alt=""
              class="object-cover dark:invert dark:mix-blend-plus-lighter dark:brightness-200"
            />
          </div>
          <div class="font-bold xl:col-span-11 col-span-12 dark:text-white">{{ data.name }}</div>
        </div>
        <div class="mb-5 text-gray-500 darK">
          <span v-html="data.description" class="line-clamp-3"></span>
        </div>
        <div class="grid grid-cols-2 gap-5">
          <div class="grid" v-if="data.staging_pipelin">
            <div class="text-sm">Staging PipeLine:</div>
            <div class="font-medium">
              {{ data.staging_pipeline }}
            </div>
          </div>
           <div class="grid" v-if="data.development_pipeline">
            <div class="text-sm">Development PipeLine:</div>
            <div class="font-medium">
              {{ data.development_pipeline }}
            </div>
          </div>
          <div class="grid" v-if="data.production_Pipeline ">
            <div class="text-sm">Production PipeLine:</div>
            <div class="font-medium">
              {{ data.production_Pipeline }}
            </div>
          </div>
          <div class="grid col-span-2">
            <div class="col-span-4">
              <div class="text-sm">Url:</div>
              <a
                :href="data.url"
                target="_blank"
                class="text-darkblue font-semibold dark:text-skyblue break-all"
                >{{ data.url }}</a
              >
            </div>
          </div>
        </div>
        <!-- <ul>
          <li class="grid grid-cols-5">
            <div class="col-span-2">Staging PipeLine:</div>
            <div class="col-span-3">
              {{ data.staging_pipeline ?? '-' }}
            </div>
          </li>
          <li class="grid grid-cols-5">
            <div class="col-span-2">Development PipeLine:</div>
            <div class="col-span-3">
              {{ data.development_pipeline ?? '-' }}
            </div>
          </li>
          <li class="grid grid-cols-5">
            <div class="col-span-2">Production PipeLine:</div>
            <div class="col-span-3">
              {{ data.production_Pipeline ?? '-' }}
            </div>
          </li>
          <li class="grid">
            <div class="col-span-4">
              <a
                :href="data.url"
                target="_blank"
                class="text-darkblue font-semibold dark:text-skyblue break-all"
                >{{ data.url }}</a
              >
            </div>
          </li>
        </ul> -->
      </div>
      <div class="flex items-center gap-x-2">
        <template v-if="data.documents_count > 0">
            <App-button
              outline
              :to="`project/${data.id}/document`"
              class="btn btn-success btn-sm whitespace-nowrap"
              >View Docs
            </App-button>
        </template>
        <template v-else>
            <App-button
              v-if="is_super_admin"
              outline
              :to="route('document.create', data.id)"
              class="btn btn-success btn-sm whitespace-nowrap"
       
              >Create document
            </App-button>
        </template>
        <!-- to="{{ route('projects.edit', data.id) }}" -->
        <App-button
          v-if="is_super_admin"
          outline
          @click="openEditModal(data)"
          class="btn btn-success btn-sm whitespace-nowrap"
          icon="cog"
        >
          Edit
        </App-button>
      </div>
      <!-- {{ isSubProject }} -->
      <!-- {{ is_super_admin  }} -->
      <div v-if="is_super_admin && !isSubProject" class="flex items-center gap-x-2 mt-4">
        <div class="text-start" v-if="is_super_admin && !isSubProject">
          <App-button @click="openAddSubProject(data.id)" outline>Add Sub Project </App-button>
        </div>
        <!-- v-if="data.sub_projects != 0" -->
        <div  v-if="!isSubProject">
          <App-button :to="route('view.sub-projects', { id: data.id })" outline
            >View Sub Projects
          </App-button>
        </div>
      </div>
    </div>
</template>
