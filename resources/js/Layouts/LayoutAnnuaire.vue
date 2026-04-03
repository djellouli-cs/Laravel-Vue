<script setup>
import { ref, onMounted, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import HelloWorld from '@/Pages/HelloWorldAnnuaire.vue'

// -----------------------------
// Reactive state
// -----------------------------
const showModal = ref(false)
const permanence = ref(null)
const buttonLabel = ref('Permanence')
const prochainCount = ref(0)
const hasProchainAlert = ref(false)
const loadingPermanence = ref(true) // track loading state

// -----------------------------
// Access Inertia props
// -----------------------------
const page = usePage()
const auth = page.props.auth

// Track current path for dynamic nav highlighting
const currentPath = ref(new URL(page.url, window.location.origin).pathname)
watch(
  () => page.url,
  (newUrl) => currentPath.value = new URL(newUrl, window.location.origin).pathname
)

// -----------------------------
// Dynamic link classes
// -----------------------------
const linkClass = (routeName) => {
  const routePath = new URL(route(routeName), window.location.origin).pathname
  return [
    'px-4 py-2 rounded-full text-sm sm:text-base font-medium transition-all duration-200',
    currentPath.value === routePath
      ? 'bg-green-600 text-white shadow'
      : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-300'
  ]
}

// -----------------------------
// Safe data fetching functions
// -----------------------------
const fetchPermanence = async () => {
  try {
    const res = await axios.get('/permanence-this-week')
    permanence.value = res.data || null
    buttonLabel.value = res.data?.PSemaine || 'Permanence'
  } catch (err) {
    console.warn('Could not fetch this-week permanence:', err)
    permanence.value = null
    buttonLabel.value = 'Permanence'
  }
}

const fetchProchainCount = async () => {
  try {
    const res = await axios.get('/prochain-permanences')
    prochainCount.value = res.data?.count || 0
    hasProchainAlert.value = prochainCount.value > 0
  } catch (err) {
    console.warn('Could not fetch prochain-permanences:', err)
    prochainCount.value = 0
    hasProchainAlert.value = false
  }
}

// -----------------------------
// Fetch data on mount
// -----------------------------
onMounted(async () => {
  loadingPermanence.value = true
  await Promise.all([fetchPermanence(), fetchProchainCount()])
  loadingPermanence.value = false
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-100">
    <!-- Header -->
    <header class="bg-gray-800 text-white py-4 px-6 text-center shadow-lg relative">
      <!-- Admin user actions -->
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

      <!-- Regular user actions -->
      <div v-else class="absolute top-4 right-4 flex items-center space-x-4">
        <Link href="/logout" method="post" as="button" class="bg-red-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full">Logout</Link>
      </div>

      <!-- Page title -->
      <HelloWorld msg="Annuaire ☎️" />

      <!-- Navigation bar -->
      <nav class="mt-6 flex flex-wrap justify-center gap-2 sm:gap-4 text-sm sm:text-base font-medium">
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('profile.show')" :class="linkClass('profile.show')">Profile</Link>
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('home')" :class="linkClass('home')">Utilisateurs</Link>
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('numero.manage')" :class="linkClass('numero.manage')">Edit Numero</Link>
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('Searsh.index')" :class="[linkClass('Searsh.index'), 'bg-green-600 text-white hover:bg-green-700 font-semibold px-4 py-2 rounded']">Recherche</Link>
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('facture')" :class="[linkClass('facture'), 'bg-green-600 text-white hover:bg-green-700 font-semibold px-4 py-2 rounded']">Facture</Link>
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('acheminements.swd')" :class="[linkClass('acheminements.swd'), 'bg-green-600 text-white hover:bg-green-700 font-semibold px-4 py-2 rounded']">Autocom</Link>
        <Link v-if="auth.user && auth.user.role === 'admin'" :href="route('permanences.index')" :class="[linkClass('permanences.index'), 'bg-green-600 text-white hover:bg-green-700 font-semibold px-4 py-2 rounded']">Permanences</Link>

        <!-- Permanence Button with Alert -->
        <Link v-if="auth.user && auth.user.role === 'admin'" 
              :href="route('permanences.this-week')" 
              :class="[linkClass('permanences.this-week'), prochainCount > 0 ? 'bg-green-600 text-white px-4 py-2 rounded relative' : 'bg-red-600 text-white px-4 py-2 rounded relative']">
          P
          <span v-if="hasProchainAlert" 
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center animate-pulse" 
                :title="`${prochainCount} permanence(s) PROCHAIN détectée(s)`">{{ prochainCount > 9 ? '9+' : prochainCount }}</span>
        </Link>
      </nav>
    </header>

    <!-- Page Content -->
    <main class="flex-1 p-6">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-6 text-center">
      <p>&copy; 2018 DTN.</p>
    </footer>
  </div>
</template>

<style scoped>
/* Modal backdrop (if used) */
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