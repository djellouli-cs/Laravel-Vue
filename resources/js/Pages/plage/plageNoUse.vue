<template>
  <div class="mt-4 flex justify-center">
    <!-- Select plage -->
    <select class="text-yellow-500 bg-slate-900 w-2/12 rounded-md px-2 py-1 m-2" v-model="plage">
      <option value="">-- Select Plage --</option>
      <option v-for="(p, index) in uniquePlages" :key="index">{{ p }}</option>
    </select>
  </div>

  <!-- Ping Button -->
  <div class="flex justify-center mt-4">
    <button
      class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
      @click="pingPlageAddresses"
      :disabled="filteredVideAddresses.length === 0 || isPinging"
    >
      Ping des adresses IP inutilisées
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
            <th class="border border-gray-400 px-4 py-2">Address</th>
            <th class="border border-gray-400 px-4 py-2">Plage</th>
            <th class="border border-gray-400 px-4 py-2">Ping</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(address, index) in filteredVideAddresses" :key="address.id">
            <td class="border border-gray-400 px-4 py-2">{{ index + 1 }}</td>
            <td class="border border-gray-400 px-4 py-2">{{ address.ipAdresses }}</td>
            <td class="border border-gray-400 px-4 py-2">{{ address.direction }}</td>
            <td class="border border-gray-400 px-4 py-2">
              <span v-if="pingResults[address.ipAdresses] === true" class="text-red-600">Utilisée</span>
              <span v-else-if="pingResults[address.ipAdresses] === false" class="text-green-600">Non Utilisée</span>
              <span v-else-if="pingResults[address.ipAdresses] === 'Error'" class="text-gray-500">❓ Erreur</span>
              <span v-else class="text-yellow-600">⏳ En attente</span>
            </td>
          </tr>
        </tbody>
      </table>
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
const rawAddresses = ref(props.ipaddresses || [])
const filters = props.filters || {}

const plage = ref(filters.plage || '')
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

const filteredVideAddresses = computed(() => {
  return rawAddresses.value.filter(addr => {
    const inSelectedPlage = !plage.value || addr.direction === plage.value
    const notUsed = addr.ipaddresses === null
    return inSelectedPlage && notUsed
  })
})

watch(plage, () => {
  router.get('/plageNoUse', {
    plage: plage.value,
  }, {
    preserveState: true,
    replace: true,
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
  const addresses = filteredVideAddresses.value
  const promises = []

  isPinging.value = true
  totalToPing.value = addresses.length
  completedPings.value = 0

  for (const addr of addresses) {
    if (!pingResults.value[addr.ipAdresses]) {
      const promise = fetchPing(addr.ipAdresses).finally(() => {
        completedPings.value++
      })
      promises.push(promise)
    }
  }

  await Promise.all(promises)
  isPinging.value = false
}
</script>
