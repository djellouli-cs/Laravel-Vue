<script setup>
import Layout from '@/Layouts/LayoutLogin.vue';
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Textinput from '../Components/Textinput.vue';

defineOptions({ layout: Layout });

const form = useForm({
  email: null,
  password: null,
  remember:null
});

const submit = () => {
  form.post(route('login'),{
    onError:()=>form.reset("password"),
  });
};
</script>

<template>
  <Head title="Login" />
  
  <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-6 mb-2">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
      <h1 class="text-2xl font-semibold text-center text-gray-700 mb-6">Login Account</h1>
      
      <form @submit.prevent="submit">
        <!-- Email -->

        <Textinput name="email" type="email" v-model="form.email" :message="form.errors.email"/>


        <!-- Password -->
        <Textinput name="password" type="password" v-model="form.password" :message="form.errors.password" />
<div class=" flex items-center justify-between">
    <div class=" flex items-center gap-2">
        <label for="remember">Remember me</label>
    <input type="checkbox" v-model="form.remember" id="remember">
    </div>

</div>

        

               <!-- Submit Button -->
        <div class="mt-6">
          <button type="submit" class="w-full py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Login
          </button>
        </div>
        <Link 
          :href="route('register')" 
          class="underline decoration-yellow-800 rounded-full p-1"
          active-class="bg-green-500 text-red-200"
        >
          Register
        </Link>
        <!-- Login Link -->
        <div class="mt-4 text-center text-sm text-gray-600">
        </div>
      </form>
    </div>
  </div>
</template>
