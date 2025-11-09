<script setup>
import Layout from '@/Layouts/LayoutLogin.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref } from 'vue';

defineOptions({ layout: Layout });

const form = useForm({
  email: null,
  password: null,
  remember: null
});

const isLoading = ref(false);
const showPassword = ref(false);

const submit = () => {
  isLoading.value = true;

  form.post(route('login'), {
    onError: () => {
      form.reset("password");
      isLoading.value = false;
    },
    onFinish: () => {
      isLoading.value = false;
    }
  });
};
</script>

<template>
  <Head title="Connexion" />

  <transition appear enter-active-class="transition-opacity duration-700" enter-from-class="opacity-0" enter-to-class="opacity-100">

    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center py-12 px-6">
      <div class="w-full max-w-md bg-white dark:bg-gray-800 p-8 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 transition-all hover:scale-[1.02]">

        <h1 class="text-2xl font-bold text-center text-gray-800 dark:text-gray-100 mb-6">🔐 Connexion au compte</h1>

        <form @submit.prevent="submit" class="space-y-4">

          <!-- Email -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
            <input
              type="email"
              v-model="form.email"
              class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Password -->
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mot de passe</label>
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password"
              class="w-full px-3 py-2 border rounded-lg bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600 focus:ring-2 focus:ring-blue-500"
            />

            <!-- Eye toggle -->
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-9 text-gray-500 hover:text-blue-500"
            >
              {{ showPassword ? "🙈" : "👁️" }}
            </button>
          </div>

          <!-- Remember me -->
          <div class="flex items-center gap-2">
            <input type="checkbox" v-model="form.remember" class="h-4 w-4">
            <label class="text-sm text-gray-600 dark:text-gray-300">Se souvenir de moi</label>
          </div>

          <!-- Submit Button with Loader -->
          <button
            type="submit"
            :disabled="isLoading"
            class="w-full flex justify-center items-center gap-2 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-all disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <!-- Loader -->
            <span v-if="isLoading" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>

            <!-- Text -->
            {{ isLoading ? "Connexion..." : "Se connecter" }}
          </button>

          <!-- Register Link -->
          <div class="text-center">
            <Link :href="route('register')" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
              Créer un nouveau compte
            </Link>
          </div>

        </form>
      </div>
    </div>

  </transition>
</template>
