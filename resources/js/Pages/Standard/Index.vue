<template>
  <div
    ref="containerRef"
    :dir="currentLang === 'ar' ? 'rtl' : 'ltr'"
    class="min-h-screen bg-gradient-to-br from-green-50 to-white py-10"
  >
    <div class="p-8 max-w-5xl mx-auto bg-white rounded-2xl shadow-xl border border-green-100">
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
            📞 {{ num.NDappel }} — {{ num.nom }}
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

      <!-- 📋 Table -->
      <table class="min-w-full border-collapse border mt-4">
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

            <!-- ✅ Editable NDappel on double-click if technologie = MOBILE -->
            <td class="border p-2" @dblclick="enableEdit(numero)">
              <input
                v-if="numero.isEditing && numero.technologie?.name?.toUpperCase() === 'MOBILE'"
                v-model="numero.NDappel"
                @blur="saveNDappel(numero)"
                class="border border-green-300 rounded-md px-2 py-1 w-full"
                placeholder="Edit NDappel"
              />
              <span v-else>{{ numero.NDappel }}</span>
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

defineOptions({ layout: Layout })

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

// 🔍 Debounce
const debouncedSearch = ref('')
watch(search, val => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => (debouncedSearch.value = val), 200)
  activeIndex.value = -1
  showSuggestions.value = true
})

// 📋 Filtered table
const displayedNumeros = computed(() => {
  const q = normalize(debouncedSearch.value)
  return props.numeros.filter(n => {
    const NDappelStr = String(n.NDappel ?? '').toLowerCase()
    return (
      NDappelStr.includes(q) ||
      normalize(n.nom).includes(q) ||
      normalize(currentLang.value === 'fr' ? n.organisme?.name_fr : n.organisme?.name).includes(q) ||
      normalize(currentLang.value === 'fr' ? n.destination?.name_fr : n.destination?.name).includes(q) ||
      normalize(currentLang.value === 'fr' ? n.technologie?.name_fr : n.technologie?.name).includes(q)
    )
  })
})

// 🟢 Enable editing on double-click (only for MOBILE)
function enableEdit(numero) {
  if (numero.technologie?.name?.toUpperCase() === 'MOBILE') {
    numero.isEditing = true
  }
}

// 💾 Save NDappel after edit
function saveNDappel(numero) {
  numero.isEditing = false
  router.post(
    '/numeros/update-ndappel',
    { id: numero.id, NDappel: numero.NDappel },
    { preserveScroll: true, preserveState: true }
  )
}

// 💡 Suggestions
const filteredSuggestions = computed(() => {
  const q = normalize(search.value)
  if (!q) return []
  return props.numeros.filter(n => {
    const NDappelStr = String(n.NDappel ?? '').toLowerCase()
    return NDappelStr.includes(q) || normalize(n.nom).includes(q)
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

function onClickOutside(e) {
  if (!containerRef.value) return
  if (!containerRef.value.contains(e.target)) {
    showSuggestions.value = false
    activeIndex.value = -1
  }
}

onMounted(() => document.addEventListener('click', onClickOutside))
onBeforeUnmount(() => document.removeEventListener('click', onClickOutside))
</script>

<style scoped>
.bg-green-50 {
  background-color: #f0fdf4;
}
</style>
