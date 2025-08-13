<template>
  <div class="max-w-5xl mx-auto p-6 bg-white shadow rounded-md">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Filtrer les Numéros</h1>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
      <!-- Organisme -->
      <div>
        <label class="block mb-1 font-semibold text-gray-700">Organisme</label>
        <select v-model="selectedOrganismes" multiple
                class="w-full border rounded px-3 py-2 h-32 shadow-sm overflow-y-auto focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="org in organismes" :key="org.id" :value="org.id">{{ org.name }}</option>
        </select>
        <small class="text-gray-500">(Ctrl+Click ou Cmd+Click pour sélectionner plusieurs)</small>
      </div>

      <!-- Destination -->
      <div>
        <label class="block mb-1 font-semibold text-gray-700">Destination</label>
        <select v-model="selectedDestinations" multiple
                class="w-full border rounded px-3 py-2 h-32 shadow-sm overflow-y-auto focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="dest in destinations" :key="dest.id" :value="dest.id">{{ dest.name }}</option>
        </select>
        <small class="text-gray-500">(Ctrl+Click ou Cmd+Click pour sélectionner plusieurs)</small>
      </div>

      <!-- Service -->
      <div>
        <label class="block mb-1 font-semibold text-gray-700">Service</label>
        <select v-model="selectedServices" multiple
                class="w-full border rounded px-3 py-2 h-32 shadow-sm overflow-y-auto focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="srv in services" :key="srv.id" :value="srv.id">{{ srv.name }}</option>
        </select>
        <small class="text-gray-500">(Ctrl+Click ou Cmd+Click pour sélectionner plusieurs)</small>
      </div>

      <!-- Technologie -->
      <div>
        <label class="block mb-1 font-semibold text-gray-700">Technologie</label>
        <select v-model="selectedTechnologies" multiple
                class="w-full border rounded px-3 py-2 h-32 shadow-sm overflow-y-auto focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="tech in technologies" :key="tech.id" :value="tech.id">{{ tech.name }}</option>
        </select>
        <small class="text-gray-500">(Ctrl+Click ou Cmd+Click pour sélectionner plusieurs)</small>
      </div>

      <!-- Groupe -->
      <div>
        <label class="block mb-1 font-semibold text-gray-700">Groupe</label>
        <select v-model="selectedGroupes" multiple
                class="w-full border rounded px-3 py-2 h-32 shadow-sm overflow-y-auto focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="groupe in groupes" :key="groupe.id" :value="groupe.id">{{ groupe.groupes }}</option>
        </select>
        <small class="text-gray-500">(Ctrl+Click ou Cmd+Click pour sélectionner plusieurs)</small>
      </div>
    </div>

    <div class="mb-4 text-right flex gap-3 justify-end">
      <button @click="toggleSort"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
        {{ sortOrder === 'asc' ? '↑' : '↓' }} Trier par Matricule
      </button>
      <button @click="resetFilters"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
        Annuler Filtrage
      </button>
      <button @click="exportToWord"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
        Exporter en Word
      </button>
    </div>

    <table class="min-w-full border border-collapse text-sm table-auto">
      <thead>
        <tr class="bg-gray-100 text-gray-700">
          <th class="border px-2 py-1 text-left">NDappel</th>
          <th class="border px-2 py-1 text-left">Organisme</th>
          <th class="border px-2 py-1 text-left">Destination</th>
          <th class="border px-2 py-1 text-left">Service</th>
          <th class="border px-2 py-1 text-left">Technologie</th>
          <th class="border px-2 py-1 text-left">Matricule</th>
          <th class="border px-2 py-1 text-left">Groupe</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="num in filteredNumeros" :key="num.id" class="hover:bg-gray-50 even:bg-gray-100">
          <td class="border px-2 py-1">{{ num.NDappel }}</td>
          <td class="border px-2 py-1">{{ num.organisme?.name || '—' }}</td>
          <td class="border px-2 py-1">{{ num.destination?.name || '—' }}</td>
          <td class="border px-2 py-1">{{ num.service?.name || '—' }}</td>
          <td class="border px-2 py-1">{{ num.technologie?.name || '—' }}</td>
          <td class="border px-2 py-1">{{ num.matricule?.matricule || '—' }}</td>
          <td class="border px-2 py-1">
            <div v-if="num.destination?.groupes && num.destination.groupes.length > 0" class="flex flex-wrap gap-1">
              <span 
                v-for="groupe in num.destination.groupes" 
                :key="groupe.id"
                class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
              >
                {{ groupe.groupes }}
              </span>
            </div>
            <span v-else class="text-gray-400 text-sm">—</span>
          </td>
        </tr>
        <tr v-if="filteredNumeros.length === 0">
          <td colspan="7" class="text-center text-gray-500 py-2">Aucun résultat.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import Layout from '@/Layouts/LayoutAnnuaire.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  numeros: Array,
  organismes: Array,
  destinations: Array,
  services: Array,
  technologies: Array,
  groupes: Array
})

const selectedOrganismes = ref([])
const selectedDestinations = ref([])
const selectedServices = ref([])
const selectedTechnologies = ref([])
const selectedGroupes = ref([])
const sortOrder = ref('asc') // 'asc' or 'desc'

function resetFilters() {
  selectedOrganismes.value = []
  selectedDestinations.value = []
  selectedServices.value = []
  selectedTechnologies.value = []
  selectedGroupes.value = []
}

function toggleSort() {
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
}

const filteredNumeros = computed(() => {
  const filtered = props.numeros.filter(num => {
    const matchOrg = selectedOrganismes.value.length === 0 || (num.organisme && selectedOrganismes.value.includes(num.organisme.id))
    const matchDest = selectedDestinations.value.length === 0 || (num.destination && selectedDestinations.value.includes(num.destination.id))
    const matchSrv = selectedServices.value.length === 0 || (num.service && selectedServices.value.includes(num.service.id))
    const matchTech = selectedTechnologies.value.length === 0 || (num.technologie && selectedTechnologies.value.includes(num.technologie.id))
    const matchGroupe = selectedGroupes.value.length === 0 || (num.destination?.groupes && num.destination.groupes.some(g => selectedGroupes.value.includes(g.id)))
    return matchOrg && matchDest && matchSrv && matchTech && matchGroupe
  })
  
  // Sort by matricule (numeric sorting)
  return filtered.sort((a, b) => {
    const matriculeA = Number(a.matricule?.matricule) || 0
    const matriculeB = Number(b.matricule?.matricule) || 0
    
    if (sortOrder.value === 'asc') {
      return matriculeA - matriculeB
    } else {
      return matriculeB - matriculeA
    }
  })
})

function exportToWord() {
  let html = `
    <html xmlns:o='urn:schemas-microsoft-com:office:office'
          xmlns:w='urn:schemas-microsoft-com:office:word'
          xmlns='http://www.w3.org/TR/REC-html40'>
    <head><meta charset='utf-8'><title>Export</title></head><body>
    <h2>Liste des Numéros Filtrés</h2>
    <table border="1" style="border-collapse:collapse;width:100%;font-family:sans-serif;">
      <thead>
        <tr style="background:#f0f0f0;">
          <th>NDappel</th>
          <th>Organisme</th>
          <th>Destination</th>
          <th>Service</th>
          <th>Technologie</th>
          <th>Groupe</th>
        </tr>
      </thead>
      <tbody>
  `

  for (const num of filteredNumeros.value) {
    html += `
      <tr>
        <td>${num.NDappel}</td>
        <td>${num.organisme?.name || '—'}</td>
        <td>${num.destination?.name || '—'}</td>
        <td>${num.service?.name || '—'}</td>
        <td>${num.technologie?.name || '—'}</td>
        <td>${num.destination?.groupes ? num.destination.groupes.map(g => g.groupes).join(', ') : '—'}</td>
      </tr>
    `
  }

  html += `
      </tbody>
    </table>
    </body></html>
  `

  const blob = new Blob(['\ufeff', html], {
    type: 'application/msword',
  })

  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = 'numeros_filtrés.doc'
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}
</script>
