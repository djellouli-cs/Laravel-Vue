<template>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-2xl mx-auto">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Créer un Fax</h1>
                    <Link
                        :href="route('faxes.index')"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
                    >
                        Retour
                    </Link>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-6">
                            <!-- NDappel Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Numéro d'appel (NDappel) *
                                </label>
                                <select
                                    v-model="form.NDappel"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.NDappel }"
                                >
                                    <option value="">Sélectionner un numéro d'appel</option>
                                    <option
                                        v-for="numero in availableNumeros"
                                        :key="numero.id"
                                        :value="numero.NDappel"
                                    >
                                        {{ numero.NDappel }} - {{ numero.destination?.organisme?.name || 'N/A' }}
                                    </option>
                                </select>
                                <p v-if="errors.NDappel" class="mt-1 text-sm text-red-600">
                                    {{ errors.NDappel }}
                                </p>
                            </div>



                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Description
                                </label>
                                <textarea
                                    v-model="form.description"
                                    rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :class="{ 'border-red-500': errors.description }"
                                    placeholder="Description optionnelle du fax"
                                ></textarea>
                                <p v-if="errors.description" class="mt-1 text-sm text-red-600">
                                    {{ errors.description }}
                                </p>
                            </div>



                            <!-- Submit Button -->
                            <div class="flex justify-end space-x-3">
                                <Link
                                    :href="route('faxes.index')"
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
                                >
                                    Annuler
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold py-2 px-4 rounded"
                                >
                                    {{ processing ? 'Création...' : 'Créer le Fax' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    availableNumeros: Array,
    errors: Object,
})

const form = useForm({
    NDappel: '',
    description: '',
})

const processing = ref(false)

const submit = () => {
    processing.value = true
    form.post(route('faxes.store'), {
        onFinish: () => {
            processing.value = false
        },
    })
}
</script> 