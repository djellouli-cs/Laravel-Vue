<template>
  <div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
             <!-- Header -->
       <div class="mb-8">
         <h1 class="text-3xl font-bold text-gray-900">Gestion ADSL</h1>
         <p class="mt-2 text-gray-600">Gérer les services internet ADSL et la facturation</p>
       </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-blue-100 rounded-lg">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
                         <div class="ml-4">
               <p class="text-sm font-medium text-gray-500">Total Services ADSL</p>
               <p class="text-2xl font-semibold text-gray-900">{{ statistics.total_adsl_factures || 0 }}</p>
             </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-green-100 rounded-lg">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
              </svg>
            </div>
                         <div class="ml-4">
               <p class="text-sm font-medium text-gray-500">Coût Mensuel</p>
               <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(statistics.total_monthly_cost || 0) }}</p>
             </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-yellow-100 rounded-lg">
              <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
                         <div class="ml-4">
               <p class="text-sm font-medium text-gray-500">Fournisseurs</p>
               <p class="text-2xl font-semibold text-gray-900">{{ statistics.providers?.length || 0 }}</p>
             </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex items-center">
            <div class="p-2 bg-purple-100 rounded-lg">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
                         <div class="ml-4">
               <p class="text-sm font-medium text-gray-500">Coût Mensuel Moyen</p>
               <p class="text-2xl font-semibold text-gray-900">{{ formatCurrency(statistics.average_monthly_cost || 0) }}</p>
             </div>
          </div>
        </div>
      </div>

      <!-- Create ADSL Service Button -->
      <div class="mb-6">
                 <button 
           @click="showCreateModal = true"
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center"
         >
           <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
           </svg>
           Ajouter un Service ADSL
         </button>
      </div>

      <!-- ADSL Services Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
                 <div class="px-6 py-4 border-b border-gray-200">
           <h3 class="text-lg font-medium text-gray-900">Services ADSL</h3>
         </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NDappel</th>
                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Forfait</th>
                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coût Mensuel</th>
                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date de Début</th>
                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                 <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="facture in adslFactures" :key="facture.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                  {{ facture.numeros?.[0]?.NDappel || 'N/A' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ facture.adsl_provider }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ facture.adsl_plan }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatCurrency(facture.adsl_monthly_cost) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ formatDate(facture.adsl_start_date) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                                     <span :class="facture.is_factured ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                     {{ facture.is_factured ? 'Facturé' : 'En Attente' }}
                   </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                     <button 
                     @click="editFacture(facture)"
                     class="text-blue-600 hover:text-blue-900 mr-3"
                   >
                     Modifier
                   </button>
                   <button 
                     @click="deleteFacture(facture.id)"
                     class="text-red-600 hover:text-red-900"
                   >
                     Supprimer
                   </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create/Edit Modal -->
      <div v-if="showCreateModal || showEditModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3">
                         <h3 class="text-lg font-medium text-gray-900 mb-4">
               {{ showEditModal ? 'Modifier le Service ADSL' : 'Créer un Service ADSL' }}
             </h3>
            
            <form @submit.prevent="showEditModal ? updateAdslFacture() : createAdslFacture()">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">NDappel</label>
                  <input 
                    v-model="form.ndappel"
                    type="text" 
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required
                  />
                </div>

                                                                   <div>
                    <label class="block text-sm font-medium text-gray-700">Fournisseur</label>
                    <select 
                      v-model="form.adsl_provider"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      required
                    >
                      <option value="">Sélectionner un Fournisseur</option>
                      <option value="Algerie Telecom">Algerie Telecom</option>
                      <option value="Mobilis">Mobilis</option>
                    </select>
                  </div>

                                 <div>
                   <label class="block text-sm font-medium text-gray-700">Forfait</label>
                   <input 
                     v-model="form.adsl_plan"
                     type="text" 
                     class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                     placeholder="ex: Fibre 100Mbps"
                     required
                   />
                 </div>

                                                                   <div>
                    <label class="block text-sm font-medium text-gray-700">Coût Mensuel (DA)</label>
                    <input 
                      v-model="form.adsl_monthly_cost"
                      type="number" 
                      step="0.01"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                      required
                    />
                  </div>

                                 <div>
                   <label class="block text-sm font-medium text-gray-700">Date de Début</label>
                   <input 
                     v-model="form.adsl_start_date"
                     type="date" 
                     class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                     required
                   />
                 </div>

                                 <div>
                   <label class="block text-sm font-medium text-gray-700">Date de Fin (Optionnel)</label>
                   <input 
                     v-model="form.adsl_end_date"
                     type="date" 
                     class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                   />
                 </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700">Notes</label>
                  <textarea 
                    v-model="form.adsl_notes"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  ></textarea>
                </div>
              </div>

              <div class="mt-6 flex justify-end space-x-3">
                                 <button 
                   type="button"
                   @click="closeModal"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded-lg"
                 >
                   Annuler
                 </button>
                 <button 
                   type="submit"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg"
                 >
                   {{ showEditModal ? 'Mettre à Jour' : 'Créer' }}
                 </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'

export default {
  props: {
    adslFactures: {
      type: Array,
      default: () => []
    },
    statistics: {
      type: Object,
      default: () => ({})
    }
  },
  setup() {
    const showCreateModal = ref(false)
    const showEditModal = ref(false)
    const editingFacture = ref(null)
    
    const form = ref({
      ndappel: '',
      adsl_provider: '',
      adsl_plan: '',
      adsl_monthly_cost: '',
      adsl_start_date: '',
      adsl_end_date: '',
      adsl_notes: ''
    })

    const resetForm = () => {
      form.value = {
        ndappel: '',
        adsl_provider: '',
        adsl_plan: '',
        adsl_monthly_cost: '',
        adsl_start_date: '',
        adsl_end_date: '',
        adsl_notes: ''
      }
    }

    const closeModal = () => {
      showCreateModal.value = false
      showEditModal.value = false
      editingFacture.value = null
      resetForm()
    }

    const createAdslFacture = () => {
      router.post(route('adsl.factures.create'), form.value, {
        onSuccess: () => {
          closeModal()
          router.reload()
        }
      })
    }

    const editFacture = (facture) => {
      editingFacture.value = facture
      form.value = {
        ndappel: facture.numeros?.[0]?.NDappel || '',
        adsl_provider: facture.adsl_provider || '',
        adsl_plan: facture.adsl_plan || '',
        adsl_monthly_cost: facture.adsl_monthly_cost || '',
        adsl_start_date: facture.adsl_start_date || '',
        adsl_end_date: facture.adsl_end_date || '',
        adsl_notes: facture.adsl_notes || ''
      }
      showEditModal.value = true
    }

    const updateAdslFacture = () => {
      router.put(route('adsl.factures.update', editingFacture.value.id), form.value, {
        onSuccess: () => {
          closeModal()
          router.reload()
        }
      })
    }

    const deleteFacture = (id) => {
      if (confirm('Are you sure you want to delete this ADSL service?')) {
        router.delete(route('adsl.factures.destroy', id), {
          onSuccess: () => {
            router.reload()
          }
        })
      }
    }

    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('ar-DZ', {
        style: 'currency',
        currency: 'DZD'
      }).format(amount || 0)
    }

    const formatDate = (date) => {
      if (!date) return 'N/A'
      return new Date(date).toLocaleDateString('fr-FR')
    }

    return {
      showCreateModal,
      showEditModal,
      editingFacture,
      form,
      closeModal,
      createAdslFacture,
      editFacture,
      updateAdslFacture,
      deleteFacture,
      formatCurrency,
      formatDate
    }
  }
}
</script> 