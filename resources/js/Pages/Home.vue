<script setup>
import Layout from '@/Layouts/LayoutAnnuaire.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash';


defineOptions({ layout: Layout });
 const props=defineProps({
    users:Object,
    searchTeam:String,
});
//format date
const getDate=(date)=>
new Date(date).toLocaleDateString("en-us",{
    year:"numeric",
    month:"long",
    day:"numeric",
});
const search=ref(props.searchTeam);
watch(search,debounce((q)=>router.get('/user', {search:q},{preserveState:true}),500
));
</script>

<template>
    <Head title="| Utilisateurs" />
<div>
      <div class=" flex justify-end mp-4">
    <div class="w-1/4">
        <input type="search" placeholder="Search" v-model="search"/>
    </div>
  </div>
  <h1 class="text-2xl font-bold mb-4">Liste des utilisateurs</h1>

  <table class="min-w-full bg-white border border-gray-300">
    <thead class="bg-gray-100">
      <tr>
        <th class="py-2 px-4 border-b">Avatar</th>
        <th class="py-2 px-4 border-b">Nom</th>
        <th class="py-2 px-4 border-b">Email</th>
        <th class="py-2 px-4 border-b">Date d'inscription</th>
      </tr>
    </thead>
    <tbody>
      <!-- Example Row -->
      <tr class="text-center" v-for="user in users.data" :key="user.id">
        <td class="py-2 px-4 border-b">
          <img
          :src="user.avatar ? ('/storage/' + user.avatar) : '/default-avatar.png'" 
            alt="Avatar"
            class="w-10 h-10 rounded-full mx-auto"
          />
        </td>
        <td class="py-2 px-4 border-b">{{ user.name }}</td>
        <td class="py-2 px-4 border-b">{{ user.email }}</td>
        <td class="py-2 px-4 border-b">{{ getDate(user.created_at)}}</td>
      </tr>
      <!-- Copy and repeat <tr> for more users -->
    </tbody>
  </table>
  <div>
<a
  v-for="link in users.links"
  :key="link.label"
  v-html="link.label"
  :href="link.url"
  class="p-1 mx-1"
  :class="{ 'text-slate-300': !link.url, 'text-blue-300 font-medium':link.active }"
></a>
<p>affichage de {{ users.from }} to {{ users.to }} de {{ users.total }} résultats</p>
</div>

</div>





</template>
