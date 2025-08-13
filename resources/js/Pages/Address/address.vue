<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import Layout from '@/Layouts/LayoutNetwork.vue'
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'

defineOptions({ layout: Layout })

// البيانات
const addresses = ref([])
const Organisme = ref('')
const plage = ref('')
const active1 = ref(true)
const active2 = ref(false)
const active3 = ref('Sélectionner')

// تحميل البيانات عند التشغيل
onMounted(() => {
  getAddresses()
})

// الوظائف
const toggleActive = () => {
  active1.value = !active1.value
  active2.value = !active2.value
  showButton()
}

const showButton = () => {
  active3.value = active1.value ? 'Sélectionner' : 'Rechercher'
}

const getAddresses = () => {
  let url = 'http://192.168.1.40:8000/api/plage'
  if (Organisme.value || plage.value) {
    url += `?organisme=${encodeURIComponent(Organisme.value)}&plage=${encodeURIComponent(plage.value)}`
  }

  axios.get(url)
    .then(res => {
      addresses.value = res.data.ip_address
    })
    .catch(err => {
      console.error('Error fetching addresses:', err)
    })
}

// الحقول الفريدة
const uniqueOrganismes = computed(() => {
  const set = new Set()
  addresses.value.forEach(addr => {
    if (addr.ipaddresse?.organisme) {
      set.add(addr.ipaddresse.organisme)
    }
  })
  return Array.from(set)
})

const uniquePlages = computed(() => {
  const set = new Set()
  addresses.value.forEach(addr => {
    if (addr.direction) {
      set.add(addr.direction)
    }
  })
  return Array.from(set)
})

// الفلترة
const filteredAddresses = computed(() => {
  return addresses.value.filter(addr => {
    const orgMatch = !Organisme.value || addr.ipaddresse?.organisme === Organisme.value
    const plageMatch = !plage.value || addr.direction === plage.value
    return orgMatch && plageMatch
  })
})

// المراقبة
watch(Organisme, () => {
  plage.value = ''
  getAddresses()
})

watch(plage, () => {
  getAddresses()
})
</script>


<template>
  <Head title="| Plage"/>
  <Link :href="route('plageTable')" class="underline decoration-yellow-800 rounded-full px-3 py-1 hover:bg-yellow-900" active-class="bg-green-500 text-red-200">Plages wilaya</Link>

  <div class="mt-4 flex justify-center">
    <!-- Input for organisme with datalist -->
    <input type="text" list="organismes" class="text-yellow-500 bg-slate-900 w-2/12 rounded-md px-2 py-1" name="organisme" v-model="Organisme" v-show="active1"/>
    <datalist id="organismes">
      <option v-for="(organisme, index) in uniqueOrganismes" :key="index">{{ organisme }}</option>
    </datalist>
    
    <!-- Input for plage with datalist -->
    <input type="text" list="plages" class="text-yellow-500 bg-slate-900 w-2/12 rounded-md px-2 py-1 m-2" name="plage" v-model="plage" v-show="active1"/>
    <datalist id="plages">
      <option v-for="(plage, index) in uniquePlages" :key="index">{{ plage }}</option>
    </datalist>
    
    <!-- Select for organisme -->
    <select class="text-yellow-500 bg-slate-900 w-2/12 rounded-md px-2 py-1 m-2" v-model="Organisme" v-show="active2">
      <option v-for="(organisme, index) in uniqueOrganismes" :key="index">{{ organisme }}</option>
    </select>
    
    <!-- Select for plage -->
    <select class="text-yellow-500 bg-slate-900 w-2/12 rounded-md px-2 py-1 m-2" v-model="plage" v-show="active2">
      <option v-for="(plage, index) in uniquePlages" :key="index">{{ plage }}</option>
    </select>
    
    <!-- Button -->
    <button type="submit" class="text-white rounded-full bg-lime-600 p-2 m-2" @click="toggleActive()">{{ active3 }}</button>
  </div>

  <table class="w-full mt-4">
    <!-- Table headers -->
    <thead>
      <tr class="bg-zinc-700">
        <th class="px-4 py-2 text-center">Organisme : <span class="font-bold text-lg text-red-500">{{ filteredAddresses.length }}</span></th>
        <th class="px-4 py-2 text-center">Destination</th>
        <th class="px-4 py-2 text-center">Plage</th>
        <th class="px-4 py-2 text-center">Application</th>
        <th class="px-4 py-2 text-center">Addresses</th>
        <th class="px-4 py-2 text-center">Mask</th>
        <th class="px-4 py-2 text-center">Port</th>
        <th class="px-4 py-2 text-center">Note</th>
      </tr>
    </thead>
    <!-- Table rows -->
    <tbody>
      <tr v-for="address in filteredAddresses" :key="address.id" class="bg-black text-white">
        <td class="px-4 py-2 text-center">{{ address.ipaddresse?.organisme || '' }}</td>
        <td class="px-4 py-2 text-center">{{ address.ipaddresse?.destination || '' }}</td>
        <td class="px-4 py-2 text-center">{{ address.direction }}</td>
        <td class="px-4 py-2 text-center">{{ address.ipaddresse?.Application || '' }}</td>
        <td class="px-4 py-2 text-center">{{ address.ipAdresses }}</td>
        <td class="px-4 py-2 text-center">{{ address.ipaddresse?.mask || '' }}</td>
        <td class="px-4 py-2 text-center">{{ address.ipaddresse?.port || '' }}</td>
        <td class="px-4 py-2 text-center">{{ address.ipaddresse?.note || '' }}</td>
      </tr>
    </tbody>
  </table>
</template>
