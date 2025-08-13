<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">✏️ Reserve</h1>

    <!-- Flash message -->
    <div v-if="flash.success" class="mb-4 text-green-600">
      {{ flash.success }}
    </div>

    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Reserve Name (optional)</label>
        <input
          v-model="form.reserve"
          type="text"
          placeholder="Leave blank to auto-generate"
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Is Reserved?</label>
        <input type="checkbox" v-model="form.is_reserved" class="mr-2" />
        <span>{{ form.is_reserved ? 'Yes' : 'No' }}</span>
      </div>

      <div class="flex space-x-2">
        <button
          type="submit"
          :disabled="!form.is_reserved"
          class="bg-blue-600 text-white px-4 py-1 rounded disabled:opacity-50"
        >
          {{ form.id ? 'Update' : 'Create' }}
        </button>
        <button type="button" @click="reset" class="bg-gray-300 px-4 py-1 rounded">Cancel</button>
      </div>
    </form>

    <!-- Table -->
    <table class="w-full border border-collapse text-sm">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Reserve Name</th>
          <th class="border px-2 py-1">Is Reserved?</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in reserves" :key="item.id || index">
          <td class="border px-2 py-1">{{ item.id }}</td>
          <td class="border px-2 py-1">{{ item.reserve ?? '—' }}</td>
          <td class="border px-2 py-1">
            <span :class="item.is_reserved ? 'text-green-600' : 'text-red-600'">
              {{ item.is_reserved ? 'Yes' : 'No' }}
            </span>
          </td>
          <td class="border px-2 py-1 space-x-2">
            <button @click="edit(item)" class="text-blue-600 hover:underline">Edit</button>
            <button @click="destroy(item.id)" class="text-red-600 hover:underline">Delete</button>
          </td>
        </tr>
        <tr v-if="reserves.length === 0">
          <td colspan="4" class="text-center text-gray-500 py-2">
            No data available.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutEdit.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  reserves: Array, // Changed 'factures' to 'reserves'
})

const reserves = computed(() => props.reserves) // Renamed 'factures' to 'reserves'
const flash = usePage().props.flash || {}

const form = ref({ id: null, is_reserved: false }) // Renamed 'is_factured' to 'is_reserved'

const save = () => {
  if (form.value.id) {
    router.put(`/manageReserve/${form.value.id}`, form.value, { // Changed route from 'manageFacture' to 'manageReserve'
      preserveScroll: true,
      onSuccess: () => reset(),
    })
  } else {
    router.post('/manageReserve', form.value, { // Changed route from 'manageFacture' to 'manageReserve'
      preserveScroll: true,
      onSuccess: () => reset(),
    })
  }
}

const edit = (item) => {
  form.value = {
    id: item.id,
    is_reserved: item.is_reserved,
    reserve: item.reserve, // Ensure to populate the 'reserve' field
  }
}

const destroy = (id) => {
  if (confirm('Are you sure you want to delete this reserve?')) { // Changed text to 'reserve'
    router.delete(`/manageReserve/${id}`, { // Changed route from 'manageFacture' to 'manageReserve'
      preserveScroll: true,
    })
  }
}

const reset = () => {
  form.value = { id: null, is_reserved: false, reserve: '' } // Reset 'reserve' as well
}
</script>
