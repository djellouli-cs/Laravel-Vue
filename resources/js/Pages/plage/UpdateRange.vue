<script setup>
import Layout from '@/Layouts/LayoutNetwork.vue'
import { Head, useForm } from '@inertiajs/vue3'

defineOptions({ layout: Layout })

const form = useForm({
  ip1: '',
  ip2: '',
  direction: '',
})
</script>

<template>
  <Head title="Update IP Range Direction" />

  <div class="p-6 max-w-xl mx-auto bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">Update Direction for IP Range</h1>

    <form @submit.prevent="form.post(route('plage.range.update'))" novalidate>
      <!-- IP Start -->
      <div class="mb-4">
        <label for="ip1" class="block text-sm font-medium text-gray-700">IP Start</label>
        <input
          v-model="form.ip1"
          id="ip1"
          type="text"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
        />
        <p v-if="form.errors.ip1" class="text-sm text-red-600 mt-1">{{ form.errors.ip1 }}</p>
      </div>

      <!-- IP End -->
      <div class="mb-4">
        <label for="ip2" class="block text-sm font-medium text-gray-700">IP End</label>
        <input
          v-model="form.ip2"
          id="ip2"
          type="text"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
        />
        <p v-if="form.errors.ip2" class="text-sm text-red-600 mt-1">{{ form.errors.ip2 }}</p>
      </div>

      <!-- Direction -->
      <div class="mb-6">
        <label for="direction" class="block text-sm font-medium text-gray-700">Direction</label>
        <input
          v-model="form.direction"
          id="direction"
          type="text"
          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
        />
        <p v-if="form.errors.direction" class="text-sm text-red-600 mt-1">{{ form.errors.direction }}</p>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded"
        :disabled="form.processing"
      >
        Update Direction
      </button>

      <!-- Success Message -->
      <div
        v-if="$page.props.flash?.success"
        class="mt-4 p-3 bg-green-100 text-green-700 rounded-md text-sm"
      >
        {{ $page.props.flash.success }}
      </div>
    </form>
  </div>
</template>
