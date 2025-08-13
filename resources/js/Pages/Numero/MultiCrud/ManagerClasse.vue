<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">✏️Classe</h1>

    <!-- Flash message -->
    <div v-if="flash.success" class="mb-4 text-green-600">
      {{ flash.success }}
    </div>

    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Classe</label>
        <input
          v-model="form.classe"
          type="text"
          required
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

    <!-- Search Input -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Search classes..."
        class="border rounded px-3 py-2 w-full"
      />
    </div>

    <!-- Table -->
    <table class="w-full border border-collapse text-sm">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Classe</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="classe in filteredClasses" :key="classe.id">
          <td class="border px-2 py-1">{{ classe.id }}</td>
          <td class="border px-2 py-1">{{ classe.classe }}</td>
          <td class="border px-2 py-1 space-x-2">
            <button @click="edit(classe)" class="text-blue-600 hover:underline">Edit</button>
            <button @click="destroy(classe.id)" class="text-red-600 hover:underline">Delete</button>
          </td>
        </tr>
        <tr v-if="filteredClasses.length === 0">
          <td colspan="3" class="text-center text-gray-500 py-2">No classes available.</td>
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

const props = defineProps({
  classes: {
    type: Array,
    default: () => [],
  },
})

const flash = usePage().props.flash || {}

const form = ref({
  id: null,
  classe: '',
})

const search = ref('')

const filteredClasses = computed(() => {
  const query = search.value.toLowerCase()
  return props.classes.filter(classe =>
    classe.classe.toLowerCase().includes(query)
  )
})

const save = () => {
  const url = form.value.id
    ? `/manageClasse/${form.value.id}`
    : '/manageClasse'

  const method = form.value.id ? 'put' : 'post'

  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => reset(),
  })
}

const edit = (classe) => {
  form.value = { ...classe }
}

const destroy = (id) => {
  if (confirm('Are you sure you want to delete this class?')) {
    router.delete(`/manageClasse/${id}`, { preserveScroll: true })
  }
}

const reset = () => {
  form.value = { id: null, classe: '' }
}
</script>
