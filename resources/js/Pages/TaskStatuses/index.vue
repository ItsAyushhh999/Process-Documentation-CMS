<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import AppTextInput from '@/components/AppTextInput.vue';
import Modal from '@/components/Modal.vue';
import {ref } from 'vue';
import ActionButton from './ActionButton.vue';
import formatterComponent from '@/utils/formatterComponent';
import AppSwitch from '@/components/AppSwitch.vue';

const props = defineProps({
  taskStatuses: {
    type: Array,
    default: []
  },
  status: {
    type: String ,
    default: '1'
  },
  finalValue: {
    type: Number,
    default: null
  }
});

const is_super_admin =  usePage().props.auth.user.is_super_admin;

let defaultCols = [
// { title: 'Name', field: 'value' },
  { title: 'Name', field: 'name', minWidth:180, },
  { title: 'Status',
   field: 'status',
   formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          isStatus: true,
          data: cell.getData().status,
        }
      });
    },
    minWidth:180,
  },
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
];
// if(is_super_admin == 1){
//   columns = [...columns, 
//   {
//     title: '',
//     field: 'id',
//     formatter: (cell) => {
//       return formatterComponent({
//         component: ActionButton,
//         props: {
//           data: cell.getData(),
//           openEditModal: openEditModal
//         }
//       });
//     }
//   }]
// }

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
      });
    },
    minWidth:180,
  }] : defaultCols;



const isModelOpen = ref(false);
const editModalVisible = ref(false);

const closeModal = () => {
  isModelOpen.value = false;
  editModalVisible.value = false
  form.reset();
};

const form = useForm({
  id: null,
  name: null,
  value: props.finalValue,
  status: '1'
});

const handleSubmitCreate = () => {

  form.post(route('taskStatuses.store'), {
    onSuccess: () => {
      closeModal();
    },
    onError: (error) => {
      console.log('error', error);
    }
  });
};

const openEditModal = (data) => {
  editModalVisible.value = true;
  console.log('form', data);
  form.id = data.id;
  form.name = data.name;
  form.value = data.value;
  form.status = data.status;
};

const handleSubmitUpdate = () => {

  form.put(route('taskStatuses.update', form.id), {
    onSuccess: () => {
      closeModal();
    },
    onError: (error) => {
      console.log('error', error);
    }
  });
};

const showActiveInactive = (val) => {
  router.get(route('taskStatuses.index', {
    status: val == '1' ? '0' : '1'
  }));
}

const onRowDrag = (val) => {
    const newRowOrder = val.map((elem) => elem.name)
    const reOrderForm = useForm({
      order: newRowOrder
    })

    reOrderForm.post(route('taskStatuses.sortRowOrder'), {
    onSuccess: () => {
      // router.reload();
      closeModal();
    },
    onError: (error) => {
      console.log('error', error);
    }
  });
}


</script>
<template>
  <Head title="Categories" />
  <AuthenticatedLayout>
    <template #pageTitle>Task Status</template>
    <template #header>
      <div class="flex items-center gap-4">
      <app-button @click="showActiveInactive(status)" outline> {{status == '1' ? 'Show Inactive': 'Show Active'}}</app-button>
      <app-button v-if="is_super_admin == 1" @click="isModelOpen = true">Create Task Status</app-button>
    </div>
    </template>
    <div class="w-full h-full" v-if="taskStatuses && taskStatuses.length > 0">
      <AppDataTable :data="taskStatuses" :columns="columns" movableRows @onRowDrag="onRowDrag"/>
    </div>

    <Modal :show="isModelOpen" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Create Task Status</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitCreate">
        <AppTextInput
          label="Status Name"
          v-model="form.name"
          name="name"
          required
          placeholder="Status Name"
          :errorMessage="form.errors.name"
          @focus="form.clearErrors('name')"
        />
        <AppTextInput
          label="Status Value"
          v-model="form.value"
          name="value"
          placeholder="value"
          idDummy
        />
        
       <AppSwitch v-model="form.status" labelLeft="Status" true-value="1" false-value="0" />

        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Create Status</AppButton>
        </div>
      </form>
    </Modal>

    <Modal :show="editModalVisible" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Task Status</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitUpdate">
       
        <AppTextInput
          label="Status Name"
          v-model="form.name"
          name="name"
          required
          placeholder="Status Name"
          :errorMessage="form.errors.name"
          @focus="form.clearErrors('name')"
        />
        <AppTextInput
          label="Status Value"
          v-model="form.value"
          name="value"
          placeholder="value"
          idDummy
        />
        
       <AppSwitch v-model="form.status" labelLeft="Status" true-value="1" false-value="0"/>
        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Update Task Status</AppButton>
        </div>
      </form>
    </Modal>
  </AuthenticatedLayout>
</template>
