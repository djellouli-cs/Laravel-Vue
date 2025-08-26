<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold text-center mb-6">Plages IP Non utilisées</h1>

    <!-- Select plage -->
    <div class="flex justify-center mb-4">
      <select
        class="text-yellow-500 bg-slate-900 w-2/12 rounded-md px-2 py-1 m-2"
        v-model="plage"
      >
        <option value="">-- Sélectionner Plage --</option>
        <option v-for="(p, index) in uniquePlages" :key="index">{{ p }}</option>
      </select>

      <!-- Toggle button -->
      <button
        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mx-2"
        @click="showOnlyVide = !showOnlyVide"
      >
        {{ showOnlyVide ? 'Afficher toutes les IPs' : 'Afficher uniquement les Non utilisées' }}
      </button>
    </div>

    <!-- Ping Button -->
    <div class="flex justify-center mt-4">
      <button
        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        @click="pingPlageAddresses"
        :disabled="filteredAddresses.length === 0 || isPinging"
      >
        Ping des adresses IP {{ showOnlyVide ? 'Non utilisées' : '' }}
      </button>
    </div>

    <!-- Ping Progress -->
    <div class="text-center mt-4">
      <div v-if="isPinging" class="text-blue-600">
        Ping en cours... {{ completedPings }} / {{ totalToPing }}
      </div>
      <div v-else-if="totalToPing > 0" class="text-green-600">
        ✅ Tous les ping sont terminés.
      </div>
    </div>

    <!-- Table display -->
    <div class="flex items-center justify-center m-2">
      <div class="mx-auto">
        <table class="table-auto border-collapse border border-gray-400">
          <thead>
            <tr class="bg-gray-200">
              <th class="border border-gray-400 px-4 py-2">#</th>
              <th class="border border-gray-400 px-4 py-2">Adresse</th>
              <th class="border border-gray-400 px-4 py-2">Plage</th>
              <th class="border border-gray-400 px-4 py-2">Ping</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(address, index) in filteredAddresses" :key="address.id">
              <td class="border border-gray-400 px-4 py-2">{{ index + 1 }}</td>
              <td class="border border-gray-400 px-4 py-2">{{ address.ipAdresses }}</td>
              <td class="border border-gray-400 px-4 py-2">
                <span v-if="address.ipaddresses.length === 0" class="text-green-600">VIDE</span>
                <span v-else>{{ address.direction }}</span>
              </td>
              <td class="border border-gray-400 px-4 py-2">
                <span v-if="pingResults[address.ipAdresses] === true" class="text-red-600">Utilisée</span>
                <span v-else-if="pingResults[address.ipAdresses] === false" class="text-green-600">Non utilisée</span>
                <span v-else-if="pingResults[address.ipAdresses] === 'Error'" class="text-gray-500">❓ Erreur</span>
                <span v-else class="text-yellow-600">⏳ En attente</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { router, usePage } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutNetwork.vue'

defineOptions({ layout: Layout })

const props = usePage().props
const rawAddresses = ref(props.plages || [])

const plage = ref('')
const showOnlyVide = ref(true)
const pingResults = ref({})
const isPinging = ref(false)
const totalToPing = ref(0)
const completedPings = ref(0)

const uniquePlages = computed(() => {
  const set = new Set()
  rawAddresses.value.forEach(addr => {
    if (addr.direction) set.add(addr.direction)
  })
  return [...set]
})

const filteredAddresses = computed(() => {
  return rawAddresses.value.filter(addr => {
    const inSelectedPlage = !plage.value || addr.direction === plage.value
    const notUsed = addr.ipaddresses.length === 0
    return inSelectedPlage && (!showOnlyVide.value || notUsed)
  })
})

watch(plage, () => {
  router.get('/plageNoUse', { plage: plage.value }, { preserveState: true, replace: true })
})

const fetchPing = (ip) => {
  return axios
    .get('/ping', { params: { ip } })
    .then(response => { pingResults.value[ip] = response.data.reachable })
    .catch(() => { pingResults.value[ip] = 'Error' })
}

const pingPlageAddresses = async () => {
  const addresses = filteredAddresses.value
  const promises = []

  isPinging.value = true
  totalToPing.value = addresses.length
  completedPings.value = 0

  for (const addr of addresses) {
    if (!pingResults.value[addr.ipAdresses]) {
      const promise = fetchPing(addr.ipAdresses).finally(() => completedPings.value++)
      promises.push(promise)
    }
  }

  await Promise.all(promises)
  isPinging.value = false
}
</script>
