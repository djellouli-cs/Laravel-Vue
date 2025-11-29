<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">⚙️ Gestion des Services</h1>

    <!-- Flash Message -->
    <div v-if="flash.success" class="mb-4 text-green-600 font-medium">
      ✅ {{ flash.success }}
    </div>

    <!-- Formulaire -->
    <form @submit.prevent="save" class="mb-6 flex flex-wrap gap-4 items-end">
      <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium mb-1">Nom du service (Original)</label>
        <input
          v-model="form.name"
          type="text"
          class="border rounded px-3 py-2 w-full"
          placeholder="Entrer le nom du service"
          required
        />
      </div>
      <div class="flex-1 min-w-[200px]">
        <label class="block text-sm font-medium mb-1">Nom du service (Français)</label>
        <input
          v-model="form.name_fr"
          type="text"
          class="border rounded px-3 py-2 w-full"
          placeholder="Entrer le nom en français (optionnel)"
        />
      </div>

      <div class="space-x-2">
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          {{ form.id ? 'Mettre à jour' : 'Créer' }}
        </button>
        <button
          v-if="form.id"
          type="button"
          @click="reset"
          class="bg-gray-300 px-4 py-2 rounded hover:bg-gray-400"
        >
          Annuler
        </button>
      </div>
    </form>

    <!-- 🔍 Barre de recherche -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        class="border rounded px-3 py-2 w-full sm:w-1/2"
        placeholder="🔍 Rechercher un service..."
      />
    </div>

    <!-- Table + Pagination -->
    <div class="border rounded overflow-x-auto w-full">
      <!-- Tableau -->
      <table class="min-w-full border-collapse text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="border px-4 py-2 text-left">ID</th>
            <th class="border px-4 py-2 text-left">Nom (Original)</th>
            <th class="border px-4 py-2 text-left">Nom (Français)</th>
            <th class="border px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="service in paginatedServices"
            :key="service.id"
            class="hover:bg-gray-50"
          >
            <td class="border px-4 py-2">{{ service.id }}</td>
            <td class="border px-4 py-2">{{ service.name }}</td>
            <td class="border px-4 py-2">{{ service.name_fr || '—' }}</td>
            <td class="border px-4 py-2 space-x-2">
              <button @click="edit(service)" class="text-blue-600 hover:underline">Modifier</button>
              <button @click="destroy(service.id)" class="text-red-600 hover:underline">Supprimer</button>
            </td>
          </tr>
          <tr v-if="filteredServices.length === 0">
            <td colspan="4" class="text-center text-gray-500 py-3">Aucun service trouvé.</td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination -->
      <div
        v-if="totalPages > 1"
        class="flex flex-wrap justify-between items-center px-4 py-3 bg-white border-t text-sm"
      >
        <!-- Précédent -->
        <div class="space-x-2">
          <button
            @click="goToPage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded 
                   bg-blue-100 text-blue-700 
                   hover:bg-blue-200 
                   disabled:opacity-50 disabled:cursor-not-allowed"
          >
            « Précédent
          </button>
        </div>

        <!-- Numéros de page + Suivant -->
        <div class="space-x-1 mt-2 sm:mt-0 flex items-center flex-wrap">
          <button
            v-for="page in totalPages"
            :key="page"
            @click="goToPage(page)"
            :class="[
              'px-3 py-1 border rounded',
              page === currentPage ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-100'
            ]"
          >
            {{ page }}
          </button>

          <button
            @click="goToPage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded 
                   bg-green-100 text-green-700 
                   hover:bg-green-200 
                   disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Suivant »
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutEdit.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  services: Array,
})

const flash = usePage().props.flash || {}

const form = ref({
  id: null,
  name: '',
  name_fr: '',
})

// 🔍 Recherche
const search = ref('')

// Filtrer les services selon la recherche
const filteredServices = computed(() => {
  const q = search.value.toLowerCase()
  return props.services.filter(s =>
    s.name.toLowerCase().includes(q) ||
    (s.name_fr && s.name_fr.toLowerCase().includes(q))
  )
})

// Pagination
const itemsPerPage = 5
const currentPage = ref(1)

const totalPages = computed(() =>
  Math.max(1, Math.ceil(filteredServices.value.length / itemsPerPage))
)

const paginatedServices = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredServices.value.slice(start, start + itemsPerPage)
})

// Reset page quand on recherche
watch(search, () => {
  currentPage.value = 1
})

function goToPage(page) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

function save() {
  const url = form.value.id ? `/services/${form.value.id}` : '/services'
  const method = form.value.id ? 'put' : 'post'

  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => {
      reset()
      goToPage(1)
    },
  })
}

function edit(service) {
  form.value = { ...service }
}

function destroy(id) {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) {
    router.delete(`/services/${id}`, {
      preserveScroll: true,
      onSuccess: () => {
        if (paginatedServices.value.length === 1 && currentPage.value > 1) {
          goToPage(currentPage.value - 1)
        }
      }
    })
  }
}

function reset() {
  form.value = { id: null, name: '', name_fr: '' }
}
</script>
