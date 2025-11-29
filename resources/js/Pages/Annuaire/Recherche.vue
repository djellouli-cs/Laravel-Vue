<template>
  <div class="max-w-5xl mx-auto p-6 bg-white shadow-md rounded-md">
    <h1 class="text-2xl font-semibold mb-6 text-gray-800">Filtrer les Numéros</h1>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
      <!-- Organisme -->
      <div>
        <label class="block mb-1 font-medium text-gray-700">Organisme</label>
        <select v-model="selectedOrganismes" multiple
          class="w-full border rounded-lg px-3 py-2 h-32 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="org in filteredOrganismesList" :key="org.id" :value="org.id">
            {{ org.name }}
          </option>
        </select>
        <small class="text-gray-500">(Ctrl+Click pour sélectionner plusieurs)</small>
      </div>

      <!-- Destination -->
      <div>
        <label class="block mb-1 font-medium text-gray-700">Destination</label>
        <select v-model="selectedDestinations" multiple
          class="w-full border rounded-lg px-3 py-2 h-32 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="dest in filteredDestinationsList" :key="dest.id" :value="dest.id">
            {{ dest.name }}
          </option>
        </select>
        <small class="text-gray-500">(Ctrl+Click pour sélectionner plusieurs)</small>
      </div>

      <!-- Service -->
      <div>
        <label class="block mb-1 font-medium text-gray-700">Service</label>
        <select v-model="selectedServices" multiple
          class="w-full border rounded-lg px-3 py-2 h-32 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="srv in filteredServicesList" :key="srv.id" :value="srv.id">
            {{ srv.name }}
          </option>
        </select>
        <small class="text-gray-500">(Ctrl+Click pour sélectionner plusieurs)</small>
      </div>

      <!-- Technologie -->
      <div>
        <label class="block mb-1 font-medium text-gray-700">Technologie</label>
        <select v-model="selectedTechnologies" multiple
          class="w-full border rounded-lg px-3 py-2 h-32 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="tech in filteredTechnologiesList" :key="tech.id" :value="tech.id">
            {{ tech.name }}
          </option>
        </select>
        <small class="text-gray-500">(Ctrl+Click pour sélectionner plusieurs)</small>
      </div>

      <!-- Groupe -->
      <div>
        <label class="block mb-1 font-medium text-gray-700">Groupe</label>
        <select v-model="selectedGroupes" multiple
          class="w-full border rounded-lg px-3 py-2 h-32 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          <option v-for="groupe in filteredGroupesList" :key="groupe.id" :value="groupe.id">
            {{ groupe.groupes }}
          </option>
        </select>
        <small class="text-gray-500">(Ctrl+Click pour sélectionner plusieurs)</small>
      </div>
    </div>

    <!-- Column Selection -->
    <div class="mb-4">
      <h3 class="text-lg font-semibold text-gray-700">Choisir les Colonnes à Exporter</h3>
      <div class="space-y-2" @dragover.prevent @drop="onDrop">
        <div v-for="(column, index) in draggableColumns" :key="column.id"
          class="flex items-center gap-2 cursor-move" draggable="true"
          @dragstart="startDrag(index)" @dragend="onDragEnd" :data-index="index">
          <input type="checkbox" v-model="exportColumns[column.id]" :id="column.id" />
          <label :for="column.id">{{ column.name }}</label>
        </div>
      </div>
    </div>

    <div class="mb-4 text-right flex gap-3 justify-end">
      <button @click="toggleSort"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
        {{ sortOrder === 'asc' ? '↑' : '↓' }} Trier par Matricule
      </button>
      <button @click="resetFilters"
        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition duration-200">
        Annuler Filtrage
      </button>
      <button @click="exportToWord"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
        Exporter en Word
      </button>
    </div>

    <!-- Table -->
    <table class="min-w-full border border-collapse text-sm table-auto">
      <thead>
        <tr class="bg-gray-100 text-gray-700">
          <th v-if="exportColumns.NDappel" class="border px-2 py-1 text-left"
            @dragstart="startDragHeader('NDappel')" draggable="true">{{ draggableColumns[0].name }}</th>
          <th v-if="exportColumns.organisme" class="border px-2 py-1 text-left"
            @dragstart="startDragHeader('organisme')" draggable="true">{{ draggableColumns[1].name }}</th>
          <th v-if="exportColumns.destination" class="border px-2 py-1 text-left"
            @dragstart="startDragHeader('destination')" draggable="true">{{ draggableColumns[2].name }}</th>
          <th v-if="exportColumns.service" class="border px-2 py-1 text-left"
            @dragstart="startDragHeader('service')" draggable="true">{{ draggableColumns[3].name }}</th>
          <th v-if="exportColumns.technologie" class="border px-2 py-1 text-left"
            @dragstart="startDragHeader('technologie')" draggable="true">{{ draggableColumns[4].name }}</th>
          <th v-if="exportColumns.groupe" class="border px-2 py-1 text-left"
            @dragstart="startDragHeader('groupe')" draggable="true">{{ draggableColumns[5].name }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="num in filteredNumeros" :key="num.id"
          class="hover:bg-gray-50 even:bg-gray-100">
          <td v-if="exportColumns.NDappel" class="border px-2 py-1">{{ num.NDappel }}</td>
          <td v-if="exportColumns.organisme" class="border px-2 py-1">{{ num.organisme?.name || '—' }}</td>
          <td v-if="exportColumns.destination" class="border px-2 py-1">{{ num.destination?.name || '—' }}</td>
          <td v-if="exportColumns.service" class="border px-2 py-1">{{ num.service?.name || '—' }}</td>
          <td v-if="exportColumns.technologie" class="border px-2 py-1">{{ num.technologie?.name || '—' }}</td>
          <td v-if="exportColumns.groupe" class="border px-2 py-1">
            <div v-if="num.destination?.groupes && num.destination.groupes.length > 0" class="flex flex-wrap gap-1">
              <span v-for="groupe in num.destination.groupes" :key="groupe.id"
                class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
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

const exportColumns = ref({
  NDappel: true,
  organisme: true,
  destination: true,
  service: true,
  technologie: true,
  groupe: true
})

const sortOrder = ref('asc')

const draggableColumns = ref([
  { id: 'NDappel', name: 'Numéro d\'Appel' },
  { id: 'organisme', name: 'Organisme' },
  { id: 'destination', name: 'Destination' },
  { id: 'service', name: 'Service' },
  { id: 'technologie', name: 'Technologie' },
  { id: 'groupe', name: 'Groupe' }
])

// --- Cross-filter lists ---
const filteredNumeros = computed(() => {
  return props.numeros
    .filter(num => selectedOrganismes.value.length === 0 || selectedOrganismes.value.includes(num.organisme?.id))
    .filter(num => selectedDestinations.value.length === 0 || selectedDestinations.value.includes(num.destination?.id))
    .filter(num => selectedServices.value.length === 0 || selectedServices.value.includes(num.service?.id))
    .filter(num => selectedTechnologies.value.length === 0 || selectedTechnologies.value.includes(num.technologie?.id))
    .filter(num => selectedGroupes.value.length === 0 || num.destination?.groupes?.some(g => selectedGroupes.value.includes(g.id)))
})

const filteredOrganismesList = computed(() => {
  return props.organismes.filter(org =>
    filteredNumeros.value.some(num => num.organisme?.id === org.id)
  )
})
const filteredDestinationsList = computed(() => {
  return props.destinations.filter(dest =>
    filteredNumeros.value.some(num => num.destination?.id === dest.id)
  )
})
const filteredServicesList = computed(() => {
  return props.services.filter(srv =>
    filteredNumeros.value.some(num => num.service?.id === srv.id)
  )
})
const filteredTechnologiesList = computed(() => {
  return props.technologies.filter(tech =>
    filteredNumeros.value.some(num => num.technologie?.id === tech.id)
  )
})
const filteredGroupesList = computed(() => {
  return props.groupes.filter(groupe =>
    filteredNumeros.value.some(num => num.destination?.groupes?.some(g => g.id === groupe.id))
  )
})

// --- Sorting ---
const toggleSort = () => {
  sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  filteredNumeros.value.sort((a, b) => {
    const valA = a.NDappel
    const valB = b.NDappel
    if (sortOrder.value === 'asc') {
      return valA > valB ? 1 : -1
    } else {
      return valA < valB ? 1 : -1
    }
  })
}

// --- Reset filters ---
const resetFilters = () => {
  selectedOrganismes.value = []
  selectedDestinations.value = []
  selectedServices.value = []
  selectedTechnologies.value = []
  selectedGroupes.value = []
}

// --- Drag/drop ---
let dragIndex = null
const startDrag = (index) => { dragIndex = index }
const onDrop = (event) => {
  const draggedColumn = draggableColumns.value[dragIndex]
  const targetIndex = Number(event.target.dataset.index)
  if (draggedColumn && targetIndex !== dragIndex) {
    draggableColumns.value.splice(dragIndex, 1)
    draggableColumns.value.splice(targetIndex, 0, draggedColumn)
  }
}

// --- Export to Word ---
const exportToWord = () => {
  let htmlContent = `
  <html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:w="urn:schemas-microsoft-com:office:word"
        xmlns="http://www.w3.org/TR/REC-html40">
    <head><meta charset="utf-8"><title>Export</title></head>
    <body>
      <h2>Liste des Numéros Filtrés</h2>
      <table border="1" style="border-collapse:collapse;width:100%;font-family:sans-serif;">
        <thead>
          <tr style="background-color:#f0f0f0;">
            ${draggableColumns.value.map(column => exportColumns.value[column.id] ? `<th>${column.name}</th>` : '').join('')}
          </tr>
        </thead>
        <tbody>
          ${filteredNumeros.value.map(num => `
            <tr>
              ${draggableColumns.value.map(column => {
                if (exportColumns.value[column.id]) {
                  let cellData = num[column.id] || '—'
                  if (typeof cellData === 'object' && cellData !== null) {
                    if (cellData.name) {
                      cellData = cellData.name
                    } else if (Array.isArray(cellData)) {
                      cellData = cellData.map(g => g.groupes).join(', ') || '—'
                    }
                  }
                  return `<td>${cellData}</td>`
                }
                return ''
              }).join('')}
            </tr>
          `).join('')}
        </tbody>
      </table>
    </body>
  </html>`
  const blob = new Blob([htmlContent], { type: 'application/msword' })
  const link = document.createElement('a')
  link.href = URL.createObjectURL(blob)
  link.download = 'exported_data.doc'
  link.click()
}
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}
th, td { padding: 10px; text-align: left; }
tr:hover { background-color: #f9fafb; }
</style>
