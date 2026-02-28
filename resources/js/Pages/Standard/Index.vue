<template>


  <div
    ref="containerRef"
    :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
    class="min-h-screen bg-gradient-to-br from-green-50 to-white py-10"
  >
  
    <div class="p-8 max-w-5xl mx-auto bg-white rounded-2xl shadow-xl border border-green-100">
  <div v-for="destination in destinations" :key="destination.id" class="mb-6">
  <div v-if="destination.name === permanence.PSemaine">
    <h2 class="text-lg font-bold text-green-800 mb-2 flex items-center gap-2">
      🎯 المداوم : {{ destination.name }}
    </h2>

    <div class="space-y-2">
      <div
        v-for="numero in mobileNumeros(destination.numeros)"
        :key="numero.id"
        class="flex items-center justify-between bg-green-50 border border-green-100 rounded-xl p-3 shadow-sm hover:bg-green-100 transition"
      >
        <div class="flex items-center gap-2">
          <span class="text-white bg-green-500 w-6 h-6 flex items-center justify-center rounded-full text-sm">📱</span>
          <span class="font-medium">{{ numero.NDappel }}</span>
        </div>
        <span class="text-green-600 font-semibold">3360</span>
      </div>

      <div
        v-if="mobileNumeros(destination.numeros).length === 0"
        class="text-gray-500 italic flex items-center gap-2"
      >
        📵 No mobile number found
      </div>
    </div>
  </div>
</div>
    <!-- 🕒 Header -->
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-green-800 flex items-center gap-2">
          <span>🕒</span>
          <span>{{ currentTime }}</span>
        </h1>

        <!-- 🌍 Language Switch -->
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
            ? 'ابحث حسب الاسم أو الرقم أو حدد الهيئة'
            : 'Recherchez par nom, numéro ou organisme'"
          class="border-2 border-green-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 rounded-lg px-4 py-3 w-full text-lg transition"
        />

        <!-- 💡 Suggestions -->
        <ul
          v-if="showSuggestionList"
          class="absolute top-full left-0 right-0 bg-white border border-green-200 rounded-xl shadow-lg mt-1 max-h-60 overflow-y-auto z-10"
        >
          <li
            v-for="(num, index) in filteredSuggestions"
            :key="'nd-' + num.id"
            @click="selectSuggestion(num.NDappel)"
            :class="[ 'px-5 py-2 cursor-pointer transition',
              index === activeIndex ? 'bg-green-100 font-semibold' : 'hover:bg-green-50'
            ]"
          >
            📞 {{ num.NDappel }} — {{ num.name }}
          </li>
          <li
            v-for="(org, index) in filteredOrganismes"
            :key="'org-' + index"
            @click="selectSuggestion(org)"
            :class="[ 'px-5 py-2 cursor-pointer transition text-blue-800',
              index + filteredSuggestions.length === activeIndex ? 'bg-green-100 font-semibold' : 'hover:bg-green-50'
            ]"
          >
            🏢 {{ org }}
          </li>
        </ul>
      </div>

      <!-- 🏷️ Selected Tags -->
      <div v-if="selectedOrganisme || selectedDestination" class="flex flex-wrap gap-2 mt-2 mb-4">
        <!-- Organisme Tag -->
        <div
          v-if="selectedOrganisme"
          class="flex items-center gap-2 bg-green-100 text-green-800 px-3 py-1 rounded-full shadow-sm text-sm font-medium"
        >
          🏢 <span>{{ selectedOrganisme }}</span>
          <button
            @click="selectedOrganisme = null; selectedDestination = null"
            class="text-green-700 hover:text-green-900 ml-1"
          >
            ❌
          </button>
        </div>

        <!-- Destination Tag -->
        <div
          v-if="selectedDestination"
          class="flex items-center gap-2 bg-green-100 text-green-800 px-3 py-1 rounded-full shadow-sm text-sm font-medium"
        >
          🎯 <span>{{ selectedDestination }}</span>
          <button
            @click="selectedDestination = null"
            class="text-green-700 hover:text-green-900 ml-1"
          >
            ❌
          </button>
        </div>

        <!-- Clear All Tag -->
        <div
          class="flex items-center gap-2 bg-red-100 text-red-800 px-3 py-1 rounded-full shadow-sm text-sm font-medium cursor-pointer hover:bg-red-200 transition"
          @click="clearAllFilters"
        >
          🧹 <span>{{ currentLang === 'ar' ? 'مسح الكل' : 'Clear All' }}</span>
        </div>
      </div>

      <!-- 🏢 Destination Selector -->
      <div v-if="selectedOrganisme && destinationsByOrganisme.length > 0" class="mt-2 space-y-3">
        <label class="block text-green-700 font-medium mb-1">
          {{ currentLang === 'ar' ? 'اختر الوجهة:' : 'Select Destination:' }}
        </label>
        <select
          v-model="selectedDestination"
          class="border border-green-300 rounded-lg px-3 py-2 text-green-800 w-full"
        >
          <option value="">
            {{ currentLang === 'ar' ? 'بدون وجهة' : 'No destination' }}
          </option>
          <option
            v-for="(dest, i) in destinationsByOrganisme"
            :key="i"
            :value="dest"
          >
            {{ dest }}
          </option>
        </select>
      </div>

      <!-- 📋 Table -->
      <table class="min-w-full border-collapse border mt-6">
        <thead class="bg-green-50">
          <tr v-if="currentLang === 'ar'">
            <th class="border p-2 text-left">الرقم</th>
            <th class="border p-2 text-left">الهيئة</th>
            <th class="border p-2 text-left">الوجهة</th>
            <th class="border p-2 text-left">المصلحة</th>
            <th class="border p-2 text-left">الخط المباشر</th>
          </tr>
          <tr v-else>
            <th class="border p-2 text-left">Matricule</th>
            <th class="border p-2 text-left">Organisme</th>
            <th class="border p-2 text-left">Destination</th>
            <th class="border p-2 text-left">Service</th>
            <th class="border p-2 text-left">NDappel</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="numero in displayedNumeros"
            :key="numero.id"
            class="hover:bg-green-50"
          >
            <td class="border p-2">{{ numero.matricule?.matricule ?? '—' }}</td>
            <td class="border p-2">
              {{ currentLang === 'fr' ? numero.organisme?.name_fr ?? '—' : numero.organisme?.name ?? '—' }}
            </td>
            <td class="border p-2">
              {{ currentLang === 'fr' ? numero.destination?.name_fr ?? '—' : numero.destination?.name ?? '—' }}
            </td>
            <td class="border p-2">
              {{ currentLang === 'fr' ? numero.service?.name_fr ?? '—' : numero.service?.name ?? '—' }}
            </td>

            <td
  class="border p-2 relative"
  @dblclick="enableEdit(numero)"
>
  <!-- Edit Input -->
  <input
    v-if="numero.isEditing && numero.technologie?.name?.toUpperCase() === 'MOBILE'"
    v-model="numero.NDappel"
    @blur="saveNDappel(numero)"
    class="border border-green-300 rounded-md px-2 py-1 w-full"
  />

  <!-- Normal Display -->
  <span
    v-else
    class="cursor-pointer text-green-700 font-semibold"
    @mouseenter="showNotes(numero)"
    @mouseleave="hideNotes"
  >
    {{ numero.NDappel }}
  </span>

  <!-- 📝 NOTES TOOLTIP -->
  <div
    v-if="hoveredNumero?.id === numero.id"
    class="absolute z-50 bg-white border border-green-300 shadow-xl rounded-lg p-3 w-64 top-full left-0 mt-2"
  >
    <div class="font-bold text-green-700 mb-2">
      📝 Notes
    </div>

    <div
      v-if="numero.notes && numero.notes.length > 0"
      class="space-y-2 max-h-40 overflow-y-auto text-sm"
    >
      <div
  v-for="note in numero.notes"
  :key="note.id"
  class="bg-green-50 border border-green-200 rounded-md p-2 text-sm break-words text-left"
  dir="ltr"
>
  {{ note.content }}
</div>
    </div>

    <div v-else class="text-gray-400 italic text-sm">
      Aucune note
    </div>
  </div>
</td>
          </tr>

          <tr v-if="displayedNumeros.length === 0">
            <td class="border p-2 text-center" colspan="5">
              {{ currentLang === 'ar' ? 'لا توجد نتائج مطابقة' : 'Aucun numéro trouvé' }}
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
import Layout from '@/Layouts/LayoutStandard.vue'
import { usePage } from '@inertiajs/vue3'

defineOptions({ layout: Layout })
const page = usePage()
const permanence = computed(() => page.props.permanence)
const destinations = computed(() => page.props.destinations)
const mobileNumeros = (numeros) => {
  return (numeros || []).filter(n => n.technologie && n.technologie.name === 'MOBILE')
}
const props = defineProps({
  numeros: { type: Array, default: () => [] }
})

const containerRef = ref(null)
const search = ref('')
const activeIndex = ref(-1)
const showSuggestions = ref(false)
const currentLang = ref('ar')
let debounceTimer = null

// 🕒 Time
const currentTime = ref('')
function updateTime() {
  const now = new Date()
  currentTime.value = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes()
    .toString()
    .padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`
}
onMounted(() => {
  updateTime()
  setInterval(updateTime, 1000)
})

const normalize = v => String(v ?? '').trim().toLowerCase()

// 🟢 Selected organisme + destination
const selectedOrganisme = ref(null)
const selectedDestination = ref(null)

// 💾 Load saved filters from localStorage
onMounted(() => {
  const savedLang = localStorage.getItem('currentLang')
  const savedSearch = localStorage.getItem('search')
  const savedOrganisme = localStorage.getItem('selectedOrganisme')
  const savedDestination = localStorage.getItem('selectedDestination')

  if (savedLang) currentLang.value = savedLang
  if (savedSearch) search.value = savedSearch
  if (savedOrganisme) selectedOrganisme.value = savedOrganisme
  if (savedDestination) selectedDestination.value = savedDestination
})

// 🧹 Clear filters
function clearAllFilters() {
  selectedOrganisme.value = null
  selectedDestination.value = null
  search.value = ''
  debouncedSearch.value = ''
  localStorage.removeItem('selectedOrganisme')
  localStorage.removeItem('selectedDestination')
  localStorage.removeItem('search')
}

// 🔍 Debounced search
const debouncedSearch = ref('')
watch(search, val => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => (debouncedSearch.value = val), 200)
  activeIndex.value = -1
  showSuggestions.value = true
  if (!val.trim()) clearAllFilters()
})

// 🧠 Save filters to localStorage
watch(currentLang, val => localStorage.setItem('currentLang', val))
watch(search, val => localStorage.setItem('search', val))
watch(selectedOrganisme, val => {
  if (val) localStorage.setItem('selectedOrganisme', val)
  else localStorage.removeItem('selectedOrganisme')
})
watch(selectedDestination, val => {
  if (val) localStorage.setItem('selectedDestination', val)
  else localStorage.removeItem('selectedDestination')
})

// 🧭 Destinations for selected organisme
const destinationsByOrganisme = computed(() => {
  if (!selectedOrganisme.value) return []
  return props.numeros
    .filter(n => {
      const orgName = currentLang.value === 'fr' ? n.organisme?.name_fr : n.organisme?.name
      return orgName === selectedOrganisme.value
    })
    .map(n => currentLang.value === 'fr' ? n.destination?.name_fr : n.destination?.name)
    .filter(Boolean)
    .filter((v, i, self) => self.indexOf(v) === i)
})

// 📋 Table Filter
const displayedNumeros = computed(() => {
  const q = normalize(debouncedSearch.value)
  return props.numeros.filter(n => {
    const NDappelStr = String(n.NDappel ?? '').toLowerCase()
    const matchesSearch =
      NDappelStr.includes(q) ||
      normalize(n.name).includes(q) ||
      normalize(currentLang.value === 'fr' ? n.organisme?.name_fr : n.organisme?.name).includes(q) ||
      normalize(currentLang.value === 'fr' ? n.destination?.name_fr : n.destination?.name).includes(q)

    const matchesOrganisme =
      !selectedOrganisme.value ||
      (currentLang.value === 'fr'
        ? n.organisme?.name_fr === selectedOrganisme.value
        : n.organisme?.name === selectedOrganisme.value)

    const matchesDestination =
      !selectedDestination.value ||
      (currentLang.value === 'fr'
        ? n.destination?.name_fr === selectedDestination.value
        : n.destination?.name === selectedDestination.value)

    return matchesSearch && matchesOrganisme && matchesDestination
  })
})

// 💡 Suggestions
const filteredSuggestions = computed(() => {
  const q = normalize(search.value)
  if (!q) return []
  return props.numeros.filter(n => {
    const NDappelStr = String(n.NDappel ?? '').toLowerCase()
    return NDappelStr.includes(q) || normalize(n.name).includes(q)
  }).slice(0, 5)
})

const filteredOrganismes = computed(() => {
  const q = normalize(search.value)
  const uniqueOrgs = [
    ...new Set(
      props.numeros.map(n =>
        currentLang.value === 'fr' ? n.organisme?.name_fr : n.organisme?.name
      ).filter(Boolean)
    )
  ]
  if (!q) return uniqueOrgs
  return uniqueOrgs.filter(org => normalize(org).includes(q))
})

const showSuggestionList = computed(
  () => showSuggestions.value && (filteredSuggestions.value.length > 0 || filteredOrganismes.value.length > 0)
)

function selectSuggestion(value) {
  const isOrganisme = filteredOrganismes.value.includes(value)
  if (isOrganisme) {
    selectedOrganisme.value = value
    selectedDestination.value = null
  } else {
    selectedOrganisme.value = null
    selectedDestination.value = null
  }

  search.value = value
  debouncedSearch.value = value
  activeIndex.value = -1
  showSuggestions.value = false
}

// ⌨️ Keyboard Navigation
function handleKeydown(e) {
  const total = filteredSuggestions.value.length + filteredOrganismes.value.length - 1
  if (e.key === 'ArrowDown') {
    e.preventDefault()
    activeIndex.value = activeIndex.value < total ? activeIndex.value + 1 : 0
  } else if (e.key === 'ArrowUp') {
    e.preventDefault()
    activeIndex.value = activeIndex.value > 0 ? activeIndex.value - 1 : total
  } else if (e.key === 'Enter') {
    e.preventDefault()
    let suggestion = null
    if (activeIndex.value < filteredSuggestions.value.length) {
      suggestion = filteredSuggestions.value[activeIndex.value]?.NDappel
    } else {
      suggestion = filteredOrganismes.value[activeIndex.value - filteredSuggestions.value.length]
    }
    if (suggestion) selectSuggestion(suggestion)
  } else if (e.key === 'Escape') {
    showSuggestions.value = false
    activeIndex.value = -1
  }
}

// 🖱️ Click outside
function onClickOutside(e) {
  if (!containerRef.value) return
  if (!containerRef.value.contains(e.target)) {
    showSuggestions.value = false
    activeIndex.value = -1
  }
}
onMounted(() => document.addEventListener('click', onClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside))

// 💾 Edit NDappel
function enableEdit(numero) {
  if (numero.technologie?.name?.toUpperCase() === 'MOBILE') {
    numero.isEditing = true
  }
}
function saveNDappel(numero) {
  numero.isEditing = false
  router.post('/numeros/update-ndappel', { id: numero.id, NDappel: numero.NDappel }, { preserveScroll: true, preserveState: true })
}
const hoveredNumero = ref(null)

function showNotes(numero) {
  hoveredNumero.value = numero
}

function hideNotes() {
  hoveredNumero.value = null
}
</script>
