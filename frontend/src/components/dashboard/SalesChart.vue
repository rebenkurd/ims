<template>
  <div class="h-80">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, registerables } from 'chart.js';

// Register all Chart.js components
Chart.register(...registerables);

const props = defineProps({
  data: {
    type: Object,
    required: true
  }
});

const chartCanvas = ref(null);
let chartInstance = null;

onMounted(() => {
  renderChart();
});

watch(() => props.data, () => {
  if (chartInstance) {
    chartInstance.destroy();
  }
  renderChart();
}, { deep: true });

const renderChart = () => {
  if (chartCanvas.value && props.data) {
    chartInstance = new Chart(chartCanvas.value, {
      type: 'bar',
      data: props.data,
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return new Intl.NumberFormat('en-US', {
                  style: 'currency',
                  currency: 'USD'
                }).format(value);
              }
            }
          }
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function(context) {
                return new Intl.NumberFormat('en-US', {
                  style: 'currency',
                  currency: 'USD'
                }).format(context.raw);
              }
            }
          },
          legend: {
            display: false
          }
        }
      }
    });
  }
};
</script>