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
  titles: {
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
  { title: 'Title', field: 'title_name',minWidth:180, },
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
    field: 'updated_by',
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
    minWidth: 80,
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
  title_name: null
});

const handleSubmitCreateTitle = () => {
  form.post(route('titles.store'), {
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
  form.title_name = data.title_name;

} 

const handleSubmitUpdateDepartment = () => {
  form.put(route('titles.update', form.id), {
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
  <Head title="Titles" />
  <AuthenticatedLayout>
    <template #header>
      <app-button @click="isModelOpen = true" v-if="is_super_admin == 1">Create Department</app-button>
    </template>
    <div class="w-full h-full" v-if="titles && titles.length > 0">
      <AppDataTable :data="titles" :columns="columns"></AppDataTable>
    </div>

    <Modal :show="isModelOpen" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Create Departments</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitCreateTitle">
        <AppTextInput
          label="Title Name"
          v-model="form.title_name"
          name="title_name"
          required
          placeholder="Title Name"
          :errorMessage="form.errors.title_name"
          @focus="form.clearErrors('title_name')"
        />
        <!-- <input type="text"  v-model="form.title_name" name="title_name"> -->
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
          label="Title Name"
          v-model="form.title_name"
          name="title_name"
          required
          placeholder="Title Name"
          :errorMessage="form.errors.title_name"
          @focus="form.clearErrors('title_name')"
        />
        <!-- <input type="text"  v-model="form.title_name" name="title_name"> -->
        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Update Department</AppButton>
        </div>
      </form>
    </Modal>
  </AuthenticatedLayout>
</template>