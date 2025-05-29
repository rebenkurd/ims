<template>
  <div class="bg-white p-4 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
      <h1 class="text-xl font-semibold">Expired Products Report</h1>
      <button
        @click="printTable"
        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 flex items-center gap-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd" />
        </svg>
        Print
      </button>
    </div>
    
    <!-- Filter Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
      <div>
        <label class="block text-sm font-medium mb-1">Product Name</label>
        <input
          type="text"
          v-model="filters.search"
          placeholder="Search Name/Code"
          class="w-full p-2 border rounded"
          @keyup.enter="fetchExpiredProducts"
        >
      </div>
      <div>
        <label class="block text-sm font-medium mb-1">To Date</label>
        <input
          type="date"
          v-model="filters.to_date"
          class="w-full p-2 border rounded"
        >
      </div>
    </div>

    <div class="flex justify-end mb-4 gap-2">
      <button
        @click="resetFilters"
        class="px-4 py-2 border rounded hover:bg-gray-50"
      >
        Close
      </button>
      <button
        @click="fetchExpiredProducts"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
      >
        Show
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center p-8">
      <span class="text-gray-500">Loading...</span>
    </div>

    <!-- Data Table -->
    <div v-else class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200" id="expiredProductsTable">
        <thead class="bg-gray-800 text-white">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">#</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Product Code</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Product Name</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Expire Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Stock</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="(product, index) in products" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap">{{ index + 1 }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ product.code }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ product.name }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ formatDate(product.expire_date) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">{{ product.minimum_qty }}</td>
          </tr>
          <tr v-if="products.length === 0">
            <td colspan="5" class="px-6 py-4 text-center">No expired products found</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useReportStore } from '@store/report/';
import axiosClient from '@/axios';

const store = useReportStore();

// Initialize filters
const filters = computed({
  get: () => store.filters,
  set: (value) => store.updateFilters(value)
});

// Initialize with default values
filters.value = {
  search: '',
  to_date: new Date().toISOString().split('T')[0],
  report_type: 'expiredProducts'
};

// Reactive state
const loading = computed(() => store.loading);
const products = computed(() => store.expiredProducts);

// Actions
const fetchExpiredProducts = () => store.fetchExpiredProducts();
const resetFilters = () => {
  store.resetFilters();
  store.fetchExpiredProducts();
};

// Helpers
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-GB'); // Formats as DD/MM/YYYY
};

const printTable = () => {
  const printWindow = window.open('', '_blank');
  if (!printWindow) {
    alert('Please allow popups for printing');
    return;
  }

  const table = document.getElementById('expiredProductsTable').outerHTML;
  const printContent = `
    <html>
      <head>
        <title>Expired Products Report</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          table { width: 100%; border-collapse: collapse; margin-top: 20px; }
          th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
          th { background-color: #1f2937; color: white; }
          tr:nth-child(even) { background-color: #f2f2f2; }
          .print-header { text-align: center; margin-bottom: 20px; }
          .print-title { font-size: 1.5rem; font-weight: bold; margin-bottom: 10px; }
          .print-filters { margin-bottom: 15px; font-size: 0.9rem; }
          @page { size: auto; margin: 5mm; }
        </style>
      </head>
      <body>
        <div class="print-header">
          <div class="print-title">Expired Products Report</div>
          <div class="print-filters">
            <div><strong>Date:</strong> ${new Date().toLocaleDateString()}</div>
            ${filters.value.search ? `<div><strong>Search:</strong> ${filters.value.search}</div>` : ''}
            <div><strong>Expired Before:</strong> ${formatDate(filters.value.to_date)}</div>
          </div>
        </div>
        ${table}
        <script>
          setTimeout(() => {
            window.print();
            window.close();
          }, 200);
        <\/script>
      </body>
    </html>
  `;

  printWindow.document.write(printContent);
  printWindow.document.close();
};

// Initialize component
onMounted(() => {
  store.fetchExpiredProducts();
});
</script>