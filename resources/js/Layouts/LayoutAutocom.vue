<script>
import HelloWorld from '@/Pages/HelloWorldAnnuaire.vue'
import { Link, usePage } from '@inertiajs/vue3'
// Remove LayoutFilter import
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'Layout',
  components: {
    HelloWorld,
    Link
    // Remove LayoutFilter from components
  },
  // Remove data property for showFilter
  computed: {
    auth() {
      return usePage().props.auth
    },
    currentPath() {
      return new URL(usePage().url, window.location.origin).pathname
    }
  },
  methods: {
    linkClass(routeName) {
      const routePath = new URL(route(routeName), window.location.origin).pathname
      return [
        'px-4 py-2 rounded-full transition-colors duration-200',
        this.currentPath === routePath
          ? 'bg-green-600 text-white'
          : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-300'
      ]
    }
    // Remove openFilter, closeFilter, onFilter methods
  }
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-100">
    <header class="bg-gray-800 text-white py-4 px-6 text-center shadow-lg relative">
      <!-- User Actions -->
      <div v-if="auth.user && (auth.user.role === 'admin' || auth.user.role === 'user')" class="absolute top-4 right-4 flex items-center space-x-4">
        <img 
          class="w-10 h-10 rounded-full border-2 border-yellow-500 shadow-md object-cover" 
          :src="auth.user.avatar ? ('/storage/' + auth.user.avatar) : '/default-avatar.png'" 
          :alt="auth.user.name"
          :title="auth.user.name"
        />
        <Link :href="route('dashboard')" class="bg-yellow-700 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">Dashboard</Link>
        <Link :href="route('register')" class="bg-yellow-700 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">Register</Link>
        <Link href="/logout" method="post" as="button" class="bg-red-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">Logout</Link>
        <Link href="/Address" as="button" class="bg-green-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">Réseau</Link>
        <Link :href="route('Annuaire.index')" as="button" class="bg-green-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">Home</Link>
        <!-- Removed Filtrage link -->
      </div>

      <div v-else class="absolute top-4 right-4 flex items-center space-x-4">
        <Link href="/logout" method="post" as="button" class="bg-red-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">Logout</Link>
      </div>

      <HelloWorld msg="Autocom ☎️" />

      <!-- Navigation -->
      <nav class="mt-6 flex flex-wrap justify-center gap-2 sm:gap-4 text-sm font-medium">
  <Link
    v-if="auth.user?.role === 'admin'"
    :href="route('acheminements.swd')"
    :class="linkClass('acheminements.swd')"
  >
    Répartiteur SWD
  </Link>
  <Link
    v-if="auth.user?.role === 'admin'"
    :href="route('acheminements.adm')"
    :class="linkClass('acheminements.adm')"
  >
    Répartiteur ADM
  </Link>

  <Link
    v-if="auth.user?.role === 'admin'"
    :href="route('acheminements.divers')"
    :class="linkClass('acheminements.divers')"
  >
    Répartiteur DIVERS
  </Link>
  <Link
    v-if="auth.user?.role === 'admin'"
    :href="route('ptt.1er112')"
    :class="linkClass('ptt.1er112')"
  >
    Répartiteur PTT (1<sup>er</sup> 112)
  </Link>
  <Link
    v-if="auth.user?.role === 'admin'"
    :href="route('ptt.2er112')"
    :class="linkClass('ptt.2er112')"
  >
    Répartiteur PTT (2<sup>eme</sup> 112)
  </Link>
</nav>

      <!-- Remove Filtrage Modal -->
    </header>

    <main class="flex-1 p-6">
      <slot />
    </main>

    <footer class="bg-gray-900 text-gray-300 py-6 text-center">
      <p>&copy; 2018 DTN.</p>
    </footer>
  </div>
</template>

<script setup>

</script>
