<template>
  <div class="p-6">
    <!-- Flash Message -->
    <div v-if="flash.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
      {{ flash.success }}
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Rechercher des groupes..."
        class="border rounded px-3 py-2 w-full"
      />
    </div>

    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 p-4 bg-gray-50 rounded">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="groupes" class="block text-sm font-medium mb-1">Nom du Groupe</label>
          <input
            id="groupes"
            v-model="form.groupes"
            type="text"
            class="border rounded px-3 py-2 w-full"
            required
          />
        </div>
        <div class="flex items-end">
          <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            {{ form.id ? 'Mettre à jour' : 'Créer' }}
          </button>
          <button 
            v-if="form.id" 
            type="button" 
            @click="reset" 
            class="ml-2 bg-gray-300 px-4 py-2 rounded hover:bg-gray-400"
          >
            Annuler
          </button>
        </div>
      </div>
    </form>

    <!-- Table -->
    <table class="w-full border border-collapse text-sm">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Nom du Groupe</th>
          <th class="border px-2 py-1">Destinations</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="groupe in filteredGroupes" :key="groupe.id">
          <td class="border px-2 py-1">{{ groupe.id }}</td>
          <td class="border px-2 py-1">{{ groupe.groupes }}</td>
          <td class="border px-2 py-1">
            <div v-if="groupe.destinations && groupe.destinations.length > 0" class="flex flex-wrap gap-1">
              <span 
                v-for="destination in groupe.destinations" 
                :key="destination.id"
                class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full"
              >
                {{ destination.name }}
              </span>
            </div>
            <span v-else class="text-gray-400 text-sm">—</span>
          </td>
          <td class="border px-2 py-1 space-x-2">
            <button @click="edit(groupe)" class="text-blue-600 hover:underline">Edit</button>
            <button @click="destroy(groupe.id)" class="text-red-600 hover:underline">Delete</button>
          </td>
        </tr>
        <tr v-if="filteredGroupes.length === 0">
          <td colspan="4" class="text-center text-gray-500 py-2">Aucun groupe trouvé.</td>
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
  groupes: {
    type: Array,
    default: () => [],
  },
})

const flash = usePage().props.flash || {}

const form = ref({
  id: null,
  groupes: '',
})

const search = ref('')

const filteredGroupes = computed(() => {
  const q = search.value.toLowerCase()
  return props.groupes.filter(g =>
    g.groupes?.toLowerCase().includes(q) ||
    g.id?.toString().includes(q)
  )
})

const save = () => {
  const url = form.value.id
    ? `/manageGroupe/${form.value.id}`
    : '/manageGroupe'

  const method = form.value.id ? 'put' : 'post'

  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => reset(),
  })
}

const edit = (groupe) => {
  form.value = { ...groupe }
}

const destroy = (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?')) {
    router.delete(`/manageGroupe/${id}`, {
      preserveScroll: true,
    })
  }
}

const reset = () => {
  form.value = {
    id: null,
    groupes: '',
  }
}
</script> 