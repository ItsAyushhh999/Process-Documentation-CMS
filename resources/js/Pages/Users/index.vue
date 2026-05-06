<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import AppChip from '@/components/AppChip.vue';
import AppTextInput from '@/components/AppTextInput.vue';
import AppAutocomplete from '@/components/AppAutocomplete.vue';
import Modal from '@/components/Modal.vue';
import { computed, ref, watch } from 'vue';
import ActionButton from './ActionButton.vue';
import formatterComponent from '@/utils/formatterComponent';
import axios from 'axios';
import AppSwitch from '@/components/AppSwitch.vue'

const props = defineProps({
  //   users
  // isDepartmentHead
  // status
  users: {
    type: Array,
    default: []
  },
  departments: {
    type: Array,
    default: []
  },
  titles: {
    type: Array,
    default: []
  },
  isDepartmentHead: {
    type: Boolean,
    default: false
  },
  status: {
    type: String,
    default: ''
  }
});

const columns = [
  { title: 'Name',
    field: 'name',
    minWidth: 240,
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          data: cell.getData(),
          isTaskListing: true,
        }
      });
    }
   },
  { title: 'Status',
    field: 'status',
    minWidth:180,
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          isStatus: true,
          data: cell.getData().status,
        }
      });
    }
   },
  { title: 'Slack Username', field: 'slack_username' , minWidth:180,},
  { title: 'Github Username',
    field: 'github_username',
    formatter: (cell) => {
      let github_username = cell.getData().github_username;
      return github_username ? github_username : '-';
    }  ,
    minWidth: 180,
  },
  { title: 'Email', field: 'email', minWidth:240, },
  { title: 'Phone', field: 'phone', minWidth:180, },
  { title: 'Department', field: 'departments_name',
    formatter: (cell) => {
      let departments_name = cell.getData().departments_name;
      return departments_name ? departments_name : '-';
    },
    minWidth:180,
  },
  { title: 'Title',
    field: 'title_name',
    formatter: (cell) => {
      let title_name = cell.getData().title_name;
      return title_name ? title_name : '-';
    },
    minWidth:180,
  },
  {
    title: 'Updated By',
    field: 'updated_by',
    formatter: (cell) => {
      let updatedBy = cell.getData().updated_by;
      return updatedBy ? updatedBy.name : '-';
    },
    minWidth:180,
  },
  {
    title: '',
    field: 'id',
    minWidth:100,
    formatter: (cell) => {
      return formatterComponent({
        component: ActionButton,
        props: {
          data: cell.getData(),
          isDepartmentHead: props.isDepartmentHead,
          openEditModal: openEditModal
        }
      });
    }
  }
];

const isModelOpen = ref(false);
const editModalVisible = ref(false);
const selectedProject = ref([]);
const selectedImage = ref(null);
const selectedImageString = ref(null);
// const showAll = ref(false);
// const filterdData = computed(() => props. )
// c

// const paramsStatus = $route
// console.log('paramsStatus', usePage().props)

const selectedDepartment = ref(null)
const selectedTitle = ref()

const closeModal = () => {
  isModelOpen.value = false;
  editModalVisible.value = false;
  selectedProject.value = [];
  selectedImage.value = null;
  selectedImageString.value = null;
  form.reset();
};


const form = useForm({
  id: null,
  name: null,
  department: null,
  title: null,
  phone: null,
  slack_username: null,
  github_username: null,
  status: null,
  feed_scope: null,
});

const handleSubmitCreate = () => {
  form.projects = selectedProject.value.map((elem) => elem.id);

  if (selectedImage.value) {
    form.logo = selectedImage.value;
  }
  // console.log('form2', form.logo);
  form.post(route('categories.store'), {
    onSuccess: () => {
      // router.reload();
      closeModal();
    },
    onError: (error) => {
      console.log('error', error);
    }
  });
};

const openEditModal = async (data) => {
  console.log('data', data)
  form.id = data.id;
  form.name = data.name;
  form.phone = data.phone;
  form.slack_username = data.slack_username
  form.github_username = data.github_username
  editModalVisible.value = true;
  form.status = data.status
  form.feed_scope = data.feed_scope

  // console.log('[rops.]', props.departments)
  selectedDepartment.value = props.departments.filter((elem) => data.department_id == elem.id)[0]
  selectedTitle.value = props.titles.filter((elem) => data.title_id == elem.id)[0]
  console.log(selectedTitle.value, 'poatato')
};

const handleSubmitUpdate = () => {
  // console.log('from update', form);

  // form.projects = selectedProject.value.map((elem) => elem.id);

  // if (selectedImage.value) {
  //   form.logo = selectedImage.value;
  // }
  // console.log('111', selectedDepartment.value)
  // console.log('222', selectedTitle.value)

  console.log('form', form.data())
  form.department = `${selectedDepartment.value.department_name},${selectedDepartment.value.id}`;
  form.title = selectedTitle.value.id;

  console.log('form 2', form.data())


  form.put(route('users.update', form.id), {
    onSuccess: () => {
      router.reload();
      closeModal();
    },
    onError: (error) => {
      console.log('error', error);
    }
  });
};



const showActiveInactive = (val) => {
  router.get(route('users.index', {
    status: val == '1' ? '0' : '1'
  }));
}

const onClose = () => {

}
</script>
<template>
  <Head title="Users" />
  <AuthenticatedLayout>
    <template #header>
      <app-button @click="showActiveInactive(status)" outline> {{status == '1' ? 'Show Inactive': 'Show Active'}}</app-button>
      <!-- <div class="">
        <AppSwitch label="Show All" v-model="showAll" :trueValue="true" :falseValue="false" />
      </div> -->
    </template>
    <div class="w-full h-full" v-if="users && users.length > 0">
      <AppDataTable :data="users" :columns="columns"></AppDataTable>
    </div>

    <Modal :show="editModalVisible" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Edit User</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitUpdate">
        <AppTextInput
          label="Name"
          v-model="form.name"
          name="name"
          required
          placeholder="Name"
          :errorMessage="form.errors.name"
          @focus="form.clearErrors('name')"
          disabled
        />

        <div class="grid grid-cols-2 gap-5">
        <div class="w-full z-10">
          <app-autocomplete
            v-model="selectedDepartment"
            placeholder="department"
            :options="departments"
            select-label="department_name"
            track-by="id"
            name="department_name"
            label="department_name"
            :errorMessage="form.errors.department"
            @focus="form.clearErrors('department')"
            />
        </div>

        <div class="w-full z-10">
          <app-autocomplete
            v-model="selectedTitle"
            placeholder="title"
            :options="titles"
            select-label="title_name"
            track-by="id"
            name="title_name"
            label="title_name"
            :errorMessage="form.errors.description"
            @focus="form.clearErrors('title')"
          />
        </div>
      </div>

        <AppTextInput
          label="Phone"
          v-model="form.phone"
          name="Number"
          required
          placeholder="Phone"
          :errorMessage="form.errors.phone"
          @focus="form.clearErrors('phone')"
        />

        <div class="grid grid-cols-2 gap-5">
        <AppTextInput
          label="Slack Username"
          v-model="form.slack_username"
          name="Slack Username"
          required
          placeholder="Slack Username"
          :errorMessage="form.errors.slack_username"
          @focus="form.clearErrors('slack_username')"
        />

        <AppTextInput
          label="Github Username"
          v-model="form.github_username"
          name="Github Username"
          placeholder="Github Username"
          />
        </div>
          <!-- Status {{ form.status }} -->
           <div class="flex flex-col-2 gap-8">
            <AppSwitch v-model="form.status" labelLeft="Status"/>

            <AppSwitch v-model="form.feed_scope" labelLeft="Feed Scope"/>

           </div>

        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Update User</AppButton>
        </div>
      </form>
    </Modal>
  </AuthenticatedLayout>
</template>
