<template>
  <div class="p-6">

    <CrudSection
      title="📗 Types"
      api="/manageType"
      :items="types"
    />
  </div>
</template>

<script setup>
import CrudSection from '@/Components/Crud/CrudSection.vue'
import Layout from '@/Layouts/LayoutEdit.vue'

defineOptions({ layout: Layout })

defineProps({
  types: Array,
})
</script>
