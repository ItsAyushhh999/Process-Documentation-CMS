<script setup>
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import AppEditor from '@/components/AppEditor.vue';
import AppTextInput from '@/components/AppTextInput.vue';
import AppButton from '@/components/AppButton.vue';
import AppAutocomplete from '@/components/AppAutocomplete.vue';
import AppSideBar from '@/components/AppSideBar.vue';
import AppIcon from '@/components/AppIcon.vue';
import Modal from '@/components/Modal.vue';
import { ref } from 'vue';


// import AppButton from '@/components/AppButton.vue';

const openPublishModal = ref(false);

const closeModal = () => {
  openPublishModal.value = false;
  form.reset();
};

const props = defineProps({
  document: {
    type: Object,
    default: null
  },
  document: {
    type: Object,
    default: null
  },
  categories: {
    type: Object,
    default: null
  },
  project_id: {
    type: String || Number,
    default: null
  },
  projects: {
    type: Object,
    default: null
  },
  isPermitted: {
    type: Boolean,
    default: false
  },
  project_name: {
    type: String || Number,
    default : null
  },
  project_url: {
    type : String || Number,
    default : null
  }
});

const base_url = route().t.url;

const form = useForm({
    documentId : props.document?.id
});

const handleDocumentPublish = () => {

  if (form.documentId) {
      form.post(route('document.status'), {
          onSuccess: () => {
            closeModal();
          },
          onError: (error) => {
            console.log('error', error);
          }
      });
  }
}

// const form = useForm({
//     projectId: props.project.id,
//     description: '',
//     name: '',
//     categories: []
// })

// const handleSubmit = () => {
//     // action=" {{ isset($document) ? route('document.update', $document->id) : route('document.store') }}"
//     // console.log('form', form.data())
//     const categoriesIds = []
//     form.categories.map(elem => {
//         categoriesIds.push(elem.id)
//     })
//     form.categories = categoriesIds

//     form.post(route('document.store'), {
//         onFinish: () => {
//             console.log('success');
//             // btnLoading.value = false
//         },
//         onError: (error) => {
//             console.log(error);
//             // errors.value = error;
//         },
//         onSuccess: () => {
//             // emit('reloadPage')
//             location.href = route('documents.index', props.project.id)
//         }
//     });
// };
console.log('props', props);

const handleSaveAsPdf = () => {
    // Target the content area to print
    const divToPrint = document.getElementById('printArea');

    // Open a new window to load content
    const newWindow = window.open("");

    // Write the content to the new window (including title and HTML content)
    newWindow.document.write("<html><head><title>" + document.title + "</title></head>");
    newWindow.document.write("<body>");
    newWindow.document.write(divToPrint.innerHTML); // Use the content of #printArea
    newWindow.document.write("</body></html>");
    newWindow.document.close(); // Necessary for IE >= 10
    newWindow.focus(); // Necessary for IE >= 10

    // Trigger the print dialog (allows the user to save as PDF manually)
    newWindow.print();
    newWindow.close();
};


</script>

<template>
  <Head title="Document Create" />
  <AuthenticatedLayout>
    <!-- <div class="fixed top-0 w-2/3 right-0 h-full bg-white z-0 dark:bg-slate-800"></div> -->
    <div class="flex justify-center">
      <!-- @if($document) -->
      <div v-if="document" id="root" class="container flex">
        <div class="lg:block hidden border-r dark:border-gray-700">
          <div class="sticky top-0">
            <div class="overflow-auto max-h-screen h-full">
              <app-side-bar
                :catagories="categories"
                :projects="projects"
                :projectid="project_id"
                :isadmin="isPermitted"
                :projectname = "props.project_name"
                :projecturl = "props.project_url"
              >
              </app-side-bar>
            </div>
          </div>
        </div>

        <div class="relative bg-white dark:bg-slate-800 flex-1 overflow-y-auto min-h-[90vh]">
          <div
            class="flex items-center sticky top-0 bg-white/90 dark:bg-slate-900/80 backdrop-filter backdrop-blur-lg z-10"
          >
            <!-- <x-input placeholder="seerch anything"></x-input> -->
            <div class="flex-1 px-4 py-4 border-b dark:border-gray-700">
              <div class="flex justify-end gap-x-3">
                <Link
                  v-if="isPermitted"
                  class="text-blue-500 flex gap-x-2 cursor-pointer items-center px-4"
                  :href="route('document.edit', document.id)"
                >
                  <app-icon name="fa-pen-to-square" />
                  Edit This Page
                </Link>
                <AppButton outline id="htmlToPdf" class="htmlToPdf" icon="fa-file-arrow-down"  @click="handleSaveAsPdf" >
                  Save as PDF
                </AppButton>
                  <!-- <AppButton v-if="isPermitted" to="/document/create/{{$project_id}}">Add New Docs</AppButton> -->
                  <AppButton v-if="isPermitted" :to="route('document.create', project_id)">Add New Docs</AppButton>
                  <app-button  @click="openPublishModal = true" v-if="isPermitted && props.document">{{ props.document.isPublished != 1 ? 'Publish' : 'Unpublish'}}</app-button>
              </div>
            </div>
          </div>
          <div class="dark:bg-slate-800 dark:text-gray-400 h-auto" id="contnet">
            <div class="flex justify-center pt-8 px-5 h-full overflow-auto" id="printArea">
              <div
                class="prose prose-ul:mt-0 prose-p:m-0.5 prose-li:m-0 prose-lg ql-editor w-[750px] dark:text-gray-300 prose-strong:dark:text-gray-300 description"
              >
                <span v-html="document.description"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
        <div
        v-else
          class="my-20 rounded p-10 flex flex-col items-center justify-center mr-16"
        >
          <img
            :src="`${base_url}/img/404.png`"
            alt=""
            class="object-cover"
          />
          <h3 class="text-[24px] font-bold mt-4 mb-3">No Documents Found!</h3>
          <span class="">Project Id: {{ project_id }} doesn't contain documents. Do you want to Add ?</span
          >
          <Link v-if="isPermitted" :href="route('document.create', project_id)" class="mt-10">
            <app-button v-if="isPermitted" type="button">Add New Docs</app-button>
          </Link>
        </div>
    </div>
    <Modal v-if="props.document" :show="openPublishModal" @close="closeModal">
      <template #header>
        <div class="mb-1">
          <h2 class="text-xl font-semibold">
            {{ props.document.isPublished === 1 ? 'Unpublish' : 'Publish' }} Document
          </h2>
          <p class="text-base text-gray-600 text-lg mt-6">
            Are you sure you want to {{ props.document.isPublished === 1 ? 'Unpublish' : 'Publish' }} this document?
          </p>
        </div>
      </template>

      <form class="p-6 grid gap-4" @submit.prevent="handleDocumentPublish">
        <!-- Hidden Input for Document ID -->
        <input
          type="hidden"
          name="documentId"
          v-model="props.document.id"
        />

        <!-- Action Buttons -->
        <div class="flex justify-end gap-4 mt-6">
          <AppButton @click="closeModal" text class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-100">
            Cancel
          </AppButton>
          <AppButton
            type="submit"
            :class="{
              'bg-red-600 hover:bg-red-700 text-white': props.document.isPublished === 1,
              'bg-blue-600 hover:bg-blue-700 text-white': props.document.isPublished !== 1
            }"
            class="px-4 py-2"
          >
            {{ props.document.isPublished === 1 ? 'Unpublish' : 'Publish' }}
          </AppButton>
        </div>
      </form>
    </Modal>

  </AuthenticatedLayout>
</template>
