<script setup>
import Layout from '@/Layouts/LayoutAnnuaire.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({ layout: Layout });

const props = defineProps({
    permanenceStats: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="min-h-screen bg-gradient-to-br from-blue-100 via-white to-blue-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="text-center mb-8">
                <transition name="fade">
                    <div
                        v-if="$page.props.auth.user"
                        class="text-center p-8 bg-white shadow-2xl rounded-2xl border border-blue-100"
                    >
                        <h1 class="text-4xl md:text-6xl font-extrabold text-gray-800 mb-4">
                            👋 {{ $page.props.flash.greet || 'Hello' }}
                        </h1>
                        <p class="text-2xl md:text-3xl font-semibold text-blue-600">
                            {{ $page.props.auth.user.name }}!
                        </p>
                    </div>
                </transition>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Permanences -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Permanences</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ permanenceStats.total }}</p>
                        </div>
                    </div>
                </div>

                <!-- Active Permanences -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Active Permanences</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ permanenceStats.active }}</p>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Permanences -->
                <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Upcoming Permanences</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ permanenceStats.upcoming }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Link
                        :href="route('permanences.index')"
                        class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
                    >
                        <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">View Permanences</span>
                    </Link>

                    <Link
                        :href="route('permanences.create')"
                        class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors"
                    >
                        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Create Permanence</span>
                    </Link>

                    <Link
                        :href="route('change-tracking.index')"
                        class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors"
                    >
                        <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Suivi des Changements</span>
                    </Link>

                    <Link
                        :href="route('numero.manage')"
                        class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors"
                    >
                        <svg class="w-6 h-6 text-indigo-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Gérer Numéros</span>
                    </Link>

                    <Link
                        :href="route('Annuaire.index')"
                        class="flex items-center p-4 bg-teal-50 rounded-lg hover:bg-teal-100 transition-colors"
                    >
                        <svg class="w-6 h-6 text-teal-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Annuaire</span>
                    </Link>

                    <Link
                        :href="route('Address.index')"
                        class="flex items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors"
                    >
                        <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-900">Network</span>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}
</style>
