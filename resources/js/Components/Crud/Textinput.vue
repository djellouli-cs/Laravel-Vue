<script setup>
const props = defineProps({
  modelValue: String,
  name: String,
  type: {
    type: String,
    default: 'text'
  },
  message: String,
})

// Emit the update event when the user types
const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div class="mb-4">
    <label :for="name" class="block text-sm font-medium text-gray-700 mb-1 capitalize">
      {{ name }}
    </label>
    <input
      :id="name"
      :type="type"
      :value="modelValue"
      @input="emit('update:modelValue', $event.target.value)"
      class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
    />
    <p v-if="message" class="text-red-500 text-xs mt-1">{{ message }}</p>
  </div>
</template>
