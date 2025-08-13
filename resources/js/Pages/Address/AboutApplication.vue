<script setup>
import { ref, computed, watch } from 'vue'
import { usePage, Head } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutNetwork.vue'
import { Document, Packer, Paragraph, Table, TableRow, TableCell } from 'docx'
import { saveAs } from 'file-saver'

defineOptions({ layout: Layout })

const props = usePage().props
const addresses = ref(props.ipaddresses || [])

// Filtering by Application (free text, contains)
const Application = ref('')

// Pagination
const currentPage = ref(1)
const itemsPerPage = 5

// Unique Applications for info or future use
const uniqueApplications = computed(() => {
  const list = addresses.value.map(item => item.Application || 'N/A')
  return [...new Set(list)].sort()
})

// Filter addresses where Application contains the search string (case-insensitive)
const filteredAddresses = computed(() => {
  if (!Application.value) return addresses.value
  return addresses.value.filter(user =>
    (user.Application || '').toLowerCase().includes(Application.value.toLowerCase())
  )
})

// Pagination slice
const paginatedAddresses = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  const end = start + itemsPerPage
  return filteredAddresses.value.slice(start, end)
})

const totalPages = computed(() =>
  Math.ceil(filteredAddresses.value.length / itemsPerPage) || 1
)

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

// Reset page to 1 if filtering changes and current page is invalid
watch([Application, filteredAddresses], () => {
  if (currentPage.value > totalPages.value) {
    currentPage.value = 1
  }
})

// Export filtered data to Word document
const exportToWord = () => {
  const rows = filteredAddresses.value.map(user =>
    new TableRow({
      children: [
        new TableCell({ children: [new Paragraph(String(user.organisme ?? ''))] }),
        new TableCell({ children: [new Paragraph(String(user.destination ?? ''))] }),
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
    saveAs(blob, 'ip_addresses.docx')
  })
}
</script>

<template>
  <Head title="Adresses IP | Application" />

  <div class="max-w-7xl mx-auto p-6 space-y-6">
    <!-- Filter -->
    <div class="space-y-2 max-w-md">
      <label for="Application" class="block text-sm font-medium text-gray-700">
        Filtrer par Application :
      </label>
      <input
        id="Application"
        v-model="Application"
        type="text"
        placeholder="Tapez pour filtrer par Application..."
        class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      />
    </div>

    <!-- Results Count -->
    <div class="text-gray-600">
      <span class="text-blue-600 font-semibold">{{ filteredAddresses.length }}</span>
      résultat<span v-if="filteredAddresses.length > 1">s</span> trouvé<span v-if="filteredAddresses.length > 1">s</span>.
    </div>

    <!-- Table -->
    <div class="overflow-auto rounded-lg border border-gray-200">
      <table class="min-w-full bg-white text-sm">
        <thead class="bg-gray-50 text-left text-gray-700 uppercase tracking-wider">
          <tr>
            <th class="px-4 py-3">Organisme</th>
            <th class="px-4 py-3">Destination</th>
            <th class="px-4 py-3">Application</th>
            <th class="px-4 py-3">Adresse</th>
            <th class="px-4 py-3">Masque</th>
            <th class="px-4 py-3">Port</th>
            <th class="px-4 py-3">Note</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in paginatedAddresses"
            :key="user.id"
            class="border-t hover:bg-blue-50"
          >
            <td class="px-4 py-2">{{ user.organisme }}</td>
            <td class="px-4 py-2">{{ user.destination }}</td>
            <td class="px-4 py-2">{{ user.Application }}</td>
            <td class="px-4 py-2">{{ user.ipAdresses }}</td>
            <td class="px-4 py-2">{{ user.mask }}</td>
            <td class="px-4 py-2">{{ user.port }}</td>
            <td class="px-4 py-2">{{ user.note }}</td>
          </tr>
          <tr v-if="paginatedAddresses.length === 0">
            <td colspan="7" class="px-4 py-4 text-center text-gray-500">
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

      <!-- Export Button -->
      <button
        @click="exportToWord"
        class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
      >
        Exporter en Word
      </button>
    </div>
  </div>
</template>
