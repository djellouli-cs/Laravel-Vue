<script setup>
import { ref, computed, watch } from 'vue'
import Layout from '@/Layouts/LayoutNetwork.vue'
import { usePage, Head } from '@inertiajs/vue3'
import { Document, Packer, Paragraph, Table, TableRow, TableCell } from 'docx'
import { saveAs } from 'file-saver'

defineOptions({ layout: Layout })

const props = usePage().props
const ipaddresses = ref(props.ipaddresses || [])
const auth = props.auth || {}

const selectedPlage = ref('')
const currentPage = ref(1)
const itemsPerPage = 5

const pingStatuses = ref({})

const uniquePlages = computed(() => {
  const set = new Set()
  ipaddresses.value.forEach(addr => {
    if (addr.plage?.direction) {
      set.add(addr.plage.direction)
    }
  })
  return Array.from(set)
})

const filteredAddresses = computed(() => {
  if (!selectedPlage.value) return ipaddresses.value
  return ipaddresses.value.filter(addr => addr.plage?.direction === selectedPlage.value)
})

const paginatedAddresses = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredAddresses.value.slice(start, end)
})

const totalPages = computed(() => Math.ceil(filteredAddresses.value.length / itemsPerPage) || 1)

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

watch([selectedPlage, filteredAddresses], () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = 1
  }
})

// Export to Word
const exportToWord = () => {
  const rows = filteredAddresses.value.map(user =>
    new TableRow({
      children: [
        new TableCell({ children: [new Paragraph(String(user.organisme ?? ''))] }),
        new TableCell({ children: [new Paragraph(String(user.destination ?? ''))] }),
        new TableCell({ children: [new Paragraph(String(user.plage?.direction ?? ''))] }),
        new TableCell({ children: [new Paragraph(String(user.Application ?? ''))] }),
        new TableCell({ children: [new Paragraph(String(user.ipAdresses ?? ''))] }),
        new TableCell({ children: [new Paragraph(String(user.mask ?? ''))] }),
        new TableCell({ children: [new Paragraph(String(user.port ?? ''))] }),
        new TableCell({ children: [new Paragraph(String(user.note ?? ''))] }),
      ]
    })
  )

  const table = new Table({
    rows: [
      new TableRow({
        children: [
          new TableCell({ children: [new Paragraph('Organisme')] }),
          new TableCell({ children: [new Paragraph('Destination')] }),
          new TableCell({ children: [new Paragraph('Plage')] }),
          new TableCell({ children: [new Paragraph('Application')] }),
          new TableCell({ children: [new Paragraph('Adresse')] }),
          new TableCell({ children: [new Paragraph('Masque')] }),
          new TableCell({ children: [new Paragraph('Port')] }),
          new TableCell({ children: [new Paragraph('Note')] }),
        ]
      }),
      ...rows
    ]
  })

  const doc = new Document({ sections: [{ children: [table] }] })

  Packer.toBlob(doc).then(blob => {
    saveAs(blob, 'plages.docx')
  })
}

// Admin-only ping
const checkPing = async (ip) => {
  if (!ip || auth.user?.role !== 'admin') return
  pingStatuses.value[ip] = 'checking'
  try {
    const response = await fetch(`/ping?ip=${ip}`)
    const data = await response.json()
    pingStatuses.value[ip] = data.reachable ? 'up' : 'down'
  } catch (error) {
    pingStatuses.value[ip] = 'error'
  }
}
</script>

<template>
  <Head title="Adresses IP | Plages" />

  <div class="max-w-7xl mx-auto p-6 space-y-6">
    <!-- Filtrage -->
    <div class="space-y-2 max-w-md">
      <label for="plageFilter" class="block text-sm font-medium text-gray-700">
        Filtrer par Plage :
      </label>
      <select
        id="plageFilter"
        v-model="selectedPlage"
        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
        <option value="">-- Toutes les plages --</option>
        <option v-for="(plage, index) in uniquePlages" :key="index" :value="plage">
          {{ plage }}
        </option>
      </select>
    </div>

    <!-- Résultats -->
    <div class="text-gray-600">
      <span class="text-blue-600 font-semibold">{{ filteredAddresses.length }}</span>
      résultat<span v-if="filteredAddresses.length > 1">s</span> trouvé<span v-if="filteredAddresses.length > 1">s</span>.
    </div>

    <!-- Tableau -->
    <div class="overflow-auto rounded-lg border border-gray-200">
      <table class="min-w-full bg-white text-sm">
        <thead class="bg-gray-50 text-left text-gray-700 uppercase tracking-wider">
          <tr>
            <th class="px-4 py-3">Organisme</th>
            <th class="px-4 py-3">Destination</th>
            <th class="px-4 py-3">Plage</th>
            <th class="px-4 py-3">Application</th>
            <th class="px-4 py-3">Adresse</th>
            <th class="px-4 py-3">Masque</th>
            <th class="px-4 py-3">Port</th>
            <th class="px-4 py-3">Note</th>
            <th class="px-4 py-3" v-if="auth.user && auth.user.role === 'admin'">Ping</th>
            <th class="px-4 py-3" v-if="auth.user && auth.user.role === 'admin'">Statut</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="address in paginatedAddresses"
            :key="address.id"
            class="border-t hover:bg-blue-50"
          >
            <td class="px-4 py-2">{{ address.organisme }}</td>
            <td class="px-4 py-2">{{ address.destination }}</td>
            <td class="px-4 py-2">{{ address.plage?.direction }}</td>
            <td class="px-4 py-2">{{ address.Application }}</td>
            <td class="px-4 py-2">{{ address.ipAdresses }}</td>
            <td class="px-4 py-2">{{ address.mask }}</td>
            <td class="px-4 py-2">{{ address.port }}</td>
            <td class="px-4 py-2">{{ address.note }}</td>
            <td class="px-4 py-2">
              <button
                v-if="auth.user && auth.user.role === 'admin'"
                @click="checkPing(address.ipAdresses)"
                class="text-blue-600 hover:underline"
              >
                Vérifier
              </button>
            </td>
            <td class="px-4 py-2">
              <span v-if="pingStatuses[address.ipAdresses] === 'checking'">⏳</span>
              <span v-else-if="pingStatuses[address.ipAdresses] === 'up'" class="text-green-600">🟢</span>
              <span v-else-if="pingStatuses[address.ipAdresses] === 'down'" class="text-red-600">🔴</span>
              <span v-else-if="pingStatuses[address.ipAdresses] === 'error'" class="text-gray-500">❓</span>
            </td>
          </tr>
          <tr v-if="paginatedAddresses.length === 0">
            <td colspan="10" class="px-4 py-4 text-center text-gray-500">
              Aucun résultat trouvé.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="overflow-x-auto">
      <table class="min-w-full border-collapse border border-gray-200 mt-6">
        <tbody>
          <tr>
            <td>
              <button
                @click="goToPage(currentPage - 1)"
                :disabled="currentPage === 1"
                class="w-full px-3 py-1 border border-gray-300 rounded-l hover:bg-gray-100 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Précédent
              </button>
            </td>

            <td v-for="page in totalPages" :key="page" class="border border-gray-300">
              <button
                @click="goToPage(page)"
                :class="[ 'w-full px-3 py-1 text-center text-sm',
                  currentPage === page
                    ? 'bg-blue-600 text-white font-semibold'
                    : 'bg-white hover:bg-gray-100'
                ]"
              >
                {{ page }}
              </button>
            </td>

            <td>
              <button
                @click="goToPage(currentPage + 1)"
                :disabled="currentPage === totalPages"
                class="w-full px-3 py-1 border border-gray-300 rounded-r hover:bg-gray-100 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Suivant
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Export Word -->
      <button
        @click="exportToWord"
        class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
      >
        Exporter en Word
      </button>
    </div>
  </div>
</template>
