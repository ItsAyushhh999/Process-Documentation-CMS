<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import AppIcon from './AppIcon.vue';
import { ref } from 'vue';
import { onClickOutside } from '@vueuse/core';
// const props = defineProps({})
// console.log("props", props)

const profileRef = ref(null);
const isModalVisible = ref(false);


onClickOutside(profileRef, () => {
  isModalVisible.value = false;
});

const page = usePage();
// console.log('page', page.props);
const user = page.props.auth.user;

const formatNameToInitials = (name) => {
  // console.log("name", name)
  const arr = name.split(' ');
  return `${arr[0].charAt(0)}${arr[1].charAt(0)}`;
};

const logout = () => {
  console.log('logout clicked');
  router.post(route('logout'), {}, {
    onFinish: () => {
      console.log('onFinish fired');
      window.location.replace('/');
    }
  });
};

</script>
<template>
  <div class="bg-blue-500 rounded-full relative" ref="profileRef">
    <button class="flex flex-col" @click="isModalVisible = !isModalVisible">
      <span
        class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white overflow-hidden"
      >
        <img
          v-if="user && user.profile_picture"
          :src="`/storage/profiles/${user.profile_picture}`"
          alt=""
          class="w-full h-full object-cover object-center"
        />
        <span v-else class="tracking-wider dark:text-slate-300">
          {{ formatNameToInitials(user.name) }}
        </span>
      </span>
    </button>

    <div
      v-if="isModalVisible"
      class="absolute -right-2 top-14 bg-white dark:bg-slate-800 rounded-lg shadow-lg flex flex-col whitespace-nowrap border overflow-hidden min-w-[240px] dark:border-slate-700"
    >
      <div class="flex items-center pt-4 px-4 pb-3 border-b gap-4 dark:border-slate-700">
        <div
          class="flex w-10 h-10 bg-blue-500 rounded-full items-center justify-center text-white overflow-hidden"
        >
          <img
            v-if="user && user.profile_picture"
            :src="`/storage/profiles/${user.profile_picture}`"
            alt=""
            class="w-full h-full object-cover object-center"
          />
          <span v-else class="tracking-wider dark:text-slate-300">
            {{ formatNameToInitials(user.name) }}
          </span>
        </div>
        <div class="flex flex-col items-start">
          <span class="font-semibold">{{ user.name }}</span>
          <span class="text-sm text-gray-600">{{ user.email }}</span>
        </div>
      </div>
      <div class="flex flex-col gap-1 p-2">
        <Link
          :href="route('profile.index')"
          class="items-center inline-flex gap-3 py-2 px-4 rounded-lg font-medium hover:bg-primary-50/50 dark:hover:bg-primary-500/50 dark:hover:text-slate-100 hover:text-primary-500 transition-all"
        >
          <AppIcon name="fa-user" />
          <span>Profile</span>
        </Link>

        <!--<Link
          :href="route('logout')"
          method="post"
          as ="button"
          class="items-center inline-flex gap-3 py-2 px-4 rounded-lg font-medium hover:bg-primary-50/50 dark:hover:bg-primary-500/50 dark:hover:text-slate-100 hover:text-primary-500 transition-all"
        >
          <AppIcon name="fa-right-from-bracket" />
          <span>Logout</span>
        </Link>-->

        <button
          @click.stop="logout"
          class="items-center inline-flex gap-3 py-2 px-4 rounded-lg font-medium hover:bg-primary-50/50 dark:hover:bg-primary-500/50 dark:hover:text-slate-100 hover:text-primary-500 transition-all"
        >
          <AppIcon name="fa-right-from-bracket" />
          <span>Logout</span>
        </button>

      </div>
    </div>
    <div
      v-if="isModalVisible"
      class="w-4 h-4 absolute bg-white dark:bg-slate-800 top-12 right-3 rotate-45 border-l border-t dark:border-slate-700"
    />
  </div>
</template>
