<template>
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Gestion des Fax</h1>
                <div class="flex space-x-3">
                    <Link
                        :href="route('faxes.statistics')"
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded"
                    >
                        📊 Statistiques
                    </Link>
                    <Link
                        :href="route('faxes.create')"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Ajouter un Fax
                    </Link>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Recherche
                        </label>
                        <input
                            v-model="search"
                            type="text"
                            placeholder="Rechercher par NDappel, fax_number, description..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @input="debouncedSearch"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Tri
                        </label>
                        <select
                            v-model="sort"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="updateFilters"
                        >
                            <option value="desc">Plus récent</option>
                            <option value="asc">Plus ancien</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button
                            @click="clearFilters"
                            class="w-full bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
                        >
                            Réinitialiser
                        </button>
                    </div>
                </div>
            </div>

            <!-- Faxes Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    NDappel
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Description
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Organisme
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Utilisateur
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="!faxes.data || faxes.data.length === 0" class="hover:bg-gray-50">
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Aucun fax trouvé.
                                </td>
                            </tr>
                            <tr v-for="fax in faxes.data" :key="fax.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ fax.NDappel }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ fax.description || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ fax.numero?.destination?.organisme?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span v-if="fax.user" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        👤 {{ fax.user.name }}
                                    </span>
                                    <span v-else class="text-gray-500">—</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <Link
                                            :href="route('faxes.show', fax.id)"
                                            class="text-blue-600 hover:text-blue-900"
                                        >
                                            Voir
                                        </Link>
                                        <Link
                                            :href="route('faxes.edit', fax.id)"
                                            class="text-indigo-600 hover:text-indigo-900"
                                        >
                                            Modifier
                                        </Link>
                                        <button
                                            @click="deleteFax(fax.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Supprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="faxes && faxes.data && faxes.data.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <Link
                                v-if="faxes.prev_page_url"
                                :href="faxes.prev_page_url"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Précédent
                            </Link>
                            <Link
                                v-if="faxes.next_page_url"
                                :href="faxes.next_page_url"
                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Suivant
                            </Link>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Affichage de
                                    <span class="font-medium">{{ faxes.from || 0 }}</span>
                                    à
                                    <span class="font-medium">{{ faxes.to || 0 }}</span>
                                    sur
                                    <span class="font-medium">{{ faxes.total || 0 }}</span>
                                    résultats
                                </p>
                            </div>
                            <div v-if="faxes.links && faxes.links.length > 0">
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <template v-for="link in faxes.links" :key="link.label">
                                        <Link
                                            v-if="link.url"
                                            :href="link.url"
                                            v-html="link.label"
                                            :class="[
                                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                link.active
                                                    ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                            ]"
                                        />
                                        <span
                                            v-else
                                            v-html="link.label"
                                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium bg-gray-100 text-gray-400 cursor-not-allowed"
                                        />
                                    </template>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { debounce } from 'lodash'

const props = defineProps({
    faxes: Object,
    search: String,
    sort: String,
})

const search = ref(props.search || '')
const sort = ref(props.sort || 'desc')

const debouncedSearch = debounce(() => {
    updateFilters()
}, 300)

const updateFilters = () => {
    router.get(route('faxes.index'), {
        search: search.value,
        sort: sort.value,
    }, {
        preserveState: true,
        replace: true,
    })
}

const clearFilters = () => {
    search.value = ''
    sort.value = 'desc'
    updateFilters()
}

const deleteFax = (id) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce fax ?')) {
        router.delete(route('faxes.destroy', id))
    }
}
</script> 