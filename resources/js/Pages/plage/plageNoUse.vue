<template>
  <div class="p-4">
    <!-- Filtre Plage -->
    <div class="mt-4 flex justify-center">
      <select
        class="text-yellow-500 bg-slate-900 w-2/12 rounded-md px-2 py-1 m-2"
        v-model="plage"
      >
        <option value="">-- Sélectionner une plage --</option>
        <option v-for="(p, index) in uniquePlages" :key="index">{{ p }}</option>
      </select>

      <button
        @click="toggleShowAll"
        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded ml-2"
      >
        {{ showAll ? 'Voir uniquement les non utilisées' : 'Voir tout' }}
      </button>
    </div>

    <!-- Bouton Ping -->
    <div class="flex justify-center mt-4">
      <button
        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
        @click="pingPlageAddresses"
        :disabled="filteredAddresses.length === 0 || isPinging"
      >
        Ping des adresses IP {{ showAll ? '' : 'non utilisées' }}
      </button>
    </div>

    <!-- Progression Ping -->
    <div class="text-center mt-4">
      <div v-if="isPinging" class="text-blue-600">
        Ping en cours... {{ completedPings }} / {{ totalToPing }}
      </div>
      <div v-else-if="totalToPing > 0" class="text-green-600">
        ✅ Tous les ping sont terminés.
      </div>
    </div>

    <!-- Tableau IPs -->
    <div class="flex items-center justify-center m-2">
      <div class="mx-auto">
        <table class="table-auto border-collapse border border-gray-400">
          <thead>
            <tr class="bg-gray-200">
              <th class="border border-gray-400 px-4 py-2">#</th>
              <th class="border border-gray-400 px-4 py-2">Adresse</th>
              <th class="border border-gray-400 px-4 py-2">Plage</th>
              <th class="border border-gray-400 px-4 py-2">Destination</th>
              <th class="border border-gray-400 px-4 py-2">Ping</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(address, index) in filteredAddresses" :key="address.id">
              <td class="border border-gray-400 px-4 py-2">{{ index + 1 }}</td>
              <td class="border border-gray-400 px-4 py-2">{{ address.ipAdresses }}</td>
              <td class="border border-gray-400 px-4 py-2">{{ address.direction }}</td>
              <td class="border border-gray-400 px-4 py-2">
                <span
                  v-if="!address.ipaddresses.length"
                  class="text-green-600 font-bold"
                >
                  VIDE
                </span>
                <span v-else>
                  {{ address.ipaddresses[0].destination || '' }}
                </span>
              </td>
              <td class="border border-gray-400 px-4 py-2">
                <span v-if="pingResults[address.ipAdresses] === true" class="text-red-600">
                  Utilisée
                </span>
                <span v-else-if="pingResults[address.ipAdresses] === false" class="text-green-600">
                  Non utilisée
                </span>
                <span v-else-if="pingResults[address.ipAdresses] === 'Error'" class="text-gray-500">
                  ❓ Erreur
                </span>
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
import { usePage } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutNetwork.vue'

defineOptions({ layout: Layout })

const props = usePage().props
const rawAddresses = ref(props.plages || [])

const plage = ref('')
const showAll = ref(false)
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
    if (showAll.value) return inSelectedPlage
    const notUsed = addr.ipaddresses.length === 0
    return inSelectedPlage && notUsed
  })
})

const fetchPing = (ip) => {
  return axios
    .get('/ping', { params: { ip } })
    .then(response => {
      pingResults.value[ip] = response.data.reachable
    })
    .catch(() => {
      pingResults.value[ip] = 'Error'
    })
}

const pingPlageAddresses = async () => {
  const addresses = filteredAddresses.value
  const promises = []

  isPinging.value = true
  totalToPing.value = addresses.length
  completedPings.value = 0

  for (const addr of addresses) {
    if (pingResults.value[addr.ipAdresses] === undefined) {
      const promise = fetchPing(addr.ipAdresses).finally(() => {
        completedPings.value++
      })
      promises.push(promise)
    }
  }

  await Promise.all(promises)
  isPinging.value = false
}

// ✅ Live auto-ping when selecting plage or toggling showAll
watch([plage, showAll], () => {
  pingPlageAddresses()
})

const toggleShowAll = () => {
  showAll.value = !showAll.value
}
</script>
