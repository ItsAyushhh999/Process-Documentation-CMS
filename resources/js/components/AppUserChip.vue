<script setup>
  const props= defineProps({
    image: {
      type: String,
      default: null,
    },
    name: {
      type: String,
      default: null,
    }
  })

  // $a=['red', 'green', 'blue', 'yellow', 'orange', 'emerald'];
  
  // $bgColor='bg-' . $a[rand(0, 5)] . '-50';
  // // dump($bgColor);
  // $defaultClass ='inline-flex items-center gap-2 rounded-full pr-4';
// @endphp

const formatName = (name) => {
  // console.log("name", name)
  const arr = name.split(' ')
  return `${arr[0]}.${arr[1].charAt(0)}`
}
const formatNameToInitials = (name) => {
  // console.log("name", name)
  const arr = name.split(' ')
  return `${arr[0].charAt(0)}${arr[1].charAt(0)}`
}
</script>
<template>
  <div class="inline-flex items-center gap-2 rounded-full pr-4 bg-blue-50 dark:bg-blue-600">
  <div
    class="h-7 w-7 bg-blue-500 rounded-full shadow-lg overflow-hidden relative z-0 text-center text-white text-xs flex items-center justify-center">
    <!-- @if($image) -->
    <img v-if="image" :src="`/storage/profiles/${image}`" class="w-7 h-7 object-cover object-center" 
    :alt="image" />

    <!-- src="path/to/profile/image.jpg" -->
    <span v-else class=" tracking-wider dark:text-slate-300">
      {{ formatNameToInitials(name) }}
    </span>
    <!-- @endif -->
  </div>
  <span class=" tracking-wider dark:text-slate-300">
    <!-- {{$name}} -->
    {{ formatName(name) }}
    <!-- <?php
      $userName = explode(' ',$name);
      echo $userName[0] . '.' . substr($userName[1], 0, 1);
      ?> -->
  </span>
</div>
</template>