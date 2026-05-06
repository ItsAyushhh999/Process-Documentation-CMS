<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage, useForm, router } from '@inertiajs/vue3';
import AppDataTable from '@/components/AppDataTable.vue';
import AppButton from '@/components/AppButton.vue';
import AppTextInput from '@/components/AppTextInput.vue';
import AppAutocomplete from '@/components/AppAutocomplete.vue';
import Modal from '@/components/Modal.vue';
import { computed, ref, watch } from 'vue';
import ActionButton from './ActionButton.vue';
import formatterComponent from '@/utils/formatterComponent';
import AppSwitch from '@/components/AppSwitch.vue'

const props = defineProps({
  categories: {
    type: Array,
    default: []
  },
  projects: {
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
  },
  getAll : {
    type: String
  }
});


const is_super_admin =  usePage().props.auth.user.is_super_admin;
const defaultCols = [
  { title: 'Category', field: 'name' , minWidth:180,},
  { title: 'Description', field: 'description' , minWidth:180,},
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
  }
];

const toggleStatusModal = ref(false);
const editCategoryDetail = ref({});
// Toggle modal visibility
const toggleCategoryStatusModal = () => {
  toggleStatusModal.value = !toggleStatusModal.value;
};

// Handle category status toggle action
const handleStatusToggle = (category) => {
  editCategoryDetail.value = category;
  toggleCategoryStatusModal();
};
const cancelCategoryStatusChange = () => {
  toggleCategoryStatusModal();
};

const handleToggleChange = () => {
  form.put(route('categories.status.update', form.id), {
    preserveScroll: true,
    onSuccess: () => {
      // Close the modal
      toggleCategoryStatusModal();
    },
    onError: (error) => {
      console.error(`Error updating status for category ID: ${form.id}`, error);
    },
  });
};


// Watcher for when category details are updated
watch(editCategoryDetail, (newDetail) => {
  if (newDetail) {
    form.id = newDetail.id || "";
    form.name = newDetail.name || "";
    form.description = newDetail.description || "";
    form.status = newDetail.status || "0"; // Ensure status is set correctly when watching
    form.projects = newDetail.projects || "";
  }
});




const columns = is_super_admin == 1 ? [...defaultCols,
{
  title: 'Status',
  field: 'status',
  formatter: (cell) => {
    const data = cell.getData();

    return formatterComponent({
      component: AppSwitch,
      props: {
        modelValue: data.status, // Pass the status value as modelValue
        onClick: () => {
          // Prevent the internal toggle state change
          handleStatusToggle(data);
        },
        disabled: true, // Disable the internal toggle (optional based on the AppSwitch component)
      },
    });
  },
  minWidth: 120,
}
,
{
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
const selectedProject = ref([]);
const selectedImage = ref(null);
const selectedImageString = ref(null);

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
  // task_type: null
  name: null,
  description: null,
  projects: null,
  // logo: null
  status: "0",
});

const handleSubmitCreate = () => {
  form.projects = selectedProject.value.map((elem) => elem.id);

  if (selectedImage.value) {
    form.logo = selectedImage.value;
  }
  // console.log('form2', form.logo);
  form.post(route('categories.store'), {
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
  form.description = data.description;
  form.status = data.status,
  selectedProject.value = data.project;
  selectedImageString.value = data.logo;
  // form.logo = data.logo;
};

const handleSubmitUpdate = () => {
  // console.log('from update', form);

  form.projects = selectedProject.value.map((elem) => elem.id);

  if (selectedImage.value) {
    form.logo = selectedImage.value;
  }
  form.put(route('categories.update', form.id), {
    onSuccess: () => {
      closeModal();
      form.reset();
    },
    onError: (error) => {
      console.log('error', error);
    }
  });
};

const selectFiles = (e) => {
  selectedImage.value = e.target.files[0];
};

const form1 = useForm({
    showAll: props.getAll ,
});


const ShowAllCategoryToggleChange = () => {
  // Prevent unnecessary requests by checking if the state really needs to change
  const newShowAll = form1.showAll === "0" ? "1" : "0"; // Toggle state

  // Make the request only if the state needs to change
  if (form1.showAll !== newShowAll) {
    form1.get(route('categories.index', { showAll: newShowAll }), {
      preserveState: true,
      onSuccess: () => {
        props.categories.value = props.categories;
      },
      onError: (errors) => {
        console.log('Error fetching categories', errors);
      },
    });
  }
};
</script>
<template>
  <Head title="Categories" />
  <AuthenticatedLayout>
    <template #header>
        <div>
            <AppSwitch class="mr-4 " v-model="form1.showAll" v-if="is_super_admin == 1"  @change="ShowAllCategoryToggleChange" labelLeft="Show All" />
            <app-button @click="isModelOpen = true" v-if="is_super_admin == 1">Create Category</app-button>
        </div>
    </template>
    <div class="w-full h-full" v-if="categories && categories.length > 0">
      <AppDataTable :data="categories" :columns="columns"></AppDataTable>
    </div>

    <Modal :show="isModelOpen" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Create Category</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitCreate">
        <AppTextInput
          label="Category"
          v-model="form.name"
          name="name"
          required
          placeholder="Category"
          :errorMessage="form.errors.name"
          @focus="form.clearErrors('name')"
        />
        <AppTextInput
          label="description"
          v-model="form.description"
          name="description"
          required
          placeholder="description"
          :errorMessage="form.errors.description"
          @focus="form.clearErrors('description')"
          :textArea="true"
        />

        <div class="w-full z-10">
          <app-autocomplete
            v-model="selectedProject"
            placeholder="Project"
            :options="projects"
            select-label="Enter"
            track-by="id"
            name="projects"
            label="name"
            :errorMessage="form.errors.description"
            @focus="form.clearErrors('description')"
            multiple
          >
            <template #default="{ option }">
              <div class="flex items-center gap-2 w-full overflow-hidden">
                <span class="grow truncate">{{ option.name }}</span>
              </div>
            </template>
          </app-autocomplete>
        </div>

        <div class="log">
          <span class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
            >Logo</span
          >
          <label class="block border border-dashed border-primary-200 p-4 rounded-md">
            <span class="sr-only">Choose Hero Image</span>
            <input
              ref="image"
              type="file"
              @change="selectFiles"
              class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
              name="bannerImage"
              @focus="form.clearErrors('logo')"
            />
          </label>
          <transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="-translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="duration-100 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-1 opacity-0 "
          >
            <div v-show="form.errors.logo">
              <p class="text-sm text-red-600 text-left">
                {{ form.errors.logo }}
              </p>
            </div>
          </transition>
        </div>
        <AppSwitch v-model="form.status" labelLeft="Status"/>

        <!-- <input type="text"  v-model="form.task_type" name="task_type"> -->
        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Create Category</AppButton>
        </div>
      </form>
    </Modal>

    <Modal :show="editModalVisible" @close="closeModal">
      <template #header>
        <h2 class="text-xl font-semibold">Edit Category</h2>
      </template>
      <form class="p-6 px-8 grid gap-5" @submit.prevent="handleSubmitUpdate">
        <AppTextInput
          label="Category"
          v-model="form.name"
          name="name"
          required
          placeholder="Category"
          :errorMessage="form.errors.name"
          @focus="form.clearErrors('name')"
        />
        <AppTextInput
          label="description"
          v-model="form.description"
          name="description"
          required
          placeholder="description"
          :errorMessage="form.errors.description"
          @focus="form.clearErrors('description')"
          :textArea="true"
        />

        <div class="w-full z-10">
          <app-autocomplete
            v-model="selectedProject"
            placeholder="Project"
            :options="projects"
            select-label="Enter"
            track-by="id"
            name="projects"
            label="name"
            :errorMessage="form.errors.description"
            @focus="form.clearErrors('description')"
            multiple
          >
            <template #default="{ option }">
              <div class="flex items-center gap-2 w-full overflow-hidden">
                <span class="grow truncate">{{ option.name }}</span>
              </div>
            </template>
          </app-autocomplete>
        </div>

        <div class="log">
          <span class="block text-sm font-medium text-gray-700 capitalize mb-1 dark:text-slate-400"
            >Logo</span
          >
          <div class="flex gap-3">
            <div
              class="w-[60px] aspect-square border rounded-md overflow-hidden"
              v-if="selectedImageString"
            >
              <img
                :src="`/storage/category/logo/${selectedImageString}`"
                :alt="selectedImageString"
                class="w-full h-full object-contain object-center"
              />
            </div>
            <div class="grow">
              <label class="block border border-dashed border-primary-200 p-4 rounded-md">
                <span class="sr-only">Choose Hero Image</span>
                <input
                  ref="image"
                  type="file"
                  @change="selectFiles"
                  class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                  name="bannerImage"
                  @focus="form.clearErrors('logo')"
                />
              </label>
              <transition
                enter-active-class="duration-300 ease-out"
                enter-from-class="-translate-y-2 opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-active-class="duration-100 ease-in"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="translate-y-1 opacity-0 "
              >
                <div v-show="form.errors.logo">
                  <p class="text-sm text-red-600 text-left">
                    {{ form.errors.logo }}
                  </p>
                </div>
              </transition>
            </div>
          </div>
        </div>
        <AppSwitch v-model="form.status" labelLeft="Status"/>

        <div class="flex justify-end gap-3 mt-8">
          <AppButton @click="closeModal" text> Cancel </AppButton>
          <AppButton type="submit" :loading="form.processing">Update Category</AppButton>
        </div>
      </form>
    </Modal>

    <Modal :show="toggleStatusModal" @close="cancelCategoryStatusChange">
  <template #header>Change Status Confirmation</template>
  <div class="p-4">
    <p class="mt-4">Are you sure you want to change the status of the Category?</p>
    <div class="flex justify-end gap-2 mt-4">
        <AppButton variant="secondary"@click="cancelCategoryStatusChange">Cancel</AppButton>
        <AppButton class="ms-3"@click="handleToggleChange">Confirm</AppButton>
    </div>
  </div>
</Modal>


  </AuthenticatedLayout>
</template>
