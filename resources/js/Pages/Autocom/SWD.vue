<template>
  <div>
    <h1>REPARTITEUR SWD</h1>

    <!-- Technologie Filter -->
    <select v-model="selectedTechnologie" @change="onTechnologieChange" class="select-field">
      <option value="">All Technologies</option>
      <option v-for="tech in technologies" :key="tech.id" :value="tech.id">
        {{ tech.name }}
      </option>
    </select>

    <!-- SWD Filter -->
    <select v-model="searchNumber" @change="loadSWDData" class="select-field">
      <option value="" disabled selected>REGLETTE SWD</option>
      <option v-for="num in availableSWDs" :key="num" :value="num">
        SWD {{ num }}
      </option>
    </select>

    <!-- Loading -->
    <div v-if="loading" class="loading">Loading...</div>

    <div v-else>
      <table v-if="filteredAcheminements.length > 0" class="reglette">
        <thead>
          <tr>
            <th v-for="num in 25" :key="num">{{ num }}</th>
          </tr>
        </thead>
        <tbody>
          <!-- First Row -->
          <tr>
            <td v-for="num in 25" :key="'row1-' + num" class="slot">
              <div v-if="getAcheminementsByColumn(num).length">
                <span
                  @mouseenter="showTooltip($event, getDestination(getAcheminementsByColumn(num)[0]))"
                  @mouseleave="hideTooltip"
                  :class="{ 'highlight-tech': isTechnologieHighlighted(getAcheminementsByColumn(num)[0]) }"
                  class="jack-number"
                >
                  {{ getAcheminementsByColumn(num)[0].numero.NDappel }}
                </span>
                <small v-if="getAcheminementsByColumn(num)[0].description" class="desc">
                  {{ getAcheminementsByColumn(num)[0].description }}
                </small>
              </div>
            </td>
          </tr>

          <!-- Extra Acheminements -->
          <tr>
            <td v-for="num in 25" :key="'row2-' + num" class="slot">
              <div v-for="(extra, index) in getAcheminementsByNDappel(num)" :key="index">
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

          <!-- Other NDappel -->
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

    <!-- Custom Tooltip -->
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

export default {
  layout: Layout,
  props: {
    acheminements: Array,
    technologies: Array
  },
  data() {
    return {
      loading: false,
      searchNumber: "",
      selectedTechnologie: "",
      filteredAcheminements: [],
      tooltip: {
        visible: false,
        text: "",
        x: 0,
        y: 0
      }
    };
  },
  computed: {
    availableSWDs() {
      let results = this.acheminements;

      if (this.selectedTechnologie) {
        results = results.filter(
          (a) => a.numero?.technologie?.id == this.selectedTechnologie
        );
      }

      const swdNumbers = new Set();
      results.forEach((a) => {
        const match = a.acheminement.match(/SWD\s+(\d+)/);
        if (match) {
          swdNumbers.add(parseInt(match[1]));
        }
      });

      return Array.from(swdNumbers).sort((a, b) => a - b);
    }
  },
  methods: {
    onTechnologieChange() {
      this.searchNumber = "";
    },

    loadSWDData() {
      this.loading = true;
      setTimeout(() => {
        let results = this.acheminements;

        if (this.searchNumber && !isNaN(this.searchNumber)) {
          results = results.filter((a) =>
            a.acheminement.includes(`SWD ${this.searchNumber}`)
          );
        }

        this.filteredAcheminements = results;
        this.loading = false;
      }, 200);
    },

    getAcheminementsByColumn(column) {
      return this.filteredAcheminements.filter(
        (ach) => ach.acheminement === `SWD ${this.searchNumber} ${column}`
      );
    },

    getAcheminementsByNDappel(column) {
      const first = this.getAcheminementsByColumn(column)[0];
      if (!first) return [];
      return this.filteredAcheminements.filter(
        (ach) =>
          ach.numero?.NDappel === first.numero?.NDappel &&
          ach.acheminement !== first.acheminement
      );
    },

    getDestination(acheminement) {
      if (!acheminement) return "<div>No Destination</div>";
      let html = [];

      if (acheminement?.numero?.fax) {
        html.push(`<div>📠 FAX</div>`);
      }
      if (acheminement?.numero?.Position) {
        html.push(`<div>📍 ${acheminement.numero.Position}</div>`);
      }
      if (acheminement?.numero?.destination) {
        html.push(`
          <div>
            🏛 <strong style="font-size:18px;">${acheminement.numero.destination.name}</strong><br>
            <span style="font-size:14px; color:#ccc;">${acheminement.numero.destination.name_fr}</span>
          </div>
        `);
      }
      if (acheminement?.numero?.organisme) {
        html.push(`
          <div>
            🏢 <strong style="font-size:18px;">${acheminement.numero.organisme.name}</strong><br>
            <span style="font-size:14px; color:#ccc;">${acheminement.numero.organisme.name_fr}</span>
          </div>
        `);
      }
      if (acheminement?.updated_at) {
        html.push(`<div>🗓 ${acheminement.updated_at.slice(0, 10)}</div>`);
      }
      if (acheminement?.description) {
        html.push(`<div>ℹ️ ${acheminement.description}</div>`);
      }
      if (acheminement?.acheminement) {
        html.push(`<div>🔌 ${acheminement.acheminement}</div>`);
      }

      return html.join("");
    },

    isTechnologieHighlighted(acheminement) {
      return (
        this.selectedTechnologie &&
        acheminement.numero?.technologie?.id == this.selectedTechnologie
      );
    },

    showTooltip(event, text) {
      this.tooltip.text = text;
      this.tooltip.x = event.pageX + 15;
      this.tooltip.y = event.pageY + 15;
      this.tooltip.visible = true;
    },

    hideTooltip() {
      this.tooltip.visible = false;
    }
  },
  created() {
    this.loadSWDData();
  }
};
</script>

<style scoped>
.select-field {
  margin-bottom: 20px;
  padding: 8px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

/* Reglette styling */
.reglette {
  border-collapse: separate;
  border-spacing: 4px;
  background: #333;
  padding: 8px;
  border-radius: 8px;
  box-shadow: inset 0 0 8px rgba(0, 0, 0, 0.8);
}

.reglette th {
  background: #222;
  color: #fff;
  padding: 6px;
  font-weight: bold;
  text-align: center;
  border-radius: 4px;
}

.slot {
  background: linear-gradient(145deg, #555, #444);
  border-radius: 6px;
  padding: 4px;
  height: 60px;
  vertical-align: middle;
  box-shadow:
    inset 0 -2px 3px rgba(0,0,0,0.5),
    inset 0 2px 3px rgba(255,255,255,0.1);
}

.jack-number {
  display: inline-block;
  background: radial-gradient(circle at 30% 30%, #ffcc00, #cc9900);
  padding: 4px 6px;
  border-radius: 4px;
  font-weight: bold;
  color: #000;
  box-shadow: inset 0 -1px 2px rgba(0,0,0,0.4);
  cursor: pointer;
}

.jack-number.secondary {
  background: radial-gradient(circle at 30% 30%, #00bfff, #0077aa);
  color: white;
}

.jack-number:hover {
  background: radial-gradient(circle at 30% 30%, #ffdd33, #cc9900);
}

.desc {
  display: block;
  font-size: 12px;
  color: #ccc;
  margin-top: 4px;
}

.highlight-tech {
  outline: 2px solid #ff5733;
}

.text-orange-700 {
  color: #c05621;
}

/* Tooltip styling */
.custom-tooltip {
  position: absolute;
  background-color: rgba(0, 0, 0, 0.95);
  color: white;
  padding: 12px 16px;
  border-radius: 6px;
  font-size: 16px;
  max-width: 400px;
  z-index: 9999;
  pointer-events: none;
}
</style>
