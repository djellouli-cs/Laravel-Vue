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

  <transition
    appear
    enter-active-class="transition ease-out duration-500"
    enter-from-class="opacity-0 translate-y-4"
    enter-to-class="opacity-100 translate-y-0"
  >
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 px-6">

      <div class="w-full max-w-md bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl p-8">

        <h1 class="text-2xl font-bold text-center text-gray-800 mb-1">
          Connexion
        </h1>
        <p class="text-center text-sm text-gray-500 mb-6">
          Connectez-vous à votre compte
        </p>

        <form @submit.prevent="submit" class="space-y-5">

          <!-- EMAIL -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Email
            </label>

            <input
              type="email"
              v-model="form.email"
              required
              autofocus
              class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
            />

            <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">
              {{ form.errors.email }}
            </p>
          </div>

          <!-- PASSWORD -->
          <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Mot de passe
            </label>

            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password"
              required
              class="w-full px-4 py-2.5 pr-12 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
            />

            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-blue-600"
            >
              {{ showPassword ? "🙈" : "👁️" }}
            </button>

            <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">
              {{ form.errors.password }}
            </p>
          </div>

          <!-- REMEMBER -->
          <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
              <input
                type="checkbox"
                v-model="form.remember"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              />
              Se souvenir de moi
            </label>

            
          </div>

          <!-- BUTTON -->
          <button
            type="submit"
            :disabled="isLoading"
            class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition disabled:opacity-60"
          >
            <span
              v-if="isLoading"
              class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"
            ></span>

            {{ isLoading ? "Connexion..." : "Se connecter" }}
          </button>

          <!-- REGISTER -->
          <p class="text-center text-sm text-gray-600">
            Pas encore inscrit ?
            <Link
              :href="route('register')"
              class="text-blue-600 font-medium hover:underline"
            >
              Créer un compte
            </Link>
          </p>

        </form>
      </div>
    </div>
  </transition>
</template>