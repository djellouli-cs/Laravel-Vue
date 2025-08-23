<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-10">
    <div class="p-8 max-w-4xl mx-auto bg-white rounded-2xl shadow-xl border border-blue-100">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-extrabold text-blue-800">Détails du numéro</h1>
        <div class="flex gap-3">
          <button @click="goBack" class="px-4 py-2 rounded-lg border hover:bg-gray-50">⬅️ Retour</button>
          <button v-if="numero?.id" @click="editNumero(numero)" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
            ✏️ Éditer
          </button>
        </div>
      </div>

      <div class="bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-2xl p-8 shadow-lg">
        <h2 class="text-2xl font-bold mb-6 text-blue-700">
          NDappel : <span class="text-blue-900">{{ numero.NDappel }}</span>
        </h2>

        <div class="grid grid-cols-2 gap-6 text-base">
          <div><span class="font-semibold text-blue-700">ID :</span> {{ numero.id }}</div>
          <div><span class="font-semibold text-blue-700">Position :</span> {{ numero.Position || '—' }}</div>

          <div>
            <span class="font-semibold text-blue-700">Organisme :</span>
            <span v-if="numero.organisme">
              {{ numero.organisme.name }}
              <span class="text-red-500 italic ml-1">{{ numero.organisme.name_fr }}</span>
            </span>
            <span v-else>—</span>
          </div>

          <div>
            <span class="font-semibold text-blue-700">Destination :</span>
            <span v-if="numero.destination">
              {{ numero.destination.name }}
              <span class="text-red-500 italic ml-1">{{ numero.destination.name_fr }}</span>
            </span>
            <span v-else>—</span>
          </div>

          <div class="col-span-2">
            <span class="font-semibold text-blue-700">Groupes :</span>
            <div v-if="numero.destination?.groupes?.length" class="flex flex-wrap gap-2 mt-2">
              <span v-for="g in numero.destination.groupes" :key="g.id ?? g" class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full border border-blue-200">
                📋 {{ g.groupes }}
              </span>
            </div>
            <span v-else class="text-gray-400">Aucun groupe assigné</span>
          </div>

          <div><span class="font-semibold text-blue-700">Classe :</span> {{ numero.classe?.classe || '—' }}</div>
          <div><span class="font-semibold text-blue-700">Type :</span> {{ numero.type?.name || '—' }}</div>

          <div class="col-span-2">
            <span class="font-semibold text-blue-700">Réserve :</span>
            <span v-if="numero.reserve">
              {{ numero.reserve.reserve ?? '—' }}
              <span class="ml-2" :class="numero.reserve?.is_reserved ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                — {{ numero.reserve?.is_reserved ? '📌 Réservé' : '❌ Non réservé' }}
              </span>
            </span>
            <span v-else>—</span>
          </div>

          <div><span class="font-semibold text-blue-700">Technologie :</span> {{ numero.technologie?.name || '—' }}</div>

          <div class="col-span-2">
            <span class="font-semibold text-blue-700">Facture :</span>
            <span v-if="numero.facture">
              {{ numero.facture.facture ?? '—' }}
              <span class="ml-2" :class="numero.facture?.is_factured ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                — {{ numero.facture?.is_factured ? '💰 Facturé' : '❌ Non facturé' }}
              </span>
            </span>
            <span v-else>—</span>
          </div>

          <div class="col-span-2">
            <span class="font-semibold text-blue-700">Poste :</span>
            <span v-if="numero.post">
              {{ numero.post.post }} — Marque : {{ numero.post.marque }}
            </span>
            <span v-else>—</span>
          </div>

          <div class="col-span-2">
            <span class="font-semibold text-blue-700">Fax :</span>
            <span v-if="numero.fax">
              {{ numero.fax.description ?? '—' }}
              <span class="ml-2" :class="numero.fax?.NDappel ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                — {{ numero.fax?.NDappel ? '📠 FAX' : '❌ Non FAX' }}
              </span>
            </span>
            <span v-else>—</span>
          </div>

          <div><span class="font-semibold text-blue-700">Matricule :</span> {{ numero.matricule?.matricule || '—' }}</div>
          <div>
            <span class="font-semibold text-blue-700">Service :</span>
            <span v-if="numero.service">
              {{ numero.service.name }}
              <span v-if="numero.service.name_fr" class="text-red-500 italic ml-1">{{ numero.service.name_fr }}</span>
            </span>
            <span v-else>—</span>
          </div>
        </div>

        <div class="mt-8">
          <h3 class="font-semibold text-lg mb-3 text-blue-800 flex items-center gap-2">📦 Acheminements</h3>
          <ul v-if="numero.acheminements?.length" class="list-disc ml-7 text-base text-blue-900">
            <li v-for="ach in numero.acheminements" :key="ach.id ?? ach.acheminement">
              {{ ach.acheminement }} — {{ formatDate(ach.updated_at) }}
            </li>
          </ul>
          <p v-else class="text-gray-400 text-base">Aucun acheminement disponible.</p>
        </div>

        <div class="mt-8">
          <h3 class="font-semibold text-lg mb-3 text-blue-800 flex items-center gap-2">📝 Notes</h3>
          <ul v-if="numero.notes?.length" class="list-disc ml-7 text-base text-blue-900">
            <li v-for="note in numero.notes" :key="note.id ?? note.created_at">
              {{ note.content }}
              <span class="text-gray-400 text-xs">({{ formatDate(note.created_at, true) }})</span>
            </li>
          </ul>
          <p v-else class="text-gray-400 text-base">Aucune note disponible.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'
import Layout from '@/Layouts/LayoutAnnuaire.vue'

defineOptions({ layout: Layout })
const props = defineProps({ numero: { type: Object, required: true } })
const numero = computed(() => props.numero)

function goBack() {
  router.visit('/Annuaire')
}
function editNumero(n) {
  if (n?.id) router.visit(`/numeros/${n.id}/edit`)
}
function formatDate(dateIso, withTime = false) {
  if (!dateIso) return '—'
  try {
    const d = new Date(dateIso)
    if (isNaN(d)) return '—'
    return withTime ? d.toLocaleString('fr-FR') : d.toLocaleDateString('fr-FR')
  } catch { return '—' }
}
</script>
