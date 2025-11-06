<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Acheminement Manager</h1>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Search by acheminement, NDappel, or description..."
        class="border rounded px-3 py-2 w-full"
      />
    </div>

    <!-- Flash message -->
    <div v-if="flash.success" class="mb-4 text-green-600">
      {{ flash.success }}
    </div>

    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium mb-1">Numero</label>
        <select v-model="form.numero_id" class="border rounded px-3 py-2 w-full" required>
          <option value="">-- Select Numero --</option>
          <option v-for="numero in filteredNumeros" :key="numero.id" :value="numero.id">
            {{ numero.NDappel }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Acheminement</label>

        <input
          v-model="form.acheminement"
          type="text"
          class="border rounded px-3 py-2 w-full"
          list="f"
          placeholder="Ex: SWD 1 10"
          required
        />

        <datalist id="f">
          <option>SWD</option>
          <option>DIVERS</option>
          <option>EX PTT</option>
          <option>RESIDENCE</option>
          <option>AVAYA</option>
          <option>DAL G</option>
          <option>DAL D</option>
          <option>RESEAU</option>
          <option>DRAG</option>
          <option>NORSTAR</option>
          <option>ADM</option>
          <option>SECT</option>
          <option>IP 10.32.1.130</option>
          <option>IP 10.32.2.130</option>
          <option>IP 10.32.3.130</option>
          <option>IP 10.32.4.130</option>
          <option>IP 10.32.5.130</option>
          <option>IP 10.32.6.130</option>
          <option>IP 10.32.7.130</option>
          <option>IP 10.32.8.130</option>
        </datalist>

        <!-- Live result message -->
        <p v-if="calcAcheminement" class="mt-1 text-sm"
           :class="calcAcheminement.includes('Bravo') ? 'text-green-600' : 'text-red-600'">
          {{ calcAcheminement }}
        </p>
      </div>

      <!-- Description -->
      <div class="col-span-full">
        <label class="block text-sm font-medium mb-1">Description</label>
        <input v-model="form.description" type="text" class="border rounded px-3 py-2 w-full" placeholder="Optional description" />
      </div>

      <div class="col-span-full flex space-x-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">
          {{ form.id ? 'Update' : 'Create' }}
        </button>
        <button type="button" @click="reset" class="bg-gray-300 px-4 py-1 rounded">
          Cancel
        </button>
      </div>
    </form>

    <!-- Table -->
    <table class="w-full border text-sm border-collapse">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Numero</th>
          <th class="border px-2 py-1">Acheminement</th>
          <th class="border px-2 py-1">Description</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in paginatedAcheminements" :key="item.id">
          <td class="border px-2 py-1">{{ item.id }}</td>
          <td class="border px-2 py-1">{{ item.numero?.NDappel || '—' }}</td>
          <td class="border px-2 py-1">{{ item.acheminement }}</td>
          <td class="border px-2 py-1">{{ item.description || '—' }}</td>
          <td class="border px-2 py-1 space-x-2">
            <button @click="edit(item)" class="text-blue-600 hover:underline">Edit</button>
            <button @click="destroy(item.id)" class="text-red-600 hover:underline">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center gap-4 items-center">
      <button @click="currentPage--" :disabled="currentPage === 1" class="px-3 py-1 border rounded">
        Prev
      </button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
      <button @click="currentPage++" :disabled="currentPage === totalPages" class="px-3 py-1 border rounded">
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
  acheminements: Array,
  numeros: Array,
})

const flash = usePage().props.flash || {}

const form = ref({ id: null, numero_id: '', acheminement: '', description: '' })

const search = ref('')
const currentPage = ref(1)
const itemsPerPage = 5

/* ✅ Acheminement live validation */
const calcAcheminement = computed(() => {
  const a = form.value.acheminement
  if (!a) return ""

  /* EX PTT */
  if (a.startsWith("EX ")) {
    let d = a.length - 1
    if (a[d] === " " && d >= 8) return "il faut pas terminé avec espace. 😤"
    if (a === "EX PTT") return "Ajouter un espace svp. 😇"
    if (a === "EX PTT ") return "Ajouter la Position svp. 😇"
    if (a.length <= 10 && a.startsWith("EX PTT ") && Number(a.substr(7, 3)) && parseInt(a.substr(7, 3)) <= 300)
      return "Bravo 😁"
    return "Faute 😡"
  }

  /* SWD + ADM (same logic) */
if (a.startsWith("SWD") || a.startsWith("ADM")) {
  let d = a.length - 1
  if (a[d] === " " && d >= 7) return "il faut pas terminé avec espace. 😤"
  if (a === "SWD" || a === "ADM") return "Ajouter un espace svp. 😇"
  if (a === "SWD " || a === "ADM ") return "Ajouter la Reglette svp. 😇"
  if (a.length <= 5 && (a.startsWith("SWD ") || a.startsWith("ADM ")) && Number(a.substr(4, 1)) && parseInt(a.substr(4, 1)) <= 8)
    return "Ajouter un espace svp. 😇"
  if (a.length <= 6 && (a.startsWith("SWD ") || a.startsWith("ADM ")) && Number(a.substr(4, 1)) && parseInt(a.substr(4, 1)) <= 8 && a.substr(5, 1) === " ")
    return "Ajouter la Position svp. 😇"
  if (a.length <= 8 && (a.startsWith("SWD ") || a.startsWith("ADM ")) && Number(a.substr(4, 1)) && parseInt(a.substr(4, 1)) <= 8 && a.substr(5, 1) === " " && Number(a.substr(6, 2)) && parseInt(a.substr(6, 2)) <= 25)
    return "Bravo 😁"
  return "Faute 😡"
}

  /* DIVERS */
  if (a.startsWith("DIV")) {
    let d = a.length - 1
    if (a[d] === " " && d >= 10) return "il faut pas terminé avec espace. 😤"
    if (a === "DIVERS") return "Ajouter un espace svp. 😇"
    if (a === "DIVERS ") return "Ajouter la Reglette svp. 😇"
    if (a.length <= 8 && a.startsWith("DIVERS ") && Number(a.substr(7, 1)) && parseInt(a.substr(7, 1)) <= 9)
      return "Ajouter un espace svp. 😇"
    if (a.length <= 9 && a.startsWith("DIVERS ") && Number(a.substr(7, 1)) && parseInt(a.substr(7, 1)) <= 9 && a.substr(8, 1) === " ")
      return "Ajouter la Position svp. 😇"
    if (a.length <= 11 && a.startsWith("DIVERS ") && Number(a.substr(7, 1)) && parseInt(a.substr(7, 1)) <= 10 && a.substr(8, 1) === " " && Number(a.substr(9, 2)) && parseInt(a.substr(9, 2)) <= 25)
      return "Bravo 😁"
    return "Faute 😡"
  }

  /* DAL G */
  if (a.startsWith("DAL G")) {
    if (a === "DAL G") return "Ajouter un espace svp. 😇"
    if (a === "DAL G ") return "Ajouter la Reglette1 svp. 😇"
    if (a.length === 7 && a.startsWith("DAL G ") && Number(a.substr(6, 1)) && parseInt(a.substr(6, 1)) <= 4)
      return "Ajouter un espace svp. 😇"
    if (a.length === 8 && a.startsWith("DAL G ") && Number(a.substr(6, 1)) && parseInt(a.substr(6, 1)) <= 4 && a.substr(7, 1) === " ")
      return "Ajouter la Reglette2 svp. 😇"
    if (a.length === 9 && a.startsWith("DAL G ") && Number(a.substr(6, 1)) && parseInt(a.substr(6, 1)) <= 4 && a.substr(7, 1) === " " && Number(a.substr(8, 1)) && parseInt(a.substr(8, 1)) <= 2)
      return "Ajouter un espace svp. 😇"
    if (a.length <= 12 && a.startsWith("DAL G ") && Number(a.substr(6, 1)) && parseInt(a.substr(6, 1)) <= 4 && a.substr(7, 1) === " " && Number(a.substr(8, 1)) && parseInt(a.substr(8, 1)) <= 2 && a.substr(9, 1) === " " && Number(a.substr(10, 2)) && parseInt(a.substr(10, 2)) <= 14)
      return "Bravo 😁"
    return "Faute 😡"
  }

  return ""
})

/* Filtering */
const filtered = computed(() => {
  const q = search.value.toLowerCase()
  return props.acheminements.filter(item =>
    item.acheminement?.toLowerCase().includes(q) ||
    item.numero?.NDappel?.toLowerCase().includes(q) ||
    item.description?.toLowerCase().includes(q)
  )
})

const paginatedAcheminements = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filtered.value.slice(start, start + itemsPerPage)
})

const totalPages = computed(() => Math.ceil(filtered.value.length / itemsPerPage))
watch(search, () => currentPage.value = 1)

const filteredNumeros = computed(() => {
  const q = search.value.toLowerCase()
  const matched = props.numeros.filter(n => n.NDappel?.toLowerCase().includes(q))
  const selected = props.numeros.find(n => n.id === form.value.numero_id)
  if (selected && !matched.some(n => n.id === selected.id)) matched.push(selected)
  return matched
})

/* CRUD */
const save = () => {
  const url = form.value.id ? `/manageAcheminement/${form.value.id}` : '/manageAcheminement'
  const method = form.value.id ? 'put' : 'post'
  router[method](url, form.value, { preserveScroll: true, onSuccess: () => reset() })
}

const edit = (item) => {
  form.value = { id: item.id, numero_id: item.numero_id, acheminement: item.acheminement, description: item.description || '' }
}

const destroy = (id) => {
  if (confirm('Are you sure you want to delete this acheminement?'))
    router.delete(`/manageAcheminement/${id}`, { preserveScroll: true })
}

const reset = () => form.value = { id: null, numero_id: '', acheminement: '', description: '' }
</script>
