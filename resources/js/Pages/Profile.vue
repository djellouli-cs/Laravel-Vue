<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Layout from '@/Layouts/LayoutAnnuaire.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  user: Object,
})

/* ================= PASSWORD ================= */
const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
})

const submitPassword = () => {
  passwordForm.put(route('profile.password.update'))
}

/* ================= EMAIL ================= */
const emailForm = useForm({
  email: props.user.email,
  current_password: '',
})

const submitEmail = () => {
  emailForm.put(route('profile.email.update'))
}

/* ================= AVATAR ================= */
const avatarPreview = ref(props.user.avatar_url)

const avatarForm = useForm({
  avatar: null,
})

const changeAvatar = (e) => {
  const file = e.target.files[0]
  if (!file) return

  avatarPreview.value = URL.createObjectURL(file)
  avatarForm.avatar = file
}

const submitAvatar = () => {
  avatarForm.post(route('profile.avatar.update'), {
    forceFormData: true,
  })
}
</script>
<template>
  <div class="max-w-2xl mx-auto p-8 bg-white rounded-2xl shadow-md mt-10">
    <h1 class="text-3xl font-bold mb-8 text-center text-gray-800">
      Profile
    </h1>

    <!-- ================= AVATAR ================= -->
    <div class="flex flex-col items-center mb-10">
      <img
        :src="avatarPreview"
        class="w-28 h-28 rounded-full object-cover border-4 border-green-400 shadow"
        alt="Avatar"
      />

      <label
        class="mt-4 cursor-pointer bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm font-medium"
      >
        Change avatar
        <input
          type="file"
          accept="image/*"
          class="hidden"
          @change="changeAvatar"
        />
      </label>

      <button
        v-if="avatarForm.avatar"
        @click="submitAvatar"
        class="mt-3 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm"
        :disabled="avatarForm.processing"
      >
        Save avatar
      </button>

      <p v-if="avatarForm.errors.avatar" class="text-red-500 text-sm mt-2">
        {{ avatarForm.errors.avatar }}
      </p>
    </div>

    <!-- ================= USER INFO ================= -->
    <div class="mb-10 border-b pb-6">
      <p class="text-gray-700"><strong>Name:</strong> {{ user.name }}</p>
      <p class="text-gray-700"><strong>Email:</strong> {{ user.email }}</p>
    </div>

    <!-- ================= EMAIL ================= -->
    <h2 class="text-2xl font-semibold mb-4 text-gray-800">
      Change Email
    </h2>

    <form @submit.prevent="submitEmail" class="space-y-6 mb-10">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          New Email
        </label>
        <input
          type="email"
          v-model="emailForm.email"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        />
        <p v-if="emailForm.errors.email" class="text-red-500 text-sm mt-1">
          {{ emailForm.errors.email }}
        </p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Current Password
        </label>
        <input
          type="password"
          v-model="emailForm.current_password"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        />
        <p
          v-if="emailForm.errors.current_password"
          class="text-red-500 text-sm mt-1"
        >
          {{ emailForm.errors.current_password }}
        </p>
      </div>

      <button
        type="submit"
        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg"
        :disabled="emailForm.processing"
      >
        Change Email
      </button>
    </form>

    <!-- ================= PASSWORD ================= -->
    <h2 class="text-2xl font-semibold mb-4 text-gray-800">
      Change Password
    </h2>

    <form @submit.prevent="submitPassword" class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Current Password
        </label>
        <input
          type="password"
          v-model="passwordForm.current_password"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        />
        <p
          v-if="passwordForm.errors.current_password"
          class="text-red-500 text-sm mt-1"
        >
          {{ passwordForm.errors.current_password }}
        </p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          New Password
        </label>
        <input
          type="password"
          v-model="passwordForm.password"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        />
        <p v-if="passwordForm.errors.password" class="text-red-500 text-sm mt-1">
          {{ passwordForm.errors.password }}
        </p>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Confirm Password
        </label>
        <input
          type="password"
          v-model="passwordForm.password_confirmation"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg"
        />
      </div>

      <button
        type="submit"
        class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg"
        :disabled="passwordForm.processing"
      >
        Change Password
      </button>
    </form>
  </div>
</template>
