<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import Layout from '@/Layouts/LayoutAnnuaire.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  user: Object,
})

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submit = () => {
  form.put(route('profile.password.update'))
}
</script>

<template>
  <div class="max-w-2xl mx-auto p-8 bg-white rounded-2xl shadow-md mt-10">
    <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">Profile</h1>

    <!-- User Info -->
    <div class="mb-8 border-b pb-6">
      <p class="text-gray-700"><strong>Name:</strong> {{ user.name }}</p>
      <p class="text-gray-700"><strong>Email:</strong> {{ user.email }}</p>
    </div>

    <!-- Change Password -->
    <h2 class="text-2xl font-semibold mb-4 text-gray-800">Change Password</h2>

    <form @submit.prevent="submit" class="space-y-6">
      <!-- Current Password -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
        <input
          type="password"
          v-model="form.current_password"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-green-400 outline-none transition"
          placeholder="Enter current password"
        />
        <div v-if="form.errors.current_password" class="text-red-500 text-sm mt-1">
          {{ form.errors.current_password }}
        </div>
      </div>

      <!-- New Password -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
        <input
          type="password"
          v-model="form.password"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-green-400 outline-none transition"
          placeholder="Enter new password"
        />
        <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
          {{ form.errors.password }}
        </div>
      </div>

      <!-- Confirm Password -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input
          type="password"
          v-model="form.password_confirmation"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-400 focus:border-green-400 outline-none transition"
          placeholder="Confirm new password"
        />
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg shadow transition disabled:opacity-50"
        :disabled="form.processing"
      >
        Change Password
      </button>

      <!-- Success message -->
      <div v-if="$page.props.flash.success" class="text-green-600 text-center mt-4 font-medium">
        {{ $page.props.flash.success }}
      </div>
    </form>
  </div>
</template>
