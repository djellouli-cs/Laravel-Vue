<script setup>
import Layout from '@/Layouts/LayoutAnnuaire.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineOptions({ layout: Layout });

const props = defineProps({
    permanence: Object,
    currentWeek: Object,
    destinations: Array,
});

const isAdmin = usePage().props.auth.user?.role === 'admin';

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR');
}

function getNDappels(psName, mobileOnly = false) {
    if (!psName || !props.destinations) return [];
    const dest = props.destinations.find(d => d.name === psName);
    if (!dest || !dest.numeros) return [];

    return dest.numeros
        .filter(n => (mobileOnly ? n.technologie?.name === 'MOBILE' : true))
        .map(n => n.NDappel)
        .filter(Boolean);
}
</script>

<template>
    <Head title="Permanence de cette semaine" />

    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl p-8">

                <!-- Header -->
                <div class="flex justify-between items-start mb-7">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            Permanence de cette semaine
                        </h1>
                        <p class="text-gray-600 mt-1">
                            Du <span class="font-semibold">{{ formatDate(permanence?.DSemaine) }}</span>
                            au <span class="font-semibold">{{ formatDate(permanence?.FSemaine) }}</span>
                        </p>
                    </div>

                    <!-- Back button only for admin -->
                    <Link
                        v-if="isAdmin"
                        :href="route('permanences.index')"
                        class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition shadow"
                    >
                        ← Retour
                    </Link>
                </div>

                <!-- No permanence alert -->
                <div v-if="!permanence"
                    class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center"
                >
                    <h3 class="text-yellow-800 font-medium">Aucune permanence programmée</h3>
                    <p class="text-sm text-yellow-700 mt-1">Cette semaine est vide.</p>

                    <Link
                        v-if="isAdmin"
                        :href="route('permanences.create')"
                        class="mt-5 inline-block bg-yellow-200 hover:bg-yellow-300 text-yellow-800 px-4 py-2 rounded-md font-medium"
                    >
                        + Créer une permanence
                    </Link>
                </div>

                <!-- Permanence Content -->
                <div v-else>

                    <!-- Grid info -->
                    <div class="grid md:grid-cols-2 gap-5 mb-6">
                        <div class="bg-blue-50 p-5 rounded-xl">
                            <p class="text-sm text-blue-700 font-medium">Période</p>
                            <p class="text-lg font-semibold text-blue-900">
                                {{ formatDate(permanence.DSemaine) }} → {{ formatDate(permanence.FSemaine) }}
                            </p>
                        </div>

                        <div class="bg-green-50 p-5 rounded-xl">
                            <p class="text-sm text-green-700 font-medium">Personnel</p>
                            <p class="text-lg font-semibold text-green-900">{{ permanence.PSemaine }}</p>

                            <div class="flex flex-wrap gap-2 mt-2">
                                <span
                                    v-for="num in getNDappels(permanence.PSemaine)"
                                    :key="num"
                                    class="text-xs bg-green-200 text-green-900 px-3 py-1 rounded-full"
                                >
                                    ☎ {{ num }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-orange-50 p-5 rounded-xl mb-6">
                        <p class="text-sm text-orange-700 font-medium">Repos</p>
                        <p class="text-lg font-semibold text-orange-900">{{ permanence.RSemaine }}</p>
                    </div>

                    <!-- Buttons for Admin only -->
                    <div v-if="isAdmin" class="flex justify-end gap-3">
                        <Link
                            :href="route('permanences.edit', permanence.id)"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg"
                        >
                            ✏ Modifier
                        </Link>

                        <Link
                            :href="route('permanences.show', permanence.id)"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"
                        >
                            👁 Voir détails
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
