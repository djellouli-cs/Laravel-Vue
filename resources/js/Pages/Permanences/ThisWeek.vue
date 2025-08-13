<script setup>
import Layout from '@/Layouts/LayoutAnnuaire.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({ layout: Layout });

const props = defineProps({
    permanence: Object,
    currentWeek: Object,
    destinations: Array,
});

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR');
}

function getMobileNDappels(psName) {
  if (!psName || !props.destinations) return [];
  const dest = props.destinations.find(d => d.name === psName);
  if (!dest) return [];
  if (Array.isArray(dest.numeros) && dest.numeros.length > 0) {
    return dest.numeros
      .filter(num => num.technologie && num.technologie.name === 'MOBILE')
      .map(num => num.NDappel)
      .filter(Boolean);
  }
  return [];
}

function getNDappels(psName) {
  if (!psName || !props.destinations) return [];
  const dest = props.destinations.find(d => d.name === psName);
  if (!dest) return [];
  if (Array.isArray(dest.numeros) && dest.numeros.length > 0) {
    return dest.numeros.map(num => num.NDappel).filter(Boolean);
  }
  return dest.NDappel ? [dest.NDappel] : [];
}
</script>

<template>
    <Head title="Permanence de cette semaine" />
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-xl p-8">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Permanence de cette semaine</h1>
                        <p class="text-md text-gray-600">
                            Du <span class="font-semibold">{{ formatDate(permanence?.DSemaine) }}</span> au <span class="font-semibold">{{ formatDate(permanence?.FSemaine) }}</span>
                        </p>
                    </div>
                    <Link
                        :href="route('permanences.index')"
                        class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2 rounded-lg flex items-center gap-2 shadow-md hover:shadow-lg transition-all duration-200"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour aux permanences
                    </Link>
                </div>

                <div v-if="!permanence" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-yellow-800">Aucune permanence programmée</h3>
                    <p class="mt-1 text-sm text-yellow-700">
                        Aucune permanence n'est programmée pour cette semaine.
                    </p>
                    <div class="mt-6">
                        <Link
                            :href="route('permanences.create')"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-yellow-800 bg-yellow-100 hover:bg-yellow-200"
                        >
                            Créer une permanence
                        </Link>
                    </div>
                </div>

                <div v-else>
                    <div class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-blue-50 p-6 rounded-lg flex flex-col justify-center items-start">
                                <h3 class="text-sm font-medium text-blue-800 mb-2">Période</h3>
                                <p class="text-lg font-semibold text-blue-900">
                                    {{ formatDate(permanence.DSemaine) }} - {{ formatDate(permanence.FSemaine) }}
                                </p>
                            </div>
                            <div class="bg-green-50 p-6 rounded-lg flex flex-col justify-center items-start">
                                <h3 class="text-sm font-medium text-green-800 mb-2">Personnel de permanence</h3>
                                <p class="text-lg font-semibold text-green-900">{{ permanence.PSemaine }}</p>
                                <div v-if="getNDappels(permanence.PSemaine).length > 0" class="mt-2 flex flex-wrap gap-2">
                                    <span v-for="num in getNDappels(permanence.PSemaine)" :key="num" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                        </svg>
                                        {{ num }}
                                    </span>
                                </div>
                                <div v-else class="text-xs text-gray-500 italic mt-2">Aucun numéro de téléphone</div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-orange-50 p-6 rounded-lg mb-8">
                        <h3 class="text-sm font-medium text-orange-800 mb-2">Repos</h3>
                        <p class="text-lg font-semibold text-orange-900">{{ permanence.RSemaine }}</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <Link
                            :href="route('permanences.edit', permanence.id)"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors"
                        >
                            Modifier
                        </Link>
                        <Link
                            :href="route('permanences.show', permanence.id)"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                        >
                            Voir détails
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template> 