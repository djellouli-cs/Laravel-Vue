<script setup>
import HelloWorld from '@/Pages/HelloWorldAnnuaire.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

// Reactive state
const showModal = ref(false)
const permanence = ref(null)
const buttonLabel = ref('Permanence')
const prochainCount = ref(0)
const hasProchainAlert = ref(false)

// Access Inertia props
const page = usePage()
const auth = page.props.auth

// Track current path reactively
const currentPath = ref(new URL(page.url, window.location.origin).pathname)
watch(
  () => page.url,
  (newUrl) => currentPath.value = new URL(newUrl, window.location.origin).pathname
)

// Function to get button class dynamically
const linkClass = (routeName) => {
  const routePath = new URL(route(routeName), window.location.origin).pathname
  return [
    'px-4 py-2 rounded-full text-sm sm:text-base font-medium transition-all duration-200',
    currentPath.value === routePath
      ? 'bg-green-600 text-white shadow'
      : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-300'
  ]
}

// Lifecycle: fetch permanence data
onMounted(async () => {
  try {
    const res = await axios.get('/api/permanence-this-week')
    permanence.value = res.data
    buttonLabel.value = res.data ? res.data.PSemaine : 'Permanence'

    const prochainRes = await axios.get('/api/prochain-permanences')
    prochainCount.value = prochainRes.data.count
    hasProchainAlert.value = prochainCount.value > 0
  } catch (error) {
    console.error('Error fetching permanence data:', error)
  }
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-100">
    <!-- Header -->
    <header class="bg-gray-800 text-white py-4 px-6 text-center shadow-lg relative">
      <!-- User Actions -->
      <div v-if="auth.user && auth.user.role === 'admin'" class="absolute top-4 right-4 flex items-center space-x-4">
        <img 
          class="w-10 h-10 rounded-full border-2 border-yellow-500 shadow-md object-cover" 
          :src="auth.user.avatar ? ('/storage/' + auth.user.avatar) : '/default-avatar.png'" 
          :alt="auth.user.name"
          :title="auth.user.name"
        />
        <Link :href="route('dashboard')" class="bg-yellow-700 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded-full">Dashboard</Link>
        <Link :href="route('register')" class="bg-yellow-700 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded-full">Register</Link>
        <Link href="/logout" method="post" as="button" class="bg-red-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full">Logout</Link>
        <Link href="/Address" as="button" class="bg-green-700 hover:bg-green-600 text-white font-semibold py-1 px-3 rounded-full">Réseau</Link>
        <Link :href="route('Annuaire.index')" as="button" class="bg-green-700 hover:bg-green-600 text-white font-semibold py-1 px-3 rounded-full">Home</Link>
      </div>

      <div v-else class="absolute top-4 right-4 flex items-center space-x-4">
        <Link href="/logout" method="post" as="button" class="bg-red-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full">Logout</Link>
      </div>

      <HelloWorld msg="Recherche 🔍" />

      <!-- Navigation -->
      <nav class="mt-6 flex flex-wrap justify-center gap-2 sm:gap-4 text-sm sm:text-base font-medium">
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('Searsh.index')" :class="[linkClass('Searsh.index'), 'bg-green-600 text-white hover:bg-green-700 font-semibold px-4 py-2 rounded']">🔎</Link>
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('Annuaire.filter')" :class="[linkClass('Annuaire.filter'), 'bg-blue-600 text-white hover:bg-blue-700 font-semibold px-4 py-2 rounded']">Filtrage</Link>
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('Annuaire.recherche')" :class="[linkClass('Annuaire.recherche'), 'bg-green-600 text-white hover:bg-green-700 font-semibold px-4 py-2 rounded']">Recherche</Link>
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('numero.manage')" :class="linkClass('numero.manage')">Edit Numero</Link>
      </nav>
    </header>

    <main class="flex-1 p-6">
      <slot />
    </main>

    <footer class="bg-gray-900 text-gray-300 py-6 text-center">
      <p>&copy; 2018 DTN.</p>
    </footer>
  </div>
</template>

<style scoped>
/* Modal styles if needed */
.modal-backdrop {
  position: fixed;
  top: 0; left: 0;
  width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.5);
  display: flex; align-items: center; justify-content: center;
  z-index: 1000;
}
.modal-dialog {
  min-width: 350px;
}
</style>