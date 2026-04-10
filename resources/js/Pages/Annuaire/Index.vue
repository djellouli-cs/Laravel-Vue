<template>
  <div ref="containerRef" class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-10">
    <div class="p-8 max-w-4xl mx-auto bg-white rounded-2xl shadow-xl border border-blue-100">
      <h1 class="text-3xl font-extrabold mb-8 text-blue-800 flex items-center gap-2">
        <span>🔍</span> <span>Rechercher un numéro</span>
      </h1>

      <!-- Champ de recherche -->
      <div class="mb-4">
        <input
          v-model="search"
          @keydown="handleKeydown"
          @focus="showSuggestions = true"
          type="text"
          placeholder="Entrez le Numéro"
          class="border-2 border-blue-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 rounded-lg px-4 py-3 w-full text-lg transition"
        />
      </div>

      <!-- Suggestions -->
      <ul
        v-if="showSuggestionList && filteredSuggestions.length > 0"
        class="border border-blue-200 bg-white rounded-xl shadow-lg mb-6 max-h-60 overflow-y-auto animate-fade-in"
      >
        <li
          v-for="(num, index) in filteredSuggestions"
          :key="num.id ?? index"
          @click="selectSuggestion(String(num.NDappel ?? ''))"
          :class="[ 
            'px-5 py-3 cursor-pointer transition', 
            activeIndex === index ? 'bg-blue-100 font-semibold text-blue-800' : 'hover:bg-blue-50' 
          ]"
        >
          {{ num.NDappel }}
        </li>
      </ul>

      <!-- Affichage des détails -->
      <template v-if="searchedNumero">
        <transition name="fade">
          <div class="bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-2xl p-8 shadow-lg animate-fade-in">
            
            <!-- Bouton éditer -->
            <div class="flex justify-end mb-4">
              <button
                @click="editNumero(searchedNumero)"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold shadow transition"
              >
                ✏️ Éditer ce numéro
              </button>
            </div>

            <h2 class="text-2xl font-bold mb-6 text-emerald-600 text-center underline" v-if="searchedNumero.organisme">
              {{ searchedNumero.destination.name_fr }}
            </h2>

            <!-- Détails principaux -->
            <div class="grid grid-cols-2 gap-6 text-base">
            <h2 class="text-2xl font-bold mb-6 text-blue-700">
              Numéro : <span class="text-blue-900">{{ searchedNumero.NDappel }}</span>
            </h2>
              <div><span class="font-semibold text-blue-700">Position :</span> {{ searchedNumero.Position }}</div>

              <div>
                <span class="font-semibold text-blue-700">Organisme : </span>
                <span v-if="searchedNumero.organisme">
                  {{ searchedNumero.organisme.name_fr }}
                  <span class=" text-amber-500 italic ml-1">{{ searchedNumero.organisme.name }}</span>
                </span>
                <span v-else>—</span>
              </div>

              <div>
                <span class="font-semibold text-blue-700">Destination : </span>
                <span v-if="searchedNumero.destination">
                  {{ searchedNumero.destination.name_fr }}
                  <span class="text-amber-500 italic ml-1">{{ searchedNumero.destination.name }}</span>
                </span>
                <span v-else>—</span>
              </div>

              <!-- Groupes -->
              <div class="col-span-2">
                <span class="font-semibold text-blue-700">Groupes :</span>
                <div v-if="searchedNumero.destination?.groupes?.length" class="flex flex-wrap gap-2 mt-2">
                  <span 
                    v-for="groupe in searchedNumero.destination.groupes" 
                    :key="groupe.id ?? groupe"
                    class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full border border-blue-200"
                  >
                    📋 {{ groupe.groupes }}
                  </span>
                </div>
                <span v-else class="text-gray-400">Aucun groupe assigné</span>
              </div>

              <div><span class="font-semibold text-blue-700">Classe :</span> {{ searchedNumero.classe?.classe || '—' }}</div>
              <div><span class="font-semibold text-blue-700">Type :</span> {{ searchedNumero.type?.name || '—' }}</div>

              <!-- Réserve -->
              <div class="col-span-2">
                <span class="font-semibold text-blue-700">Réserve :</span>
                <span v-if="searchedNumero.reserve">
                  {{ searchedNumero.reserve.reserve ?? '—' }}
                  <span 
                    class="ml-2"
                    :class="searchedNumero.reserve?.is_reserved ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'"
                  >
                    — {{ searchedNumero.reserve?.is_reserved ? '📌 Réservé' : '❌ Non réservé' }}
                  </span>
                </span>
                <span v-else>—</span>
              </div>

              <div><span class="font-semibold text-blue-700">Technologie :</span> {{ searchedNumero.technologie?.name || '—' }}</div>

              <!-- Facture -->
              <div class="col-span-2">
                <span class="font-semibold text-blue-700">Facture :</span>
                <span v-if="searchedNumero.facture">
                  {{ searchedNumero.facture.facture ?? '—' }}
                  <span 
                    class="ml-2"
                    :class="searchedNumero.facture?.is_factured ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'"
                  >
                    — {{ searchedNumero.facture?.is_factured ? '💰 Facturé' : '❌ Non facturé' }}
                  </span>
                </span>
                <span v-else>—</span>
              </div>

              <!-- Poste -->
              <div class="col-span-2">
                <span class="font-semibold text-blue-700">Poste :</span>
                <span v-if="searchedNumero.post">
                  {{ searchedNumero.post.post }} — Marque : {{ searchedNumero.post.marque }}
                </span>
                <span v-else>—</span>
              </div>

              <!-- Fax -->
              <div class="col-span-2">
                <span class="font-semibold text-blue-700">Fax :</span>
                <span v-if="searchedNumero.fax">
                  {{ searchedNumero.fax.description ?? '—' }}
                  <span 
                    class="ml-2"
                    :class="searchedNumero.fax?.NDappel ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'"
                  >
                    — {{ searchedNumero.fax?.NDappel ? '📠 FAX' : '❌ Non FAX' }}
                  </span>
                </span>
                <span v-else>—</span>
              </div>

              <div><span class="font-semibold text-blue-700">Matricule :</span> {{ searchedNumero.matricule?.matricule || '—' }}</div>
              <div>
                <span class="font-semibold text-blue-700">Service : </span>
                <span v-if="searchedNumero.service">
                  {{ searchedNumero.service.name_fr }}
                  <span v-if="searchedNumero.service.name_fr" class="text-amber-500 italic ml-1">{{ searchedNumero.service.name }}</span>
                </span>
                <span v-else>—</span>
              </div>
            </div>

            <!-- Acheminements -->
            <div class="mt-8">
              <h3 class="font-semibold text-lg mb-3 text-blue-800 flex items-center gap-2">📦 Acheminements</h3>
              <ul v-if="searchedNumero.acheminements?.length" class="list-disc ml-7 text-base text-blue-900">
                <li v-for="ach in searchedNumero.acheminements" :key="ach.id ?? ach.acheminement">
                  {{ ach.acheminement }} — {{ formatDate(ach.updated_at) }}
                </li>
              </ul>
              <p v-else class="text-gray-400 text-base">Aucun acheminement disponible.</p>
            </div>

            <!-- Notes -->
            <div class="mt-8">
              <h3 class="font-semibold text-lg mb-3 text-blue-800 flex items-center gap-2">📝 Notes</h3>
              <ul v-if="searchedNumero.notes?.length" class="list-disc ml-7 text-base text-blue-900">
                <li v-for="note in searchedNumero.notes" :key="note.id ?? note.created_at">
                  {{ note.content }}
                  <span class="text-gray-400 text-xs"> ({{ formatDate(note.updated_at, true) }}) </span>
                </li>
              </ul>
              <p v-else class="text-gray-400 text-base">Aucune note disponible pour ce numéro.</p>
            </div>
          </div>
        </transition>
      </template>

      <div v-else-if="debouncedSearch.trim() !== ''" class="text-center text-gray-400 mt-12 text-lg">
        ❌ Aucun numéro trouvé avec NDappel "<span class="font-semibold">{{ debouncedSearch }}</span>"
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutAnnuaire.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  numeros: { type: Array, default: () => [] }
})

const containerRef = ref(null)
const search = ref('')
const debouncedSearch = ref('')
const activeIndex = ref(-1)
const showSuggestions = ref(false)
let debounceTimer = null

/* 👉 Local reactive copy for real-time updates */
const localNumeros = ref([...props.numeros])

const normalize = (v) => String(v ?? '').trim().toLowerCase()
const localNotes = ref([])

onMounted(() => {

  window.Echo.channel('notes')
    .listen('.NoteUpdated', (e) => {

      const note = e.note

      const index = localNotes.value.findIndex(
        n => n.id === note.id
      )

      // DELETE
      if (e.deleted) {
        if (index !== -1) {
          localNotes.value.splice(index, 1)
        }
        return
      }

      // UPDATE or CREATE
      if (index !== -1) {
        localNotes.value.splice(index, 1, note)
      } else {
        localNotes.value.unshift(note)
      }
    })
})

onBeforeUnmount(() => {
  window.Echo.leaveChannel('notes')
})
// Debounce input
watch(search, (val) => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    debouncedSearch.value = val
  }, 300)
  activeIndex.value = -1
  showSuggestions.value = Boolean(val && String(val).trim() !== '')
})

// Match exact NDappel
const searchedNumero = computed(() => {
  const q = normalize(debouncedSearch.value)
  if (!q) return null
  return localNumeros.value.find((n) => normalize(n.NDappel) === q) || null
})

// Suggestions
const filteredSuggestions = computed(() => {
  const q = normalize(search.value)
  if (!q) return []
  return localNumeros.value.filter((n) => normalize(n.NDappel).includes(q))
})

const showSuggestionList = computed(() => {
  return showSuggestions.value && filteredSuggestions.value.length > 0 && !searchedNumero.value
})

function selectSuggestion(ndappel) {
  search.value = ndappel
  debouncedSearch.value = ndappel
  activeIndex.value = -1
  showSuggestions.value = false
}

function handleKeydown(event) {
  const max = filteredSuggestions.value.length - 1
  if (event.key === 'ArrowDown') {
    event.preventDefault()
    activeIndex.value = activeIndex.value < max ? activeIndex.value + 1 : 0
    showSuggestions.value = true
  } else if (event.key === 'ArrowUp') {
    event.preventDefault()
    activeIndex.value = activeIndex.value > 0 ? activeIndex.value - 1 : max
    showSuggestions.value = true
  } else if (event.key === 'Enter') {
    event.preventDefault()
    if (activeIndex.value >= 0 && filteredSuggestions.value[activeIndex.value]) {
      selectSuggestion(String(filteredSuggestions.value[activeIndex.value].NDappel ?? ''))
    } else if (String(search.value).trim() !== '') {
      debouncedSearch.value = search.value
      showSuggestions.value = false
    }
  } else if (event.key === 'Escape') {
    showSuggestions.value = false
    activeIndex.value = -1
  }
}

// Close suggestions when a numero is found
watch(searchedNumero, (val) => {
  if (val) {
    showSuggestions.value = false
    activeIndex.value = -1
  }
})

function onClickOutside(e) {
  if (!containerRef.value) return
  if (!containerRef.value.contains(e.target)) {
    showSuggestions.value = false
    activeIndex.value = -1
  }
}
/* =========================
   🚀 REAL TIME ECHO
========================= */
onMounted(() => {
  document.addEventListener('click', onClickOutside)

  // optional URL prefill
  const urlParams = new URLSearchParams(window.location.search)
  const ndappel = urlParams.get('ndappel')
  if (ndappel) {
    search.value = ndappel
    debouncedSearch.value = ndappel
  }

  // REALTIME LISTENER
  window.Echo.channel('numeros')
    .listen('.NumeroUpdated', (e) => {

      const updated = e.numero
      //console.log('REALTIME EVENT:', e)

      const index = localNumeros.value.findIndex(
        (n) => n.id === updated.id
      )

      // DELETE
      if (e.deleted) {
        if (index !== -1) {
          localNumeros.value.splice(index, 1)
        }
        return
      }

      // UPDATE
      if (index !== -1) {
        localNumeros.value[index] = {
          ...localNumeros.value[index],
          ...updated
        }
      } 
      // CREATE
      else {
        localNumeros.value.unshift(updated)
      }
    })
})

onBeforeUnmount(() => {
  document.removeEventListener('click', onClickOutside)

  // optional cleanup (good practice)
  window.Echo.leaveChannel('numeros')
})

// Format date
function formatDate(dateIso, withTime = false) {
  if (!dateIso) return '—'
  try {
    const d = new Date(dateIso)
    if (isNaN(d)) return '—'
    return withTime ? d.toLocaleString('fr-FR') : d.toLocaleDateString('fr-FR')
  } catch {
    return '—'
  }
}

// Redirection vers édition
function editNumero(numero) {
  if (numero?.id) {
    router.visit(`/numeros/${numero.id}/edit`)
  }
}
</script>
<style scoped>
.bg-blue-100 { background-color: #ebf8ff; }
</style>
