<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import AppDataTable from '../../components/AppDataTable.vue';
import formatterComponent from '@/utils/formatterComponent';
import ActionButton from './ActionButton.vue';
import { inject } from 'vue';

const props = defineProps({
  results: {
    type: Array,
    default: []
  },
  type: {
    type: String,
    default: ''
  },
  search: {
    type: String,
    default: ''
  }
});
const filters = inject('filters');
const projectsColumns = [
  {
    title: 'Task Id#',
    field: 'id',
    formatter: 'link',
    formatterParams:(cell) => ({
      target:"_blank",
      url: `./tasks/${cell.getData().id}/edit`
    })
  },
  { title: 'Title', field: 'title', formatter: 'textarea' },
  {
    title: 'Project',
    field: 'project_id',
    formatter: (cell) => {
      return cell.getData().project.name;
    }
  },
  {
    title: 'Status',
    field: 'status',
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          isStatus: true,
          data: cell.getData().status
        }
      });
    }
  },
  {
    title: 'priority',
    field: 'priority',
    formatter: (cell) => {
      // console.log('cell.getData()', cell.getData());
      return formatterComponent({
        component: ActionButton,
        props: {
          isPriority: true,
          data: cell.getData().priority
        }
      });
    }
  }
];
const DocumentColumns = [
  {
    title: 'Document Id#',
    field: 'id',
    formatter: 'link',
    formatterParams:(cell) => {
      const data = cell.getData();
      return{
      target:"_blank",
      url: `./project/${data.project_id}/document/${data.id}`
    }}
  },
  { title: 'Name', field: 'name' },
  {
    title: 'Updated At',
    field: 'updated_at',
    formatter: (cell) => {
      return filters.formatDate(cell.getData().updated_at);
    }
  }
];
</script>
<template>
  <Head title="Feeds" />
  <AuthenticatedLayout>
    <template #pageTitle
      >Search Result for <strong>{{ search }}</strong> in <strong>{{ type }}</strong></template
    >
    <div class="w-full h-full" v-if="results && results.length > 0">
      <AppDataTable
        :data="results"
        :columns="type == 'projects' ? projectsColumns : DocumentColumns"
      ></AppDataTable>
    </div>
  </AuthenticatedLayout>
</template>

<style>
.scroller {
  height: 100%;
}
</style>
