<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">✏️ Note</h1>

    <!-- Flash message -->
    <div v-if="flash.success" class="mb-4 text-green-600">
      {{ flash.success }}
    </div>

    <!-- Search for Numero -->
    <div>
      <label class="block text-sm font-medium mb-1">Search Numero</label>
      <input
        v-model="search"
        type="text"
        placeholder="Type to filter NDappel..."
        class="border rounded px-3 py-2 w-full"
      />
    </div>

    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Note Content</label>
        <textarea
          v-model="form.content"
          class="border rounded px-3 py-2 w-full"
          rows="3"
          required
        />
      </div>

      <!-- Filtered Numero Select -->
      <div>
        <label class="block text-sm font-medium mb-1">Numero</label>
        <select
          v-model="form.numero_id"
          required
          class="border rounded px-3 py-2 w-full"
        >
          <option value="" disabled>Select Numero</option>
          <option
            v-for="numero in filteredNumeros"
            :key="numero.id"
            :value="numero.id"
          >
            {{ numero.NDappel }}
          </option>
        </select>
      </div>

      <!-- Buttons -->
      <div class="flex space-x-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">
          {{ form.id ? 'Update' : 'Create' }}
        </button>
        <button type="button" @click="reset" class="bg-gray-300 px-4 py-1 rounded">
          Cancel
        </button>
      </div>
    </form>

    <!-- Table -->
    <table class="w-full border border-collapse text-sm">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Content</th>
          <th class="border px-2 py-1">Numero</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="note in paginatedNotes" :key="note.id">
          <td class="border px-2 py-1">{{ note.id }}</td>
          <td class="border px-2 py-1">{{ note.content }}</td>
          <td class="border px-2 py-1">{{ note.numero?.NDappel }}</td>
          <td class="border px-2 py-1 space-x-2">
            <button @click="edit(note)" class="text-blue-600 hover:underline">Edit</button>
            <button @click="destroy(note.id)" class="text-red-600 hover:underline">Delete</button>
          </td>
        </tr>
        <tr v-if="paginatedNotes.length === 0">
          <td colspan="4" class="text-center text-gray-500 py-2">No notes available.</td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination Controls -->
    <div class="mt-4 flex justify-between items-center text-sm">
      <button
        @click="currentPage--"
        :disabled="currentPage === 1"
        class="px-3 py-1 border rounded disabled:opacity-50"
      >
        Prev
      </button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button
        @click="currentPage++"
        :disabled="currentPage === totalPages"
        class="px-3 py-1 border rounded disabled:opacity-50"
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
  notes: Array,
  numeros: Array,
})

const notes = computed(() => props.notes)
const numeros = computed(() => props.numeros)
const flash = usePage().props.flash || {}

const search = ref('')
const currentPage = ref(1)
const notesPerPage = 5

const form = ref({
  id: null,
  content: '',
  numero_id: '',
})

// Filter dropdown options
const filteredNumeros = computed(() => {
  if (!search.value) return numeros.value
  const query = search.value.toLowerCase()
  return numeros.value.filter(n =>
    n.NDappel?.toString().toLowerCase().includes(query)
  )
})

// Filter notes in table
const filteredNotes = computed(() => {
  if (!search.value) return notes.value
  const query = search.value.toLowerCase()
  return notes.value.filter(note =>
    note.content?.toLowerCase().includes(query) ||
    note.numero?.NDappel?.toString().toLowerCase().includes(query)
  )
})

// Paginated notes
const paginatedNotes = computed(() => {
  const start = (currentPage.value - 1) * notesPerPage
  const end = start + notesPerPage
  return filteredNotes.value.slice(start, end)
})

// Total pages
const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredNotes.value.length / notesPerPage))
)

// Reset to first page when search changes
watch(search, () => {
  currentPage.value = 1
})

const save = () => {
  const url = form.value.id
    ? `/manageNote/${form.value.id}`
    : '/manageNote'

  const method = form.value.id ? 'put' : 'post'

  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => reset(),
  })
}

const edit = (note) => {
  form.value = {
    id: note.id,
    content: note.content,
    numero_id: note.numero_id,
  }
}

const destroy = (id) => {
  if (confirm('Are you sure you want to delete this note?')) {
    router.delete(`/manageNote/${id}`, { preserveScroll: true })
  }
}

const reset = () => {
  form.value = { id: null, content: '', numero_id: '' }
}
</script>
