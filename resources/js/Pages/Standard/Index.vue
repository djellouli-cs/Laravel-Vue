<template>
  <div
    ref="containerRef"
    :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
    class="min-h-screen bg-gradient-to-br from-green-50 to-white py-10"
  >
    <div class="p-8 max-w-5xl mx-auto bg-white rounded-2xl shadow-xl border border-green-100">

      <!-- 🎯 Destinations / Permanence -->
      <div v-if="filteredDestinations.length">
        <div v-for="destination in filteredDestinations" :key="destination.id" class="mb-6">
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

      <div v-else class="text-center text-gray-500 italic p-6">
        ⚠️ {{ currentLang === 'ar' ? 'لا توجد مداومة هذا الأسبوع' : 'No permanence this week' }}
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
          <option value="">{{ currentLang === 'ar' ? 'بدون وجهة' : 'No destination' }}</option>
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
              <input
                v-if="numero.isEditing && numero.technologie?.name?.toUpperCase() === 'MOBILE'"
                v-model="numero.NDappel"
                @blur="saveNDappel(numero)"
                class="border border-green-300 rounded-md px-2 py-1 w-full"
              />
              <span
                v-else
                class="cursor-pointer text-green-700 font-semibold"
                @mouseenter="showNotes(numero)"
                @mouseleave="hideNotes()"
              >
                {{ numero.NDappel }}
              </span>

              <!-- 📝 Notes Tooltip -->
              <div
                v-if="hoveredNumero?.id === numero.id"
                class="absolute z-50 bg-white border border-green-300 shadow-xl rounded-lg p-3 w-64 top-full left-0 mt-2"
              >
                <div class="font-bold text-green-700 mb-2">📝 Notes</div>
                <div v-if="numero.notes?.length" class="space-y-2 max-h-40 overflow-y-auto text-sm">
                  <div
                    v-for="note in numero.notes"
                    :key="note.id"
                    class="bg-green-50 border border-green-200 rounded-md p-2 text-sm break-words text-left"
                    dir="ltr"
                  >
                    {{ note.content }}
                  </div>
                </div>
                <div v-else class="text-gray-400 italic text-sm">Aucune note</div>
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
import { router, usePage } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutStandard.vue'
import '@/Echo.js'

defineOptions({ layout: Layout })

const page = usePage()
const props = defineProps({ numeros: { type: Array, default: () => [] } })

// Declare currentLang FIRST before using it
const currentLang = ref(localStorage.getItem('currentLang') || 'ar')

// ⏰ Live clock - Now currentLang is defined
const currentTime = ref('')
function updateTime() {
  const now = new Date()
  currentTime.value = now.toLocaleTimeString(currentLang.value === 'fr' ? 'fr-FR' : 'ar-DZ')
}

// Initialize time
updateTime()
// Set interval for clock
const timeInterval = setInterval(updateTime, 1000)

// Use reactive refs for real-time data
const localNumeros = ref([])
const permanence = ref(null)
const destinations = ref([])

// Initialize data
const initData = () => {
  localNumeros.value = [...props.numeros]
  permanence.value = page.props.permanence
  destinations.value = page.props.destinations || []
}

initData()

// Watch for props changes
watch(() => props.numeros, (newVal) => {
  localNumeros.value = [...newVal]
}, { deep: true })

watch(() => page.props.permanence, (newVal) => {
  permanence.value = newVal
})

watch(() => page.props.destinations, (newVal) => {
  destinations.value = newVal || []
})

// Filtered destinations for permanence
const filteredDestinations = computed(() => {
  if (!permanence.value?.PSemaine) return []
  return destinations.value.filter(d => d.name === permanence.value.PSemaine)
})

const mobileNumeros = (numeros) => {
  return (numeros || []).filter(n => n.technologie?.name === 'MOBILE')
}

// 💻 Real-time Echo updates
onMounted(() => {
  if (window.Echo) {
    // Channel for numeros updates
    window.Echo.channel('numeros')
      .listen('.NumeroUpdated', (e) => {
        console.log('Real-time update received:', e)
        const updated = e.numero
        const index = localNumeros.value.findIndex(n => n.id === updated.id)
        
        if (e.deleted) {
          if (index !== -1) {
            localNumeros.value.splice(index, 1)
          }
        } else {
          if (index !== -1) {
            // Preserve isEditing state
            const wasEditing = localNumeros.value[index].isEditing
            Object.assign(localNumeros.value[index], updated)
            if (wasEditing) localNumeros.value[index].isEditing = true
          } else {
            localNumeros.value.unshift(updated)
          }
        }
        
        // Force reactivity
        localNumeros.value = [...localNumeros.value]
      })

    // Channel for permanence updates
    window.Echo.channel('permanences')
      .listen('.PermanenceUpdated', (e) => {
        console.log('Permanence update received:', e)
        permanence.value = e.deleted ? null : e.permanence
      })
  } else {
    console.warn('Echo not initialized')
  }
})

// Cleanup interval on unmount
onBeforeUnmount(() => {
  clearInterval(timeInterval)
})

// 🔍 Search and filters
const containerRef = ref(null)
const search = ref(localStorage.getItem('search') || '')
const selectedOrganisme = ref(localStorage.getItem('selectedOrganisme') || null)
const selectedDestination = ref(localStorage.getItem('selectedDestination') || null)
const showSuggestions = ref(false)
const activeIndex = ref(-1)

// Save to localStorage
watch([search, currentLang, selectedOrganisme, selectedDestination], () => {
  localStorage.setItem('search', search.value)
  localStorage.setItem('currentLang', currentLang.value)
  if (selectedOrganisme.value) localStorage.setItem('selectedOrganisme', selectedOrganisme.value)
  else localStorage.removeItem('selectedOrganisme')
  if (selectedDestination.value) localStorage.setItem('selectedDestination', selectedDestination.value)
  else localStorage.removeItem('selectedDestination')
})

// 🏢 Destinations by selected organisme
const destinationsByOrganisme = computed(() => {
  if (!selectedOrganisme.value) return []
  return localNumeros.value
    .filter(n => {
      const org = currentLang.value === 'fr' ? n.organisme?.name_fr : n.organisme?.name
      return org === selectedOrganisme.value
    })
    .map(n => currentLang.value === 'fr' ? n.destination?.name_fr : n.destination?.name)
    .filter(Boolean)
    .filter((v, i, self) => self.indexOf(v) === i)
})

// 📋 Displayed numeros — reactive and real-time
const normalize = v => String(v ?? '').trim().toLowerCase()
const displayedNumeros = computed(() => {
  const q = normalize(search.value)
  return localNumeros.value.filter(n => {
    const NDappelStr = String(n.NDappel ?? '').toLowerCase()
    const nameStr = normalize(n.name)
    const orgStr = normalize(currentLang.value === 'fr' ? n.organisme?.name_fr : n.organisme?.name)
    const destStr = normalize(currentLang.value === 'fr' ? n.destination?.name_fr : n.destination?.name)
    
    const matchesSearch = !q || NDappelStr.includes(q) || nameStr.includes(q) || orgStr.includes(q) || destStr.includes(q)
    const matchesOrganisme = !selectedOrganisme.value || orgStr === normalize(selectedOrganisme.value)
    const matchesDestination = !selectedDestination.value || destStr === normalize(selectedDestination.value)
    
    return matchesSearch && matchesOrganisme && matchesDestination
  })
})

// 💡 Suggestions — real-time
const filteredSuggestions = computed(() => {
  const q = normalize(search.value)
  if (!q) return []
  return localNumeros.value.filter(n => 
    String(n.NDappel ?? '').toLowerCase().includes(q) || normalize(n.name).includes(q)
  ).slice(0, 5)
})

const filteredOrganismes = computed(() => {
  const q = normalize(search.value)
  const unique = [...new Set(localNumeros.value.map(n => 
    currentLang.value === 'fr' ? n.organisme?.name_fr : n.organisme?.name
  ).filter(Boolean))]
  return !q ? unique : unique.filter(org => normalize(org).includes(q))
})

const showSuggestionList = computed(() => showSuggestions.value && (filteredSuggestions.value.length || filteredOrganismes.value.length))

// 🎯 Select suggestion
function selectSuggestion(value) {
  if (filteredOrganismes.value.includes(value)) {
    selectedOrganisme.value = value
    selectedDestination.value = null
  } else {
    selectedOrganisme.value = null
    selectedDestination.value = null
  }
  search.value = value
  showSuggestions.value = false
  activeIndex.value = -1
}

// ⌨️ Keyboard navigation
function handleKeydown(e) {
  const total = filteredSuggestions.value.length + filteredOrganismes.value.length - 1
  if (e.key === 'ArrowDown') { 
    e.preventDefault(); 
    activeIndex.value = activeIndex.value < total ? activeIndex.value + 1 : 0 
  }
  else if (e.key === 'ArrowUp') { 
    e.preventDefault(); 
    activeIndex.value = activeIndex.value > 0 ? activeIndex.value - 1 : total 
  }
  else if (e.key === 'Enter') { 
    e.preventDefault()
    let suggestion = activeIndex.value < filteredSuggestions.value.length 
      ? filteredSuggestions.value[activeIndex.value]?.NDappel 
      : filteredOrganismes.value[activeIndex.value - filteredSuggestions.value.length]
    if (suggestion) selectSuggestion(suggestion)
  }
  else if (e.key === 'Escape') {
    showSuggestions.value = false
    activeIndex.value = -1
  }
}

// 🖱️ Outside click
function onClickOutside(e) {
  if (!containerRef.value) return
  if (!containerRef.value.contains(e.target)) {
    showSuggestions.value = false
    activeIndex.value = -1
  }
}
onMounted(() => document.addEventListener('click', onClickOutside))
onBeforeUnmount(() => {
  document.removeEventListener('click', onClickOutside)
})

// ✏️ Edit NDappel
function enableEdit(numero) { 
  if (numero.technologie?.name?.toUpperCase() === 'MOBILE') {
    numero.isEditing = true
  }
}

function saveNDappel(numero) {
  numero.isEditing = false
  router.post('/numeros/update-ndappel', { 
    id: numero.id, 
    NDappel: numero.NDappel 
  }, { 
    preserveScroll: true, 
    preserveState: true 
  })
}

// 📝 Notes tooltip
const hoveredNumero = ref(null)
function showNotes(numero) { 
  hoveredNumero.value = numero 
}
function hideNotes() { 
  hoveredNumero.value = null 
}

// 🧹 Clear all filters
function clearAllFilters() {
  selectedOrganisme.value = null
  selectedDestination.value = null
  search.value = ''
}
</script>