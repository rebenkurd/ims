<template>
  <div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-800">{{ title }}</h2>
    <p v-if="subtitle" class="text-sm text-gray-500">{{ subtitle }}</p>
  </div>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true
  },
  subtitle: {
    type: String,
    default: ''
  }
});
</script>