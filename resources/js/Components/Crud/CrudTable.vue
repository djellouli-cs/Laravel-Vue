<template>
  <table class="w-full border border-collapse text-sm">
    <thead>
      <tr>
        <th class="border px-2 py-1">ID</th>
        <th class="border px-2 py-1">Name</th>
        <th class="border px-2 py-1">Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(item, index) in items" :key="item.id || index">
        <td class="border px-2 py-1">{{ item.id }}</td>
        <td class="border px-2 py-1">{{ item.name }}</td>
        <td class="border px-2 py-1 space-x-2">
          <button
            type="button"
            @click="$emit('edit', item)"
            class="text-blue-600 hover:underline"
          >
            Edit
          </button>
          <button
            type="button"
            @click="$emit('delete', item.id)"
            class="text-red-600 hover:underline"
          >
            Delete
          </button>
        </td>
      </tr>
      <tr v-if="items.length === 0">
        <td colspan="3" class="text-center text-gray-500 py-2">
          No data available.
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script setup>
defineProps({
  items: {
    type: Array,
    required: true,
    default: () => []
  }
})

defineEmits(['edit', 'delete'])
</script>
