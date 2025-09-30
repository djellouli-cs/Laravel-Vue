<script setup>
import Layout from '@/Layouts/LayoutAnnuaire.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Textinput from '../Components/Textinput.vue';
import { onMounted, ref } from 'vue';

defineOptions({ layout: Layout });

// Destinations list passed from backend
const props = defineProps({
  destinations: Array,
});

const form = useForm({
  destination_id: null, // used to fetch name from Destination
  email: null,
  password: null,
  password_confirmation: null,
  avatar: null,
});

const change = (e) => {
  form.avatar = e.target.files[0];
};

const submit = () => {
  form.post(route('register'), {
    forceFormData: true, // important to upload files
  });
};
</script>
<template>
  <Head title="Register" />
  
  <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-6">
    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
      <h1 class="text-2xl font-semibold text-center text-gray-700 mb-6">Create a New Account</h1>
      
      <form @submit.prevent="submit" enctype="multipart/form-data">
        <!-- Avatar Upload -->
        <div class="mb-4">
          <label for="avatar" class="block text-gray-700 font-medium mb-1">Avatar (optional)</label>
          <input 
            type="file" 
            id="avatar" 
            @input="change" 
            class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4
                   file:rounded-full file:border-0
                   file:text-sm file:font-semibold
                   file:bg-blue-50 file:text-blue-700
                   hover:file:bg-blue-100"
          />
          <p v-if="form.errors.avatar" class="text-red-500 text-sm mt-1">{{ form.errors.avatar }}</p>
        </div>

        <!-- Destination Select -->
        <div class="mb-4">
          <label for="destination_id" class="block text-gray-700 font-medium mb-1">Select Destination</label>
          <select
            id="destination_id"
            v-model="form.destination_id"
            class="w-full border rounded px-3 py-2"
          >
            <option value="">-- Select a destination --</option>
            <option 
              v-for="dest in destinations" 
              :key="dest.id" 
              :value="dest.id"
            >
              {{ dest.name }}
            </option>
          </select>
          <p v-if="form.errors.destination_id" class="text-red-500 text-sm mt-1">{{ form.errors.destination_id }}</p>
        </div>

        <!-- Email -->
        <Textinput 
          name="email" 
          type="email" 
          v-model="form.email" 
          :message="form.errors.email"
        />

        <!-- Password -->
        <Textinput 
          name="password" 
          type="password" 
          v-model="form.password" 
          :message="form.errors.password"
        />

        <!-- Confirm Password -->
        <Textinput 
          name="password_confirmation" 
          type="password" 
          v-model="form.password_confirmation"
        />

        <!-- Submit Button -->
        <div class="mt-6">
          <button 
            type="submit" 
            class="w-full py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            Register
          </button>
        </div>
        
        <!-- Login Link -->
        <div class="mt-4 text-center text-sm text-gray-600">
          <p>
            Already a user? 
            <a :href="route('login')" class="text-blue-600 hover:text-blue-800 font-medium">
              Login here
            </a>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>
