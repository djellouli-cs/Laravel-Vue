<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

// =====================
// Props
// =====================
const props = defineProps({
  factures: {
    type: Array,
    default: () => []
  }
})

// =====================
// Local Language (FR / AR only)
// =====================
const lang = ref('fr') // default language

function switchLang(l) {
  if (!['fr', 'ar'].includes(l)) return
  lang.value = l
}

// RTL detection for this component
const isRTL = computed(() => lang.value === 'ar')

// =====================
// Translations
// =====================
const t = {
  fr: {
    title: "Toutes les factures",
    search: "Rechercher par facture ou numero...",
    tech: "Filtrer par Technologie",
    org: "Filtrer par Organisme",
    reset: "Réinitialiser",
    empty: "Aucune facture trouvée",
    headers: {
      id: "ID",
      facture: "Facture",
      numero: "Numéro",
      destination: "Destination",
      organisme: "Organisme",
      technologie: "Technologie"
    }
  },
  ar: {
    title: "كل الفواتير",
    search: "ابحث برقم الفاتورة أو الرقم...",
    tech: "تصفية حسب التكنولوجيا",
    org: "تصفية حسب الهيئة",
    reset: "إعادة تعيين",
    empty: "لا توجد فواتير",
    headers: {
      id: "id",
      facture: "الفاتورة",
      numero: "الرقم",
      destination: "الوجهة",
      organisme: "الهيئة",
      technologie: "التكنولوجيا"
    }
  }
}

const tr = computed(() => t[lang.value])

// =====================
// Filters
// =====================
const searchQuery = ref('')
const selectedTechnologies = ref([])
const selectedOrganismes = ref([])

// Dropdown states
const techDropdownOpen = ref(false)
const orgDropdownOpen = ref(false)

// Dropdown refs for click outside
const techDropdownRef = ref(null)
const orgDropdownRef = ref(null)

// =====================
// Dynamic name helper (FR/AR)
// =====================
function getName(obj) {
  if (!obj) return ''
  return lang.value === 'fr'
    ? obj.name_fr || obj.name || ''
    : obj.name || obj.name_fr || ''
}

// =====================
// Click outside dropdowns
// =====================
function handleClickOutside(event) {
  if (techDropdownRef.value && !techDropdownRef.value.contains(event.target)) {
    techDropdownOpen.value = false
  }
  if (orgDropdownRef.value && !orgDropdownRef.value.contains(event.target)) {
    orgDropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})

// =====================
// Computed lists
// =====================
const allTechnologies = computed(() => {
  const set = new Set()
  props.factures.forEach(f =>
    f.numeros.forEach(n => {
      const name = getName(n.technologie)
      if (name) set.add(name)
    })
  )
  return Array.from(set)
})

const allOrganismes = computed(() => {
  const set = new Set()
  props.factures.forEach(f =>
    f.numeros.forEach(n => {
      const name = getName(n.organisme)
      if (name) set.add(name)
    })
  )
  return Array.from(set)
})

// =====================
// Filtering factures
// =====================
const filteredFactures = computed(() => {
  return props.factures
    .map(f => {
      let numeros = f.numeros.filter(n => {
        const query = searchQuery.value.toLowerCase()
        return (
          !query ||
          (f.facture && f.facture.toLowerCase().includes(query)) ||
          (n.NDappel && n.NDappel.toLowerCase().includes(query))
        )
      })

      if (selectedTechnologies.value.length > 0) {
        numeros = numeros.filter(n =>
          selectedTechnologies.value.includes(getName(n.technologie))
        )
      }

      if (selectedOrganismes.value.length > 0) {
        numeros = numeros.filter(n =>
          selectedOrganismes.value.includes(getName(n.organisme))
        )
      }

      if (numeros.length === 0) return null
      return { ...f, numeros }
    })
    .filter(f => f !== null)
})

// =====================
// Reset filters
// =====================
function resetFilters() {
  searchQuery.value = ''
  selectedTechnologies.value = []
  selectedOrganismes.value = []
}
</script>

<template>
  <div
    :dir="isRTL ? 'rtl' : 'ltr'"
    :class="['p-6 bg-gray-100 min-h-screen', isRTL ? 'text-right' : 'text-left']"
  >
    <!-- Language Switch -->
    <div class="flex justify-end gap-2 mb-2">
      <button @click="switchLang('fr')" class="px-2 py-1 border rounded">FR</button>
      <button @click="switchLang('ar')" class="px-2 py-1 border rounded">AR</button>
    </div>

    <h1 class="text-2xl font-bold mb-4">{{ tr.title }}</h1>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4 mb-4 items-center">
      <!-- Search -->
      <input
        v-model="searchQuery"
        type="text"
        :placeholder="tr.search"
        class="px-4 py-2 border rounded-lg w-full md:w-1/3"
      />

      <!-- Technologie Dropdown -->
      <div class="relative w-full md:w-1/4" ref="techDropdownRef">
        <button
          @click.stop="techDropdownOpen = !techDropdownOpen"
          :class="['w-full px-4 py-2 border rounded-lg bg-white flex', isRTL ? 'flex-row-reverse' : 'justify-between']"
        >
          <span>{{ selectedTechnologies.length === 0 ? tr.tech : selectedTechnologies.join(', ') }}</span>
          ▼
        </button>
        <div v-if="techDropdownOpen" class="absolute z-10 w-full bg-white border rounded mt-1 max-h-48 overflow-y-auto">
          <div
            v-for="tech in allTechnologies"
            :key="tech"
            :class="['flex items-center px-4 py-2 hover:bg-gray-100', isRTL ? 'flex-row-reverse' : '']"
          >
            <input type="checkbox" :value="tech" v-model="selectedTechnologies" class="mr-2" />
            {{ tech }}
          </div>
        </div>
      </div>

      <!-- Organisme Dropdown -->
      <div class="relative w-full md:w-1/4" ref="orgDropdownRef">
        <button
          @click.stop="orgDropdownOpen = !orgDropdownOpen"
          :class="['w-full px-4 py-2 border rounded-lg bg-white flex', isRTL ? 'flex-row-reverse' : 'justify-between']"
        >
          <span>{{ selectedOrganismes.length === 0 ? tr.org : selectedOrganismes.join(', ') }}</span>
          ▼
        </button>
        <div v-if="orgDropdownOpen" class="absolute z-10 w-full bg-white border rounded mt-1 max-h-48 overflow-y-auto">
          <div
            v-for="org in allOrganismes"
            :key="org"
            :class="['flex items-center px-4 py-2 hover:bg-gray-100', isRTL ? 'flex-row-reverse' : '']"
          >
            <input type="checkbox" :value="org" v-model="selectedOrganismes" class="mr-2" />
            {{ org }}
          </div>
        </div>
      </div>

      <!-- Reset Button -->
      <button @click="resetFilters" class="px-4 py-2 bg-red-500 text-white rounded-lg">
        {{ tr.reset }}
      </button>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full bg-white border rounded-lg">
        <thead class="bg-gray-200">
          <tr>
            <th :class="isRTL ? 'text-right px-4 py-2 border-b' : 'text-left px-4 py-2 border-b'">{{ tr.headers.id }}</th>
            <th :class="isRTL ? 'text-right px-4 py-2 border-b' : 'text-left px-4 py-2 border-b'">{{ tr.headers.facture }}</th>
            <th :class="isRTL ? 'text-right px-4 py-2 border-b' : 'text-left px-4 py-2 border-b'">{{ tr.headers.numero }}</th>
            <th :class="isRTL ? 'text-right px-4 py-2 border-b' : 'text-left px-4 py-2 border-b'">{{ tr.headers.destination }}</th>
            <th :class="isRTL ? 'text-right px-4 py-2 border-b' : 'text-left px-4 py-2 border-b'">{{ tr.headers.organisme }}</th>
            <th :class="isRTL ? 'text-right px-4 py-2 border-b' : 'text-left px-4 py-2 border-b'">{{ tr.headers.technologie }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="f in filteredFactures" :key="f.id">
            <td class="px-4 py-2 border-b">{{ f.id }}</td>
            <td class="px-4 py-2 border-b">{{ f.facture }}</td>
            <td class="px-4 py-2 border-b">
              <div v-for="n in f.numeros" :key="n.id">{{ n.NDappel }}</div>
            </td>
            <td class="px-4 py-2 border-b">
              <div v-for="n in f.numeros" :key="n.id">{{ getName(n.destination) || '-' }}</div>
            </td>
            <td class="px-4 py-2 border-b">
              <div v-for="n in f.numeros" :key="n.id">{{ getName(n.organisme) || '-' }}</div>
            </td>
            <td class="px-4 py-2 border-b">
              <div v-for="n in f.numeros" :key="n.id">{{ getName(n.technologie) || '-' }}</div>
            </td>
          </tr>
          <tr v-if="filteredFactures.length === 0">
            <td colspan="6" class="text-center text-gray-500 py-4">{{ tr.empty }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>