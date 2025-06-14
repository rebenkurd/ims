<template>
  <div class="bg-white p-4 rounded-lg shadow">
    <div class="flex justify-between items-center mb-3">
      <h1 class="text-xl font-semibold">
        World Bank Report
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
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium mb-1">Report Date</label>
          <input 
            type="date" 
            v-model="filters.report_date" 
            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Search</label>
          <input 
            type="text" 
            v-model="filters.search" 
            placeholder="Search report content"
            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
          >
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Report Type</label>
          <select 
            v-model="filters.report_type" 
            class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm"
          >
            <option value="all">All Reports</option>
            <option value="financial">Financial</option>
            <option value="economic">Economic</option>
            <option value="development">Development</option>
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
          @click="fetchWorldBankReports" 
          class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          Show
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center p-8">
      <Spinner class="my-6" />
    </div>

    <!-- Data Table -->
    <div v-else class="overflow-x-auto">
      <table class="table-auto w-full">
        <thead class="bg-gray-800 text-white">
          <tr>
            <TableHeaderCell class="border-b-2 p-2 text-left">#</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Report Date</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Title</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Type</TableHeaderCell>
            <TableHeaderCell class="border-b-2 p-2 text-left">Summary</TableHeaderCell>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(report, index) in reports" :key="report.id" class="animate-fade-in-down">
            <td class="border-b-2 p-2">{{ index + 1 }}</td>
            <td class="border-b-2 p-2">{{ formatDate(report.date) }}</td>
            <td class="border-b-2 p-2">{{ report.title }}</td>
            <td class="border-b-2 p-2">{{ report.type }}</td>
            <td class="border-b-2 p-2">{{ report.summary }}</td>
          </tr>
          <tr v-if="reports.length === 0">
            <td colspan="5" class="border-b-2 p-2 text-center">No reports found</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="!loading && reports.length > 0" class="flex justify-between items-center mt-5">
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
import Spinner from '@components/core/Spinner.vue';
import TableHeaderCell from '@components/core/table/TableHeaderCell.vue';
import { PrinterIcon } from '@heroicons/vue/24/outline';

const store = useReportStore();

// Initialize
onMounted(() => {
  store.fetchWorldBankReports();
});

// Reactive state
const loading = computed(() => store.loading);
const reports = computed(() => store.worldBankReports);
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
const fetchWorldBankReports = () => store.fetchWorldBankReports();
const resetFilters = () => {
  store.resetFilters();
  store.fetchWorldBankReports();
};
const changePage = (ev, link) => {
  ev.preventDefault();
  if (!link.url || link.active) return;
  store.changePage(link.url);
};

// Helpers
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-GB'); // Formats as DD/MM/YYYY
};

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
    const reportType = filters.value.report_type 
      ? filters.value.report_type === 'all' 
        ? 'All' 
        : filters.value.report_type.charAt(0).toUpperCase() + filters.value.report_type.slice(1)
      : 'All';

    const printContent = `
<!DOCTYPE html>
<html>
<head>
  <title>World Bank Report</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #1f2937; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .print-header { margin-bottom: 20px; }
    .print-title { text-align: center; font-size: 1.5rem; margin-bottom: 10px; }
    .print-filters { margin-bottom: 15px; }
    @page { size: auto; margin: 5mm; }
  </style>
</head>
<body>
  <div class="print-header">
    <h1 class="print-title">World Bank Report</h1>
    <div class="print-filters">
      <p><strong>Report Date:</strong> ${formatDate(filters.value.report_date)}</p>
      <p><strong>Report Type:</strong> ${reportType}</p>
      ${filters.value.search ? `<p><strong>Search:</strong> ${filters.value.search}</p>` : ''}
    </div>
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