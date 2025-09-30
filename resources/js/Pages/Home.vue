<script setup>
import Layout from '@/Layouts/LayoutAnnuaire.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

defineOptions({ layout: Layout });

const props = defineProps({
  users: Object,
  searchTeam: String,
});

const search = ref(props.searchTeam);

// Format created_at date
const getDate = (date) =>
  new Date(date).toLocaleDateString("en-us", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });

// Search when typing (debounced)
watch(
  search,
  debounce((value) => {
    router.get('/home', { search: value }, { preserveState: true });
  }, 500)
);

// 🔥 Delete user
const deleteUser = (id) => {
  if (confirm("Are you sure you want to delete this user?")) {
    router.delete(`/users/${id}`, {
      preserveScroll: true,
    });
  }
};

// 🔄 Change role
const changeUserRole = (id, role) => {
  router.patch(`/users/${id}/role`, { role }, {
    preserveScroll: true,
  });
};
</script>
<template>
  <Head title="Utilisateurs" />

  <div>
    <div class="flex justify-end mb-4">
      <div class="w-1/4">
        <input
          type="search"
          placeholder="Search..."
          v-model="search"
          class="w-full border px-3 py-1 rounded"
        />
      </div>
    </div>

    <h1 class="text-2xl font-bold mb-4">Liste des utilisateurs</h1>

    <table class="min-w-full bg-white border border-gray-300 text-sm">
      <thead class="bg-gray-100 text-left">
        <tr>
          <th class="py-2 px-4 border-b">Avatar</th>
          <th class="py-2 px-4 border-b">Nom</th>
          <th class="py-2 px-4 border-b">Email</th>
          <th class="py-2 px-4 border-b">Date d'inscription</th>
          <th class="py-2 px-4 border-b">Role</th>
          <th class="py-2 px-4 border-b">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users.data" :key="user.id" class="text-center">
          <td class="py-2 px-4 border-b">
            <img
              :src="user.avatar ? '/storage/' + user.avatar : '/default-avatar.png'"
              alt="Avatar"
              class="w-10 h-10 rounded-full mx-auto"
            />
          </td>
          <td class="py-2 px-4 border-b">{{ user.name }}</td>
          <td class="py-2 px-4 border-b">{{ user.email }}</td>
          <td class="py-2 px-4 border-b">{{ getDate(user.created_at) }}</td>

          <!-- Role dropdown -->
          <td class="py-2 px-4 border-b">
            <select
              v-model="user.role"
              @change="changeUserRole(user.id, user.role)"
              class="border px-2 py-1 rounded"
            >
              <option value="user">user</option>
              <option value="admin">admin</option>
            </select>
          </td>

          <!-- Delete button -->
          <td class="py-2 px-4 border-b">
            <button
              @click="deleteUser(user.id)"
              class="text-red-500 hover:underline"
            >
              Supprimer
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4 flex items-center gap-2">
      <a
        v-for="link in users.links"
        :key="link.label"
        v-html="link.label"
        :href="link.url"
        class="px-2"
        :class="{
          'text-slate-300': !link.url,
          'text-blue-600 font-bold underline': link.active,
        }"
      ></a>
    </div>

    <p class="mt-2 text-sm text-gray-600">
      Affichage de {{ users.from }} à {{ users.to }} sur {{ users.total }} utilisateurs.
    </p>
  </div>
</template>
