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
  departments: {
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
  // { title: 'Id', field: 'id' },
  { title: 'Department Name', field: 'department_name', minWidth:180 },
  {
    title: 'Created By',
    field: 'created_by',
    formatter: (cell) => {
      return cell.getData().created_by.name;
    },
    minWidth:180
  },
  {
    title: 'Updated By',
    field: 'updated_by',
    formatter: (cell) => {
      return cell.getData().created_by.name;
    },
    minWidth:180
  },
 
];

const columns = is_super_admin == 1 ? [...defaultCols,  {
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
    }
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
  department_name: null
});

const handleSubmitCreateDepartment = () => {
  form.post(route('departments.store'), {
    onSuccess: () => {
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
  form.department_name = data.department_name;

} 

const handleSubmitUpdateDepartment = () => {
  form.put(route('departments.update', form.id), {
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
  <Head title="Departments" />
  <AuthenticatedLayout>
    <template #header>
      <app-button @click="isModelOpen = true" v-if="is_super_admin == 1">Create Department</app-button>
    </template>
    <div class="w-full h-full" v-if="departments && departments.length > 0">
      <AppDataTable :data="departments" :columns="columns"></AppDataTable>
    </div>

    <Modal :show="isModelOpen" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Create Departments</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitCreateDepartment">
        <AppTextInput
          label="Department Name"
          v-model="form.department_name"
          name="department_name"
          required
          placeholder="Department Name"
          :errorMessage="form.errors.department_name"
          @focus="form.clearErrors('department_name')"
        />
        <!-- <input type="text"  v-model="form.department_name" name="department_name"> -->
        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Create Department</AppButton>
        </div>
      </form>
    </Modal>

    <Modal :show="editModalVisible" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Edit Department</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitUpdateDepartment">
        <AppTextInput
          label="Department Name"
          v-model="form.department_name"
          name="department_name"
          required
          placeholder="Department Name"
          :errorMessage="form.errors.department_name"
          @focus="form.clearErrors('department_name')"
        />
        <!-- <input type="text"  v-model="form.department_name" name="department_name"> -->
        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Update Department</AppButton>
        </div>
      </form>
    </Modal>
  </AuthenticatedLayout>
</template>