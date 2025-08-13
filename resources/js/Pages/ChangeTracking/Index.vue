<template>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Suivi des Changements</h1>
                <div class="flex space-x-3">
                    <Link
                        :href="route('dashboard')"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        ← Dashboard
                    </Link>
                    <Link
                        :href="route('numero.manage')"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Gérer Numéros
                    </Link>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Aujourd'hui</p>
                            <p class="text-2xl font-bold text-gray-900">{{ getTodayTotal() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Cette Semaine</p>
                            <p class="text-2xl font-bold text-gray-900">{{ getWeekTotal() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ce Mois</p>
                            <p class="text-2xl font-bold text-gray-900">{{ getMonthTotal() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Utilisateurs Actifs</p>
                            <p class="text-2xl font-bold text-gray-900">{{ getActiveUsers() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Filtres</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Table</label>
                        <select v-model="filters.table" @change="updateFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="all">Toutes les tables</option>
                            <option v-for="(name, key) in tables" :key="key" :value="key">{{ name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
                        <select v-model="filters.user" @change="updateFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="all">Tous les utilisateurs</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                        <select v-model="filters.action" @change="updateFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="all">Toutes les actions</option>
                            <option value="created">Créé</option>
                            <option value="updated">Modifié</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                        <input 
                            type="date" 
                            v-model="filters.start_date" 
                            @change="updateFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                        <input 
                            type="date" 
                            v-model="filters.end_date" 
                            @change="updateFilters"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Par page</label>
                        <select v-model="filters.per_page" @change="updateFilters" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                
                <!-- Quick Date Presets -->
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-sm font-medium text-gray-700 mr-2">Préréglages rapides:</span>
                    <button 
                        @click="setDateRange('today')" 
                        class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium px-3 py-1 rounded"
                    >
                        Aujourd'hui
                    </button>
                    <button 
                        @click="setDateRange('yesterday')" 
                        class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium px-3 py-1 rounded"
                    >
                        Hier
                    </button>
                    <button 
                        @click="setDateRange('week')" 
                        class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium px-3 py-1 rounded"
                    >
                        Cette semaine
                    </button>
                    <button 
                        @click="setDateRange('month')" 
                        class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium px-3 py-1 rounded"
                    >
                        Ce mois
                    </button>
                    <button 
                        @click="setDateRange('last_month')" 
                        class="bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-medium px-3 py-1 rounded"
                    >
                        Mois dernier
                    </button>
                </div>
                
                <!-- Clear Filters Button -->
                <div class="mt-4 flex justify-end">
                    <button 
                        @click="clearFilters" 
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
                    >
                        Effacer les filtres
                    </button>
                </div>
            </div>

            <!-- Changes Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Changements Récents</h3>
                        <div v-if="filters.start_date || filters.end_date" class="text-sm text-gray-600">
                            <span v-if="filters.start_date && filters.end_date">
                                📅 Filtre: {{ formatDisplayDate(filters.start_date) }} - {{ formatDisplayDate(filters.end_date) }}
                            </span>
                            <span v-else-if="filters.start_date">
                                📅 Depuis: {{ formatDisplayDate(filters.start_date) }}
                            </span>
                            <span v-else-if="filters.end_date">
                                📅 Jusqu'au: {{ formatDisplayDate(filters.end_date) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Table
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Enregistrement
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Utilisateur
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Détails
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="changes.length === 0" class="hover:bg-gray-50">
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Aucun changement trouvé.
                                </td>
                            </tr>
                            <tr v-for="change in changes" :key="`${change.table}-${change.id}`" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <span v-if="change.action === 'created'" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        ✨ Créé
                                    </span>
                                    <span v-else class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        ✏️ Modifié
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ change.table_name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ change.record_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span v-if="change.user" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        👤 {{ change.user.name }}
                                    </span>
                                    <span v-else class="text-gray-500">—</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDate(change.updated_at) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="space-y-1">
                                        <div v-for="(value, key) in change.details" :key="key" class="text-xs">
                                            <span class="font-medium">{{ key }}:</span> {{ value }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
    changes: Array,
    filters: Object,
    stats: Object,
    users: Array,
    tables: Object,
})

const filters = ref({
    table: props.filters.table || 'all',
    user: props.filters.user || 'all',
    action: props.filters.action || 'all',
    per_page: props.filters.per_page || 20,
    start_date: props.filters.start_date || '',
    end_date: props.filters.end_date || '',
})

const updateFilters = () => {
    router.get(route('change-tracking.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
    })
}

const clearFilters = () => {
    filters.value = {
        table: 'all',
        user: 'all',
        action: 'all',
        per_page: 20,
        start_date: '',
        end_date: '',
    }
    updateFilters()
}

const setDateRange = (range) => {
    const today = new Date()
    const yesterday = new Date(today)
    yesterday.setDate(yesterday.getDate() - 1)
    
    const startOfWeek = new Date(today)
    startOfWeek.setDate(today.getDate() - today.getDay())
    
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1)
    const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1)
    const endOfLastMonth = new Date(today.getFullYear(), today.getMonth(), 0)
    
    switch (range) {
        case 'today':
            filters.value.start_date = today.toISOString().split('T')[0]
            filters.value.end_date = today.toISOString().split('T')[0]
            break
        case 'yesterday':
            filters.value.start_date = yesterday.toISOString().split('T')[0]
            filters.value.end_date = yesterday.toISOString().split('T')[0]
            break
        case 'week':
            filters.value.start_date = startOfWeek.toISOString().split('T')[0]
            filters.value.end_date = today.toISOString().split('T')[0]
            break
        case 'month':
            filters.value.start_date = startOfMonth.toISOString().split('T')[0]
            filters.value.end_date = today.toISOString().split('T')[0]
            break
        case 'last_month':
            filters.value.start_date = lastMonth.toISOString().split('T')[0]
            filters.value.end_date = endOfLastMonth.toISOString().split('T')[0]
            break
    }
    updateFilters()
}

const formatDate = (date) => {
    return new Date(date).toLocaleString('fr-FR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const formatDisplayDate = (dateString) => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const getTodayTotal = () => {
    if (!props.stats) return 0
    return Object.values(props.stats).reduce((total, stat) => total + stat.today, 0)
}

const getWeekTotal = () => {
    if (!props.stats) return 0
    return Object.values(props.stats).reduce((total, stat) => total + stat.this_week, 0)
}

const getMonthTotal = () => {
    if (!props.stats) return 0
    return Object.values(props.stats).reduce((total, stat) => total + stat.this_month, 0)
}

const getActiveUsers = () => {
    if (!props.users) return 0
    return props.users.length
}
</script> 