<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Éditer le Numéro</h1>

    <!-- Flash Message -->
    <div v-if="flash.success" class="mb-4 text-green-600">
      {{ flash.success }}
    </div>

    <!-- Form -->
    <form @submit.prevent="update" class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
      <!-- NDappel -->
      <div>
        <label for="NDappel" class="block text-sm font-medium mb-1">NDappel</label>
        <input
          id="NDappel"
          v-model="form.NDappel"
          type="text"
          class="border rounded px-3 py-2 w-full"
          required
        />
      </div>

      <!-- Position -->
      <div>
        <label for="Position" class="block text-sm font-medium mb-1">Position</label>
        <input
          id="Position"
          list="positionOptions"
          v-model="form.Position"
          type="text"
          class="border rounded px-3 py-2 w-full"
          required
        />
        <datalist id="positionOptions">
          <option>EXTERNE</option>
          <option>PTT (1er 112)</option>
          <option>PTT (2eme 112)</option>
          <option>0 0</option>
          <option>4 0</option>
        </datalist>
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
          Mettre à jour
        </button>
        <button type="button" @click="goBack" class="bg-gray-300 px-4 py-1 rounded">Annuler</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutEdit.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  numero: Object,
  destinations: Array,
  classes: Array,
  types: Array,
  reserves: Array,
  technologies: Array,
  factures: Array,
  matricules: Array,
  organismes: Array,
  services: Array,
  acheminements: Array,
})

const flash = usePage().props.flash || {}

const form = ref({
  id: props.numero.id,
  NDappel: props.numero.NDappel || '',
  Position: props.numero.Position || '',
  destination_id: props.numero.destination_id || props.numero.destination?.id || null,
  classe_id: props.numero.classe_id || props.numero.classe?.id || null,
  type_id: props.numero.type_id || props.numero.type?.id || null,
  reserve_id: props.numero.reserve_id || props.numero.reserve?.id || null,
  technologie_id: props.numero.technologie_id || props.numero.technologie?.id || null,
  facture_id: props.numero.facture_id || props.numero.facture?.id || null,
  matricule_id: props.numero.matricule_id || props.numero.matricule?.id || null,
  organisme_id: props.numero.organisme_id || props.numero.organisme?.id || null,
  service_id: props.numero.service_id || props.numero.service?.id || null,
})

const filteredDestinations = computed(() => props.destinations)

const restSelectOptions = computed(() => ({
  classe_id: props.classes,
  type_id: props.types,
  reserve_id: props.reserves,
  technologie_id: props.technologies,
  facture_id: props.factures,
  matricule_id: props.matricules,
  service_id: props.services,
}))

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

function update() {
  router.put(`/manageNumero/${form.value.id}`, form.value, {
    onSuccess: () => {
      router.visit('/manageNumero')
    },
  })
}

function goBack() {
  router.visit('/manageNumero')
}
</script> 