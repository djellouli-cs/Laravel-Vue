<script setup>
defineProps({
  msg: {
    type: String,
    required: true
  }
})
</script>

<template>
  <div >
    <h2 class="text-green-500 text-6xl font-bold ">{{ msg }}</h2>
    
  </div>
</template>

