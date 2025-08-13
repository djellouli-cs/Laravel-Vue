<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Acheminement Manager</h1>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Search by acheminement, NDappel, or description..."
        class="border rounded px-3 py-2 w-full"
      />
    </div>

    <!-- Flash message -->
    <div v-if="flash.success" class="mb-4 text-green-600">
      {{ flash.success }}
    </div>

    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Numero</label>
        <select v-model="form.numero_id" class="border rounded px-3 py-2 w-full" required>
          <option value="">-- Select Numero --</option>
          <option v-for="numero in filteredNumeros" :key="numero.id" :value="numero.id">
            {{ numero.NDappel }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Acheminement</label>
        <input v-model="form.acheminement" type="text" class="border rounded px-3 py-2 w-full" required />
      </div>

      <!-- ✅ Description field -->
      <div class="col-span-full">
        <label class="block text-sm font-medium mb-1">Description</label>
        <input v-model="form.description" type="text" class="border rounded px-3 py-2 w-full" placeholder="Optional description" />
      </div>

      <div class="col-span-full flex space-x-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">
          {{ form.id ? 'Update' : 'Create' }}
        </button>
        <button type="button" @click="reset" class="bg-gray-300 px-4 py-1 rounded">
          Cancel
        </button>
      </div>
    </form>

    <!-- Table -->
    <table class="w-full border text-sm border-collapse">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Numero</th>
          <th class="border px-2 py-1">Acheminement</th>
          <th class="border px-2 py-1">Description</th> <!-- ✅ -->
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in paginatedAcheminements" :key="item.id">
          <td class="border px-2 py-1">{{ item.id }}</td>
          <td class="border px-2 py-1">{{ item.numero?.NDappel || '—' }}</td>
          <td class="border px-2 py-1">{{ item.acheminement }}</td>
          <td class="border px-2 py-1">{{ item.description || '—' }}</td> <!-- ✅ -->
          <td class="border px-2 py-1 space-x-2">
            <button @click="edit(item)" class="text-blue-600 hover:underline">Edit</button>
            <button @click="destroy(item.id)" class="text-red-600 hover:underline">Delete</button>
          </td>
        </tr>
        <tr v-if="paginatedAcheminements.length === 0">
          <td colspan="5" class="text-center text-gray-500 py-2">No acheminements found.</td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center gap-4 items-center">
      <button
        @click="currentPage--"
        :disabled="currentPage === 1"
        class="px-3 py-1 border rounded"
      >
        Prev
      </button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button
        @click="currentPage++"
        :disabled="currentPage === totalPages"
        class="px-3 py-1 border rounded"
      >
        Next
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutEdit.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  acheminements: Array,
  numeros: Array,
})

const flash = usePage().props.flash || {}

const form = ref({
  id: null,
  numero_id: '',
  acheminement: '',
  description: '', // ✅
})

const search = ref('')
const currentPage = ref(1)
const itemsPerPage = 5

// Filter list
const filtered = computed(() => {
  const q = search.value.toLowerCase()
  return props.acheminements.filter(item =>
    item.acheminement?.toLowerCase().includes(q) ||
    item.numero?.NDappel?.toLowerCase().includes(q) ||
    item.description?.toLowerCase().includes(q) // ✅ search by description
  )
})

// Pagination
const paginatedAcheminements = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filtered.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => Math.ceil(filtered.value.length / itemsPerPage))

watch(search, () => { currentPage.value = 1 })

// Filtered numeros for select
const filteredNumeros = computed(() => {
  const q = search.value.toLowerCase()
  const matched = props.numeros.filter(n => n.NDappel?.toLowerCase().includes(q))
  const selected = props.numeros.find(n => n.id === form.value.numero_id)
  if (selected && !matched.some(n => n.id === selected.id)) {
    matched.push(selected)
  }
  return matched
})

const save = () => {
  const url = form.value.id ? `/manageAcheminement/${form.value.id}` : '/manageAcheminement'
  const method = form.value.id ? 'put' : 'post'
  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => reset(),
  })
}

const edit = (item) => {
  form.value = {
    id: item.id,
    numero_id: item.numero_id,
    acheminement: item.acheminement,
    description: item.description || '', // ✅
  }
}

const destroy = (id) => {
  if (confirm('Are you sure you want to delete this acheminement?')) {
    router.delete(`/manageAcheminement/${id}`, { preserveScroll: true })
  }
}

const reset = () => {
  form.value = {
    id: null,
    numero_id: '',
    acheminement: '',
    description: '', // ✅
  }
}
</script>
