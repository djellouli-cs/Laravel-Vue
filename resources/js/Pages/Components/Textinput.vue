<script setup>
import { defineProps, defineModel } from 'vue'

const model = defineModel({
  type: null, // or whatever types you expect
  required: true,
})

const props = defineProps({
  name: {
    type: String,
    required: true,
  },
  type: {
    type: String,
    default: 'text', // default should be a string literal
  },
  message: String,
})
</script>

<template>
  <div>
    <div class="mb-4">
      <label :for="name" class="block text-sm font-medium text-gray-600">{{ name }}</label>
      <input
        :type="type"
        :id="name"
        v-model="model"
        class="mt-2 w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
      <small class="text-red-600" v-if="message">{{ message }}</small>
    </div>
  </div>
</template>
