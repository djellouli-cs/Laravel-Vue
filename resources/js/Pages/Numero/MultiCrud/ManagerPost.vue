<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">✏️ Post</h1>

    <!-- Flash message -->
    <div v-if="flash.success" class="mb-4 text-green-600">
      {{ flash.success }}
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Search by post, marque or numero..."
        class="border rounded px-3 py-2 w-full"
      />
    </div>

    <!-- Form: always visible -->
    <form @submit.prevent="save" class="mb-6 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Post</label>
        <input
          v-model="form.post"
          type="text"
          required
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Marque</label>
        <input
          v-model="form.marque"
          type="text"
          required
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Numero</label>
        <select
          v-model="form.numero_id"
          required
          class="border rounded px-3 py-2 w-full"
        >
          <option value="" disabled>Select Numero</option>
          <option
            v-for="numero in filteredNumeros"
            :key="numero.id"
            :value="numero.id"
          >
            {{ numero.name ?? numero.NDappel }}
          </option>
        </select>
      </div>

      <div class="flex space-x-2">
        <button
          type="submit"
          class="bg-blue-600 text-white px-4 py-1 rounded"
        >
          {{ form.id ? 'Update' : 'Create' }}
        </button>
        <button
          type="button"
          v-if="form.id"
          @click="cancelEdit"
          class="bg-gray-300 px-4 py-1 rounded"
        >
          Cancel
        </button>
      </div>
    </form>

    <!-- Table -->
    <table class="w-full border border-collapse text-sm">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Post</th>
          <th class="border px-2 py-1">Marque</th>
          <th class="border px-2 py-1">Numero</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in filteredClasses" :key="item.id || index">
          <td class="border px-2 py-1">{{ item.id }}</td>
          <td class="border px-2 py-1">{{ item.post }}</td>
          <td class="border px-2 py-1">{{ item.marque }}</td>
          <td class="border px-2 py-1">
            {{ item.numero?.name ?? item.numero?.NDappel }}
          </td>
          <td class="border px-2 py-1 space-x-2">
            <button
              @click="edit(item)"
              class="text-blue-600 hover:underline"
              type="button"
            >
              Edit
            </button>
            <button
              @click="destroy(item.id)"
              class="text-red-600 hover:underline"
              type="button"
            >
              Delete
            </button>
          </td>
        </tr>
        <tr v-if="filteredClasses.length === 0">
          <td colspan="5" class="text-center text-gray-500 py-2">
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
  classes: Array,
  numeros: Array,
})

const classes = computed(() => props.classes)
const numeros = computed(() => props.numeros)
const flash = usePage().props.flash || {}

const form = ref({
  id: null,
  post: '',
  marque: '',
  numero_id: '',
})

const search = ref('')

const filteredClasses = computed(() => {
  const term = search.value.toLowerCase()
  return classes.value.filter(
    (item) =>
      item.post?.toLowerCase().includes(term) ||
      item.marque?.toLowerCase().includes(term) ||
      item.numero?.NDappel?.toLowerCase().includes(term) ||
      item.numero?.name?.toLowerCase().includes(term)
  )
})

const filteredNumeros = computed(() => {
  const term = search.value.toLowerCase()
  return numeros.value.filter(
    (n) =>
      n.NDappel?.toLowerCase().includes(term) ||
      n.name?.toLowerCase().includes(term)
  )
})

const save = () => {
  const url = form.value.id ? `/managePost/${form.value.id}` : '/managePost'
  const method = form.value.id ? 'put' : 'post'

  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => resetForm(),
  })
}

const edit = (item) => {
  form.value = {
    id: item.id,
    post: item.post,
    marque: item.marque,
    numero_id: item.numero_id,
  }
}

const destroy = (id) => {
  if (confirm('Are you sure you want to delete this post?')) {
    router.delete(`/managePost/${id}`, {
      preserveScroll: true,
    })
  }
}

const resetForm = () => {
  form.value = {
    id: null,
    post: '',
    marque: '',
    numero_id: '',
  }
}

const cancelEdit = () => {
  resetForm()
}
</script>
