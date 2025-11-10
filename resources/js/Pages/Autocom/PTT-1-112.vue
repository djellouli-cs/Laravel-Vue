<template>
  <div class="p-6 min-h-screen bg-gradient-to-br from-yellow-50 to-white">
    <h1 class="text-3xl font-extrabold mb-6 text-yellow-800">
      REPARTITEUR PTT (1er 112)
    </h1>

    <!-- NDappel input + SELECT REGLETTE -->
    <div class="mb-6 flex gap-4 flex-wrap">
      <!-- NDappel Input -->
      <input
        v-model="searchNDappel"
        @input="goToNDappelReglette"
        type="text"
        placeholder="Enter NDappel..."
        class="px-4 py-2 border rounded-lg"
      />

      <!-- Reglette Select -->
      <div>
        <label class="font-bold mr-2">Choose Reglette:</label>
        <select v-model="selectedReglette" class="border px-3 py-2 rounded">
          <option value="">-- Select Reglette --</option>
          <option v-for="r in regletteNumbers" :key="r" :value="r">
            Reglette {{ r }}
          </option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="text-center py-6 text-yellow-700 font-semibold text-xl">
      Loading...
    </div>

    <div v-else>
      <!-- SHOW ONLY SELECTED REGLETTE -->
      <div v-if="selectedReglette && reglettesMap[selectedReglette]">
        <h2 class="text-xl font-bold mb-2 text-yellow-700">
          Reglette {{ selectedReglette }}
        </h2>

        <table class="reglette w-full shadow-lg">
          <thead>
            <tr>
              <th v-for="n in 10" :key="n">{{ n }}</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td v-for="pos in 10" :key="pos" class="slot">
                <div v-if="reglettesMap[selectedReglette][pos]">
                  <span
                    @mouseenter="showTooltip($event, getDestination(reglettesMap[selectedReglette][pos]))"
                    @mouseleave="hideTooltip"
                    @dblclick="goToAnnuaire(reglettesMap[selectedReglette][pos].NDappel)"
                    class="jack-number"
                  >
                    {{ reglettesMap[selectedReglette][pos].NDappel }}
                  </span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Tooltip -->
    <div
      v-if="tooltip.visible"
      class="custom-tooltip"
      :style="{ top: tooltip.y + 'px', left: tooltip.x + 'px' }"
      v-html="tooltip.text"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";
import Layout from "@/Layouts/LayoutAutocom.vue";

defineOptions({ layout: Layout });

const props = defineProps({ numeros: Array });

const loading = ref(true);
const selectedReglette = ref("");
const searchNDappel = ref(""); // NDappel input
const tooltip = ref({ visible: false, text: "", x: 0, y: 0 });

// Map numeros to reglettes
const reglettesMap = computed(() => {
  const map = {};
  props.numeros
    .filter(n => n.Position?.startsWith("PTT (1er 112)"))
    .forEach(n => {
      const num = parseInt(n.Position.replace("PTT (1er 112) ", ""));
      const reglette = Math.ceil(num / 10);
      const position = num % 10 === 0 ? 10 : num % 10;

      if (!map[reglette]) map[reglette] = {};
      map[reglette][position] = n;
    });
  return map;
});

const regletteNumbers = computed(() =>
  Object.keys(reglettesMap.value).map(Number).sort((a, b) => a - b)
);

function getDestination(n) {
  if (!n) return "No data";
  let html = [];
  if (n.organisme) html.push(`🏢 ${n.organisme.name}`);
  if (n.destination) html.push(`🏛 ${n.destination.name}`);
  if (n.description) html.push(`ℹ️ ${n.description}`);
  return html.join("<br>");
}

function showTooltip(e, text) {
  tooltip.value = { visible: true, text, x: e.pageX + 15, y: e.pageY + 15 };
}

function hideTooltip() {
  tooltip.value.visible = false;
}

function goToAnnuaire(ndappel) {
  if (!ndappel) return;
  router.visit(route("Annuaire.index", { ndappel }));
}

// Navigate to reglette based on NDappel
function goToNDappelReglette() {
  if (!searchNDappel.value) return;

  const match = props.numeros.find(n => n.NDappel === searchNDappel.value && n.Position?.startsWith("PTT (1er 112)"));
  if (match) {
    const num = parseInt(match.Position.replace("PTT (1er 112) ", ""));
    selectedReglette.value = Math.ceil(num / 10);
  }
}

onMounted(() => {
  setTimeout(() => (loading.value = false), 300);
});
</script>

<style scoped>
.reglette {
  border-collapse: separate;
  border-spacing: 4px;
  background: #333;
  padding: 8px;
  border-radius: 8px;
}
.reglette th {
  background: #222;
  color: #fff;
  padding: 6px;
  border-radius: 4px;
}
.slot {
  background: #555;
  border-radius: 6px;
  height: 50px;
  text-align: center;
  transition: transform 0.2s;
}
.slot:hover {
  transform: scale(1.05);
}
.jack-number {
  background: #ffcc00;
  padding: 4px 6px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background 0.2s, transform 0.2s;
}
.jack-number:hover {
  background: #ffc700;
  transform: scale(1.1);
}
.custom-tooltip {
  position: absolute;
  background: black;
  color: white;
  padding: 10px;
  border-radius: 6px;
  z-index: 999;
  pointer-events: none;
}
</style>
