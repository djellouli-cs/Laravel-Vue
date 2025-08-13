<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Organisme Manager</h1>

    <!-- Flash message -->
    <div v-if="flash.success" class="mb-4 text-green-600">
      {{ flash.success }}
    </div>

    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input
          v-model="form.name"
          type="text"
          class="border rounded px-3 py-2 w-full"
          required
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Nom en français</label>
        <input
          v-model="form.name_fr"
          type="text"
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <div class="flex space-x-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">
          {{ form.id ? 'Update' : 'Create' }}
        </button>
        <button type="button" @click="reset" class="bg-gray-300 px-4 py-1 rounded">
          Cancel
        </button>
      </div>
    </form>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Search by ID, name, or name_fr..."
        class="border rounded px-3 py-2 w-full"
      />
    </div>

    <!-- Table -->
    <table class="w-full border border-collapse text-sm">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Name</th>
          <th class="border px-2 py-1">Name FR</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in filteredOrganismes" :key="item.id">
          <td class="border px-2 py-1">{{ item.id }}</td>
          <td class="border px-2 py-1">{{ item.name }}</td>
          <td class="border px-2 py-1">{{ item.name_fr }}</td>
          <td class="border px-2 py-1 space-x-2">
            <button @click="edit(item)" class="text-blue-600 hover:underline">Edit</button>
            <button @click="destroy(item.id)" class="text-red-600 hover:underline">Delete</button>
          </td>
        </tr>
        <tr v-if="filteredOrganismes.length === 0">
          <td colspan="4" class="text-center text-gray-500 py-2">No organismes found.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutEdit.vue'

defineOptions({ layout: Layout })

// Props from controller
const props = defineProps({
  organismes: {
    type: Array,
    default: () => [],
  },
})

// Flash messages
const flash = usePage().props.flash || {}

// Form
const form = ref({
  id: null,
  name: '',
  name_fr: '',
})

// Search input
const search = ref('')

// Computed filtered list
const filteredOrganismes = computed(() => {
  const q = search.value.toLowerCase()
  return props.organismes.filter(o =>
    o.name?.toLowerCase().includes(q) ||
    o.name_fr?.toLowerCase().includes(q) ||
    o.id?.toString().includes(q)
  )
})

// Save (create or update)
const save = () => {
  const url = form.value.id
    ? `/manageOrganisme/${form.value.id}`
    : '/manageOrganisme'

  const method = form.value.id ? 'put' : 'post'

  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => reset(),
  })
}

// Edit
const edit = (organisme) => {
  form.value = { ...organisme }
}

// Delete
const destroy = (id) => {
  if (confirm('Are you sure you want to delete this organisme?')) {
    router.delete(`/manageOrganisme/${id}`, {
      preserveScroll: true,
    })
  }
}

// Reset form
const reset = () => {
  form.value = { id: null, name: '', name_fr: '' }
}
</script>
