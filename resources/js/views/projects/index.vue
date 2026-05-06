<template>
    <div class="grid lg:grid-cols-4 grid-cols-2 gap-5 gap-y-10">
        <app-card v-for="project in projects">
            <template #header>
                <div class="flex flex-col p-4">
                    <div class="grid pb-8 gap-x-5 xl:grid-flow-col grid-flow-row items-center xl:gap-y-0 gap-y-2">
                        <div class="xl:col-span-1 w-28 col-span-12"> <img src= "{{url('/img/logo.png')}}" alt="" class="object-cover dark:invert dark:mix-blend-plus-lighter dark:brightness-200"></div>
                        <div class="font-bold xl:col-span-11 col-span-12 dark:text-white">{{  project.title }}</div>
                    </div>
                    <div class="pb-5 text-gray-500 dark:text-slate-300 justify-self-start flex">{{ projectdescription }}</div> 
                </div>
            </template>
            {{ project.description }}
            <template #footer>
                <div class="flex items-center gap-x-5">
                    <app-button :to="'project/'+project.id+ '/documentation/1'"  class="btn btn-success btn-sm">View Docs </app-button>
                    <a :href="project.link" target="_blank" class="text-darkblue font-semibold dark:text-skyblue">{{ project.projectLink }}</a>
                </div>
            </template>
        </app-card>
    </div>
</template>
<script setup>
import { onMounted, ref } from 'vue';
const projects = ref([
    {
        id: 1,
        title: "title",
        description: "description",
        link: "",
        projectLink: ""
    }
])
onMounted(() => {
    fetch("/projects").then((res) => {
        console.log(res)
    })
})
</script>