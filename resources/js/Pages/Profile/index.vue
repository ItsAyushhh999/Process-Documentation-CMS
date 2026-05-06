<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppTextInput from '@/components/AppTextInput.vue';
import AppButton from '@/components/AppButton.vue';
import AppBgCard from '@/components/AppBgCard.vue';
import AppIcon from '@/components/AppIcon.vue';
import { ref } from 'vue';
const props = defineProps({
  user: {
    type: Object,
    default: null
  }
});
const selectedImage = ref(null)
const form = useForm({
  profile_picture: null,
  slack_username: props.user.slack_username,
  phone: props.user.phone,
  secondary_phone: props.user.secondary_phone
});


const previewImage = (event) => {
  const imageFiles = event.target.files;
  console.log('imageFiles', imageFiles[0]);
  selectedImage.value = imageFiles;
  if (imageFiles.length > 0) {
    const imgSrc = URL.createObjectURL(imageFiles[0]);
    const imagePreviewElement = document.querySelector('#preview-selected-image');
    imagePreviewElement.src = imgSrc;
    imagePreviewElement.onload = function() {
      URL.revokeObjectURL(imagePreviewElement.src)
    }
  }
  // console.log('imageFiles',   profile_picture.value)
  
};

const handleSubmitUpdateProfile = () => {
    form.profile_picture = selectedImage.value ? selectedImage.value[0] : null;
    // console.log("form.data", form.data())
    // return
  form.post(route('profile.update'), {
    onSuccess: () => {
      console.log("update success")
    },
    onError: (error)=>{
      console.log('error', error)
    }
  })
  }
</script>

<template>
  <Head title="Profile" />

  <AuthenticatedLayout>
    <div class="w-full grid lg:grid-cols-3 md:grid-cols-2 grid-cols-1 gap-5">
      <AppBgCard class="bg-white rounded-lg shadow-md py-6 p-4 sm:px-6" style="width: 500px">
        <div class="flex flex-col gap-5">
          <div class="flex flex-col items-center">
            <div class="flex items-center">
              <div class="rounded-full w-[120px] h-[120px] p-4 relative">
                <img
                  id="preview-selected-image"
                  class="rounded-full w-full h-full object-center object-cover"
                  :src="`/storage/profiles/${user.profile_picture}`"
                />
                <div class="absolute right-3 bottom-6 flex flex-col items-center">
                  <label
                    for="file-upload"
                    class="flex cursor-pointer items-center w-8 h-8 rounded-full bg-gray-200/50 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:bg-gray-400 focus:ring-primary-500  justify-center"
                  >
                    <span class="text-sm font-medium text-center text-gray-700 whitespace-nowrap">
                      <app-icon name="pen-to-square"/>
                    </span>
                  </label>
                </div>
              </div>
            </div>
          </div>
          <div class="flex flex-col justify-center items-center text-center">
            <div class="flex flex-col justify-start gap-2">
              <div>
                <span class="text-2xl font-semibold">
                  {{ user.name }}
                </span>
              </div>
              <div class="text-gray-400">{{ user.email }}</div>
              <!-- <div>
                <AppChip color="sky">
                  {{ user.title_id ? user.title_id : 'NA' }}
                </AppChip>
              </div> -->
            </div>
          </div>
        </div>
        <div style="">
          <form  @submit.prevent="handleSubmitUpdateProfile">
            <!-- @csrf @method('PUT') -->
            <div class="grid px-4 py-8 gap-5">
              <input
                ref="profile_picture"
                type="file"
                name="profile_picture"
                id="file-upload"
                accept="image/*"
                @change="previewImage"
                class="hidden"
              />
              <AppTextInput
                label="Slack Username"
                type="text"
                v-model="form.slack_username"
                placeholder="Slack Username"
                name="slack_username"
                :errorMessage="form.errors.slack_username"
              />
              <AppTextInput
                label="Phone no."
                type="text"
                v-model="form.phone"
                placeholder="Phone Number"
                name="phone"
                :errorMessage="form.errors.phone"
              />
              <AppTextInput
                label="Secondary Phone no."
                type="text"
                v-model="form.secondary_phone"
                placeholder="Secondary Phone No."
                name="secondary_phone"
                 :errorMessage="form.errors.secondary_phone"
              />
            </div>
            <div class="px-4">
              <AppButton
                type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600"
                :loading="form.processing"
              >
                Update Profile
              </AppButton>
            </div>
          </form>
        </div>
      </AppBgCard>
    </div>
  </AuthenticatedLayout>
</template>
