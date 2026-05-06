<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head,useForm } from '@inertiajs/vue3';
import AppButton from '@/components/AppButton.vue';
import Modal from '@/components/Modal.vue';
import {ref } from 'vue';
import AppBgCard from '@/components/AppBgCard.vue';

const props = defineProps({
  user: {
    type: Array,
    default: []
  },
  projects: {
    type: Array,
    default: []
  },
  permissions: {
    type: Array,
    default: []
  },
  userPermissions: {
    type: Array,
    default: []
  }
});

const selectedPermissions = ref([]);
const isModalOpen = ref(false)

selectedPermissions.value = props.userPermissions.map(
  (elem) => `${elem.permission_id}:${elem.project_id}`
);

const selectAllSubProjectsByPermission = (permissionId, project) => {
  // arr.push(`${permissionId}:${project.id}`)
  let parentPermission = `${permissionId}:${project.id}`;

  let arr = [];
  project.subprojects.map((elem) => {
    const childPermission = `${permissionId}:${elem.id}`;
    arr.push(childPermission);
  });

  if (selectedPermissions.value.includes(parentPermission)) {
    toggleAllChildPermission(false, arr);
    selectedPermissions.value = selectedPermissions.value.filter(
      (elem) => elem != parentPermission
    );
  } else {
    selectedPermissions.value = [...selectedPermissions.value, parentPermission];
    toggleAllChildPermission(true, arr);
  }
};

const toggleAllChildPermission = (value, childrenPermission) => {

  if (value) {   // add
    selectedPermissions.value = selectedPermissions.value.concat(childrenPermission);
  } else { //remove
    childrenPermission.map((elem) => {
      selectedPermissions.value = selectedPermissions.value.filter((item) => item != elem);
    });
  }
  selectedPermissions.value = [...new Set(selectedPermissions.value)];
};

const markAllCheckboxes = () => {
  let arr = [];
  props.projects.map((project) => {
    const parentPermission = [`1:${project.id}`, `2:${project.id}`, `3:${project.id}`];
    arr.push(...parentPermission);
    if (project.subprojects && project.subprojects.length > 0) {
      project.subprojects.map((elem) => {
        const childPermission = [`1:${elem.id}`, `2:${elem.id}`, `3:${elem.id}`];
        arr.push(...childPermission);
      });
    }
  });
  selectedPermissions.value = arr;
};

// const openUpdateModal = () => {
//   isModalOpen.value = true;
// }

const form = useForm({
    permissions: []
  })

const handleSubmitUpdatePermission = () => {
  form.permissions= selectedPermissions.value;

  form.patch(route('permissions.update', props.user.id), {
    onSuccess: ()=> {
      isModalOpen.value = false;
    }, onError: (error)=> {
      console.log('error', error)
    }
  })
}
</script>
<template>
  <Head title="Users" />
  <AuthenticatedLayout>
    <template #header>
      <div class="inline-flex gap-4">
        <app-button
          outline
          @click="markAllCheckboxes()"
        >
          Mark All
        </app-button>
        <app-button @click="isModalOpen = true">
          Update permission
        </app-button>
      </div>
    </template>

    <AppBgCard class="flex flex-col gap-y-6 px-4 pb-10">
      <div class="grid grid-cols-4">
        <div class="pr-6 text-left py-3">Project Name</div>
        <div v-for="permission in permissions" class="px-6 text-left py-3">
          {{ permission.name }}
        </div>
      </div>
      <div class="border-b border-dashed" v-for="project in projects">
        <template v-if="project.subprojects && project.subprojects.length > 0">
          <div class="grid grid-cols-4">
            <div class="pr-6 text-left py-3 font-semibold">{{ project.name }}</div>
            <button
              v-for="permission in permissions"
              class="px-6 text-left py-3 underline"
              @click="selectAllSubProjectsByPermission(permission.id, project)"
            >
              <input
                type="checkbox"
                :id="project.id"
                :value="`${permission.id}:${project.id}`"
                v-model="selectedPermissions"
                disabled
                class="hidden"
              />
              <!-- {{ permission.id }}{{ project.id }} -->
              Select/ Deselect all
            </button>
          </div>
          <div v-for="(sub, index) in project.subprojects" class="grid grid-cols-4">
            <!-- {{ sub }} -->
            <div class="pl-2 pr-6 text-left py-3">{{ index + 1 }}. {{ sub.name }}</div>
            <div v-for="permission in permissions" class="px-6 text-left py-3">
              <input
                type="checkbox"
                :id="sub.id"
                :value="`${permission.id}:${sub.id}`"
                v-model="selectedPermissions"
              />
              <!-- {{ permission.id }}-{{ sub.id }} -->
            </div>
          </div>
        </template>
        <div class="grid grid-cols-4" v-else>
          <div class="pr-6 text-left py-3 font-semibold">{{ project.name }}</div>
          <div v-for="permission in permissions" class="px-6 text-left py-3">
            <input
              type="checkbox"
              :id="project.id"
              :value="`${permission.id}:${project.id}`"
              v-model="selectedPermissions"
            />
            <!-- {{ permission.id }}{{ project.id }} -->
          </div>
        </div>
      </div>
  </AppBgCard>

    <Modal :show="isModalOpen" @close="isModalOpen = false">
      <template #header>
        Update User Permission
      </template>
     <div class="px-8 pt-4 pb-6 flex flex-col gap-10">
        <span>
          Are you sure you want to update permission for {{ user.name }} ?
        </span>
        <div class="flex justify-end gap-x-3">
          <app-button @click="isModalOpen = false" outline>Cancel</app-button>
          <app-button @click="handleSubmitUpdatePermission()" :loading="form.processing">Update Permission</app-button>
        </div>
     </div>
    </Modal>

  </AuthenticatedLayout>
</template>
