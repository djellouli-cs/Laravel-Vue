<template>
  <div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Créer un Fax</h1>
        <Link
          :href="route('faxes.index')"
          class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
        >
          Retour
        </Link>
      </div>

      <div class="bg-white rounded-lg shadow-md p-6">
        <form @submit.prevent="submit">
          <div class="grid grid-cols-1 gap-6">
            
            <!-- NDappel & Organisme Searchable Dropdown -->
            <div class="relative">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Numéro d'appel (NDappel) ou Organisme *
              </label>
              <input
                type="text"
                v-model="ndappelSearch"
                @focus="showDropdown = true"
                @keydown.arrow-down.prevent="highlightIndex = Math.min(highlightIndex + 1, filteredNumeros.length - 1)"
                @keydown.arrow-up.prevent="highlightIndex = Math.max(highlightIndex - 1, 0)"
                @keydown.enter.prevent="selectHighlighted"
                placeholder="Tapez NDappel ou Organisme"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.NDappel }"
              />
              <ul
                v-if="showDropdown && filteredNumeros.length"
                class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-y-auto shadow-lg"
              >
                <li
                  v-for="(numero, index) in filteredNumeros"
                  :key="numero.id"
                  @mousedown.prevent="selectNumero(numero.NDappel)"
                  :class="[
                    'px-4 py-2 cursor-pointer hover:bg-blue-100',
                    highlightIndex === index ? 'bg-blue-200 font-semibold' : ''
                  ]"
                >
                  {{ numero.NDappel }} - {{ numero.destination?.organisme?.name || 'N/A' }}
                </li>
              </ul>
              <p v-if="errors.NDappel" class="mt-1 text-sm text-red-600">
                {{ errors.NDappel }}
              </p>
            </div>

            <!-- Description -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Description
              </label>
              <textarea
                v-model="form.description"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': errors.description }"
                placeholder="Description optionnelle du fax"
              ></textarea>
              <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                {{ errors.description }}
              </p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
              <Link
                :href="route('faxes.index')"
                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
              >
                Annuler
              </Link>
              <button
                type="submit"
                :disabled="processing"
                class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold py-2 px-4 rounded"
              >
                {{ processing ? 'Création...' : 'Créer le Fax' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  availableNumeros: Array,
  errors: Object,
})

const form = useForm({
  NDappel: '',
  description: '',
})
const processing = ref(false)

// NDappel Search Dropdown
const ndappelSearch = ref('')
const showDropdown = ref(false)
const highlightIndex = ref(0)

// Filter by NDappel OR Organisme name
const filteredNumeros = computed(() => {
  const q = ndappelSearch.value.toLowerCase()
  return props.availableNumeros.filter(n =>
    n.NDappel.toLowerCase().includes(q) ||
    (n.destination?.organisme?.name?.toLowerCase() || '').includes(q)
  )
})

function selectNumero(ndappel) {
  form.NDappel = ndappel
  ndappelSearch.value = ndappel
  showDropdown.value = false
  highlightIndex.value = 0
}

function selectHighlighted() {
  if (filteredNumeros.value[highlightIndex.value]) {
    selectNumero(filteredNumeros.value[highlightIndex.value].NDappel)
  }
}

// Close dropdown when clicking outside
function handleClickOutside(event) {
  if (!event.target.closest('.relative')) {
    showDropdown.value = false
  }
}
document.addEventListener('click', handleClickOutside)

// Submit form
const submit = () => {
  processing.value = true
  form.post(route('faxes.store'), {
    onFinish: () => {
      processing.value = false
    },
  })
}
</script>