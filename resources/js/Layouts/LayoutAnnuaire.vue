<script setup>
import HelloWorld from '@/Pages/HelloWorldAnnuaire.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
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
const currentPath = new URL(page.url, window.location.origin).pathname

// Methods
const linkClass = (routeName) => {
  const routePath = new URL(route(routeName), window.location.origin).pathname
  return [
    'px-4 py-2 rounded-full transition-colors duration-200',
    currentPath === routePath
      ? 'bg-green-600 text-white'
      : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-300'
  ]
}

// Lifecycle
onMounted(async () => {
  try {
    // Get current week permanence
    const res = await axios.get('/api/permanence-this-week')
    permanence.value = res.data
    buttonLabel.value = res.data ? res.data.PSemaine : 'Permanence'
    
    // Check for PROCHAIN permanences
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
    <header class="bg-gray-800 text-white py-4 px-6 text-center shadow-lg relative">
      <!-- User Actions -->
      <div v-if="auth.user && auth.user.role === 'admin'" class="absolute top-4 right-4 flex items-center space-x-4">
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
      </div>

      <div v-else class="absolute top-4 right-4 flex items-center space-x-4">
        <Link href="/logout" method="post" as="button" class="bg-red-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">Logout</Link>
      </div>

      <HelloWorld msg="Annuaire ☎️" />

      <!-- Navigation -->
      <nav class="mt-6 flex flex-wrap justify-center gap-2 sm:gap-4 text-sm font-medium">
        <Link
          v-if="auth.user && auth.user.role === 'admin'"
          :href="route('profile.show')"
          :class="linkClass('profile.show')"
        >
          Profile
        </Link>
        <Link
          v-if="auth.user && auth.user.role === 'admin'"
          :href="route('home')"
          :class="linkClass('home')"
        >
          Utilisateurs
        </Link>

        <Link
          v-if="auth.user && auth.user.role === 'admin'"
          :href="route('numero.manage')"
          :class="linkClass('numero.manage')"
        >
          Edit Numero
        </Link>

        <Link
          v-if="auth.user && auth.user.role === 'admin'"
          :href="route('Annuaire.filter')"
          :class="[linkClass('Annuaire.filter'), 'bg-blue-600 text-white hover:bg-blue-700 font-semibold px-4 py-2 rounded transition-all duration-200']"
        >
          Filtrage
        </Link>
        <Link
          v-if="auth.user && auth.user.role === 'admin'"
          :href="route('Annuaire.recherche')"
          :class="[linkClass('Annuaire.recherche'), 'bg-green-600 text-white hover:bg-green-700 font-semibold px-4 py-2 rounded transition-all duration-200']"
        >
          Recherche
        </Link>
        <Link
          v-if="auth.user && auth.user.role === 'admin'"
          :href="route('permanences.index')"
          :class="[linkClass('permanences.index'), 'bg-green-600 text-white hover:bg-green-700 font-semibold px-4 py-2 rounded transition-all duration-200']"
        >
          Permanences
        </Link>
        <Link
          v-if="auth.user && auth.user.role === 'admin'"
          :href="route('acheminements.swd')"
          :class="[linkClass('acheminements.swd'), 'bg-green-600 text-white hover:bg-green-700 font-semibold px-4 py-2 rounded transition-all duration-200']"
        >
          Autocom
        </Link>
        <Link
          v-if="auth.user && auth.user.role === 'admin'"
          :href="route('permanences.this-week')"
          :class="[
            linkClass('permanences.this-week'), 
            prochainCount > 0 
              ? 'bg-green-600 text-white hover:bg-green-700 font-semibold px-4 py-2 rounded transition-all duration-200 relative'
              : 'bg-red-600 text-white hover:bg-red-700 font-semibold px-4 py-2 rounded transition-all duration-200 relative'
          ]"
        >
          P
          <!-- Alert indicator for PROCHAIN permanences -->
          <span 
            v-if="hasProchainAlert"
            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center animate-pulse"
            :title="`${prochainCount} permanence(s) PROCHAIN détectée(s)`"
          >
            {{ prochainCount > 9 ? '9+' : prochainCount }}
          </span>
        </Link>
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
.modal-backdrop {
  position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
  background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center;
  z-index: 1000;
}
.modal-dialog {
  min-width: 350px;
}
</style>
