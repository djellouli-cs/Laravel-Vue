<script setup>
import Layout from '@/Layouts/LayoutAnnuaire.vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import { ref, computed, reactive } from 'vue';

defineOptions({ layout: Layout });

const props = defineProps({
    permanence: Object,
    destinations: Array,
    selected_destinations: Array,
});

const form = useForm({
    DSemaine: props.permanence.DSemaine,
    FSemaine: props.permanence.FSemaine,
    PSemaine: props.permanence.PSemaine,
    RSemaine: props.permanence.RSemaine,
});

// Reactive state for week selection
const weekState = reactive({
    activeWeek: null,
    currentDate: new Date()
});

// Computed property for current date display
const currentDateDisplay = computed(() => {
    const date = weekState.currentDate;
    return date.toLocaleDateString('fr-FR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
});

// Computed properties for week calculations
const weekOptions = computed(() => {
    const date = weekState.currentDate;
    const j = date.getDate();
    const y = date.getFullYear();
    const m1 = date.getMonth() + 1;
    
    return [
        { id: 1, label: 'Cette semaine', offset: -7 },
        { id: 2, label: 'Prochain semaine', offset: 1 },
        { id: 3, label: '3ème Sem', offset: 8 },
        { id: 4, label: '4ème Sem', offset: 15 },
        { id: 5, label: '5ème Sem', offset: 22 },
        { id: 6, label: '6ème Sem', offset: 29 }
    ];
});

// Computed property to get week dates
const getWeekDates = computed(() => {
    return (weekNumber) => {
        const weekOption = weekOptions.value.find(w => w.id === weekNumber);
        if (!weekOption) return { start: '', end: '' };
        
        const date = weekState.currentDate;
        const j = date.getDate();
        const y = date.getFullYear();
        const m1 = date.getMonth() + 1;
        const startOffset = weekOption.offset;
        
        // Find the Sunday for the selected week
        for (let x = startOffset; x <= startOffset + 7; x++) {
            const date1 = new Date(y, m1 - 1, j + x);
            const d1 = date1.toLocaleDateString("fr", { weekday: "long" });
            
            if (d1 === "dimanche") {
                // Found Sunday, calculate week start and end
                const j2 = date1.getDate();
                const j22 = j2 <= 9 ? "0" + j2 : j2;
                const m2 = date1.getMonth() + 1;
                const m22 = m2 <= 9 ? "0" + m2 : m2;
                const y2 = date1.getFullYear();
                const w1 = y2 + "-" + m22 + "-" + j22; // Start date (Sunday)
                
                // Calculate end date (Saturday)
                const date2 = new Date(y2, m2 - 1, j2 + 6);
                const j3 = date2.getDate();
                const j33 = j3 <= 9 ? "0" + j3 : j3;
                const m3 = date2.getMonth() + 1;
                const m33 = m3 <= 9 ? "0" + m3 : m3;
                const y3 = date2.getFullYear();
                const w2 = y3 + "-" + m33 + "-" + j33; // End date (Saturday)
                
                return { start: w1, end: w2 };
            }
        }
        
        return { start: '', end: '' };
    };
});

// Vue.js method to select week
const selectWeek = (weekNumber) => {
    const dates = getWeekDates.value(weekNumber);
    form.DSemaine = dates.start;
    form.FSemaine = dates.end;
    weekState.activeWeek = weekNumber;
};

// Vue.js method to reset week selection
const resetWeekSelection = () => {
    weekState.activeWeek = null;
};

function submit() {
    form.put(`/permanences/${props.permanence.id}`);
}
</script>

<template>
    <Head title="Edit Permanence" />

    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Permanence</h1>
                <p class="mt-2 text-sm text-gray-600">Update permanence information and destinations</p>
            </div>
            <Link
                :href="route('permanences.show', permanence.id)"
                class="text-blue-600 hover:text-blue-800 flex items-center gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Details
            </Link>
        </div>

        <!-- Error Messages -->
        <div v-if="form.errors.length > 0" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                <li v-for="error in form.errors" :key="error">{{ error }}</li>
            </ul>
        </div>

        <form @submit.prevent="submit" class="space-y-8">
            <!-- Basic Information -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Basic Information</h2>
                </div>
                
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Start Week (DSemaine) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                v-model="form.DSemaine"
                                :class="[
                                    'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                                    form.errors.DSemaine ? 'border-red-500' : 'border-gray-300'
                                ]"
                            />
                            <p v-if="form.errors.DSemaine" class="mt-1 text-sm text-red-600">{{ form.errors.DSemaine }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                End Week (FSemaine) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                v-model="form.FSemaine"
                                :class="[
                                    'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                                    form.errors.FSemaine ? 'border-red-500' : 'border-gray-300'
                                ]"
                            />
                            <p v-if="form.errors.FSemaine" class="mt-1 text-sm text-red-600">{{ form.errors.FSemaine }}</p>
                        </div>
                    </div>

                    <!-- Week Selection Buttons -->
                    <div class="mt-6">
                        <div class="flex justify-between items-center mb-3">
                            <label class="block text-sm font-medium text-gray-700">Quick Week Selection</label>
                            <span class="text-sm text-gray-500">Today: {{ currentDateDisplay }}</span>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button 
                                v-for="week in weekOptions"
                                :key="week.id"
                                type="button" 
                                @click="selectWeek(week.id)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                                :class="{ 'bg-green-500 text-white': weekState.activeWeek === week.id }"
                            >
                                <span v-if="week.id === 3">3<sup>ème</sup> Sem</span>
                                <span v-else-if="week.id === 4">4<sup>ème</sup> Sem</span>
                                <span v-else-if="week.id === 5">5<sup>ème</sup> Sem</span>
                                <span v-else-if="week.id === 6">6<sup>ème</sup> Sem</span>
                                <span v-else>{{ week.label }}</span>
                            </button>
                        </div>
                        
                        <!-- Display selected week info -->
                        <div v-if="weekState.activeWeek" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-sm text-blue-800">
                                <strong>Selected Week:</strong> 
                                {{ form.DSemaine }} to {{ form.FSemaine }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                PSemaine <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.PSemaine"
                                :class="[
                                    'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                                    form.errors.PSemaine ? 'border-red-500' : 'border-gray-300'
                                ]"
                            >
                                <option value="">Select PSemaine</option>
                                <option
                                    v-for="dest in destinations"
                                    :key="dest.id"
                                    :value="dest.name"
                                >
                                    {{ dest.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.PSemaine" class="mt-1 text-sm text-red-600">{{ form.errors.PSemaine }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                RSemaine <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.RSemaine"
                                :class="[
                                    'w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent',
                                    form.errors.RSemaine ? 'border-red-500' : 'border-gray-300'
                                ]"
                            >
                                <option value="">Select RSemaine</option>
                                <option
                                    v-for="dest in destinations"
                                    :key="dest.id"
                                    :value="dest.name"
                                >
                                    {{ dest.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.RSemaine" class="mt-1 text-sm text-red-600">{{ form.errors.RSemaine }}</p>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4">
                <Link
                    :href="route('permanences.show', permanence.id)"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Cancel
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors"
                >
                    <span v-if="form.processing">Updating...</span>
                    <span v-else>Update Permanence</span>
                </button>
            </div>
        </form>
    </div>
</template> 