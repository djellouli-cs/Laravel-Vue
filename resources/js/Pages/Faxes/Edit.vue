<template>
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Modifier le Fax</h1>
                <div class="flex space-x-3">
                    <Link
                        :href="route('faxes.index')"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                    >
                        ← Retour
                    </Link>
                    <Link
                        :href="route('faxes.show', fax.id)"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Voir le Fax
                    </Link>
                </div>
            </div>

            <!-- Error Messages -->
            <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h3 class="font-semibold mb-2">Erreurs de validation :</h3>
                <ul class="list-disc ml-4">
                    <li v-for="(error, field) in form.errors" :key="field">
                        {{ error }}
                    </li>
                </ul>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <form @submit.prevent="submit">
                    <!-- NDappel -->
                    <div class="mb-4">
                        <label for="NDappel" class="block text-sm font-medium text-gray-700 mb-2">
                            Numéro d'appel *
                        </label>
                        <select
                            id="NDappel"
                            v-model="form.NDappel"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.NDappel }"
                            required
                        >
                            <option value="">Sélectionner un numéro</option>
                            <option 
                                v-for="numero in availableNumeros" 
                                :key="numero.NDappel" 
                                :value="numero.NDappel"
                            >
                                {{ numero.NDappel }} - {{ numero.destination?.organisme?.name || 'Organisme inconnu' }}
                            </option>
                        </select>
                        <p v-if="form.errors.NDappel" class="text-red-600 text-sm mt-1">{{ form.errors.NDappel }}</p>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.description }"
                            placeholder="Description optionnelle du fax..."
                        ></textarea>
                        <p v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3">
                        <Link
                            :href="route('faxes.index')"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded"
                        >
                            Annuler
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold py-2 px-4 rounded"
                        >
                            <span v-if="form.processing">Enregistrement...</span>
                            <span v-else>Mettre à jour</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Current Fax Info -->
            <div class="mt-6 bg-gray-50 rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Informations actuelles</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="font-medium">Numéro d'appel :</span>
                        <span class="ml-2">{{ fax.NDappel }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Description :</span>
                        <span class="ml-2">{{ fax.description || 'Aucune description' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Organisme :</span>
                        <span class="ml-2">{{ fax.numero?.destination?.organisme?.name || 'Non assigné' }}</span>
                    </div>
                    <div>
                        <span class="font-medium">Créé par :</span>
                        <span class="ml-2">
                            <span v-if="fax.user" class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                👤 {{ fax.user.name }}
                            </span>
                            <span v-else class="text-gray-500">Utilisateur inconnu</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    fax: Object,
    availableNumeros: Array,
    errors: Object,
})

const form = useForm({
    NDappel: props.fax.NDappel,
    description: props.fax.description || '',
})

const submit = () => {
    form.put(route('faxes.update', props.fax.id))
}
</script> 