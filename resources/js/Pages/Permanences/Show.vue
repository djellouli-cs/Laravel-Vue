<script setup>
import Layout from '@/Layouts/LayoutAnnuaire.vue';
import { Head, Link, router } from '@inertiajs/vue3';

defineOptions({ layout: Layout });

const props = defineProps({
    permanence: Object,
});

function deletePermanence() {
    if (confirm('Are you sure you want to delete this permanence?')) {
        router.delete(`/permanences/${props.permanence.id}`);
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatDateShort(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR');
}
</script>

<template>
    <Head :title="`Permanence - ${permanence.PSemaine}`" />

    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Détails de la permanence</h1>
                <p class="mt-2 text-sm text-gray-600">Voir les informations de la permanence et les destinations</p>
            </div>
            <div class="flex gap-3">
                <Link
                    :href="route('permanences.edit', permanence.id)"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Modifier
                </Link>
                <button
                    @click="deletePermanence"
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Supprimer
                </button>
            </div>
        </div>

        <!-- Success Message -->
        <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ $page.props.flash.success }}
        </div>

        <!-- Back Button -->
        <div class="mb-6">
            <Link
                :href="route('permanences.index')"
                class="text-blue-600 hover:text-blue-800 flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour aux permanences
            </Link>
        </div>

        <!-- Permanence Details -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Informations sur la permanence</h2>
            </div>
            
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Period Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Période</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Date de début</label>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDate(permanence.DSemaine) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Date de fin</label>
                                <p class="mt-1 text-sm text-gray-900">{{ formatDate(permanence.FSemaine) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Durée</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ Math.ceil((new Date(permanence.FSemaine) - new Date(permanence.DSemaine)) / (1000 * 60 * 60 * 24)) }} jours
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Week Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Détails de la semaine</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">PSemaine</label>
                                <p class="mt-1 text-sm text-gray-900">{{ permanence.PSemaine }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">RSemaine</label>
                                <p class="mt-1 text-sm text-gray-900">{{ permanence.RSemaine }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <!-- Additional Information -->
        <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Informations supplémentaires</h2>
            </div>
            
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Créé le</label>
                        <p class="mt-1 text-sm text-gray-900">{{ formatDate(permanence.created_at) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Dernière modification</label>
                        <p class="mt-1 text-sm text-gray-900">{{ formatDate(permanence.updated_at) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 