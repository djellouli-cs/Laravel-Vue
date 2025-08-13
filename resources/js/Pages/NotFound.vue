<!-- resources/js/Pages/NotFound.vue -->
<template>
  <Head title="| Page non trouvée" />

  <section class="min-h-screen bg-gray-100 flex items-center justify-center">
    <div class="bg-white shadow-xl rounded-lg p-10 max-w-xl w-full text-center">
      <h1 class="text-6xl font-extrabold text-red-600 mb-4">404</h1>
      <p class="text-2xl font-semibold text-gray-800 mb-2">Oups ! Page non trouvée.</p>
      <p class="text-gray-500 mb-6">La page que vous recherchez n’existe pas ou a été déplacée.</p>
      
      <Link
        href="/about"
        class="inline-block px-6 py-3 bg-lime-500 text-white font-medium rounded hover:bg-lime-600 transition"
      >
        ← Retour
      </Link>
    </div>
  </section>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutNetwork.vue'

defineOptions({ layout: Layout })
</script>
