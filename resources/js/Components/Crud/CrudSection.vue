<template>
  <section class="mb-10 border p-4 rounded shadow-sm">
    <h2 class="text-xl font-bold mb-4">{{ title }}</h2>

    <CrudForm
      :modelValue="form"
      @submit="save"
      @update:modelValue="v => form = v"
    />

    <CrudTable
      :items="items"
      @edit="edit"
      @delete="destroy"
    />
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import CrudForm from './CrudForm.vue'
import CrudTable from './CrudTable.vue'

const props = defineProps({
  title: String,
  api: String,
  items: {
    type: Array,
    default: () => [],
  },
})

const form = ref({ id: null, name: '' })

const save = () => {
  console.log('Saving...', form.value)

  if (form.value.id) {
    router.put(`${props.api}/${form.value.id}`, form.value, {
      preserveScroll: true,
      onSuccess: () => resetForm(),
      onError: (errors) => console.error(errors)
    })
  } else {
    router.post(props.api, form.value, {
      preserveScroll: true,
      onSuccess: () => resetForm(),
      onError: (errors) => console.error(errors)
    })
  }
}

const edit = (item) => {
  console.log('Edit:', item)
  form.value = { ...item }
}

const destroy = (id) => {
  if (confirm('Are you sure you want to delete this item?')) {
    router.delete(`${props.api}/${id}`, {
      preserveScroll: true,
      onError: (errors) => console.error(errors)
    })
  }
}

const resetForm = () => {
  form.value = { id: null, name: '' }
}
</script>
