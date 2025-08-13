<template>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Statistiques des Fax</h1>
                <div class="flex space-x-3">
                    <Link
                        :href="route('faxes.index')"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        📠 Gérer les Fax
                    </Link>
                    <Link
                        :href="route('numero.manage')"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                    >
                        📞 Gérer les Numéros
                    </Link>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Numeros -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Total Numéros</p>
                                <p class="text-2xl font-bold text-gray-900">{{ statistics.total_numeros }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Faxes -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Numéros Fax</p>
                                <p class="text-2xl font-bold text-green-600">{{ statistics.total_faxes }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Non-Fax Numeros -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Numéros Téléphone</p>
                                <p class="text-2xl font-bold text-gray-600">{{ statistics.non_fax_numeros }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Percentage -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">% Fax</p>
                                <p class="text-2xl font-bold text-purple-600">{{ statistics.fax_percentage }}%</p>
                            </div>
                        </div>
                    </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Répartition Fax vs Téléphone</h3>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div 
                            class="bg-green-600 h-4 rounded-full transition-all duration-500"
                            :style="{ width: statistics.fax_percentage + '%' }"
                        ></div>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 mt-2">
                        <span>{{ statistics.total_faxes }} Fax</span>
                        <span>{{ statistics.non_fax_numeros }} Téléphone</span>
                    </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions Rapides</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <Link
                            :href="route('faxes.create')"
                            class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Ajouter un Fax</p>
                                <p class="text-sm text-gray-600">Créer un nouveau fax</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('faxes.index')"
                            class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Voir tous les Fax</p>
                                <p class="text-sm text-gray-600">Liste complète des fax</p>
                            </div>
                        </Link>

                        <Link
                            :href="route('numero.manage')"
                            class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Gérer Numéros</p>
                                <p class="text-sm text-gray-600">Voir tous les numéros</p>
                            </div>
                        </Link>
                    </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    statistics: Object,
})
</script> 