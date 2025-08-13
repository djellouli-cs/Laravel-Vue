<template>
  <form @submit.prevent="emit('submit')">
    <div class="mb-3">
      <label class="block text-sm font-medium mb-1">Name</label>
     <input
  v-model="model.name"
  type="text"
  required
  class="border rounded px-3 py-1 w-full"
/>

    </div>
    <div class="flex space-x-2">
      <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">
        {{ model.id ? 'Update' : 'Create' }}
      </button>
      <button type="button" @click="reset" class="bg-gray-300 px-3 py-1 rounded">
        Cancel
      </button>
    </div>
  </form>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({ modelValue: Object })
const emit = defineEmits(['submit', 'update:modelValue'])

const model = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val),
})

const reset = () => {
  emit('update:modelValue', { id: null, name: '' })
}
</script>a
