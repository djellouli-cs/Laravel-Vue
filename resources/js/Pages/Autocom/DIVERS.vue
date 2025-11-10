<template>
  <div class="p-6 min-h-screen bg-gradient-to-br from-blue-50 to-white">
    <h1 class="text-3xl font-extrabold mb-6 text-blue-800">REPARTITEUR DIVERS</h1>

    <!-- Filters -->
    <div class="flex gap-4 mb-6 flex-wrap">
      <!-- NDappel Input -->
      <input
        v-model="searchNDappel"
        @input="goToNDappelReglette"
        type="text"
        placeholder="Enter NDappel..."
        class="px-4 py-2 border rounded-lg text-lg"
      />

      <!-- DIVERS Filter -->
      <select v-model="searchNumber" @change="loadDIVERSData" class="px-4 py-2 border rounded-lg text-lg">
        <option value="" disabled selected>REGLETTE DIVERS</option>
        <option v-for="num in availableDIVERSs" :key="num" :value="num">
          DIVERS {{ num }}
        </option>
      </select>

      <!-- Technologie Filter -->
      <select v-model="selectedTechnologie" @change="onTechnologieChange" class="px-4 py-2 border rounded-lg text-lg">
        <option value="">All Technologies</option>
        <option v-for="tech in technologies" :key="tech.id" :value="tech.id">
          {{ tech.name }}
        </option>
      </select>
    </div>

    <div v-if="loading" class="text-center py-4 text-blue-700 font-semibold">Loading...</div>

    <div v-else>
      <table v-if="filteredAcheminements.length > 0" class="reglette">
        <thead>
          <tr>
            <th v-for="num in 25" :key="num">{{ num }}</th>
          </tr>
        </thead>
        <tbody>
          <!-- First Row: main NDappel -->
          <tr>
            <td v-for="num in 25" :key="'row1-' + num" class="slot">
              <div v-if="getAcheminementsByColumn(num).length">
                <span
                  @mouseenter="showTooltip($event, getDestination(getAcheminementsByColumn(num)[0]))"
                  @mouseleave="hideTooltip"
                  @dblclick="goToAnnuaire(getAcheminementsByColumn(num)[0].numero.NDappel)"
                  :class="{ 'highlight-tech': isTechnologieHighlighted(getAcheminementsByColumn(num)[0]) }"
                  class="jack-number main"
                >
                  {{ getAcheminementsByColumn(num)[0].numero.NDappel }}
                </span>
                <small v-if="getAcheminementsByColumn(num)[0].description" class="desc">
                  {{ getAcheminementsByColumn(num)[0].description }}
                </small>
              </div>
            </td>
          </tr>

          <!-- Second Row: other acheminements for same NDappel -->
          <tr>
            <td v-for="num in 25" :key="'row2-' + num" class="slot">
              <div v-for="(extra, index) in getOtherAcheminements(num)" :key="index">
                <span
                  @mouseenter="showTooltip($event, getDestination(extra))"
                  @mouseleave="hideTooltip"
                  :class="{ 'highlight-tech': isTechnologieHighlighted(extra) }"
                  class="jack-number secondary"
                >
                  <span class="text-orange-700">//</span> {{ extra.acheminement }}
                </span>
                <small v-if="extra.description" class="desc">{{ extra.description }}</small>
              </div>
            </td>
          </tr>

          <!-- Third Row: other NDappel in same DIVERS -->
          <tr>
            <td v-for="num in 25" :key="'row3-' + num" class="slot">
              <div v-for="(extra, index) in getAcheminementsByColumn(num).slice(1)" :key="index">
                <span
                  @mouseenter="showTooltip($event, getDestination(extra))"
                  @mouseleave="hideTooltip"
                  :class="{ 'highlight-tech': isTechnologieHighlighted(extra) }"
                  class="jack-number secondary"
                >
                  <span class="text-orange-700">+</span> {{ extra.numero.NDappel }}
                </span>
                <small v-if="extra.description" class="desc">{{ extra.description }}</small>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div
      v-if="tooltip.visible"
      class="custom-tooltip"
      :style="{ top: tooltip.y + 'px', left: tooltip.x + 'px' }"
    >
      <div v-html="tooltip.text"></div>
    </div>
  </div>
</template>

<script>
import Layout from "@/Layouts/LayoutAutocom.vue";
import { router } from "@inertiajs/vue3";
import { route } from "ziggy-js";

export default {
  layout: Layout,
  props: { acheminements: Array, technologies: Array },
  data() {
    return {
      loading: false,
      searchNumber: "",
      selectedTechnologie: "",
      searchNDappel: "", // input field
      filteredAcheminements: [],
      tooltip: { visible: false, text: "", x: 0, y: 0 }
    };
  },
  computed: {
    availableDIVERSs() {
      let results = this.acheminements;
      if (this.selectedTechnologie) {
        results = results.filter(a => a.numero?.technologie?.id == this.selectedTechnologie);
      }
      const numbers = new Set();
      results.forEach(a => {
        const match = a.acheminement.match(/DIVERS\s+(\d+)/);
        if (match) numbers.add(parseInt(match[1]));
      });
      return Array.from(numbers).sort((a,b)=>a-b);
    }
  },
  methods: {
    onTechnologieChange() { this.loadDIVERSData(); },

    loadDIVERSData() {
      this.loading = true;
      setTimeout(() => {
        let results = this.acheminements;
        if (this.searchNumber && !isNaN(this.searchNumber)) {
          results = results.filter(a => a.acheminement.includes(`DIVERS ${this.searchNumber}`));
        }
        if (this.selectedTechnologie) {
          results = results.filter(a => a.numero?.technologie?.id == this.selectedTechnologie);
        }
        this.filteredAcheminements = results;
        this.loading = false;
      }, 100);
    },

    getAcheminementsByColumn(column) {
      return this.filteredAcheminements.filter(ach => ach.acheminement === `DIVERS ${this.searchNumber} ${column}`);
    },

    getOtherAcheminements(column) {
      const first = this.getAcheminementsByColumn(column)[0];
      if (!first) return [];
      const ndappel = first.numero?.NDappel;
      if (!ndappel) return [];
      return this.acheminements.filter(ach => ach.numero?.NDappel === ndappel && ach !== first);
    },

    getDestination(a) {
      if (!a) return "<div>No Destination</div>";
      const html = [];
      if (a.numero?.Position) html.push(`<div>📍 ${a.numero.Position}</div>`);
      if (a.numero?.destination) html.push(`<div>🏛 ${a.numero.destination.name}</div>`);
      if (a.numero?.organisme) html.push(`<div>🏢 ${a.numero.organisme.name}</div>`);
      if (a.description) html.push(`<div>ℹ️ ${a.description}</div>`);
      return html.join("");
    },

    isTechnologieHighlighted(a) { return this.selectedTechnologie && a.numero?.technologie?.id == this.selectedTechnologie; },
    showTooltip(e,text) { this.tooltip = { visible:true, text, x:e.pageX+15, y:e.pageY+15 }; },
    hideTooltip() { this.tooltip.visible = false; },
    goToAnnuaire(ndappel) { if(ndappel) router.visit(route('Annuaire.index',{ndappel})); },

    // NEW: Navigate to the NDappel's reglette
    goToNDappelReglette() {
      if (!this.searchNDappel) return;

      const match = this.acheminements.find(a => a.numero?.NDappel == this.searchNDappel);
      if (match) {
        const numMatch = match.acheminement.match(/DIVERS\s+(\d+)/);
        if (numMatch) {
          this.searchNumber = parseInt(numMatch[1]);
          this.loadDIVERSData();
        }
      }
    }
  },
  created() { this.loadDIVERSData(); }
};
</script>

<style scoped>
.reglette { border-collapse: separate; border-spacing: 4px; background: #333; padding: 8px; border-radius: 8px; box-shadow: inset 0 0 8px rgba(0,0,0,0.8); }
.reglette th { background: #222; color:#fff; padding:6px; font-weight:bold; text-align:center; border-radius:4px; }
.slot { background: linear-gradient(145deg,#555,#444); border-radius:6px; padding:4px; height:60px; vertical-align:middle; box-shadow: inset 0 -2px 3px rgba(0,0,0,0.5), inset 0 2px 3px rgba(255,255,255,0.1); transition: transform 0.2s; }
.slot:hover { transform: scale(1.03); }
.jack-number { display:inline-block; background: radial-gradient(circle at 30% 30%, #ffcc00, #cc9900); padding:4px 6px; border-radius:4px; font-weight:bold; color:#000; box-shadow: inset 0 -1px 2px rgba(0,0,0,0.4); cursor:pointer; transition: transform 0.2s, background 0.2s; }
.jack-number.main:hover { transform: scale(1.1); background: radial-gradient(circle at 30% 30%, #ffdd33,#cc9900); }
.jack-number.secondary { background: radial-gradient(circle at 30%30%, #00bfff,#0077aa); color:white; }
.desc { display:block; font-size:12px; color:#ccc; margin-top:4px; }
.highlight-tech { outline: 2px solid #ff5733; }
.text-orange-700 { color:#c05621; }
.custom-tooltip { position:absolute; background-color: rgba(0,0,0,0.95); color:white; padding:12px 16px; border-radius:6px; font-size:16px; max-width:400px; z-index:9999; pointer-events:none; }
</style>
