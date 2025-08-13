<script setup>
import Layout from '@/Layouts/LayoutNetwork.vue';
import { Head } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash';


defineOptions({ layout: Layout });
 const props=defineProps({
    plages:Object,
    searchTeam:String,
});

const search=ref(props.searchTeam);
watch(search,debounce((q)=>router.get('/plageTable', {search:q},{preserveState:true}),500
));
</script>

<template>
    <Head title="| PlageTable" />
<Link
  :href="route('plage.range.form')"
  class="underline decoration-yellow-800 rounded-full px-3 py-1 hover:bg-yellow-900"
  active-class="bg-green-500 text-red-200"
>
  Edit Range
</Link>

<div>
      <div class=" flex justify-end mp-4">
    <div class="w-1/4">
        <input type="search" placeholder="Search" v-model="search"/>
    </div>
  </div>
  <h1 class="text-2xl font-bold mb-4">PLage List</h1>

  <table class="min-w-full bg-white border border-gray-300">
    <thead class="bg-gray-100">
      <tr>
        <th class="py-2 px-4 border-b">Ip</th>
        <th class="py-2 px-4 border-b">Direction</th>
        
      </tr>
    </thead>
    <tbody>
      <!-- Example Row -->
      <tr class="text-center" v-for="plage in plages.data" :key="plage.id">
        
        <td class="py-2 px-4 border-b">{{ plage.ipAdresses }}</td>
        <td class="py-2 px-4 border-b">{{ plage.direction }}</td>
      </tr>
      <!-- Copy and repeat <tr> for more users -->
    </tbody>
  </table>
  <div>
<a
  v-for="link in plages.links"
  :key="link.label"
  v-html="link.label"
  :href="link.url"
  class="p-1 mx-1"
  :class="{ 'text-slate-300': !link.url, 'text-blue-300 font-medium':link.active }"
></a>
<p class="mt-4 text-sm text-gray-600">
  Showing <span class="font-medium text-gray-800">{{ plages.from }}</span>
  to <span class="font-medium text-gray-800">{{ plages.to }}</span>
  of <span class="font-medium text-gray-800">{{ plages.total }}</span> results
</p>
</div>

</div>



   


</template>
