<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import AppTextInput from '@/components/AppTextInput.vue';
import Modal from '@/components/Modal.vue';
import { computed, ref, watch } from 'vue';
import ActionButton from './ActionButton.vue'
import formatterComponent from '@/utils/formatterComponent'

const props = defineProps({
  task_types: {
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


const is_super_admin =  usePage().props.auth.user.is_super_admin;
const defaultCols = [
  { title: 'Task Type', field: 'type',minWidth:180, },
  {
    title: 'Created By',
    field: 'created_by',
    formatter: (cell) => {
      return cell.getData().created_by.name;
    },
    minWidth:180,
  },
  {
    title: 'Updated By',
    field: 'created_by',
    formatter: (cell) => {
      return cell.getData().created_by.name;
    },
    minWidth:180,
  },
  // {
  //   title: '',
  //   field: 'id',
  //   formatter: (cell) => {
  //     return formatterComponent({
  //       component: ActionButton,
  //       props: {
  //         data: cell.getData(),
  //         openEditModal: openEditModal
  //       }
  //     })
  //   }
  // }
];

const columns = is_super_admin == 1 ? [...defaultCols,   {
    title: '',
    field: 'id',
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          data: cell.getData(),
          openEditModal: openEditModal
        }
      })
    },
    minWidth:180,
  }] : defaultCols;


const isModelOpen = ref(false);
const editModalVisible = ref(false);

const closeModal = () => {
  isModelOpen.value = false;
  editModalVisible.value = false;
  form.reset();
};

const form = useForm({
  id: null,
  task_type: null
});

const handleSubmitCreate = () => {
  form.post(route('taskTypes.store'), {
    onSuccess: () => {
      // router.reload()
      closeModal();
    },
    onError: (error) => {
      console.log('error', error);
    },
  });
};


const openEditModal = (data) => {
  editModalVisible.value = true
  console.log('form', data)
  form.id = data.id
  form.task_type = data.type;

} 

const handleSubmitUpdate = () => {
  form.put(route('taskTypes.update', form.id), {
    onSuccess: () => {
      router.reload()
      closeModal();
    },
    onError: (error) => {
      console.log('error', error);
    },
  });
}

</script>
<template>
  <Head title="TaskTypes" />
  <AuthenticatedLayout>
    <template #header>
      <app-button @click="isModelOpen = true" v-if="is_super_admin == 1">Create Task Type</app-button>
    </template>
    <div class="w-full h-full" v-if="task_types && task_types.length > 0">
      <AppDataTable :data="task_types" :columns="columns"></AppDataTable>
    </div>

    <Modal :show="isModelOpen" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Create Task Type</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitCreate">
        <AppTextInput
          label="Task Type"
          v-model="form.task_type"
          name="task_type"
          required
          placeholder="Task Type"
          :errorMessage="form.errors.task_type"
          @focus="form.clearErrors('task_type')"
        />
        <!-- <input type="text"  v-model="form.task_type" name="task_type"> -->
        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Create Task Type</AppButton>
        </div>
      </form>
    </Modal>

    <Modal :show="editModalVisible" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Edit Task Type</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitUpdate">
        <AppTextInput
          label="Task Type"
          v-model="form.task_type"
          name="task_type"
          required
          placeholder="Task Type"
          :errorMessage="form.errors.task_type"
          @focus="form.clearErrors('task_type')"
        />
        <!-- <input type="text"  v-model="form.task_type" name="task_type"> -->
        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Update TaskType</AppButton>
        </div>
      </form>
    </Modal>
  </AuthenticatedLayout>
</template>