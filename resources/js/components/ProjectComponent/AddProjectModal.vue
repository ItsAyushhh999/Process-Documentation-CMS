<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/components/Modal.vue'
import AppButton from '@/components/AppButton.vue'
import AppTextInput from '@/components/AppTextInput.vue'
import AppEditor from '@/components/AppEditor.vue'


const props = defineProps({
  title: {
    type: String,
    default: 'Add Product'
  }
})

const isModelOpen = ref(false);

const form = useForm({
  id: null,
  name: null,
  description: '',
  url: null,
  development_pipeline: null,
  staging_pipeline: null,
  production_Pipeline: null,
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

const closeModal = () => {
  isModelOpen.value = false;
  form.reset()
}

const openModal = () => {
  isModelOpen.value = true
}
</script>

<template>

<app-button @click="openModal">{{ title }}</app-button>

</template>