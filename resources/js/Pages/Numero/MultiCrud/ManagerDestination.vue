<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Destination Manager</h1>

    <!-- Flash Message -->
    <div v-if="flash.success" class="mb-4 text-green-600">
      {{ flash.success }}
    </div>

    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input v-model="form.name" type="text" class="border rounded px-3 py-2 w-full" required />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Nom en français</label>
        <input v-model="form.name_fr" type="text" class="border rounded px-3 py-2 w-full" />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Organisme</label>
        <select v-model="form.organisme_id" required class="border rounded px-3 py-2 w-full">
          <option value="" disabled>Select organisme</option>
          <option v-for="org in organismes" :key="org.id" :value="org.id">
            {{ org.name }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Groupes</label>
        <select v-model="form.groupes" multiple class="border rounded px-3 py-2 w-full" style="min-height: 100px;">
          <option v-for="groupe in groupes" :key="groupe.id" :value="groupe.id">
            {{ groupe.groupes }}
          </option>
        </select>
        <p class="text-sm text-gray-600 mt-1">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs groupes</p>
      </div>

      <div class="flex space-x-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">
          {{ form.id ? 'Update' : 'Create' }}
        </button>
        <button type="button" @click="reset" class="bg-gray-300 px-4 py-1 rounded">Cancel</button>
      </div>
    </form>

    <!-- Search -->
    <div class="mb-4">
      <input v-model="search" type="text" placeholder="Search destinations..." class="border rounded px-3 py-2 w-full" />
    </div>

    <!-- Table -->
    <table class="w-full border border-collapse text-sm">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Name</th>
          <th class="border px-2 py-1">Name FR</th>
          <th class="border px-2 py-1">Organisme</th>
          <th class="border px-2 py-1">Groupes</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="dest in paginatedDestinations" :key="dest.id">
          <td class="border px-2 py-1">{{ dest.id }}</td>
          <td class="border px-2 py-1">{{ dest.name }}</td>
          <td class="border px-2 py-1">{{ dest.name_fr }}</td>
          <td class="border px-2 py-1">{{ dest.organisme?.name }}</td>
          <td class="border px-2 py-1">
            <div v-if="dest.groupes && dest.groupes.length > 0" class="flex flex-wrap gap-1">
              <span 
                v-for="groupe in dest.groupes" 
                :key="groupe.id"
                class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
              >
                {{ groupe.groupes }}
              </span>
            </div>
            <span v-else class="text-gray-400 text-sm">—</span>
          </td>
          <td class="border px-2 py-1 space-x-2">
            <button @click="edit(dest)" class="text-blue-600 hover:underline">Edit</button>
            <button @click="destroy(dest.id)" class="text-red-600 hover:underline">Delete</button>
          </td>
        </tr>
        <tr v-if="paginatedDestinations.length === 0">
          <td colspan="6" class="text-center text-gray-500 py-2">No destinations found.</td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination Controls -->
    <div class="mt-4 flex justify-center items-center gap-4">
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
  destinations: Array,
  organismes: Array,
  groupes: Array,
})

const flash = usePage().props.flash || {}

const form = ref({
  id: null,
  name: '',
  name_fr: '',
  organisme_id: '',
  groupes: [],
})

const search = ref('')
const currentPage = ref(1)
const itemsPerPage = 5

const filteredDestinations = computed(() => {
  const q = search.value.toLowerCase()
  return props.destinations.filter(dest =>
    dest.name?.toLowerCase().includes(q) ||
    dest.name_fr?.toLowerCase().includes(q) ||
    dest.organisme?.name?.toLowerCase().includes(q)
  )
})

const totalPages = computed(() => Math.ceil(filteredDestinations.value.length / itemsPerPage))

const paginatedDestinations = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredDestinations.value.slice(start, start + itemsPerPage)
})

watch(search, () => {
  currentPage.value = 1
})

const save = () => {
  const url = form.value.id ? `/manageDestination/${form.value.id}` : '/manageDestination'
  const method = form.value.id ? 'put' : 'post'

  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => reset(),
  })
}

const edit = (dest) => {
  form.value = { 
    ...dest,
    groupes: dest.groupes ? dest.groupes.map(g => g.id) : []
  }
}

const destroy = (id) => {
  if (confirm('Are you sure you want to delete this destination?')) {
    router.delete(`/manageDestination/${id}`, {
      preserveScroll: true,
    })
  }
}

const reset = () => {
  form.value = { id: null, name: '', name_fr: '', organisme_id: '', groupes: [] }
}
</script>
