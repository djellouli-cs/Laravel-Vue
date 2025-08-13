<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Toutes les adresses</h1>
    <button @click="startAdd" class="bg-green-600 text-white px-4 py-2 rounded mb-4">Ajouter une adresse</button>

    <!-- Barre de recherche -->
    <div class="mb-4">
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Rechercher IP, Organisme, Destination, Application..."
        class="w-full border rounded px-2 py-1"
      />
    </div>

    <div class="text-gray-600">
      <span class="text-blue-600 font-semibold">{{ filteredIpAddresses.length }}</span>
      résultat<span v-if="filteredIpAddresses.length > 1">s</span> trouvé<span v-if="filteredIpAddresses.length > 1">s</span>.
    </div>

    <!-- Tableau -->
    <table class="w-full table-auto border-collapse border border-gray-300">
      <thead class="bg-gray-100">
        <tr>
          <th class="border px-4 py-2 text-left">Adresse IP</th>
          <th class="border px-4 py-2 text-left">Organisme</th>
          <th class="border px-4 py-2 text-left">Destination</th>
          <th class="border px-4 py-2 text-left">Application</th>
          <th class="border px-4 py-2 text-left">Actions</th>
          <th class="border px-4 py-2 text-left">Ping</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="ip in paginatedIpAddresses" :key="ip.id" class="hover:bg-gray-50">
          <td class="border px-4 py-2">{{ ip.ipAdresses }}</td>
          <td class="border px-4 py-2">{{ ip.organisme }}</td>
          <td class="border px-4 py-2">{{ ip.destination }}</td>
          <td class="border px-4 py-2">{{ ip.Application }}</td>
          <td class="border px-4 py-2 space-x-2">
            <button @click="edit(ip)" class="text-blue-600 hover:underline">Modifier</button>
            <button @click="destroy(ip)" class="text-red-600 hover:underline">Supprimer</button>
          </td>
          <td class="border px-4 py-2">
            <button @click="checkPing(ip.ipAdresses)" class="text-sm text-blue-700 hover:underline">Vérifier</button>
            <span v-if="pingStatuses[ip.ipAdresses] === 'checking'">⏳</span>
            <span v-else-if="pingStatuses[ip.ipAdresses] === 'up'">🟢</span>
            <span v-else-if="pingStatuses[ip.ipAdresses] === 'down'">🔴</span>
            <span v-else-if="pingStatuses[ip.ipAdresses] === 'error'">❓</span>
          </td>
        </tr>

        <!-- Formulaire d'édition -->
        <tr v-if="editingId !== null">
          <td colspan="6" class="border px-4 py-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium">Adresse IP</label>
                <input v-model="form.ipAdresses" class="w-full border rounded px-2 py-1" />
              </div>
              <div>
                <label class="block text-sm font-medium">Organisme</label>
                <input v-model="form.organisme" class="w-full border rounded px-2 py-1" />
              </div>
              <div>
                <label class="block text-sm font-medium">Destination</label>
                <input v-model="form.destination" class="w-full border rounded px-2 py-1" />
              </div>
              <div>
                <label class="block text-sm font-medium">Application</label>
                <input v-model="form.Application" class="w-full border rounded px-2 py-1" />
              </div>
              <div>
                <label class="block text-sm font-medium">Port</label>
                <input v-model="form.port" class="w-full border rounded px-2 py-1" />
              </div>
              <div>
                <label class="block text-sm font-medium">Masque</label>
                <input v-model="form.mask" class="w-full border rounded px-2 py-1" />
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium">Note</label>
                <textarea v-model="form.note" class="w-full border rounded px-2 py-1"></textarea>
              </div>
              <div class="col-span-2 flex gap-4">
                <button @click="save" class="bg-green-600 text-white px-4 py-2 rounded">Enregistrer</button>
                <button @click="add" class="bg-green-600 text-white px-4 py-2 rounded">Ajouter</button>
                <button @click="cancelEdit" class="bg-gray-400 text-white px-4 py-2 rounded">Annuler</button>
              </div>
            </div>
          </td>
        </tr>

        <!-- Formulaire d'ajout -->
        <tr v-if="showAddForm">
          <td colspan="6" class="border px-4 py-4 bg-green-50">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium">Adresse IP</label>
                <input v-model="form.ipAdresses" class="w-full border rounded px-2 py-1" />
              </div>
              <div>
                <label class="block text-sm font-medium">Organisme</label>
                <input v-model="form.organisme" class="w-full border rounded px-2 py-1" />
              </div>
              <div>
                <label class="block text-sm font-medium">Destination</label>
                <input v-model="form.destination" class="w-full border rounded px-2 py-1" />
              </div>
              <div>
                <label class="block text-sm font-medium">Application</label>
                <input v-model="form.Application" class="w-full border rounded px-2 py-1" />
              </div>
              <div>
                <label class="block text-sm font-medium">Port</label>
                <input v-model="form.port" class="w-full border rounded px-2 py-1" />
              </div>
              <div>
                <label class="block text-sm font-medium">Masque</label>
                <input v-model="form.mask" class="w-full border rounded px-2 py-1" />
              </div>
              <div class="col-span-2">
                <label class="block text-sm font-medium">Note</label>
                <textarea v-model="form.note" class="w-full border rounded px-2 py-1"></textarea>
              </div>
              <div class="col-span-2 flex gap-4">
                <button @click="add" class="bg-green-600 text-white px-4 py-2 rounded">Ajouter</button>
                <button @click="cancelAdd" class="bg-gray-400 text-white px-4 py-2 rounded">Annuler</button>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination améliorée -->
    <div class="mt-6 flex flex-wrap justify-center gap-1 text-sm">
      <button
        @click="changePage(currentPage - 1)"
        :disabled="currentPage === 1"
        class="px-3 py-1 border border-gray-300 rounded disabled:opacity-50"
      >
        Précédent
      </button>

      <button
        v-for="page in visiblePages"
        :key="page"
        @click="changePage(page)"
        :class="[
          'px-3 py-1 border border-gray-300 rounded',
          page === currentPage ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-gray-100'
        ]"
      >
        {{ page }}
      </button>

      <button
        @click="changePage(currentPage + 1)"
        :disabled="currentPage === totalPages"
        class="px-3 py-1 border border-gray-300 rounded disabled:opacity-50"
      >
        Suivant
      </button>
    </div>
  </div>
</template>

<script setup>
import Layout from '@/Layouts/LayoutNetwork.vue'
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: Layout })

const props = defineProps({
  ipaddresses: {
    type: Array,
    required: true,
  },
})

const currentPage = ref(1)
const itemsPerPage = 5
const searchQuery = ref('')
const editingId = ref(null)
const showAddForm = ref(false)

const form = ref({
  ipAdresses: '',
  organisme: '',
  destination: '',
  Application: '',
  port: '',
  mask: '',
  note: '',
})

const filteredIpAddresses = computed(() => {
  if (!searchQuery.value) return props.ipaddresses
  return props.ipaddresses.filter(ip => {
    return (
      ip.ipAdresses.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      ip.organisme.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      ip.destination.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      ip.Application.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  })
})

const totalPages = computed(() => Math.ceil(filteredIpAddresses.value.length / itemsPerPage))

const paginatedIpAddresses = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredIpAddresses.value.slice(start, start + itemsPerPage)
})

const changePage = (page) => {
  if (page < 1 || page > totalPages.value) return
  currentPage.value = page
}

const edit = (ip) => {
  editingId.value = ip.id
  form.value = { ...ip }
}

const cancelEdit = () => {
  editingId.value = null
  resetForm()
}

const save = () => {
  router.put(route('Address.update', editingId.value), form.value, {
    onSuccess: () => editingId.value = null,
  })
}

const destroy = (ip) => {
  if (confirm(`Are you sure you want to delete IP ${ip.ipAdresses}?`)) {
    router.delete(route('Address.destroy', ip.id))
  }
}

const startAdd = () => {
  showAddForm.value = true
  editingId.value = null
  resetForm()
}

const cancelAdd = () => {
  showAddForm.value = false
  resetForm()
}

const add = () => {
  router.post(route('Address.store'), form.value, {
    onSuccess: () => cancelAdd(),
  })
}

const resetForm = () => {
  form.value = {
    ipAdresses: '',
    organisme: '',
    destination: '',
    Application: '',
    port: '',
    mask: '',
    note: '',
  }
}

const visiblePages = computed(() => {
  const pages = []
  const maxVisible = 7
  if (totalPages.value <= maxVisible) {
    for (let i = 1; i <= totalPages.value; i++) pages.push(i)
  } else {
    if (currentPage.value <= 4) {
      pages.push(...[1, 2, 3, 4, 5, '...', totalPages.value])
    } else if (currentPage.value >= totalPages.value - 3) {
      pages.push(1, '...')
      for (let i = totalPages.value - 4; i <= totalPages.value; i++) pages.push(i)
    } else {
      pages.push(1, '...', currentPage.value - 1, currentPage.value, currentPage.value + 1, '...', totalPages.value)
    }
  }
  return pages
})

// ✅ PING Feature
const pingStatuses = ref({})

const checkPing = async (ip) => {
  if (!ip) return
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
