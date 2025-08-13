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
        <Link :href="route('dashboard')" class="bg-yellow-700 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">
          Dashboard
        </Link>
        <Link :href="route('register')" class="bg-yellow-700 hover:bg-yellow-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">
          Register
        </Link>
        <Link href="/logout" method="post" as="button" class="bg-red-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">
          Logout
        </Link>
        <Link href="/Address" as="button" class="bg-green-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">
          Address
        </Link>
        <Link href="/Annuaire" as="button" class="bg-green-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">
          Home
        </Link>
      </div>

      <div v-else class="absolute top-4 right-4 flex items-center space-x-4">
        <Link href="/logout" method="post" as="button" class="bg-red-700 hover:bg-red-600 text-white font-semibold py-1 px-3 rounded-full transition-all duration-200">
          Logout
        </Link>
      </div>

      <HelloWorld msg="✎ Edit" />

      <!-- Navigation -->
      <nav class="mt-6 flex flex-wrap justify-center gap-2 sm:gap-4 text-sm font-medium">
        <Link v-if="auth.user.role === 'admin'" :href="route('technologies')" :class="linkClass('technologies')">Technologies</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('classe.manage')" :class="linkClass('classe.manage')">Classe</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('type.manage')" :class="linkClass('type.manage')">Type</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('Post.manage')" :class="linkClass('Post.manage')">Post</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('facture.manage')" :class="linkClass('facture.manage')">Facture</Link>
        <Link
        v-if="auth.user.role === 'admin'"
          :href="route('adsl.index')"
          :class="[linkClass('adsl.index'), 'bg-purple-600 text-white hover:bg-purple-700 font-semibold px-4 py-2 rounded transition-all duration-200']"
        >
          ADSL
        </Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('reserve.manage')" :class="linkClass('reserve.manage')">Reserve</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('matricule.manage')" :class="linkClass('matricule.manage')">Matricule</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('note.manage')" :class="linkClass('note.manage')">Note</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('organisme.manage')" :class="linkClass('organisme.manage')">Organisme</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('groupe.manage')" :class="linkClass('groupe.manage')">Groupe</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('destination.manage')" :class="linkClass('destination.manage')">Destination</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('numero.service')" :class="linkClass('numero.service')">Service</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('acheminement.manage')" :class="linkClass('acheminement.manage')">Acheminement</Link>
        <Link v-if="auth.user.role === 'admin'" :href="route('numero.manage')" :class="linkClass('numero.manage')">Numeros</Link>
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

<script>
import HelloWorld from '@/Pages/HelloWorldAnnuaire.vue'
import { Link, usePage } from '@inertiajs/vue3'

export default {
  name: 'Layout',
  components: {
    HelloWorld,
    Link
  },
  computed: {
    auth() {
      return usePage().props.auth
    },
    currentPath() {
      // Ensure we're always comparing pathname only
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
  }
}
</script>

<style scoped>
.avatar {
  transition: transform 0.2s ease;
}
.avatar:hover {
  transform: scale(1.05);
}
</style>
