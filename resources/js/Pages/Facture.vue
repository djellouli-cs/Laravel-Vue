<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
  factures: {
    type: Array,
    default: () => []
  }
})

// Filtres
const searchQuery = ref('')
const selectedTechnologies = ref([])
const selectedOrganismes = ref([])

// Dropdowns
const techDropdownOpen = ref(false)
const orgDropdownOpen = ref(false)

// Références pour click outside
const techDropdownRef = ref(null)
const orgDropdownRef = ref(null)

function handleClickOutside(event) {
  if (techDropdownRef.value && !techDropdownRef.value.contains(event.target)) {
    techDropdownOpen.value = false
  }
  if (orgDropdownRef.value && !orgDropdownRef.value.contains(event.target)) {
    orgDropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Technologies uniques
const allTechnologies = computed(() => {
  const set = new Set()
  props.factures.forEach(f =>
    f.numeros.forEach(n => {
      if (n.technologie && n.technologie.name) set.add(n.technologie.name)
    })
  )
  return Array.from(set)
})

// Organismes uniques
const allOrganismes = computed(() => {
  const set = new Set()
  props.factures.forEach(f =>
    f.numeros.forEach(n => {
      if (n.organisme && n.organisme.name) set.add(n.organisme.name)
    })
  )
  return Array.from(set)
})

// Factures filtrées et numeros filtrés
const filteredFactures = computed(() => {
  return props.factures
    .map(f => {
      // filtrer numeros par recherche
      let numeros = f.numeros.filter(n => {
        const matchesSearch =
          !searchQuery.value ||
          (f.facture && f.facture.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
          (f.provider && f.provider.toLowerCase().includes(searchQuery.value.toLowerCase()))
        return matchesSearch
      })

      // filtrer numeros par technologie
      if (selectedTechnologies.value.length > 0) {
        numeros = numeros.filter(n =>
          n.technologie && selectedTechnologies.value.includes(n.technologie.name)
        )
      }

      // filtrer numeros par organisme
      if (selectedOrganismes.value.length > 0) {
        numeros = numeros.filter(n =>
          n.organisme && selectedOrganismes.value.includes(n.organisme.name)
        )
      }

      // ne garder la facture que si elle a au moins un numero filtré
      if (numeros.length === 0) return null

      return {
        ...f,
        numeros
      }
    })
    .filter(f => f !== null)
})

// Réinitialiser les filtres
function resetFilters() {
  searchQuery.value = ''
  selectedTechnologies.value = []
  selectedOrganismes.value = []
}
</script>

<template>
  <div class="p-6 bg-gray-100 min-h-screen">
    <h1 class="text-2xl font-bold mb-4">Toutes les factures</h1>

    <!-- Filtres -->
    <div class="flex flex-col md:flex-row gap-4 mb-4 items-center">
      <!-- Recherche -->
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Rechercher par facture ou fournisseur..."
        class="px-4 py-2 border rounded-lg w-full md:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-400"
      />

      <!-- Dropdown Technologie -->
      <div class="relative w-full md:w-1/4" ref="techDropdownRef">
        <button
          @click.stop="techDropdownOpen = !techDropdownOpen"
          class="w-full px-4 py-2 border rounded-lg bg-white text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
          <span>
            {{ selectedTechnologies.length === 0 ? 'Filtrer par Technologie' : selectedTechnologies.join(', ') }}
          </span>
          <span>▼</span>
        </button>
        <div v-if="techDropdownOpen" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto">
          <div v-for="tech in allTechnologies" :key="tech" class="flex items-center px-4 py-2 hover:bg-gray-100">
            <input type="checkbox" :value="tech" v-model="selectedTechnologies" class="mr-2" />
            <span>{{ tech }}</span>
          </div>
        </div>
      </div>

      <!-- Dropdown Organisme -->
      <div class="relative w-full md:w-1/4" ref="orgDropdownRef">
        <button
          @click.stop="orgDropdownOpen = !orgDropdownOpen"
          class="w-full px-4 py-2 border rounded-lg bg-white text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-blue-400"
        >
          <span>
            {{ selectedOrganismes.length === 0 ? 'Filtrer par Organisme' : selectedOrganismes.join(', ') }}
          </span>
          <span>▼</span>
        </button>
        <div v-if="orgDropdownOpen" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-48 overflow-y-auto">
          <div v-for="org in allOrganismes" :key="org" class="flex items-center px-4 py-2 hover:bg-gray-100">
            <input type="checkbox" :value="org" v-model="selectedOrganismes" class="mr-2" />
            <span>{{ org }}</span>
          </div>
        </div>
      </div>

      <!-- Bouton Réinitialiser -->
      <button
        @click="resetFilters"
        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400"
      >
        Réinitialiser les filtres
      </button>
    </div>

    <!-- Tableau des factures -->
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
        <thead class="bg-gray-200">
          <tr>
            <th class="px-4 py-2 border-b text-left">ID</th>
            <th class="px-4 py-2 border-b text-left">Facture</th>
            <th class="px-4 py-2 border-b text-left">Fournisseur</th>
            <th class="px-4 py-2 border-b text-left">Numéro</th>
            <th class="px-4 py-2 border-b text-left">Destination</th>
            <th class="px-4 py-2 border-b text-left">Organisme</th>
            <th class="px-4 py-2 border-b text-left">Technologie</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="f in filteredFactures" :key="f.id" class="hover:bg-gray-50">
            <td class="px-4 py-2 border-b">{{ f.id }}</td>
            <td class="px-4 py-2 border-b">{{ f.facture }}</td>
            <td class="px-4 py-2 border-b">{{ f.provider }}</td>

            <td class="px-4 py-2 border-b">
              <div v-for="n in f.numeros" :key="n.id">{{ n.NDappel }}</div>
            </td>
            <td class="px-4 py-2 border-b">
              <div v-for="n in f.numeros" :key="n.id">{{ n.destination ? n.destination.name : '-' }}</div>
            </td>
            <td class="px-4 py-2 border-b">
              <div v-for="n in f.numeros" :key="n.id">{{ n.organisme ? n.organisme.name : '-' }}</div>
            </td>
            <td class="px-4 py-2 border-b">
              <div v-for="n in f.numeros" :key="n.id">{{ n.technologie ? n.technologie.name : '-' }}</div>
            </td>
          </tr>

          <tr v-if="filteredFactures.length === 0">
            <td colspan="7" class="text-center px-4 py-2 text-gray-500">Aucune facture trouvée.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>