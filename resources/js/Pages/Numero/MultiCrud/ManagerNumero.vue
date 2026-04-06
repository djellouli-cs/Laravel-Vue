<template>
  <div class="p-6">
    <!-- Header -->
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
    <div v-if="flash.value?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
      {{ flash.value.success }}
    </div>

    <!-- Validation Errors -->
    <div v-if="Object.keys(form.errors).length" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
      <h3 class="font-semibold mb-2">Erreurs de validation :</h3>
      <ul class="list-disc ml-4">
        <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
      </ul>
    </div>

    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- NDappel -->
      <div>
        <label for="NDappel" class="block text-sm font-medium mb-1">NDappel</label>
        <input
          id="NDappel"
          v-model.trim="form.NDappel"
          type="text"
          :class="inputClass('NDappel')"
          required
        />
        <p v-if="form.errors.NDappel" class="text-red-600 text-sm mt-1">{{ form.errors.NDappel }}</p>
      </div>

      <!-- Position -->
      <div>
        <label for="Position" class="block text-sm font-medium mb-1">Position</label>
        <input
          id="Position"
          v-model.trim="form.Position"
          type="text"
          list="positionOptions"
          :class="inputClass('Position')"
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
        <p v-if="form.Position" class="text-sm mt-1" :class="positionClass(calcPosition())">{{ calcPosition() }}</p>
      </div>

      <!-- Organisme -->
      <div>
        <label for="organisme_id" class="block text-sm font-medium mb-1">Organisme</label>
        <select id="organisme_id" v-model.number="form.organisme_id" class="border rounded px-3 py-2 w-full">
          <option value="">--</option>
          <option v-for="org in organismes" :key="org.id" :value="org.id">{{ org.name }}</option>
        </select>
      </div>

      <!-- Destination -->
      <div>
        <label for="destination_id" class="block text-sm font-medium mb-1">Destination</label>
        <select id="destination_id" v-model.number="form.destination_id" class="border rounded px-3 py-2 w-full">
          <option value="">--</option>
          <option v-for="dest in filteredDestinations" :key="dest.id" :value="dest.id">{{ dest.name }}</option>
        </select>
      </div>

      <!-- Dynamic Selects -->
      <div v-for="(options, key) in restSelectOptions" :key="key">
        <label :for="key" class="block text-sm font-medium mb-1">{{ getFieldLabel(key) }}</label>
        <select :id="key" v-model.number="form[key]" class="border rounded px-3 py-2 w-full">
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
      <button @click="currentPage--" :disabled="currentPage === 1" class="px-3 py-1 border rounded">Précédent</button>
      <span>Page {{ currentPage }} sur {{ totalPages }}</span>
      <button @click="currentPage++" :disabled="currentPage === totalPages" class="px-3 py-1 border rounded">Suivant</button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { router, usePage, Link, useForm } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutEdit.vue'
import '@/Echo.js'

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

const localNumeros = ref([...props.numeros])
const page = usePage()
const flash = computed(() => props.flash || page.props.flash || {})

const form = useForm({
  id: null,
  NDappel: '',
  Position: '',
  TN: '',
  destination_id: null,
  classe_id: null,
  type_id: null,
  reserve_id: null,
  technologie_id: null,
  facture_id: null,
  matricule_id: null,
  organisme_id: null,
  service_id: null,
})

const search = ref('')
const currentPage = ref(1)
const itemsPerPage = 5

watch(search, () => { currentPage.value = 1 })

const filteredNumeros = computed(() => {
  const q = search.value.toLowerCase()
  return localNumeros.value.filter(n =>
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
const totalPages = computed(() => Math.max(1, Math.ceil(filteredNumeros.value.length / itemsPerPage)))

const organismes = props.organismes
const filteredDestinations = computed(() =>
  form.organisme_id
    ? props.destinations.filter(d => d.organisme_id === form.organisme_id)
    : props.destinations
)

const restSelectOptions = {
  classe_id: props.classes,
  type_id: props.types,
  reserve_id: props.reserves,
  technologie_id: props.technologies,
  facture_id: props.factures,
  matricule_id: props.matricules,
  service_id: props.services,
}

function getFieldLabel(key) {
  const labels = {
    classe_id: 'Classe',
    type_id: 'Type',
    reserve_id: 'Réserve',
    technologie_id: 'Technologie',
    facture_id: 'Facture',
    matricule_id: 'Matricule',
    service_id: 'Service',
  }
  return labels[key] || key.replace('_id', '')
}

function inputClass(field) {
  return [
    'border rounded px-3 py-2 w-full',
    form.errors[field]
      ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
      : 'focus:border-blue-500 focus:ring-blue-500'
  ]
}

function positionClass(msg) {
  if (msg.includes('Bravo')) return 'text-green-600'
  if (msg.includes('Ajouter')) return 'text-yellow-600'
  if (msg.includes('Faute') || msg.includes('espace')) return 'text-red-600'
  return ''
}

function calcPosition() {
  const pos = form.Position.trim()
  if (!pos) return ''
  if (pos === 'EXTERNE') return 'Bravo 😁'

  const ptt1 = /^PTT \(1er 112\) ?(\d{0,3})?$/
  const ptt2 = /^PTT \(2eme 112\) ?(\d{0,3})?$/
  const tn = /^(0|4) (\d{1,2}) ?(\d{0,2})?$/

  if (ptt1.test(pos) || ptt2.test(pos) || tn.test(pos)) {
    if (pos.endsWith(' ')) return 'Ajouter la Position svp. 😇'
    return 'Bravo 😁'
  }
  return 'Format invalide. Utilisez PTT ou TN (0 X ou 4 X) 😡'
}

const save = () => {
  if (form.id) {
    form.put(`/manageNumero/${form.id}`, { preserveScroll: true, onSuccess: reset })
  } else {
    form.post('/manageNumero', { preserveScroll: true, onSuccess: reset })
  }
}

const edit = numero => {
  form.reset()
  Object.assign(form, {
    id: numero.id,
    NDappel: numero.NDappel,
    Position: numero.Position,
    TN: numero.TN,
    destination_id: numero.destination_id,
    classe_id: numero.classe_id,
    type_id: numero.type_id,
    reserve_id: numero.reserve_id,
    technologie_id: numero.technologie_id,
    facture_id: numero.facture_id,
    matricule_id: numero.matricule_id,
    organisme_id: numero.organisme_id,
    service_id: numero.service_id
  })
}

const destroy = id => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce numéro ?')) {
    router.delete(`/manageNumero/${id}`, { preserveScroll: true })
  }
}

const reset = () => {
  form.reset()
  Object.assign(form, {
    id: null,
    NDappel: '',
    Position: '',
    TN: '',
    destination_id: null,
    classe_id: null,
    type_id: null,
    reserve_id: null,
    technologie_id: null,
    facture_id: null,
    matricule_id: null,
    organisme_id: null,
    service_id: null
  })
}

onMounted(() => {
  window.Echo.channel('numeros')
    .listen('.NumeroUpdated', e => {
      const updated = e.numero
      const index = localNumeros.value.findIndex(n => n.id === updated.id)

      if (e.deleted) {
        // remove deleted item
        if (index !== -1) localNumeros.value.splice(index, 1)
      } else {
        // update or add new item
        if (index !== -1) localNumeros.value[index] = updated
        else localNumeros.value.unshift(updated)
      }
    })
})
</script>