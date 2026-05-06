<template>
<div class="border dark:border-gray-700 py-2 rounded-xl">
    <span class="text-sm px-4 font-light text-gray-500 dark:text-gray-400 ">{{placeholder}}</span>

  <div class="relative block">
    <div class=" flex justify-between px-4 cursor-pointer" @click="isOptionVisible=!isOptionVisible">
      <div class="truncate">{{name}}</div>
      <div class=" droparrow">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>
      </div>
    </div>
    <div v-if="isOptionVisible" class="absolute  border bg-white rounded-b-lg left-0 right-0 top-full transition-all max-h-[320px] overflow-y-auto">
      <ol>
        <li v-for="project in projects" :key="project.id" class="py-1 cursor-pointer truncate hover:bg-blue-50 px-4" :value="project.id" @click="onChange($event)">{{project.name}}</li>
      </ol>
    </div>
  </div>
  </div>
</template>



<script>

export default {
  name: "AppSelect",
  props: {
    modelValue: {
      type: [String, Array, Number, Object],
      default: () => [],
    },
    native: {
      type: Boolean,
      default: false,
    },
    placeholder:{
      type: String,
      default: 'placeholder'
    },
    projects:{
      default: null
    },
    name:{
      default: null
    }

  },
  data(){
    return{
      isOptionVisible: false,
    }
  },
  created(){
    // window.addEventListener('click', (e) => {
    //   console.log(e.target.classList.contains('sOption'))
    //         if (!e.target.classList.contains('sOption')){
    //           console.log("clicked")
    //           this.isOptionVisible = false
    //         }
    //       })
  },
  methods: {
     onChange(event){
      console.log("vlaue", event.target.value)
      location.href = '/project/'+ event.target.value + '/document/';
      this.isOptionVisible = false
    },
  }
};
</script>