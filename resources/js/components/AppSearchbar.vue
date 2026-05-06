<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const searchKey = ref('');
const searchBy = ref('projects');
const handleSearch = () => {
  // const form = useForm({})
  const data = {
    // _token: 'np7P1TDUoHpdAXyfTwmOm0U1X3Ysd0uzRvqdPvEA',
    search: searchKey.value,
    type: searchBy.value
  };

  const form = useForm({
    search: searchKey.value,
    type: searchBy.value
  });

  // console.log('data', data);
  form.get(route('search.list'), {
    onSuccess: () => console.log('success'),
    onError:(error) => console.log('error', error)
  });
};
</script>

<template>
  <div class="border min-w-[520px] rounded-full dark:border-slate-700">
    <form @submit.prevent="handleSearch" class="flex pl-1 py-1 pr-6" id="searchByForm">
      <select
        class="w-auto text-sm dark:text-gray-400 rounded-full bg-gray-100 dark:bg-slate-800 border-gray-100 dark:border-slate-800"
        name="type"
        v-model="searchBy"
      >
        <option value="projects">Projects</option>
        <option value="documents">Documents</option>
      </select>
      <span class="relative w-full flex mb-2 sm:mb-0 ml-3 sm:ml-0 items-center">
        <input
          class="w-full form-control rounded-l-full bg-transparent border-transparent placeholder:text-gray-400 dark:text-gray-400 focus:outline-0 focus:ring-0 focus:border-transparent focus:ring-primary-200 focus:ring-opacity-50 dark:focus:ring-gray-700"
          type="search"
          name="search"
          placeholder="Search..."
          aria-label="Search Projects"
          required=""
          v-model="searchKey"
        />
      </span>

      <button type="submit">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="14"
          height="14"
          viewBox="0 0 16 16"
          role="img"
          class="fill-current"
        >
          <path
            fill-rule="evenodd"
            clip-rule="evenodd"
            d="M10.6002 12.0498C9.49758 12.8568 8.13777 13.3333 6.66667 13.3333C2.98477 13.3333 0 10.3486 0 6.66667C0 2.98477 2.98477 0 6.66667 0C10.3486 0 13.3333 2.98477 13.3333 6.66667C13.3333 8.15637 12.8447 9.53194 12.019 10.6419C12.0265 10.6489 12.0338 10.656 12.0411 10.6633L15.2935 13.9157C15.6841 14.3063 15.6841 14.9394 15.2935 15.33C14.903 15.7205 14.2699 15.7205 13.8793 15.33L10.6269 12.0775C10.6178 12.0684 10.6089 12.0592 10.6002 12.0498ZM11.3333 6.66667C11.3333 9.244 9.244 11.3333 6.66667 11.3333C4.08934 11.3333 2 9.244 2 6.66667C2 4.08934 4.08934 2 6.66667 2C9.244 2 11.3333 4.08934 11.3333 6.66667Z"
          ></path>
        </svg>
      </button>
    </form>
  </div>
</template>
