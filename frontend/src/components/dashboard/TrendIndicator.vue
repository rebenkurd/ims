<template>
  <span class="trend-indicator" :class="trendClass">
    <svg v-if="value === 'up'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd" />
    </svg>
    <svg v-else-if="value === 'down'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z" clip-rule="evenodd" />
    </svg>
    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
    </svg>
  </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  value: {
    type: String,
    default: 'neutral',
    validator: (value) => ['up', 'down', 'neutral'].includes(value)
  }
});

const trendClass = computed(() => {
  return {
    'text-green-500': props.value === 'up',
    'text-red-500': props.value === 'down',
    'text-gray-500': props.value === 'neutral'
  };
});
</script>

<style scoped>

</style>