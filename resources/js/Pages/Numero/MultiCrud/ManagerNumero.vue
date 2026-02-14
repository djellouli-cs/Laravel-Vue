<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Gestionnaire de Numéros</h1>
      <div class="flex space-x-2">
        <Link 
          :href="route('faxes.statistics')" 
          class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200"
        >
          📊 Statistiques Fax
        </Link>
        <Link 
          :href="route('faxes.index')" 
          class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200"
        >
          📠 Gérer les Fax
        </Link>
        <Link 
          :href="route('groupe.manage')" 
          class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors duration-200"
        >
          📋 Gérer les Groupes
        </Link>
      </div>
    </div>

    <!-- Flash Messages -->
    <div v-if="flash.value && flash.value.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
      {{ flash.value.success }}
    </div>
    
    <!-- Error Messages -->
    <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      <h3 class="font-semibold mb-2">Erreurs de validation :</h3>
      <ul class="list-disc ml-4">
        <li v-for="(error, field) in form.errors" :key="field">
          {{ error }}
        </li>
      </ul>
    </div>
    


    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- NDappel -->
      <div>
        <label for="NDappel" class="block text-sm font-medium mb-1">NDappel</label>
        <input
          id="NDappel"
          v-model="form.NDappel"
          type="text"
          :class="[
            'border rounded px-3 py-2 w-full',
            form.errors.NDappel ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'focus:border-blue-500 focus:ring-blue-500'
          ]"
          required
        />
        <p v-if="form.errors.NDappel" class="text-red-600 text-sm mt-1">{{ form.errors.NDappel }}</p>
      </div>

      <!-- Position -->
      <div>
        <label for="Position" class="block text-sm font-medium mb-1">Position</label>
        <input
          id="Position"
          v-model="form.Position"
          type="text"
          list="positionOptions"
          :class="[
            'border rounded px-3 py-2 w-full',
            form.errors.Position ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'focus:border-blue-500 focus:ring-blue-500'
          ]"
          required
        />
        <datalist id="positionOptions">
          <option>EXTERNE</option>
          <option>PTT (1er 112)</option>
          <option>PTT (2eme 112)</option>
          <option>0 0</option>
          <option>4 0</option>
        </datalist>
        <p v-if="form.errors.Position" class="text-red-600 text-sm mt-1">{{ form.errors.Position }}</p>
        <p v-if="form.Position && calcPosition()" class="text-sm mt-1" :class="{
          'text-green-600': calcPosition().includes('Bravo'),
          'text-yellow-600': calcPosition().includes('Ajouter'),
          'text-red-600': calcPosition().includes('Faute') || calcPosition().includes('espace')
        }">{{ calcPosition() }}</p>
      </div>

      <!-- Organisme -->
      <div>
        <label for="organisme_id" class="block text-sm font-medium mb-1">Organisme</label>
        <select
          id="organisme_id"
          v-model.number="form.organisme_id"
          class="border rounded px-3 py-2 w-full"
        >
          <option value="">--</option>
          <option v-for="org in organismes" :key="org.id" :value="org.id">{{ org.name }}</option>
        </select>
      </div>

      <!-- Destination -->
      <div>
        <label for="destination_id" class="block text-sm font-medium mb-1">Destination</label>
        <select
          id="destination_id"
          v-model.number="form.destination_id"
          class="border rounded px-3 py-2 w-full"
        >
          <option value="">--</option>
          <option v-for="dest in filteredDestinations" :key="dest.id" :value="dest.id">{{ dest.name }}</option>
        </select>
      </div>

      <!-- Dynamic Selects -->
      <div v-for="(options, key) in restSelectOptions" :key="key">
        <label :for="key" class="block text-sm font-medium mb-1">{{ getFieldLabel(key) }}</label>
        <select
          :id="key"
          v-model.number="form[key]"
          class="border rounded px-3 py-2 w-full"
        >
          <option value="">--</option>
          <option v-for="opt in options" :key="opt.id" :value="opt.id">
            {{ opt.name || opt.classe || opt.reserve || opt.matricule || opt.facture || opt.NDappel }}
          </option>
        </select>
      </div>

      <!-- Buttons -->
      <div class="col-span-full flex flex-wrap items-center gap-2">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded">
          {{ form.id ? 'Mettre à jour' : 'Créer' }}
        </button>
        <button type="button" @click="reset" class="bg-gray-300 px-4 py-1 rounded">Annuler</button>
      </div>
    </form>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Rechercher par NDappel ou Position..."
        class="border rounded px-3 py-2 w-full"
      />
    </div>

    <!-- Total Count -->
    <div class="mb-2 text-sm text-gray-700">
      Total des Numéros : {{ totalNumeros }}
    </div>

    <!-- Table -->
    <table class="w-full border border-collapse text-sm">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">NDappel</th>
          <th class="border px-2 py-1">Position</th>
          <th class="border px-2 py-1">Organisme</th>
          <th class="border px-2 py-1">Destination</th>
          <th class="border px-2 py-1">Classe</th>
          <th class="border px-2 py-1">Type</th>
          <th class="border px-2 py-1">Service</th>
          <th class="border px-2 py-1">Fax</th>
          <th class="border px-2 py-1">Acheminements</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="numero in paginatedNumeros" :key="numero.id">
          <td class="border px-2 py-1">{{ numero.id }}</td>
          <td class="border px-2 py-1">{{ numero.NDappel }}</td>
          <td class="border px-2 py-1">{{ numero.Position }}</td>
          <td class="border px-2 py-1">{{ numero.organisme?.name || '—' }}</td>
          <td class="border px-2 py-1">{{ numero.destination?.name || '—' }}</td>
          <td class="border px-2 py-1">{{ numero.classe?.classe || '—' }}</td>
          <td class="border px-2 py-1">{{ numero.type?.name || '—' }}</td>
          <td class="border px-2 py-1">{{ numero.service?.name || '—' }}</td>
          <td class="border px-2 py-1">
            <span v-if="numero.fax" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
              📠 Fax
            </span>
            <span v-else class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
              📞 Téléphone
            </span>
          </td>
          <td class="border px-2 py-1">
            <ul v-if="numero.acheminements?.length" class="list-disc ml-4">
              <li v-for="ach in numero.acheminements" :key="ach.id">
                {{ ach.acheminement }} — {{ new Date(ach.updated_at).toLocaleDateString() }}
              </li>
            </ul>
            <span v-else>—</span>
          </td>
          
          <td class="border px-2 py-1 space-x-2">
            <button @click="edit(numero)" class="text-blue-600 hover:underline">Modifier</button>
            <button @click="destroy(numero.id)" class="text-red-600 hover:underline">Supprimer</button>
          </td>
        </tr>
        <tr v-if="paginatedNumeros.length === 0">
          <td colspan="12" class="text-center text-gray-500 py-2">Aucun numéro trouvé.</td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center gap-4 items-center">
      <button
        @click="currentPage--"
        :disabled="currentPage === 1"
        class="px-3 py-1 border rounded"
      >
        Précédent
      </button>

      <span>Page {{ currentPage }} sur {{ totalPages }}</span>

      <button
        @click="currentPage++"
        :disabled="currentPage === totalPages"
        class="px-3 py-1 border rounded"
      >
        Suivant
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router, usePage, Link, useForm } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutEdit.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  numeros: Array,
  destinations: Array,
  classes: Array,
  types: Array,
  reserves: Array,
  technologies: Array,
  factures: Array,
  matricules: Array,
  organismes: Array,
  services: Array,
  errors: Object,
  flash: Object,
})

const page = usePage()
const flash = computed(() => props.flash || page.props.flash || {})



const form = useForm({
  id: null,
  NDappel: usePage().props.old?.NDappel || '',
  Position: usePage().props.old?.Position || '',
  TN: usePage().props.old?.TN || '',
  destination_id: usePage().props.old?.destination_id || null,
  classe_id: usePage().props.old?.classe_id || null,
  type_id: usePage().props.old?.type_id || null,
  reserve_id: usePage().props.old?.reserve_id || null,
  technologie_id: usePage().props.old?.technologie_id || null,
  facture_id: usePage().props.old?.facture_id || null,
  matricule_id: usePage().props.old?.matricule_id || null,
  organisme_id: usePage().props.old?.organisme_id || null,
  service_id: usePage().props.old?.service_id || null,
})

const search = ref('')
const currentPage = ref(1)
const itemsPerPage = 5

const filteredNumeros = computed(() => {
  const q = search.value.toLowerCase()
  return props.numeros.filter(n =>
    n.NDappel?.toLowerCase().includes(q) ||
    n.Position?.toLowerCase().includes(q) ||
    (n.fax ? 'fax' : 'téléphone').toLowerCase().includes(q)
  )
})

const paginatedNumeros = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return filteredNumeros.value.slice(start, start + itemsPerPage)
})

const totalNumeros = computed(() => filteredNumeros.value.length)

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(filteredNumeros.value.length / itemsPerPage))
})

// Remove any watcher or computed that auto-fills the form based on NDappel while editing
// No such watcher is present in the current code, but ensure the edit and reset logic is isolated

// The form is only updated by clicking Edit or Cancel
// The search input and filteredNumeros are only for the table, not for the form
// No changes needed to computed or watchers

// Optionally, add a comment to clarify this for future maintenance:
// ---
// NOTE: When editing, changing NDappel to an existing value will NOT auto-update the form with another numero's data.
// The form is only updated by clicking Edit (fills form) or Cancel (resets form).
// ---

const filteredDestinations = computed(() => {
  if (!form.organisme_id) return props.destinations
  return props.destinations.filter(d => d.organisme_id === form.organisme_id)
})

const restSelectOptions = {
  classe_id: props.classes,
  type_id: props.types,
  reserve_id: props.reserves,
  technologie_id: props.technologies,
  facture_id: props.factures,
  matricule_id: props.matricules,
  service_id: props.services,
}

// Function to get French labels for form fields
function getFieldLabel(key) {
  const labels = {
    'classe_id': 'Classe',
    'type_id': 'Type',
    'reserve_id': 'Réserve',
    'technologie_id': 'Technologie',
    'facture_id': 'Facture',
    'matricule_id': 'Matricule',
    'service_id': 'Service',
  }
  return labels[key] || key.replace('_id', '')
}

// Function to validate Position field format (handles both PTT and TN formats)
function calcPosition() {
  if (!form.Position) {
    return ''
  }
  // Position = EXTERNE → always BRAVO
if (form.Position === 'EXTERNE') {
  return "Bravo  😁 ";
}
  // PTT format validation
  if (form.Position.substr(0, 6) === "PTT (1") {
    var d = form.Position.length - 1;
    if (form.Position.substr(d, 1) === ' ' && d >= 14) {
      return " il faut pas terminé avec espace.  😤 ";
    }
    if (form.Position === 'PTT (1er 112)') {
      return " Ajouter un espace svp.😇";
    } else if (form.Position === 'PTT (1er 112) ') {
      return " Ajouter la Position svp.😇";
    } else if (form.Position.length <= 17 && form.Position.substr(0, 14) === 'PTT (1er 112) ' && Number(form.Position.substr(14, 3)) && parseInt(form.Position.substr(14, 3), 10) <= 120) {
      return "Bravo  😁 ";
    } else {
      return "Faute  😡 ";
    }
  } else if (form.Position.substr(0, 6) === "PTT (2") {
    var d = form.Position.length - 1;
    if (form.Position.substr(d, 1) === ' ' && d >= 15) {
      return " il faut pas terminé avec espace.  😤 ";
    }
    if (form.Position === 'PTT (2eme 112)') {
      return " Ajouter un espace svp.😇";
    } else if (form.Position === 'PTT (2eme 112) ') {
      return " Ajouter la Position svp.😇";
    } else if (form.Position.length <= 18 && form.Position.substr(0, 15) === 'PTT (2eme 112) ' && Number(form.Position.substr(15, 3)) && parseInt(form.Position.substr(15, 3), 10) <= 120) {
      return "Bravo  😁 ";
    } else {
      return "Faute  😡 ";
    }
  }
  
  // TN format validation - Position 0 0 format
  if (form.Position.substr(0, 2) == '0 ') {
    if (form.Position === '0 0') {
      return " Ajouter un espace svp.😇"
    } else if (form.Position === '0 0 ') {
      return " Ajouter la carte svp.😇"
    } else if (form.Position.length === 5 && form.Position.substr(0, 4) === '0 0 ' && Number(form.Position.substr(4, 1)) && parseInt(form.Position.substr(4, 1), 10) <= 9) {
      return " Ajouter un espace svp.😇"
    } else if (form.Position.length === 6 && form.Position.substr(0, 4) === '0 0 ' && Number(form.Position.substr(4, 2)) && parseInt(form.Position.substr(4, 2), 10) <= 15 && parseInt(form.Position.substr(4, 2), 10) > 9) {
      return " Ajouter un espace svp.😇"
    } else if (form.Position.length === 6 && form.Position.substr(0, 4) === '0 0 ' && Number(form.Position.substr(4, 1)) && parseInt(form.Position.substr(4, 1), 10) <= 9 && form.Position.substr(5, 1) === ' ') {
      return " Ajouter position svp.😇"
    } else if (form.Position.length === 7 && form.Position.substr(0, 4) === '0 0 ' && Number(form.Position.substr(4, 2)) && parseInt(form.Position.substr(4, 2), 10) <= 15 && parseInt(form.Position.substr(4, 2), 10) > 9 && form.Position.substr(6, 1) === ' ') {
      return " Ajouter position svp.😇"
    } else if (form.Position.length <= 8 && form.Position.substr(0, 4) === '0 0 ' && Number(form.Position.substr(4, 1)) && parseInt(form.Position.substr(4, 1), 10) <= 9 && form.Position.substr(5, 1) === ' ' && Number(form.Position.substr(6, 2)) && parseInt(form.Position.substr(6, 2), 10) <= 15) {
      return " Bravo  😁 "
    } else if (form.Position.length <= 9 && form.Position.substr(0, 4) === '0 0 ' && Number(form.Position.substr(4, 2)) && parseInt(form.Position.substr(4, 2), 10) <= 15 && parseInt(form.Position.substr(4, 2), 10) > 9 && form.Position.substr(6, 1) === ' ' && Number(form.Position.substr(7, 2)) && parseInt(form.Position.substr(7, 2), 10) <= 15) {
      return " Bravo  😁 "
    } else {
      return " Faute  😡 "
    }
  }
  // TN format validation - Position 4 0 format
  else if (form.Position.substr(0, 2) == '4 ') {
    if (form.Position === '4 0') {
      return " Ajouter un espace svp.😇"
    } else if (form.Position === '4 0 ') {
      return " Ajouter la carte svp.😇"
    } else if (form.Position.length === 5 && form.Position.substr(0, 4) === '4 0 ' && Number(form.Position.substr(4, 1)) && parseInt(form.Position.substr(4, 1), 10) <= 9) {
      return " Ajouter un espace svp.😇"
    } else if (form.Position.length === 6 && form.Position.substr(0, 4) === '4 0 ' && Number(form.Position.substr(4, 2)) && parseInt(form.Position.substr(4, 2), 10) <= 15 && parseInt(form.Position.substr(4, 2), 10) > 9) {
      return " Ajouter un espace svp.😇"
    } else if (form.Position.length === 6 && form.Position.substr(0, 4) === '4 0 ' && Number(form.Position.substr(4, 1)) && parseInt(form.Position.substr(4, 1), 10) <= 9 && form.Position.substr(5, 1) === ' ') {
      return " Ajouter position svp.😇"
    } else if (form.Position.length === 7 && form.Position.substr(0, 4) === '4 0 ' && Number(form.Position.substr(4, 2)) && parseInt(form.Position.substr(4, 2), 10) <= 15 && parseInt(form.Position.substr(4, 2), 10) > 9 && form.Position.substr(6, 1) === ' ') {
      return " Ajouter position svp.😇"
    } else if (form.Position.length <= 8 && form.Position.substr(0, 4) === '4 0 ' && Number(form.Position.substr(4, 1)) && parseInt(form.Position.substr(4, 1), 10) <= 9 && form.Position.substr(5, 1) === ' ' && Number(form.Position.substr(6, 2)) && parseInt(form.Position.substr(6, 2), 10) <= 15) {
      return " Bravo  😁 "
    } else if (form.Position.length <= 9 && form.Position.substr(0, 4) === '4 0 ' && Number(form.Position.substr(4, 2)) && parseInt(form.Position.substr(4, 2), 10) <= 15 && parseInt(form.Position.substr(4, 2), 10) > 9 && form.Position.substr(6, 1) === ' ' && Number(form.Position.substr(7, 2)) && parseInt(form.Position.substr(7, 2), 10) <= 15) {
      return " Bravo  😁 "
    } else {
      return " Faute  😡 "
    }
  }
  
  return " Format invalide. Utilisez PTT ou format TN (0 X ou 4 X) 😡"
}



const save = () => {
  if (form.id) {
    form.put(`/manageNumero/${form.id}`, {
      preserveScroll: true,
      onSuccess: () => reset(),
    })
  } else {
    form.post('/manageNumero', {
      preserveScroll: true,
      onSuccess: () => reset(),
    })
  }
}

const edit = (numero) => {
  form.reset()
  // Manually set form values instead of using fill
  form.id = numero.id
  form.NDappel = numero.NDappel
  form.Position = numero.Position
  form.TN = numero.TN
  form.destination_id = numero.destination_id
  form.classe_id = numero.classe_id
  form.type_id = numero.type_id
  form.reserve_id = numero.reserve_id
  form.technologie_id = numero.technologie_id
  form.facture_id = numero.facture_id
  form.matricule_id = numero.matricule_id
  form.organisme_id = numero.organisme_id
  form.service_id = numero.service_id
}

const destroy = (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce numéro ?')) {
    router.delete(`/manageNumero/${id}`, { preserveScroll: true })
  }
}

const reset = () => {
  form.reset()
  // Also reset the form fields to their initial state
  form.id = null
  form.NDappel = ''
  form.Position = ''
  form.TN = ''
  form.destination_id = null
  form.classe_id = null
  form.type_id = null
  form.reserve_id = null
  form.technologie_id = null
  form.facture_id = null
  form.matricule_id = null
  form.organisme_id = null
  form.service_id = null
}

const organismes = props.organismes
</script>
