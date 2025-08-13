<template>
  <div class="p-6">

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="technologies..."
        class="border rounded px-3 py-2 w-full"
      />
    </div>

    <!-- Technologies CRUD Section -->
    <CrudSection
      title="✏️ Technologies"
      api="/technologies"
      :items="filteredTechnologies"
    />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import CrudSection from '@/Components/Crud/CrudSection.vue'
import Layout from '@/Layouts/LayoutEdit.vue'

defineOptions({ layout: Layout })

const props = defineProps({
  technologies: Array,
  classes: Array,
  types: Array,
})

const search = ref('')

const filteredTechnologies = computed(() => {
  if (!search.value) return props.technologies
  const query = search.value.toLowerCase()

  return props.technologies.filter(tech =>
    (tech.name && tech.name.toLowerCase().includes(query)) ||
    (tech.description && tech.description.toLowerCase().includes(query))
  )
})
</script>
