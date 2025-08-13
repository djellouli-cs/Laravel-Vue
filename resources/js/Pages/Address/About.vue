<script setup>
import { ref } from 'vue'
import { usePage, Head, Link } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutNetwork.vue'

defineOptions({ layout: Layout })

const props = usePage().props
const addresses = ref(props.ipaddresses)

const ipAdresses = ref('')
const active1 = ref(true)
const active2 = ref(false)
const active3 = ref('Sélectionner')

const toggleInput = () => {
  active1.value = !active1.value
  active2.value = !active2.value
  active3.value = active1.value ? 'Sélectionner' : 'Rechercher'
}
</script>

<template>
  <Head title=" | Addresses" />

  <div class="container mx-auto px-4 py-6 space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-lime-800">IP Addresses</h1>
     
    </div>

    <div class="bg-gradient-to-r from-lime-200 via-lime-700 to-lime-950 rounded-2xl p-6 text-white shadow-lg">
      <p class="text-lg mb-4 font-semibold">Total Addresses: <span class="font-bold">{{ addresses.length }}</span></p>

      <!-- Input / Select Toggle -->
      <div class="flex items-center space-x-4 mb-4">
        <input
          type="text"
          list="ip-list"
          v-model="ipAdresses"
          v-show="active1"
          class="text-yellow-500 bg-slate-900 w-full md:w-1/3 p-2 rounded"
          placeholder="Type IP Address..."
        />
        <datalist id="ip-list">
          <option v-for="user in addresses" :key="user.id">{{ user.ipAdresses }}</option>
        </datalist>

        <select
          v-model="ipAdresses"
          v-show="active2"
          class="text-yellow-500 bg-slate-900 w-full md:w-1/3 p-2 rounded"
        >
          <option v-for="user in addresses" :key="user.id">{{ user.ipAdresses }}</option>
        </select>

        <button
          type="button"
          class="text-white bg-lime-600 hover:bg-lime-700 px-4 py-2 rounded-full"
          @click="toggleInput"
        >
          {{ active3 }}
        </button>
      </div>

      <!-- Result Table -->
      <template v-for="user in addresses" :key="user.id">
        <div v-if="user.ipAdresses === ipAdresses" class="overflow-x-auto mt-6">
          <table class="w-full text-left bg-black rounded-lg overflow-hidden">
            <tbody>
              <tr><td class="bg-zinc-700 px-4 py-2">Organisme</td><td class="text-yellow-500 px-4 py-2">{{ user.organisme }}</td></tr>
              <tr><td class="bg-zinc-700 px-4 py-2">Destination</td><td class="text-yellow-500 px-4 py-2">{{ user.destination }}</td></tr>
              <tr><td class="bg-zinc-700 px-4 py-2">Plage</td><td class="text-yellow-500 px-4 py-2">{{ user.plage?.direction || '' }}</td></tr>
              <tr><td class="bg-zinc-700 px-4 py-2">Application</td><td class="text-yellow-500 px-4 py-2">{{ user.Application }}</td></tr>
              <tr><td class="bg-zinc-700 px-4 py-2">Adresses</td><td class="text-yellow-500 px-4 py-2">{{ user.ipAdresses }}</td></tr>
              <tr><td class="bg-zinc-700 px-4 py-2">Mask</td><td class="text-yellow-500 px-4 py-2">{{ user.mask }}</td></tr>
              <tr><td class="bg-zinc-700 px-4 py-2">Port</td><td class="text-yellow-500 px-4 py-2">{{ user.port }}</td></tr>
              <tr><td class="bg-zinc-700 px-4 py-2">Note</td><td class="text-yellow-500 px-4 py-2">{{ user.note }}</td></tr>
            </tbody>
          </table>
        </div>
      </template>
    </div>
  </div>
</template>
