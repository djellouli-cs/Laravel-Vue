<template>
  <div
    ref="containerRef"
    :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
    class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-6"
    @click="handleContainerClick"
  >
    <div class="container mx-auto px-4 max-w-7xl pb-20">
      <!-- En-tête simplifié -->
      <div class="mb-6">
        <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
          <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
              <div class="bg-blue-600 rounded-lg p-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
              </div>
              <div>
                <h1 class="text-xl font-bold text-gray-800">Standard Téléphonique</h1>
                <p class="text-xs text-gray-500">Consultation annuaire</p>
              </div>
            </div>
            
            <div class="flex items-center gap-4">
              <!-- Horloge compacte -->
              <div class="text-center">
                <div class="text-2xl font-mono font-bold text-blue-700 bg-blue-50 px-3 py-1 rounded-lg">
                  {{ currentTime }}
                </div>
                <div class="text-xs text-gray-500 mt-1">
                  {{ currentDate }}
                </div>
              </div>
              
              <!-- Langue -->
              <select
                v-model="currentLang"
                class="border border-gray-300 rounded-lg px-3 py-1.5 text-gray-700 text-sm bg-white hover:bg-gray-50 transition cursor-pointer"
              >
                <option value="ar">العربية</option>
                <option value="fr">Français</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Carte permanence compacte -->
      <div v-if="permanence.PSemaine" class="mb-6">
        <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-lg border border-amber-200 p-3">
          <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2">
              <div class="bg-amber-500 rounded-full p-1.5">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <p class="text-xs font-medium text-amber-700">{{ currentLang === 'ar' ? 'المداوم' : 'Permanence' }}</p>
                <p class="text-sm font-bold text-amber-800">{{ permanence.PSemaine }}</p>
              </div>
            </div>
            
            <!-- Numéros mobiles de permanence -->
            <div v-if="mobilePermanenceNumbers.length > 0" class="flex flex-wrap gap-2">
              <div
                v-for="numero in mobilePermanenceNumbers"
                :key="numero.id"
                class="flex items-center gap-1.5 bg-white rounded-md px-2 py-1 text-xs border border-amber-200"
              >
                <span class="text-amber-600">📱</span>
                <span class="font-mono font-semibold text-amber-800">{{ numero.NDappel }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Zone de recherche optimisée -->
      <div ref="searchSectionRef" class="bg-white rounded-xl shadow-md border border-gray-200 p-4 mb-6">
        <div ref="searchContainerRef" class="relative">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
              ref="searchInputRef"
              v-model="search"
              @keydown="handleKeydown"
              @focus="showSuggestions = true"
              type="text"
              :placeholder="currentLang === 'ar'
                ? 'ابحث بالاسم، الرقم أو الهيئة...'
                : 'Rechercher par nom, numéro ou organisme...'"
              class="w-full pl-9 pr-8 py-2.5 border border-gray-300 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 rounded-lg text-sm transition-all"
            />
            <button
              v-if="search"
              @click="clearSearch"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 text-sm"
            >
              ✕
            </button>
          </div>

          <!-- Suggestions compactes -->
          <ul
            v-if="showSuggestionList"
            class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto z-20 text-sm"
          >
            <li
              v-for="(num, index) in filteredSuggestions"
              :key="'nd-' + num.id"
              @click="selectSuggestion(num.NDappel)"
              :class="[
                'px-3 py-2 cursor-pointer transition flex items-center gap-2',
                index === activeIndex ? 'bg-blue-50 border-l-2 border-blue-500' : 'hover:bg-gray-50'
              ]"
            >
              <span class="text-blue-600">📞</span>
              <div class="flex-1">
                <span class="font-mono font-medium">{{ num.NDappel }}</span>
                <span class="text-gray-500 text-xs ml-2">{{ num.name }}</span>
              </div>
            </li>
            <li
              v-for="(org, index) in filteredOrganismes"
              :key="'org-' + index"
              @click="selectSuggestion(org)"
              :class="[
                'px-3 py-2 cursor-pointer transition flex items-center gap-2',
                index + filteredSuggestions.length === activeIndex ? 'bg-blue-50 border-l-2 border-blue-500' : 'hover:bg-gray-50'
              ]"
            >
              <span class="text-gray-600">🏢</span>
              <span>{{ org }}</span>
            </li>
          </ul>
        </div>

        <!-- Filtres actifs compacts -->
        <div v-if="selectedOrganisme || selectedDestination || search" class="flex flex-wrap gap-1.5 mt-3">
          <span v-if="selectedOrganisme" class="inline-flex items-center gap-1 bg-blue-100 text-blue-800 px-2 py-1 rounded-md text-xs">
            🏢 {{ selectedOrganisme }}
            <button @click="selectedOrganisme = null; selectedDestination = null" class="hover:text-blue-900">✕</button>
          </span>
          <span v-if="selectedDestination" class="inline-flex items-center gap-1 bg-green-100 text-green-800 px-2 py-1 rounded-md text-xs">
            🎯 {{ selectedDestination }}
            <button @click="selectedDestination = null" class="hover:text-green-900">✕</button>
          </span>
          <span v-if="search" class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 px-2 py-1 rounded-md text-xs">
            🔍 "{{ search }}"
            <button @click="clearSearch" class="hover:text-gray-900">✕</button>
          </span>
          <button
            v-if="selectedOrganisme || selectedDestination || search"
            @click="clearAllFilters"
            class="text-xs text-red-600 hover:text-red-800 px-2 py-1"
          >
            🧹 {{ currentLang === 'ar' ? 'مسح' : 'Effacer' }}
          </button>
        </div>
      </div>

      <!-- Sélecteur de destination compact -->
      <div v-if="selectedOrganisme && destinationsByOrganisme.length > 0" class="mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-3">
          <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs font-medium text-gray-600">{{ currentLang === 'ar' ? 'الوجهة:' : 'Destination:' }}</span>
            <div class="flex flex-wrap gap-1.5">
              <button
                v-for="dest in destinationsByOrganisme"
                :key="dest"
                @click="selectedDestination = dest"
                :class="[
                  'px-2 py-1 rounded-md text-xs transition-all',
                  selectedDestination === dest
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                ]"
              >
                {{ dest }}
              </button>
              <button
                v-if="selectedDestination"
                @click="selectedDestination = null"
                class="px-2 py-1 rounded-md text-xs bg-red-100 text-red-600 hover:bg-red-200"
              >
                ✕
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Tableau des résultats -->
      <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
        <div class="bg-gray-50 px-4 py-2.5 border-b border-gray-200">
          <h2 class="text-sm font-semibold text-gray-700">
            {{ currentLang === 'ar' ? 'نتائج البحث' : 'Résultats' }}
            <span class="text-xs text-gray-500 ml-1">({{ displayedNumeros.length }})</span>
          </h2>
        </div>

        <!-- Tableau compact et lisible -->
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
              <tr v-if="currentLang === 'ar'">
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">الرقم</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">الهيئة</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">الوجهة</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">المصلحة</th>
                <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600">رقم الهاتف</th>
              </tr>
              <tr v-else>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Matricule</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Organisme</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Destination</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Service</th>
                <th class="px-3 py-2 text-left text-xs font-semibold text-gray-600">Numéro</th>
               </tr>
            </thead>

            <tbody>
              <tr
                v-for="numero in displayedNumeros"
                :key="numero.id"
                class="border-b border-gray-100 hover:bg-blue-50/30 transition-colors"
              >
                <td class="px-3 py-2 text-xs text-gray-600">
                  {{ numero.matricule?.matricule ?? '—' }}
                </td>
                <td class="px-3 py-2">
                  <span class="inline-block px-1.5 py-0.5 bg-gray-100 rounded text-xs text-gray-700">
                    {{ currentLang === 'fr' ? numero.organisme?.name_fr ?? '—' : numero.organisme?.name ?? '—' }}
                  </span>
                </td>
                <td class="px-3 py-2 text-xs text-gray-600">
                  {{ currentLang === 'fr' ? numero.destination?.name_fr ?? '—' : numero.destination?.name ?? '—' }}
                </td>
                <td class="px-3 py-2 text-xs text-gray-600">
                  {{ currentLang === 'fr' ? numero.service?.name_fr ?? '—' : numero.service?.name ?? '—' }}
                </td>

                <td class="px-3 py-2 relative">
                  <div
                    class="relative"
                    @mouseenter="showNotes(numero)"
                    @mouseleave="hideNotes"
                  >
                    <!-- Mode édition -->
                    <div
                      v-if="editingNumeroId === numero.id"
                      class="flex items-center gap-1"
                    >
                      <input
                        v-model="editingValue"
                        @blur="saveEdit(numero)"
                        @keyup.enter="saveEdit(numero)"
                        @keyup.escape="cancelEdit"
                        class="border border-blue-400 rounded px-1.5 py-0.5 w-28 text-xs focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-200"
                        ref="editInputRef"
                        autofocus
                      />
                      <span class="text-xs text-gray-500">↵</span>
                    </div>
                    
                    <!-- Mode normal -->
                    <div
                      v-else
                      @dblclick="startEdit(numero)"
                      class="cursor-pointer"
                    >
                      <span class="font-mono font-medium text-blue-700 text-sm">
                        {{ numero.NDappel }}
                      </span>
                    </div>

                    <!-- Tooltip notes compact -->
                    <div
                      v-if="hoveredNumero?.id === numero.id && numero.notes?.length > 0"
                      class="absolute z-50 bg-gray-800 text-white text-xs rounded-md p-2 w-56 top-full left-0 mt-1 shadow-lg"
                    >
                      <div class="font-medium mb-1 text-gray-300">📝 Notes:</div>
                      <div v-for="note in numero.notes" :key="note.id" class="text-gray-200 leading-relaxed">
                        {{ note.content }}
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

              <tr v-if="displayedNumeros.length === 0">
                <td class="px-3 py-8 text-center" colspan="5">
                  <div class="flex flex-col items-center gap-1 text-gray-400">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm">{{ currentLang === 'ar' ? 'لا توجد نتائج' : 'Aucun résultat' }}</p>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Bouton flottant en bas de page pour retour rapide à la recherche -->
    <button
      @click="quickBackToSearch"
      class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-all duration-300 hover:scale-110 z-50 group"
      :title="currentLang === 'ar' ? 'العودة إلى البحث' : 'Retour à la recherche'"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
      <span class="absolute -top-8 right-0 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
        {{ currentLang === 'ar' ? 'بحث سريع' : 'Recherche rapide' }}
      </span>
    </button>

    <!-- Pied de page simplifié -->
    <div class="fixed bottom-0 left-0 right-0 text-center text-xs text-gray-400 bg-white/80 backdrop-blur-sm py-2 border-t border-gray-200">
      {{ currentLang === 'ar' ? '🔍 انقر مرتين على الرقم المحمول لتعديله' : '🔍 Double-cliquez sur un numéro pour le modifier' }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutStandard.vue'
import { usePage } from '@inertiajs/vue3'

import '@/Echo.js'

const localNumeros = ref([...props.numeros])
// 💻 Real-time Echo updates
onMounted(() => {
  window.Echo.channel('numeros')
    .listen('.NumeroUpdated', e => {
      const updated = e.numero
      const index = localNumeros.value.findIndex(n => n.id === updated.id)
      if (e.deleted) {
        if (index !== -1) localNumeros.value.splice(index, 1)
      } else {
        if (index !== -1) Object.assign(localNumeros.value[index], updated)
        else localNumeros.value.unshift(updated)
      }
    })

  window.Echo.channel('permanences')
    .listen('.PermanenceUpdated', e => {
      permanence.value = e.deleted ? null : e.permanence
    })
})

defineOptions({ layout: Layout })
const page = usePage()
const permanence = computed(() => page.props.permanence)
const destinations = computed(() => page.props.destinations)

const mobilePermanenceNumbers = computed(() => {
  const permDest = destinations.value.find(d => d.name === permanence.value.PSemaine)
  if (!permDest) return []
  return (permDest.numeros || []).filter(n => n.technologie && n.technologie.name === 'MOBILE')
})

const props = defineProps({
  numeros: { type: Array, default: () => [] }
})

const containerRef = ref(null)
const searchContainerRef = ref(null)
const searchSectionRef = ref(null)
const searchInputRef = ref(null)
const search = ref('')
const activeIndex = ref(-1)
const showSuggestions = ref(false)
const currentLang = ref('ar')
let debounceTimer = null

// Édition
const editingNumeroId = ref(null)
const editingValue = ref('')
const editInputRef = ref(null)

// Date et heure
const currentTime = ref('')
const currentDate = ref('')
function updateDateTime() {
  const now = new Date()
  currentTime.value = `${now.getHours().toString().padStart(2, '0')}:${now.getMinutes()
    .toString()
    .padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`
  currentDate.value = now.toLocaleDateString(currentLang.value === 'fr' ? 'fr-FR' : 'ar-DZ', {
    weekday: 'short',
    day: 'numeric',
    month: 'short'
  })
}
onMounted(() => {
  updateDateTime()
  setInterval(updateDateTime, 1000)
})

watch(currentLang, () => updateDateTime())

const normalize = v => String(v ?? '').trim().toLowerCase()

const selectedOrganisme = ref(null)
const selectedDestination = ref(null)

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

function clearSearch() {
  search.value = ''
  debouncedSearch.value = ''
  showSuggestions.value = false
}

function clearAllFilters() {
  selectedOrganisme.value = null
  selectedDestination.value = null
  search.value = ''
  debouncedSearch.value = ''
  localStorage.removeItem('selectedOrganisme')
  localStorage.removeItem('selectedDestination')
  localStorage.removeItem('search')
}

// Retour rapide à la recherche avec scroll automatique
function quickBackToSearch() {
  clearAllFilters()
  nextTick(() => {
    if (searchInputRef.value) {
      searchInputRef.value.focus()
      // Scroll en douceur vers la section de recherche
      if (searchSectionRef.value) {
        searchSectionRef.value.scrollIntoView({ behavior: 'smooth', block: 'center' })
      }
    }
  })
}

const debouncedSearch = ref('')
watch(search, val => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => (debouncedSearch.value = val), 200)
  activeIndex.value = -1
  showSuggestions.value = true
  if (!val.trim()) clearAllFilters()
})

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

function handleContainerClick(event) {
  if (searchContainerRef.value && !searchContainerRef.value.contains(event.target)) {
    showSuggestions.value = false
    activeIndex.value = -1
  }
}

// Fonctions d'édition
function startEdit(numero) {
  // Vérifier si c'est un numéro mobile (technologie MOBILE)
  if (numero.technologie?.name?.toUpperCase() === 'MOBILE') {
    editingNumeroId.value = numero.id
    editingValue.value = numero.NDappel
    nextTick(() => {
      if (editInputRef.value) {
        editInputRef.value.focus()
        editInputRef.value.select()
      }
    })
  }
}

function saveEdit(numero) {
  if (editingValue.value !== numero.NDappel) {
    // Sauvegarder la modification
    router.post('/numeros/update-ndappel', 
      { id: numero.id, NDappel: editingValue.value }, 
      { preserveScroll: true, preserveState: true }
    )
  }
  cancelEdit()
}

function cancelEdit() {
  editingNumeroId.value = null
  editingValue.value = ''
}

const hoveredNumero = ref(null)

function showNotes(numero) {
  if (numero.notes?.length > 0) {
    hoveredNumero.value = numero
  }
}

function hideNotes() {
  hoveredNumero.value = null
}
</script>