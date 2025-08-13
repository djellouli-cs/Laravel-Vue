<template>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Détails du Fax</h1>
                    <div class="flex space-x-3">
                        <Link
                            :href="route('faxes.edit', fax.id)"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Modifier
                        </Link>
                        <Link
                            :href="route('faxes.index')"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
                        >
                            Retour
                        </Link>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-gray-900">
                                Fax #{{ fax.id }}
                            </h2>

                        </div>
                    </div>

                    <!-- Content -->
                    <div class="px-6 py-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Fax Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du Fax</h3>
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Numéro d'appel (NDappel)</dt>
                                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ fax.NDappel }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ fax.description || 'Aucune description' }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Créé le</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ new Date(fax.created_at).toLocaleDateString('fr-FR') }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Modifié le</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ new Date(fax.updated_at).toLocaleDateString('fr-FR') }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Related Numero Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Numéro Associé</h3>
                                <div v-if="fax.numero" class="space-y-3">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Numéro d'appel</dt>
                                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ fax.numero.NDappel }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Position</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ fax.numero.Position }}</dd>
                                    </div>
                                    <div v-if="fax.numero.destination">
                                        <dt class="text-sm font-medium text-gray-500">Destination</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ fax.numero.destination.name }}</dd>
                                    </div>
                                    <div v-if="fax.numero.destination?.organisme">
                                        <dt class="text-sm font-medium text-gray-500">Organisme</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ fax.numero.destination.organisme.name }}</dd>
                                    </div>
                                    <div v-if="fax.numero.technologie">
                                        <dt class="text-sm font-medium text-gray-500">Technologie</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ fax.numero.technologie.name }}</dd>
                                    </div>
                                    <div v-if="fax.numero.type">
                                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ fax.numero.type.name }}</dd>
                                    </div>
                                    <div v-if="fax.numero.classe">
                                        <dt class="text-sm font-medium text-gray-500">Classe</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ fax.numero.classe.name }}</dd>
                                    </div>
                                </div>
                                <div v-else class="text-sm text-gray-500">
                                    Aucun numéro associé trouvé
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                ID: {{ fax.id }}
                            </div>
                            <div class="flex space-x-3">
                                <button
                                    @click="deleteFax"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                >
                                    Supprimer
                                </button>
                                <Link
                                    :href="route('faxes.edit', fax.id)"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                >
                                    Modifier
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import LayoutAnnuaire from '@/Layouts/LayoutAnnuaire.vue'

const props = defineProps({
    fax: Object,
})

const deleteFax = () => {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce fax ?')) {
        router.delete(route('faxes.destroy', props.fax.id))
    }
}
</script> 