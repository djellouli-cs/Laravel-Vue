<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">PTT (1er 112) Positions</h1>

    <div v-for="(regletteNumeros, reglette) in filteredPositions" :key="reglette" class="mb-4">
      <h2 class="font-semibold">{{ reglette }}</h2>
      <div class="grid grid-cols-10 gap-2 mt-2">
        <div v-for="numero in regletteNumeros" :key="numero.id" class="p-2 border rounded text-center">
          {{ numero.NDappel }}<br>
          <small>{{ numero.organisme?.name }}</small><br>
          <small>{{ numero.destination?.name }}</small>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Layout from "@/Layouts/LayoutAutocom.vue";

defineOptions({ layout: Layout });

const props = defineProps({
  numeros: Array
});

import { computed } from 'vue';

// Filter numeros whose Position starts with "PTT (1er 112)"
const filteredPositions = computed(() => {
  const positions = {};

  props.numeros
    .filter(n => n.Position?.startsWith("PTT (1er 112)"))
    .forEach(n => {
      const reglette = n.Position; // Use Position as reglette
      if (!positions[reglette]) positions[reglette] = [];
      positions[reglette].push(n);
    });

  return positions;
});
</script>
