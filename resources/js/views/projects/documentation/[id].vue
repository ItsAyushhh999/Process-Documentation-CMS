<template>
    <div>Documentation Details {{ $route.params.id }}</div>
    <p>Message is: {{ message }}</p>
<input v-model="message" placeholder="edit me" />
</template>
<script setup>
import { onMounted, ref } from 'vue';

const message = ref('')
onMounted(() => {
    console.log("Mounted")
})
</script>