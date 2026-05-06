 <template>
  <div class="relative xl:w-[280px] min-h[100vh] flex flex-col">
    <div
      class="flex flex-col justify-center items-center mb-2 border-b dark:border-gray-700 px-4 py-4"
    >
      <div class="flex w-[180px] h-[60px] items-center justify-center mb-6 dark:bg-slate-900 p-2">
        <img
          :src="`${base_url}/img/logo.png`"
          class="w-full h-auto object-contain object-center dark:grayscale dark:brightness-50"
          alt=""
        />
      </div>
      <span class="text-xl font-semibold text-primary-500 mb-2 text-center dark:text-gray-200">{{ projectname }}</span>
      <a :href="url" class="text-sm text-primary-500 break-all dark:text-blue-300 mb-6">{{ projecturl }}</a>

      <div class="w-full z-10">
        <app-autocomplete
          v-model="selected"
          placeholder="Project"
          :options="projects"
          select-label="Enter"
          track-by="id"
          name="project"
          label="name"
          group-values="subprojects"
          group-label="group_name"
        >
          <template #default="{ option }">
            <div class="flex items-center gap-2">
              <div class="grow">{{ option.name }}</div>
            </div>
          </template>
        </app-autocomplete>
      </div>
    </div>

    <div class="flex flex-col px-4">
      <draggable
        class="w-full"
        v-model="sortcatagories"
        @change="catagorylog($event)"
        :sort="isSortable"
        :disabled="isDraagDisabled"
      >
        <template #item="{ element }">
          <ol
            class="text-gray-500 px-2 p-1.5 mb-1 rounded-md relative group last:mb-0"
            :class="[isSortable ? 'bg-white dark:bg-slate-800' : '']"
            :key="element.id"
          >
            <li class="cursor-pointer">
              <div
                class="flex justify-between text-gray-600 dark:text-gray-300 items-center"
                @click="triggerList($event, element.id)"
              >
                <span class="text-base font-bold">{{ element.name }}</span>
                <template v-if="element.documents.length > 0">
                  <template v-if="isListVisible(element.id)">
                    <app-icon :name="isListVisible(element.id) ? 'fa-chevron-down' : 'fa-chevron-right'"
                    class="text-gray-400 cursor-pointer"></app-icon>
                  </template>
                  <template v-else>
                    <span
                      class="absolute -left-4 opacity-0 group-hover:opacity-100 cursor-pointer"
                      :class="[isDraagDisabled ? 'hidden' : '']"
                    >
                      <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="14"
                        height="14"
                        fill="currentColor"
                        class="bi bi-arrows-move"
                        viewBox="0 0 16 16"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M7.646.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1-.708.708L8.5 1.707V5.5a.5.5 0 0 1-1 0V1.707L6.354 2.854a.5.5 0 1 1-.708-.708l2-2zM8 10a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 14.293V10.5A.5.5 0 0 1 8 10zM.146 8.354a.5.5 0 0 1 0-.708l2-2a.5.5 0 1 1 .708.708L1.707 7.5H5.5a.5.5 0 0 1 0 1H1.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2zM10 8a.5.5 0 0 1 .5-.5h3.793l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L14.293 8.5H10.5A.5.5 0 0 1 10 8z"
                        />
                      </svg>
                    </span>
                    <app-icon class="text-gray-400" name="fa-angle-right"></app-icon>
                  </template>
                </template>
              </div>

              <template v-if="isListVisible(element.id)">
                <div class="transition-all mt-1">
                  <draggable
                    class="dragArea list-group w-full"
                    :list="element.documents"
                    @change="documentlog(element.documents)"
                    :sort="isSortable"
                    :disabled="isDraagDisabled"
                  >
                    <template #item="{ element: document }">
                      <ol
                        class="text-blue-500 pl-4 py-1 mb-1 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-md group/edit"
                        :key="document.id"
                        :class="[activeDocument === document.id ? 'bg-gray-50' : '']"
                      >
                        <li class="cursor-pointer">
                          <Link
                            :href="route('documents.show', { project: projectid, document: document.id })"
                            :only="['document']"
                            preserve-state
                            preserve-scroll
                            @click="setActiveDocument(document.id)"
                          >
                            <div class="flex justify-between text-gray-500 dark:text-gray-400 w-[220px]"
                            :class="[activeDocument === document.id ? 'text-gray-600 font-bold' : '']">
                              <span class="text-base font-semibold truncate">{{ document.name }}</span>
                            </div>
                          </Link>
                        </li>
                      </ol>
                    </template>
                  </draggable>
                </div>
              </template>
            </li>
          </ol>
        </template>
      </draggable>
    </div>
  </div>
</template>


<script setup>
// Imports
import AppSelect from '@/components/AppSelect.vue';
import AppIcon from '@/components/AppIcon.vue';
import AppAutocomplete from '@/components/AppAutocomplete.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import draggable from 'vuedraggable';

import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';

// export default {
//   layout: 'AuthenticatedLayout', // Use the persistent layout
// };
defineOptions({ layout: AuthenticatedLayout })
// Props
const props = defineProps({
  catagories: {
    default: null,
    type: [Object, Array],
  },
  projects: {
    default: [],
    type: [Object, Array],
  },
  projectid: {
    default: null,
    type: Number,
  },
  isadmin: {
    default: false,
    type: Boolean,
  },
  projectname: {
    type: String || Number,
    default: null,
  },
  projecturl: {
    type: String || Number,
    default: null,
  },
});

// Refs
const isSortable = ref(false);
// const isListvisible = ref(false);
// const vissiblelistid = ref(null);
const selected = ref(null);
const sortcatagories = ref([]);
const isDraagDisabled = ref(false);
const base_url = route().t.url;
const visibleCategories = ref([]);
const activeDocument = ref([]);

// Lifecycle
isSortable.value = props.isadmin;
isDraagDisabled.value = !props.isadmin;
selected.value = props.projects.filter((elem) => elem.id == props.projectid)[0];
sortcatagories.value = props.catagories;

// Methods

function setActiveDocument(documentId) {
  activeDocument.value = documentId;  // Set the active document ID
}

function triggerList(event, categoryId) {
  // Check if the clicked target is a category and not a document
  const isCategoryClicked = event.target.closest('li.cursor-pointer');
  
  if (isCategoryClicked) {
    visibleCategories.value[categoryId] = !visibleCategories.value[categoryId];
  }
}

// Check if a category's documents should be visible
function isListVisible(categoryId) {
  return visibleCategories.value[categoryId] || false;
}


function catagorylog(event) {
  // Create an array of document IDs based on the event (document order)
  let categoriesData = sortcatagories.value.map((elem) => elem.id);

  // Prepare the payload with the document ids and projectId
  const payload = {
    ids: categoriesData,
    projectId: props.projectid, // Use the `projectid` prop
  };

  // Retrieve the XSRF token from the cookie
  const xsrfToken = document.cookie
    .split(';')
    .find(cookie => cookie.trim().startsWith('XSRF-TOKEN='))
    ?.split('=')[1];

  // Ensure the token is found
  if (!xsrfToken) {
    console.error("CSRF token not found!");
    return;
  }

  // Use `router.put` to send the PUT request with the CSRF token in headers
  router.put('/project/category/order', payload, {
    headers: {
      'X-XSRF-TOKEN': xsrfToken, // Include XSRF-TOKEN in the request headers
    },
    preserveState: true,
    onSuccess: (response) => {
      console.log('Category order updated successfully:', response);
    },
    onError: (error) => {
      console.error('Error updating category order:', error);
    },
  });
}

function documentlog(event) {
  // Create sortingDocs by mapping over the event and assigning positions
  let sortingDocs = event.map((elem) => elem.id);  // Ensure only IDs are included in the payload

  const payload = {
    ids: sortingDocs,  // Only pass the array of IDs, not the entire document object
    projectId: props.projectid,  // Ensure projectId is just a primitive value (number)
  };

  const serializedPayload = structuredClone(payload);
  console.log(serializedPayload, 'test'); // Check the payload before sending
  // Retrieve the XSRF token from the cookie
  const xsrfToken = document.cookie
    .split(';')
    .find(cookie => cookie.trim().startsWith('XSRF-TOKEN='))
    ?.split('=')[1];

  // Ensure the token is found
  if (!xsrfToken) {
    console.error("CSRF token not found!");
    return;
  }

  // Use `router.put` to send the PUT request with the CSRF token in headers
  router.put('/project/document/order', serializedPayload, {
    headers: {
      'X-XSRF-TOKEN': xsrfToken, // Include XSRF-TOKEN in the request headers
    },
    preserveState: true,
    onSuccess: (response) => {
      console.log('Document order updated successfully:', response);
    },
    onError: (error) => {
      console.error('Error updating document order:', error);
    },
  });
}

watch(
  () => selected.value,
  () => {
    // Ensure the selected project is valid and then navigate using just the project ID
    if (selected.value && selected.value.id) {
      // Navigate to the project document route using just the ID
      router.get(`/project/${selected.value.id}/document`);
    }
  }
);
</script> 
