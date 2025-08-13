<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">✏️ Facture</h1>

    <!-- Flash message -->
    <div v-if="flash.success" class="mb-4 text-green-600">
      {{ flash.success }}
    </div>

    <!-- Form -->
    <form @submit.prevent="save" class="mb-6 space-y-4">
      <div>
        <label class="block text-sm font-medium mb-1">Numéro de Facture (optionnel)</label>
        <input
          v-model="form.facture"
          type="text"
          placeholder="Laisser vide pour auto-générer"
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Fournisseur</label>
        <select 
          v-model="form.provider"
          class="border rounded px-3 py-2 w-full"
        >
          <option value="">Sélectionner un Fournisseur</option>
          <option value="Algerie Telecom">Algerie Telecom</option>
          <option value="Mobilis">Mobilis</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Forfait</label>
        <input
          v-model="form.plan"
          type="text"
          placeholder="ex: Forfait Basique, Forfait Premium"
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Coût Mensuel (DA)</label>
        <input
          v-model="form.monthly_cost"
          type="number"
          step="0.01"
          placeholder="0.00"
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Date de Début</label>
        <input
          v-model="form.start_date"
          type="date"
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Date de Fin (Optionnel)</label>
        <input
          v-model="form.end_date"
          type="date"
          class="border rounded px-3 py-2 w-full"
        />
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Notes</label>
        <textarea
          v-model="form.notes"
          rows="3"
          placeholder="Notes supplémentaires..."
          class="border rounded px-3 py-2 w-full"
        ></textarea>
      </div>

      <div>
        <label class="block text-sm font-medium mb-1">Est Facturé ?</label>
        <input type="checkbox" v-model="form.is_factured" class="mr-2" />
        <span>{{ form.is_factured ? 'Oui' : 'Non' }}</span>
      </div>

      <div class="flex space-x-2">
        <button
          type="submit"
          :disabled="!form.is_factured"
          class="bg-blue-600 text-white px-4 py-1 rounded disabled:opacity-50"
        >
          {{ form.id ? 'Mettre à Jour' : 'Créer' }}
        </button>
        <button type="button" @click="reset" class="bg-gray-300 px-4 py-1 rounded">
          Annuler
        </button>
      </div>
    </form>

    <!-- Table -->
    <table class="w-full border border-collapse text-sm">
      <thead>
        <tr>
          <th class="border px-2 py-1">ID</th>
          <th class="border px-2 py-1">Numéro de Facture</th>
          <th class="border px-2 py-1">Fournisseur</th>
          <th class="border px-2 py-1">Forfait</th>
          <th class="border px-2 py-1">Coût Mensuel</th>
          <th class="border px-2 py-1">Date de Début</th>
          <th class="border px-2 py-1">Est Facturé ?</th>
          <th class="border px-2 py-1">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in paginated" :key="item.id">
          <td class="border px-2 py-1">{{ item.id }}</td>
          <td class="border px-2 py-1">{{ item.facture ?? '—' }}</td>
          <td class="border px-2 py-1">{{ item.provider ?? '—' }}</td>
          <td class="border px-2 py-1">{{ item.plan ?? '—' }}</td>
          <td class="border px-2 py-1">{{ formatCurrency(item.monthly_cost) }}</td>
          <td class="border px-2 py-1">{{ formatDate(item.start_date) }}</td>
          <td class="border px-2 py-1">
            <span :class="item.is_factured ? 'text-green-600' : 'text-red-600'">
              {{ item.is_factured ? 'Oui' : 'Non' }}
            </span>
          </td>
          <td class="border px-2 py-1 space-x-2">
            <button @click="edit(item)" class="text-blue-600 hover:underline">Modifier</button>
            <button @click="destroy(item.id)" class="text-red-600 hover:underline">Supprimer</button>
          </td>
        </tr>
        <tr v-if="paginated.length === 0">
          <td colspan="8" class="text-center text-gray-500 py-2">
            Aucune donnée disponible.
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center items-center gap-4">
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
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutEdit.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  factures: Array,
})

const flash = usePage().props.flash || {}
const form = ref({ 
  id: null, 
  facture: '', 
  provider: '',
  plan: '',
  monthly_cost: '',
  start_date: '',
  end_date: '',
  notes: '',
  is_factured: false 
})

// Pagination
const currentPage = ref(1)
const itemsPerPage = 5

const totalPages = computed(() =>
  Math.ceil(props.factures.length / itemsPerPage)
)

const paginated = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage
  return props.factures.slice(start, start + itemsPerPage)
})

const save = () => {
  const url = form.value.id
    ? `/manageFacture/${form.value.id}`
    : '/manageFacture'
  const method = form.value.id ? 'put' : 'post'

  router[method](url, form.value, {
    preserveScroll: true,
    onSuccess: () => reset(),
  })
}

const edit = (item) => {
  form.value = {
    id: item.id,
    facture: item.facture ?? '',
    provider: item.provider ?? '',
    plan: item.plan ?? '',
    monthly_cost: item.monthly_cost ?? '',
    start_date: item.start_date ?? '',
    end_date: item.end_date ?? '',
    notes: item.notes ?? '',
    is_factured: item.is_factured,
  }
}

const destroy = (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')) {
    router.delete(`/manageFacture/${id}`, {
      preserveScroll: true,
    })
  }
}

const reset = () => {
  form.value = { 
    id: null, 
    facture: '', 
    provider: '',
    plan: '',
    monthly_cost: '',
    start_date: '',
    end_date: '',
    notes: '',
    is_factured: false 
  }
}

const formatCurrency = (amount) => {
  if (!amount) return '—'
  return new Intl.NumberFormat('ar-DZ', {
    style: 'currency',
    currency: 'DZD'
  }).format(amount)
}

const formatDate = (date) => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('fr-FR')
}
</script>
