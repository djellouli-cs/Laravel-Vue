<template>
  <div class="p-6 min-h-screen bg-gradient-to-br from-blue-50 to-white">
    <h1 class="text-3xl font-extrabold mb-6 text-blue-800">
      REPARTITEUR PTT (2eme 112)
    </h1>

    <!-- ✅ SELECT REGLETTE -->
    <div class="mb-6">
      <label class="font-bold mr-2">Choose Reglette:</label>
      <select v-model="selectedReglette" class="border px-3 py-2 rounded">
        <option value="">-- Select Reglette --</option>
        <option v-for="r in regletteNumbers" :key="r" :value="r">
          Reglette {{ r }}
        </option>
      </select>
    </div>

    <div v-if="loading" class="text-center py-6 text-blue-700 font-semibold text-xl">
      Loading...
    </div>

    <div v-else>

      <!-- ✅ SHOW ONLY SELECTED REGLETTE -->
      <div v-if="selectedReglette && reglettesMap[selectedReglette]">
        <h2 class="text-xl font-bold mb-2 text-blue-700">
          Reglette {{ selectedReglette }}
        </h2>

        <table class="reglette">
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
                    @dblclick="goToAnnuaire(reglettesMap[selectedReglette][pos].numero?.NDappel)"
                    class="jack-number"
                  >
                    {{ reglettesMap[selectedReglette][pos].acheminement }}
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

<script>
import Layout from "@/Layouts/LayoutAutocom.vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";

export default {
  layout: Layout,
  props: {
    acheminements: Array,
  },
  data() {
    return {
      loading: true,
      selectedReglette: "",
      tooltip: { visible: false, text: "", x: 0, y: 0 },
    };
  },

  computed: {
    reglettesMap() {
      const map = {};

      this.acheminements
        .filter(a => a.acheminement.startsWith("PTT (2eme 112)"))
        .forEach(a => {
          const num = parseInt(a.acheminement.replace("PTT (2eme 112) ", ""));

          const reglette = Math.ceil(num / 10);
          const position = num % 10 === 0 ? 10 : num % 10;

          if (!map[reglette]) map[reglette] = {};
          map[reglette][position] = a;
        });

      return map;
    },

    // ✅ list of available reglettes for select
    regletteNumbers() {
      return Object.keys(this.reglettesMap).map(Number).sort((a, b) => a - b);
    }
  },

  methods: {
    getDestination(a) {
      if (!a) return "No data";
      let html = [];
      if (a.numero?.Position) html.push(`📍 ${a.numero.Position}`);
      if (a.numero?.destination) html.push(`🏛 ${a.numero.destination.name}`);
      if (a.numero?.organisme) html.push(`🏢 ${a.numero.organisme.name}`);
      if (a.description) html.push(`ℹ️ ${a.description}`);
      return html.join("<br>");
    },
    showTooltip(e, text) {
      this.tooltip = { visible: true, text, x: e.pageX + 15, y: e.pageY + 15 };
    },
    hideTooltip() {
      this.tooltip.visible = false;
    },
    goToAnnuaire(ndappel) {
      if (!ndappel) return;
      router.visit(route("Annuaire.index", { ndappel }));
    },
  },

  mounted() {
    setTimeout(() => {
      this.loading = false;
    }, 300);
  }
};
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
}
.jack-number {
  background: #ffcc00;
  padding: 4px 6px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}
.custom-tooltip {
  position: absolute;
  background: black;
  color: white;
  padding: 10px;
  border-radius: 6px;
  z-index: 999;
}
</style>
