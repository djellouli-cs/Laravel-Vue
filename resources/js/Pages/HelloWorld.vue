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
    <h3 class="text-lg text-gray-400 ">
      Tous les adresses de wilaya d'el bayadh    </h3>
  </div>
</template>

