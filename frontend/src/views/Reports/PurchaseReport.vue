<template>
  <div class="bg-white p-4 rounded-lg shadow">
    <div class="flex justify-between items-center mb-3">
      <h1 class="text-xl font-semibold">
        Purchase Report
      </h1>
      <button 
        @click="printTable"
        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 flex items-center gap-1"
      >
        <PrinterIcon class="h-5 w-5" />
        <span>Print</span>
      </button>
    </div>

    <!-- Filter Section -->
    <div class="flex justify-between border-b-2 pb-3 mb-4">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">From Date</label>
          <input 
            type="date" 
            v-model="filters.from_date" 
            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">To Date</label>
          <input 
            type="date" 
            v-model="filters.to_date" 
            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Supplier</label>
          <select 
            v-model="filters.supplier_id" 
            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
          >
            <option value="">All Suppliers</option>
            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
              {{ supplier.name }}
            </option>
          </select>
        </div>
  <div>
    <label class="block text-sm font-medium mb-1">Payment Status</label>
    <select 
      v-model="filters.payment_status" 
      class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
    >
      <option value="all">All</option>
      <option value="paid">Paid</option>
      <option value="partial">Partial</option>
      <option value="due">Due</option>
    </select>
  </div>

      </div>

      <div class="flex items-end gap-2">
        <button 
          @click="resetFilters" 
          class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50"
        >
          Reset
        </button>
        <button 
          @click="fetchPurchases" 
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          Show
        </button>
      </div>
    </div>

    <!-- Data Table -->
    <div class="overflow-x-auto">
      <table class="table-auto w-full">
        <thead class="bg-gray-800 text-gray-800">
          <tr>
            <TableHeaderCell class="border-b-2 p-2 text-left">Purchase Code</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Date</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Supplier</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Total</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Paid</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Due</TableHeaderCell>
          </tr>
        </thead>
                <tbody v-if="loading">
            <tr>
              <td colspan="11">
      <Spinner class="my-6" />

              </td>
            </tr>
          </tbody>
        <tbody v-else>
          <tr v-for="purchase in purchases" :key="purchase.id" class="animate-fade-in-down">
            <td class="border-b-2 p-2">{{ purchase.purchase_code }}</td>
            <td class="border-b-2 p-2">{{ formatDate(purchase.created_at) }}</td>
            <td class="border-b-2 p-2">{{ purchase.supplier?.name }}</td>
            <td class="border-b-2 p-2">${{ purchase.total_amount  || 0.00 }}</td>
            <td class="border-b-2 p-2">${{ purchase.paid_amount || 0.00 }}</td>
            <td class="border-b-2 p-2">${{ purchase.due_amount  || 0.00 }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="!loading && purchases.length > 0" class="flex justify-between items-center mt-5">
      <span>
        Showing from {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }}
      </span>
      <nav 
        v-if="pagination.total > pagination.per_page"
        class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
        aria-label="Pagination"
      >
        <a
          v-for="(link, i) in pagination.links"
          :key="i"
          :disabled="!link.url"
          href="#"
          @click="changePage($event, link)"
          aria-current="page"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
          :class="[
            link.active
              ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
              : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
            i === 0 ? 'rounded-l-md' : '',
            i === pagination.links.length - 1 ? 'rounded-r-md' : '',
            !link.url ? ' bg-gray-100 text-gray-700': ''
          ]"
          v-html="link.label"
        >
        </a>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useReportStore } from '@store/report/';
import axiosClient from '@/axios';
import Spinner from '@components/core/Spinner.vue';
import TableHeaderCell from '@components/core/table/TableHeaderCell.vue';
import { PrinterIcon } from '@heroicons/vue/24/outline';

const store = useReportStore();
const suppliers = ref([]);

// Initialize
onMounted(async () => {
  await fetchSuppliers();
  store.fetchPurchases();
});

// Fetch suppliers using your existing endpoint
const fetchSuppliers = async () => {
  try {
    const response = await axiosClient.get('/suppliers-for-select');
    suppliers.value = response.data;
  } catch (error) {
    console.error('Error fetching suppliers:', error);
  }
};

// Reactive state
const loading = computed(() => store.loading);
const purchases = computed(() => store.purchases);
const pagination = computed(() => ({
  from: store.pagination.from,
  to: store.pagination.to,
  total: store.pagination.total,
  per_page: store.pagination.per_page,
  links: store.pagination.links
}));

const filters = computed({
  get: () => store.filters,
  set: (value) => store.updateFilters(value)
});

// Actions
const fetchPurchases = () => store.fetchPurchases();
const resetFilters = () => {
  store.resetFilters();
  store.fetchPurchases();
};
const changePage = (ev, link) => {
  ev.preventDefault();
  if (!link.url || link.active) return;
  store.changePage(link.url);
};

// Helpers
const formatDate = (dateString) => new Date(dateString).toLocaleDateString();


const printTable = () => {
  try {
    const printWindow = window.open('', '_blank');
    if (!printWindow) {
      console.error('Could not open print window. Please allow popups.');
      return;
    }

    const table = document.querySelector('.overflow-x-auto table');
    if (!table) {
      console.error('Could not find table to print');
      printWindow.close();
      return;
    }

    const tableClone = table.cloneNode(true);
    const supplierName = filters.supplier_id 
      ? suppliers.find(s => s.id === filters.supplier_id)?.name || 'All'
      : 'All';
    
    const paymentStatus = filters.payment_status 
      ? filters.payment_status === 'all' 
        ? 'All' 
        : filters.payment_status.charAt(0).toUpperCase() + filters.payment_status.slice(1)
      : 'All';

    const printContent = `
<!DOCTYPE html>
<html>
<head>
  <title>Purchase Report</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #1f2937; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .print-header { margin-bottom: 20px; }
    .print-title { text-align: center; font-size: 1.5rem; margin-bottom: 10px; }
    @page { size: auto; margin: 5mm; }
  </style>
</head>
<body>
  <div class="print-header">
    <h1 class="print-title">Purchase Report</h1>
    <p><strong>Date Range:</strong> ${filters.from_date || ''} to ${filters.to_date || ''}</p>
    <p><strong>Supplier:</strong> ${supplierName}</p>
    <p><strong>Payment Status:</strong> ${paymentStatus}</p>
  </div>
  ${tableClone.outerHTML}
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
  } catch (error) {
    console.error('Printing error:', error);
  }
};

</script>