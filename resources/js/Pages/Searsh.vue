<template>
  <div
    ref="containerRef"
    :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
    class="min-h-screen bg-gradient-to-br from-green-50 to-white py-10"
  >
    <div class="p-8 max-w-6xl mx-auto bg-white rounded-2xl shadow-xl border border-green-100">

      <!-- 🕒 Header -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-green-800">
          🕒 {{ currentTime }}
        </h1>

        <select
          v-model="currentLang"
          class="border border-green-300 rounded-lg px-3 py-1 text-green-800 font-semibold"
        >
          <option value="ar">العربية</option>
          <option value="fr">Français</option>
        </select>
      </div>

      <!-- 🔍 Search -->
      <div class="mb-4 relative">
        <input
          v-model="search"
          @keydown="handleKeydown"
          @focus="showSuggestions = true"
          type="text"
          :placeholder="currentLang === 'ar'
            ? 'ابحث حسب الاسم أو الرقم أو الهيئة'
            : 'Rechercher par nom, numéro ou organisme'"
          class="border-2 border-green-200 focus:border-green-400 rounded-lg px-4 py-3 w-full text-lg"
        />

        <!-- Suggestions -->
        <ul
          v-if="showSuggestionList"
          class="absolute top-full left-0 right-0 bg-white border rounded-xl shadow-lg mt-1 max-h-60 overflow-y-auto z-20"
        >
          <li
            v-for="(num, index) in filteredSuggestions"
            :key="'nd-' + num.id"
            @click="selectSuggestion(num.NDappel)"
            :class="[
              'px-4 py-2 cursor-pointer',
              index === activeIndex ? 'bg-green-100 font-semibold' : 'hover:bg-green-50'
            ]"
          >
            📞 {{ num.NDappel }} — {{ num.name }}
          </li>

          <li
            v-for="(org, index) in filteredOrganismes"
            :key="'org-' + index"
            @click="selectOrganisme(org)"
            :class="[
              'px-4 py-2 cursor-pointer text-blue-800',
              index + filteredSuggestions.length === activeIndex
                ? 'bg-green-100 font-semibold'
                : 'hover:bg-green-50'
            ]"
          >
            🏢 {{ org }}
          </li>
        </ul>
      </div>

      <!-- 🏢 Selected Filters -->
      <div class="flex flex-wrap gap-4 mb-4">
        <div
          v-if="selectedOrganisme"
          class="flex items-center gap-2 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium"
        >
          🏢 {{ selectedOrganisme }}
          <button @click="clearOrganisme">❌</button>
        </div>

        <div
          v-if="selectedDestination"
          class="flex items-center gap-2 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium"
        >
          🎯 {{ selectedDestination }}
          <button @click="selectedDestination = ''">❌</button>
        </div>

        <div
          v-if="selectedOrganisme || selectedDestination"
          @click="clearAllFilters"
          class="flex items-center gap-2 bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium cursor-pointer hover:bg-red-200"
        >
          🧹 {{ currentLang === 'ar' ? 'مسح الكل' : 'Clear All' }}
        </div>
      </div>

      <!-- 🌍 Destination Filter -->
      <div v-if="selectedOrganisme && destinationsByOrganisme.length" class="mb-4">
        <label class="block mb-1 text-green-700 font-medium">
          {{ currentLang === 'fr' ? 'Destination' : 'الوجهة' }}
        </label>
        <select
          v-model="selectedDestination"
          class="border border-green-300 rounded-lg px-3 py-2 w-full"
        >
          <option value="">
            {{ currentLang === 'fr' ? 'All Destinations' : 'كل الوجهات' }}
          </option>
          <option
            v-for="dest in destinationsByOrganisme"
            :key="dest"
            :value="dest"
          >
            {{ dest }}
          </option>
        </select>
      </div>

      <!-- 📋 Table -->
      <table class="min-w-full border-collapse border mt-6">
        <thead class="bg-green-50">
          <tr>
            <th class="border p-2">Matricule</th>
            <th class="border p-2">{{ currentLang === 'fr' ? 'Organisme' : 'الهيئة' }}</th>
            <th class="border p-2">{{ currentLang === 'fr' ? 'Destination' : 'الوجهة' }}</th>
            <th class="border p-2">{{ currentLang === 'fr' ? 'Service' : 'المصلحة' }}</th>
            <th class="border p-2">NDappel</th>
          </tr>
        </thead>

        <tbody>
          <tr
  v-for="numero in filteredNumeros"
  :key="numero.id"
  @click="goToDetails(numero.NDappel, $event)"
  class="group hover:bg-green-50 cursor-pointer transition"
>
  <td class="border p-2 relative">
    <!-- Tooltip -->
    <div
      class="absolute left-1/2 -translate-x-1/2 -top-7
             opacity-0 group-hover:opacity-100
             transition-all duration-200
             pointer-events-none z-50"
    >
      <div class="bg-green-800 text-white text-xs px-3 py-1 rounded-lg shadow-lg whitespace-nowrap">
        Double-Click Pour + Info
      </div>
    </div>

    {{ numero.matricule?.matricule ?? '—' }}
  </td>

  <td class="border p-2">{{ getName(numero.organisme) }}</td>
  <td class="border p-2">{{ getName(numero.destination) }}</td>
  <td class="border p-2">{{ getName(numero.service) }}</td>

  <td class="border p-2" @click.stop @dblclick="enableEdit(numero)">
    <input
      v-if="numero.isEditing && canEdit(numero)"
      v-model="numero.NDappel"
      @blur="saveNDappel(numero)"
      class="border border-green-300 rounded-md px-2 py-1 w-full"
    />
    <span v-else>{{ numero.NDappel }}</span>
  </td>
</tr>
        </tbody>
      </table>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import Layout from '@/Layouts/LayoutRecherche.vue'

defineOptions({ layout: Layout })
const props = defineProps({
  numeros: { type: Array, default: () => [] }
})

/* ================= STATE ================= */
const currentLang = ref('ar')
const search = ref('')
const debouncedSearch = ref('')
const showSuggestions = ref(false)
const activeIndex = ref(-1)
const containerRef = ref(null)
let debounceTimer = null
const selectedOrganisme = ref('')
const selectedDestination = ref('')

/* ================= TIME ================= */
const currentTime = ref('')
function updateTime() {
  const now = new Date()
  currentTime.value =
    now.getHours().toString().padStart(2, '0') + ':' +
    now.getMinutes().toString().padStart(2, '0') + ':' +
    now.getSeconds().toString().padStart(2, '0')
}
onMounted(() => {
  updateTime()
  setInterval(updateTime, 1000)
})

/* ================= HELPERS ================= */
function getName(obj) {
  if (!obj) return '—'
  return currentLang.value === 'fr'
    ? obj.name_fr ?? obj.name ?? '—'
    : obj.name ?? obj.name_fr ?? '—'
}

function canEdit(numero) {
  const tech = numero.technologie?.name?.toUpperCase()
  const org = numero.organisme?.name
  return tech === 'MOBILE' || (tech === 'ALGERIE TELECOM' && org === 'الولايات')
}

function enableEdit(numero) {
  if (canEdit(numero)) numero.isEditing = true
}

function saveNDappel(numero) {
  numero.isEditing = false
  router.post('/numeros/update-ndappel', {
    id: numero.id,
    NDappel: numero.NDappel
  }, { preserveScroll: true, preserveState: true })
}

function goToDetails(ndappel, event) {
  if (event.target.tagName === 'INPUT') return
  if (!ndappel) return
  router.visit(route('Annuaire.index', { ndappel }))
}

/* ================= SEARCH ================= */
const normalize = v => String(v ?? '').toLowerCase()

watch(search, val => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    debouncedSearch.value = val
  }, 200)
  showSuggestions.value = true
})

const filteredSuggestions = computed(() => {
  const q = normalize(search.value)
  if (!q) return []
  return props.numeros.filter(n =>
    normalize(n.NDappel).includes(q) ||
    normalize(n.name).includes(q)
  ).slice(0, 5)
})

const filteredOrganismes = computed(() => {
  const q = normalize(search.value)
  const unique = [...new Set(
    props.numeros.map(n => getName(n.organisme)).filter(Boolean)
  )]
  return q ? unique.filter(o => normalize(o).includes(q)) : unique
})

const showSuggestionList = computed(() =>
  showSuggestions.value &&
  (filteredSuggestions.value.length || filteredOrganismes.value.length)
)

function selectOrganisme(org) {
  selectedOrganisme.value = org
  selectedDestination.value = ''
  search.value = org
  debouncedSearch.value = org
  showSuggestions.value = false
}

function selectSuggestion(nd) {
  search.value = nd
  debouncedSearch.value = nd
  showSuggestions.value = false
}

function clearOrganisme() {
  selectedOrganisme.value = ''
  selectedDestination.value = ''
}

const destinationsByOrganisme = computed(() => {
  if (!selectedOrganisme.value) return []
  return [...new Set(
    props.numeros
      .filter(n => getName(n.organisme) === selectedOrganisme.value)
      .map(n => getName(n.destination))
      .filter(Boolean)
  )]
})

const filteredNumeros = computed(() => {
  const q = normalize(debouncedSearch.value)
  return props.numeros.filter(n => {
    const matchesSearch =
      normalize(n.NDappel).includes(q) ||
      normalize(n.name).includes(q) ||
      normalize(getName(n.organisme)).includes(q) ||
      normalize(getName(n.destination)).includes(q)

    const matchesOrganisme =
      !selectedOrganisme.value ||
      getName(n.organisme) === selectedOrganisme.value

    const matchesDestination =
      !selectedDestination.value ||
      getName(n.destination) === selectedDestination.value

    return matchesSearch && matchesOrganisme && matchesDestination
  })
})

function clearAllFilters() {
  selectedOrganisme.value = ''
  selectedDestination.value = ''
  search.value = ''
  debouncedSearch.value = ''
}

function handleKeydown(e) {
  const total =
    filteredSuggestions.value.length +
    filteredOrganismes.value.length - 1

  if (e.key === 'ArrowDown') {
    e.preventDefault()
    activeIndex.value =
      activeIndex.value < total ? activeIndex.value + 1 : 0
  } else if (e.key === 'ArrowUp') {
    e.preventDefault()
    activeIndex.value =
      activeIndex.value > 0 ? activeIndex.value - 1 : total
  } else if (e.key === 'Escape') {
    showSuggestions.value = false
  }
}

function onClickOutside(e) {
  if (!containerRef.value.contains(e.target)) {
    showSuggestions.value = false
  }
}

onMounted(() =>
  document.addEventListener('click', onClickOutside)
)

onBeforeUnmount(() =>
  document.removeEventListener('click', onClickOutside)
)
</script>